<?php 

    if (session_status() == PHP_SESSION_NONE) {
        include("seguridad.php");
    }

    

    $nombreusuario = $_SESSION["nombre"];
    $dni = $_SESSION["dni"];
    //$rol = $_SESSION["rol"]
 

    if (($_SESSION["rol"] == "usuario")) {

?>         

        <aside class="asidedca">
            <div class="form-login">
                <h3>Hola, <?php echo $nombreusuario?></h3>";

                <div class="form-login enlaces-user">
                    <a href="miCuenta.php">Mi cuenta</a>
                    <a href="misPedidos.php">Mis Pedidos</a>
                    <a href="resetpassword.php">Actualizar Contraseña</a>
                    <a href="salir.php">Cerrar Sesión</a>
                </div>

                <?php
                if (isset($_GET['resetcontraseña']) && $_GET['resetcontraseña'] == 'OK') {
                    echo '<p class="mensaje-contraseña">Contraseña actualizada correctamente</p>';
                }
                ?>
            </div>
        </aside>

<?php

    } else  if (($_SESSION["rol"] == "administrador")) {

?>

        <aside class="asidedca">
            <div class="form-login">
                <h3>Hola, <?php echo $nombreusuario?></h3>

                <div class="form-login enlaces-user">
                    <a href="clientes.php">Clientes</a>
                    <a href="empleados.php">Empleados</a>
                    <a href="menucategorias.php">Categorías</a>
                    <a href="menuarticulos.php">Artículos</a>
                    <a href="">Pedidos</a>
                    <a href="resetpassword.php">Actualizar Contraseña</a>
                    <a href="salir.php">Cerrar Sesión</a>
                </div>
            </div>
        </aside>
     

<?php

    } else {

?>

        <aside class="asidedca">
            <div class="form-login">
                <h3>Hola, <?php echo $nombreusuario?></h3>

                <div class="form-login enlaces-user">
                    <a href="clientes.php">Clientes</a>
                    <a href="menucategorias.php">Categorías</a>
                    <a href="menuarticulos.php">Artículos</a>
                    <a href="">Pedidos</a>
                    <a href="resetpassword.php">Actualizar Contraseña</a>
                    <a href="salir.php">Cerrar Sesión</a>
                </div>
            </div>
        </aside>


    

<?php

    }

?>

        