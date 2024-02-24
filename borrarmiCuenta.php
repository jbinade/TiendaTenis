<?php

include("seguridad.php");
include ('La-carta.php');

$rol = $_SESSION["rol"];

$rol = $_SESSION["rol"];

if ($rol !== "usuario") {
    header("Location: index.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include("conectar_db.php");

    $dni = $_SESSION["dni"];
    
   

    try {

        $con = new Conexion();
        $conexion = $con->conectar_db();
        $stmt = $conexion->prepare('UPDATE clientes SET activo = 0 WHERE dni = :dni');
        $stmt->bindParam(':dni', $dni, PDO::PARAM_STR);
        $stmt->execute();

        header("Location: salir.php");
        
    } catch(PDOException $e) {
            echo 'Error al eliminar el cliente: ' . $e->getMessage();
    }
        
      
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TENNISMATCH</title>
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <?php include("conectar_db.php");?>
    <?php include("funciones.php");?>
    <?php include("header.php");?>
    
    <div class="contenedor">
        
            <aside class="asideizq">
                
                <div class="desplegable">
                        <?php

                            $con = new Conexion();
                            $conexion = $con->conectar_db();
                            // Realizar la consulta para obtener las categorías principales
                            $stmtCategorias = $conexion->prepare("SELECT * FROM categorias WHERE codcategoriapadre IS NULL AND activo = 1");
                            $stmtCategorias->execute();

                            // Iterar sobre las categorías principales
                            while ($categoria = $stmtCategorias->fetch(PDO::FETCH_ASSOC)) {
                                echo '<div class="categoria-desplegable">';
                                echo '<button class="btn-desplegable" onclick="menuDesplegable(\'' . $categoria['nombre'] . '\', this)">' . $categoria['nombre'] . ' +</button>';
                                echo '<ul class="enlaces-desplegable" id="' . $categoria['nombre'] . '-menu">';

                                // Realizar la consulta para obtener las subcategorías de esta categoría principal
                                $stmtSubcategorias = $conexion->prepare("SELECT * FROM categorias WHERE codcategoriapadre = :codcategoriapadre AND activo = 1");
                                $stmtSubcategorias->bindParam(':codcategoriapadre', $categoria['codigo'], PDO::PARAM_INT);
                                $stmtSubcategorias->execute();

                                echo '<li><a href="articulos.php?cod=' . $categoria['codigo'] . '">Ver Todo</a></li>';
                                // Iterar sobre las subcategorías y mostrarlas como enlaces
                                while ($subcategoria = $stmtSubcategorias->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<li><a href="articulos.php?cod=' . $subcategoria['codigo'] . '">' . $subcategoria['nombre'] . '</a></li>';
                                }

                                echo '</ul>';
                                echo '</div>';
                            }
                        ?>

                </div>

            </aside>

            <main class="contenido-principal">

                <form class="formulario" action="borrarmiCuenta.php" method="post">

                    <h2>Eliminar Cuenta</h2>

                    <div class="form-campos form-cambio-contraseña">

                        <label for="contraseña">¿Deseas eliminar tu cuenta?</label> 

                        <div class="botones-form">
                            <button class="btn-registro" type="submit">Enviar</button>
                            <a class="btn-registro" href="index.php">Cancelar</a>
                        </div>
                    </div>
                </form>    
    
            </main>

        <?php include("zona.php");?>

    </div>

    <?php include("footer.php");?>

 
</body>
</html>              