<?php

include("seguridad.php");

include ('La-carta.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if(isset($_REQUEST["dni"])) {
        $dni = $_REQUEST["dni"];
    }

    try {

        include("conectar_db.php");
        $con = new Conexion();
        $conexion = $con->conectar_db();
        $stmt = $conexion->prepare('UPDATE clientes SET activo = 0 WHERE dni = :dni');
        $stmt->bindParam(':dni', $dni, PDO::PARAM_STR);
        $stmt->execute();

        header("Location: empleados.php");
        
    } catch(PDOException $e) {
            echo 'Error al eliminar el empleado: ' . $e->getMessage();
    }
        
      
}

?>


<?php
if(isset($_REQUEST["dni"])) {
    $dni = $_REQUEST["dni"];

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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

                <form class="formulario" action="borrarempleado.php?dni=<?php echo $dni; ?>" method="post">

                    <h2>Eliminar Empleado</h2>

                    <div class="form-campos form-cambio-contraseña">

                        <label for="contraseña">¿Deseas eliminar este empleado?</label> 

                        <div class="botones-form">
                            <button class="btn-registro" type="submit">Eliminar</button>
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