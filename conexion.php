<?php

    include("conectar_db.php");

    $email = $_REQUEST["email"];
    $contrasena = $_REQUEST["contrasena"];

    try {
        $con = new Conexion();
        $verificarLogin = $con->verificarLogin($email);

      
        if (!$verificarLogin || !password_verify($contrasena, $verificarLogin["contrasena"])) {

            header("Location: index.php?error=1");
            exit();

        } else {

            session_start();
            $_SESSION["autenticado"] = true;
            $_SESSION["nombre"] = $verificarLogin["nombre"];
            $_SESSION["dni"] = $verificarLogin["dni"];
            $_SESSION["rol"] = $verificarLogin["rol"];


            // Redireccionar al usuario después de iniciar sesión
            header("Location: index.php");
            exit();
        }

    } catch (PDOException $e) {
        echo "Error en la consulta: " . $e->getMessage();
        die();
    }


?>
