<?php
    session_start();
  include 'La-carta.php';
  $cart = new Cart;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./css/styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>
       function updateCartItem(obj,id){
            $.get("AccionCarta.php", {action:"updateCartItem", id:id, qty:obj.value}, function(data){
                if(data == 'ok'){
                    location.reload();
                }else{
                    alert('Cart update failed, please try again.');
                }
            });
        }
    </script>
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
 
            <div class="contenido-tabla">

                <div class="tabla">

                    <h3>Tu Cesta</h3> 

                    <table>
                        <tr id="campos">
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Descuento</th>
                            <th>Subtotal</th>
                            <th class="borrarUser"></th>
                        </tr>

<?php


                            $total_items = $cart->total_items();
                            if($total_items > 0) {
                                //get cart items from session
                                $cartItems = $cart->contents();
                                foreach($cartItems as $item){

                                    $porcentajeDescuento = isset($item["descuento"]) ? $item["descuento"] * 100 : 0;
                                    if (isset($item["price"], $item["descuento"], $item["qty"])) {
                                        $precioConDescuento = $item["price"] * (1 - $item["descuento"]);
                                        $subtotalConDescuento = $precioConDescuento * $item["qty"];
                                    } else {
                                        $subtotalConDescuento = 0;
                                    }
?>

                        <tr class="campos-cesta">
                            <td><?php echo $item["id"]; ?></td>
                            <td><?php echo $item["name"]; ?></td>
                            <td><?php echo $item["price"].' €'; ?></td>
                            <td><input type="number" class="cantidad" value="<?php echo $item["qty"]; ?>" onchange="updateCartItem(this, '<?php echo $item['rowid']; ?>')"></td>
                            <td><?php echo $porcentajeDescuento . "%"; ?></td>
                            <td><?php echo $subtotalConDescuento . ' €'; ?></td> 
                            <td>
                            <a href="AccionCarta.php?action=removeCartItem&id=<?php echo $item["rowid"]; ?>" class="btn btn-danger"><img src='./images/borrar.jpg' alt='Borrar'></a>
                            </td>
                        </tr>
<?php                   
                                } 

                            } else { ?>

                        <tr>
                            <td></td>
                            <td colspan="5"><p class="cesta-vacia">Tu cesta está vacía</p></td>
                        </tr>
<?php 
                        } ?>
    
                        <tr>
                            <?php if($cart->total_items() > 0) { ?>
                                <td colspan="2"></td>
                                <td colspan="2"></td>
                                <td colspan="1"></td>
                                <td ><strong class="total-pedido" id="total_pedido">TOTAL PEDIDO: <?php echo $cart->total().' €'; ?></strong></td>
                                <td colspan="1"></td>
                            
<?php 
                                } ?>
                        </tr>
    
                    </table>

                    <div class="botones-form">
                        <a class="btn-registro" href="index.php">Seguir Comprando</a>
                        <a class="btn-registro" href="">Realizar Pedido</a>
                    </div>

                </div>    

            </div>

        </main>

        <?php 
            if (isset($_SESSION['autenticado']) && $_SESSION['autenticado'] === true) {
                // Si está autenticado, incluye el contenido de la zona
                include("zona.php");
            } else {
                // Si no está autenticado, incluye el formulario de inicio de sesión
                include("login.php");
            }
        ?>

    </div>

    <?php include("footer.php");?>

    <script src="js.js"></script>
</body>
</html>                               