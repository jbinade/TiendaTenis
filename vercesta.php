<?php
    include ("claseCarrito.php");
    $carrito = new Carrito;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./css/styles.css">
    <script>
       function updateCartItem(button, codigo, action, precio, descuento) {
            let cantidadInput = button.parentElement.querySelector('.cantidad');
            let cantidad = parseInt(cantidadInput.value);
            let subtotalElement = document.getElementById('subtotal_' + codigo);
            let subtotal = parseFloat(subtotalElement.innerText.replace(' €', ''));

            if (action === 'incrementar') {
                cantidad++;
                subtotal += parseFloat(precio * (1 - descuento));
            } else if (action === 'decrementar') {
                if (cantidad > 1) {
                    cantidad--;
                    subtotal -= parseFloat(precio * (1 - descuento));
                } else {
                    // Si la cantidad es 1 y hay más de un artículo en la cesta, elimina la fila
                    if (document.querySelectorAll('.campos-cesta').length > 1) {
                        button.closest('tr').remove();
                    } else {

                        // Si solo hay un artículo en la cesta, elimina la fila que muestra el total del pedido
                        document.querySelector('.total-pedido').closest('tr').remove();

                        // Si solo hay un artículo en la cesta, reemplaza la fila con el mensaje de cesta vacía
                        let nuevaFila = document.createElement('tr');
                        nuevaFila.innerHTML = `
                            <td></td>
                            <td colspan="5"><p class="cesta-vacia">Tu cesta está vacía</p></td>
                        `;
                        button.closest('tr').replaceWith(nuevaFila);
                        return;
                    }
                }
            }

            cantidadInput.value = cantidad;
            subtotalElement.innerText = subtotal.toFixed(2) + ' €';

            // Actualizar subtotal y total del pedido
            updateCartTotals();
        }


        function updateCartTotals() {
            let subtotalElements = document.querySelectorAll('.campos-cesta td[id^="subtotal_"]');
            let totalPedidoElement = document.getElementById('total_pedido');
            let totalPedido = 0;

            subtotalElements.forEach(function(element) {
                totalPedido += parseFloat(element.innerText.replace(' €', ''));
            });

            totalPedidoElement.innerText = 'TOTAL PEDIDO: ' + totalPedido.toFixed(2) + ' €';
        }

    </script>
</head>
<body>
    <?php include("conectar_db.php");?>
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


                            $total_items = $carrito->total_items();
                            if($total_items > 0) {
                                //get cart items from session
                                $cartItems = $carrito->contents();
                                foreach($cartItems as $item){

                                    $porcentajeDescuento = $item["descuento"] * 100;

                                    // Calcular el subtotal con el descuento aplicado
                                    $precioConDescuento = $item["precio"] * (1 - $item["descuento"]);
                                    $subtotalConDescuento = $precioConDescuento * $item["cantidad"];
                            
?>

                        <tr class="campos-cesta">
                            <td><?php echo isset($item["codigo"]) ? $item["codigo"] : ""; ?></td>
                            <td><?php echo isset($item["nombre"]) ? $item["nombre"] : ""; ?></td>
                            <td><?php echo isset($item["precio"]) ? $item["precio"].' €' : ""; ?></td>
                            <td>
                                <button class="btn-decrementar" onclick="updateCartItem(this, '<?php echo isset($item['codigo']) ? $item['codigo'] : ''; ?>', 'decrementar', <?php echo $item['precio']; ?>, <?php echo $item['descuento']; ?>)">-</button>
                                <input type="number" class="cantidad" value="<?php echo isset($item['cantidad']) ? $item['cantidad'] : ''; ?>" disabled>
                                <button class="btn-incrementar" onclick="updateCartItem(this, '<?php echo isset($item['codigo']) ? $item['codigo'] : ''; ?>', 'incrementar', <?php echo $item['precio']; ?>, <?php echo $item['descuento']; ?>)">+</button>
                            </td>
                            <td><?php echo $porcentajeDescuento . "%"; ?></td>
                            <td id="subtotal_<?php echo isset($item["codigo"]) ? $item["codigo"] : ""; ?>"><?php echo isset($item["subtotal"]) ? $subtotalConDescuento . ' €' : ""; ?></td> 
                            <td>
                                <a href="AccionCarta.php?action=removeCartItem&codigo=<?php echo isset($item["codigo"]) ? $item["codigo"] : ""; ?>" class="btn" onclick="return confirm('Confirma eliminar?')"><img src='./images/borrar.jpg' alt='Borrar'></a>
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
                            <?php if($carrito->total_items() > 0) { ?>
                                <td colspan="2"></td>
                                <td colspan="2"></td>
                                <td colspan="1"></td>
                                <td ><strong class="total-pedido" id="total_pedido">TOTAL PEDIDO: <?php echo $carrito->total().' €'; ?></strong></td>
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