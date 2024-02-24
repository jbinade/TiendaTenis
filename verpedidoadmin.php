<?php
include("seguridad.php");
include ('La-carta.php');
include("conectar_db.php");

if(isset($_REQUEST["pedido"])) {
    $idPedido = $_REQUEST["pedido"];

}

$con = new Conexion();
$conexion = $con->conectar_db();

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
 
            <div class="contenido-tabla">

                <div class="tabla">

                    <h2>DATOS DEL PEDIDO</h2> 

                    <table class="consultar-pedido">
                        <?php
                        try {

                            $stmtPedido = $conexion->prepare("SELECT * FROM pedidos WHERE idPedido = :idPedido");
                            $stmtPedido->bindParam(':idPedido', $idPedido, PDO::PARAM_STR);
                            $stmtPedido->execute();
                            $resPedido = $stmtPedido->fetch(PDO::FETCH_OBJ);

                        } catch(PDOException $e) {
                            echo 'Error al seleccionar el pedido: ' . $e->getMessage();
                        }
                        ?>
                            
                            <tr id="campos">
                                <th>Num. Pedido</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Estado</th>
                            </tr>

                            <tr>
                                <td><?php echo $resPedido->idPedido; ?></td>
                                <td><?php echo $resPedido->fecha; ?></td>
                                <td><?php echo $resPedido->total; ?></td>
                                <td><?php echo $resPedido->estado; ?></td>
                            </tr>
                    </table>

                    <h4>TUS DATOS:</h4>

                    <table class="consultar-pedido">

                        <?php
                        try {
                            $stmtCliente = $conexion->prepare("SELECT * FROM clientes WHERE dni = :dni");
                            $stmtCliente->bindParam(':dni', $resPedido->codCliente, PDO::PARAM_STR);
                            $stmtCliente->execute();
                            $resCliente = $stmtCliente->fetch(PDO::FETCH_OBJ);

                        } catch(PDOException $e) {
                            echo 'Error al seleccionar el pedido: ' . $e->getMessage();
                        }
                        
                        ?>

                        <tr id="campos">
                            <th>DNI</th>
                            <th>Nombre</th>
                            <th>Dirección</th>
                            <th>Localidad</th>
                            <th>Provincia</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                        </tr>

                        <tr>
                            <td><?php echo $resCliente->dni; ?></td>
                            <td><?php echo $resCliente->nombre; ?></td>
                            <td><?php echo $resCliente->direccion; ?></td>
                            <td><?php echo $resCliente->localidad; ?></td>
                            <td><?php echo $resCliente->provincia; ?></td>
                            <td><?php echo $resCliente->telefono; ?></td>
                            <td><?php echo $resCliente->email; ?></td>
                        </tr>
                    </table>

                    <h4>DETALLE:</h4>

                    <table class="consultar-pedido">
                        <tr id="campos">
                            <th>Artículo</th>
                            <th>Nombre</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Descuento</th>
                            <th>Subtotal</th>
                        </tr>

<?php

                        $stmtLinea = $conexion->prepare("SELECT * FROM lineapedido WHERE numPedido = :numPedido");
                        $stmtLinea->bindParam(':numPedido', $resPedido->idPedido, PDO::PARAM_STR);
                        $stmtLinea->execute();
                        
                        while ($resLinea = $stmtLinea->fetch(PDO::FETCH_OBJ)) {
                            try {

                                $stmtArticulo = $conexion->prepare("SELECT * FROM articulos WHERE codigo = :codigo");
                                $stmtArticulo->bindParam(':codigo', $resLinea->codArticulo, PDO::PARAM_STR);
                                $stmtArticulo->execute();
                                $resArticulo = $stmtArticulo->fetch(PDO::FETCH_OBJ);
                            } catch(PDOException $e) {
                                echo 'Error al seleccionar el pedido: ' . $e->getMessage();
                            }


                            echo "<tr>";
                                echo "<td>" . $resLinea->codArticulo . "</td>";
                                echo "<td>" . $resArticulo->nombre . "</td>";
                                echo "<td>" . $resLinea->cantidad . "</td>";
                                echo "<td>" . $resLinea->precio . " €</td>";
                                echo "<td>" . $resLinea->descuento . "%</td>";
                                echo "<td>" . $resLinea->preciototal . " €</td>";
                            echo "</tr>";
                        }
                        
?>
                        
                            
                    </table>

                    <div class="botones-form">
                        <a class="btn-registro" href="estadopedido.php?pedido=<?php echo $resPedido->idPedido; ?>&estado=<?php echo $resPedido->estado; ?>">Actualizar Estado</a>
                        <a class="btn-registro" href="borrarpedido.php?pedido=<?php echo $resPedido->idPedido; ?>&estado=<?php echo $resPedido->estado; ?>">Cancelar Pedido</a>
                        <a class="btn-registro" href="pedidos.php">Volver a Pedidos</a>
                    </div>

                </div>

            </div>

        </main>

        <?php include("zona.php")?>

    </div>

    <?php include("footer.php");?>

  
</body>
</html>                                   