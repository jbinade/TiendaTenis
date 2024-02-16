<?php

    session_start();
    include 'La-carta.php';
    
    
    
    if (isset($_SESSION["rol"])) {

        $dni = $_SESSION["dni"];

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
 
            <div class="contenido-tabla">

                <div class="tabla">

                    <h3 class="tu-pedido">Tu Pedido</h3> 

                    <table>
                        <tr id="campos">
                            
                        </tr>

<?php

                            $cart = new Cart;
                            $total_items = $cart->total_items();
                            if($total_items > 0) {
                                //get cart items from session
                                $cartItems = $cart->contents();
                                foreach($cartItems as $item){

                                    $con = new Conexion();
                                    $conexion = $con->conectar_db();
                                    $stmt = $conexion->prepare("SELECT * FROM articulos WHERE codigo = :codigo");
                                    $stmt->bindParam(':codigo', $item["id"]);
                                    $stmt->execute();
                                    $fila = $stmt->fetch(PDO::FETCH_ASSOC);

                                    $porcentajeDescuento = isset($item["descuento"]) ? $item["descuento"] * 100 : 0;
                                    if (isset($item["price"], $item["descuento"], $item["qty"])) {
                                        $precioConDescuento = $item["price"] * (1 - $item["descuento"]);
                                        $subtotalConDescuento = $precioConDescuento * $item["qty"];
                                    } else {
                                        $subtotalConDescuento = 0;
                                    }
?>

                                <tr class="cesta-pedido">
                                    <td ><?php echo '<img class="imagen-cesta" src="' . $fila['imagen'] . '"alt="imagen">'; ?></td>
                                    <td class="producto"><?php echo $item["qty"]; ?></td>
                                    <td><?php echo $item["name"]; ?></td>
                                    <td><?php echo $item["price"].' €'; ?></td>
                                    <td><?php echo $porcentajeDescuento . "% Dto."; ?></td>
                                    <td><?php echo $subtotalConDescuento . ' €'; ?></td> 
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
                                
                            
<?php 
                                } ?>
                        </tr>
    
                    </table>

                    <div class="botones-form">
                        <a class="btn-registro" href="datosenvio.php">Continuar</a>
                    </div>

                </div>    

            </div>

        </main>

        <?php 
            include("zona.php");
           
        ?>

    </div>

    <?php include("footer.php");?>

   
</body>
</html>                               

<?php

    } else {
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

        

        <main class="contenido-principal">
            
            <div class="tramitacion">
            
                <div class="tramitacion-cliente">
                    <h3>Nuevo Cliente</h3>
                    <p class="tramitacion-cliente-cuenta1">¿Necesitas una cuenta?</p>
                    <p class="tramitacion-cliente-cuenta">Al crear una cuenta en TENNISMATCH podrá realizar sus compras rápidamente,
                    revisar el estado de sus pedidos y consultar sus anteriores operaciones.</p>
                    <a class="btn-login" href="formRegistro.php">Registrarse</a>
                </div>

                <div class="tramitacion-login">
                    <form action="conexion.php" method="post" class="login">
                        <h3>Ya Soy Cliente</h3>
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email">

                        <label for="contraseña">Contraseña</label>
                        <input type="password" name="contrasena" id="contraseña">

                        <input class="btn-login" type="submit" name="login" id="login" value="Enviar">
                        
                        <div class="enlaces-login">
                            <a href="cambioContraseña.php">¿Has olvidado tu contraseña?</a>
                        </div>
                    </form>
                </div>
            </div>
            
        </main>

        
    </div>

    <?php include("footer.php");?>

  
</body>
</html>              


<?php
    }
?>

























