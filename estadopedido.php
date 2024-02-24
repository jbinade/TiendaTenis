<?php

include("seguridad.php");

include ('La-carta.php');

$rol = $_SESSION["rol"];

if ($rol == "usuario") {
    header("Location: index.php");
}

include("conectar_db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if(isset($_REQUEST["estado"])) {
        $estado = $_REQUEST["estado"];
    }

    if(isset($_REQUEST["pedido"])) {
        $idPedido = $_REQUEST["pedido"];
    }

    try {

        
        $con = new Conexion();
        $conexion = $con->conectar_db();
        $stmt = $conexion->prepare('UPDATE pedidos SET estado = :estado WHERE idPedido = :idPedido');
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindParam(':idPedido', $idPedido, PDO::PARAM_STR);
        $stmt->execute();

        header("Location: pedidos.php");
        
    } catch(PDOException $e) {
            echo 'Error al eliminar el articulo: ' . $e->getMessage();
    }
        
      
}

?>



<?php
if(isset($_REQUEST["pedido"])) {
    $idPedido = $_REQUEST["pedido"];

}

if(isset($_REQUEST["estado"])) {
    $estado = $_REQUEST["estado"];

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
            
            <form class="formulario" action="estadopedido.php?pedido=<?php echo $idPedido; ?>" method="post">

                <h3>Actualizar el Estado del Pedido</h3>

                <div class="form-campos form-cambio-contraseña">

                    <label for="categoria"></label>
                        <select class="opcion-desplegable" name="estado" id="estado" selected>
                            <option value=""><?php echo $estado; ?></option>
                            <option value="Creado">Creado</option>
                            <option value="En Preparación">En Preparación</option>
                            <option value="Enviado">Enviado</option>
                           
                        </select>

                    
                        
                    <div class="botones-form dni-empleado">
                        <button class="btn-registro" type="submit">Actualizar</button>
                        <a class="btn-registro" href="pedidos.php">Cancelar</a>
                    </div>
                </div>
            </form>
           
        </main>

        <?php include("zona.php");?>

    </div>

    <?php include("footer.php");?>

   
</body>
</html>         