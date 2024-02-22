<?php

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    // Redirigir a index.php
    header("Location: index.php");
    exit; // Asegura que el script se detenga después de la redirección
}

?>


<aside class="contenedor asidedca">

    <div class="form-login">
        <h3>Iniciar Sesión</h3>

        <form action="conexion.php" method="post" class="login">
            <label for="email">Email</label>
            <input type="email" name="email" id="email">

            <label for="contraseña">Contraseña</label>
            <input type="password" name="contrasena" id="contraseña">

            <input class="btn-login" type="submit" name="login" id="login" value="Enviar">
        </form>

        <div class="enlaces-login">
            <a href="cambioContraseña.php">¿Has olvidado tu contraseña?</a>
            <a class="registro" href="formRegistro.php">Registrarse</a>
        </div>

        <?php
        if (isset($_GET['resetcontraseña']) && $_GET['resetcontraseña'] == 'true') {
            echo '<p class="mensaje-contraseña">Contraseña actualizada correctamente</p>';
        }

        if (isset($_GET['registro']) && $_GET['registro'] == 'OK') {
            echo '<p class="mensaje-contraseña">Registro realizado correctamente</p>';
        }

        if (isset($_GET['cliente']) && $_GET['cliente'] == 'OK') {
            echo '<p class="mensaje-contraseña">Registro realizado correctamente</p>';
        }

        if (isset($_GET['error']) && $_GET['error'] == 1) {
            echo '<p class="mensaje-contraseña">Usuario no registrado</p>';
        }

        $cart = new Cart;

        $total_items = $cart->total_items();
        if($total_items > 0) {
            //get cart items from session
            $Items = count($cart->contents());
            $total = $cart->total();
            
            echo "<div class='carrito'>";
            echo '<img src="./images/carrito.png" alt="carrito">';
            if ($Items == 1) {
                echo "<p>" . $Items . " Artículo</p>";
            } else {
                echo "<p>" . $Items . " Artículos</p>";
            }
            
            echo "<p>" . $total . " €</p>";
            echo "<a class='btn-registro' href='vercesta.php'>Ver Cesta</a>";
            echo "</div>";
        }  
           
           ?>




    </div>
    
</aside>