<?php

    include("seguridad.php");
    include 'La-carta.php';
    
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
 
            <form class="formulario" action="AccionCarta.php?action=placeOrder&dni=<?php echo $dni; ?>" method="post">

                <h2>Seleccione un Método de Pago</h2>

                <div class="form-pago">

                    <div class="elegir-pago">
                        <div class="metodo-pago">
                            <input type="radio" id="visa" name="payment_method" value="visa">
                            <label for="visa"><img src="./images/visa.svg" alt="Visa"></label>
                        </div>
                        <div class="metodo-pago">
                            <input type="radio" id="mastercard" name="payment_method" value="mastercard">
                            <label for="mastercard"><img src="./images/mastercard.svg" alt="Mastercard"></label>
                        </div>
                    </div>

                    <div class="elegir-pago">
                        <div class="metodo-pago">
                            <input type="radio" id="paypal" name="payment_method" value="paypal">
                            <label for="paypal"><img src="./images/paypal.svg" alt="PayPal"></label>
                        </div>
                        <div class="metodo-pago">
                            <input type="radio" id="bizum" name="payment_method" value="bizum">
                            <label for="bizum"><img src="./images/skrill.svg" alt="Bizum"></label>
                        </div>
                    </div>
                    
                    <div class="botones-form">
                        <button class="btn-registro" type="submit">Realizar Pedido</button>
                    </div>
                </div>
            </form>

        </main>

        <?php 
            include("zona.php");
           
        ?>

    </div>

    <?php include("footer.php");?>

   
</body>
</html>                               

