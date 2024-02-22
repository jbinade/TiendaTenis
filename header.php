<?php

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    // Redirigir a index.php
    header("Location: index.php");
    exit; // Asegura que el script se detenga después de la redirección
}

?>

<header class="cabecera">
    <div class="header">
        <div class="logo">
            <a href=""><img src="./images/logo.png" alt="imagen logo"></a>
            <h1><a href="index.php">MATCH<span>POINT</span></a></h1>
        </div>


        <form action="index.php" method="GET"> 
            <div class="barra">
                <input type="text" name="buscar" placeholder="Buscar">
                <button type="submit"><img src="./images/lupa.png" alt="imagen lupa"></button>
            </div>
        </form>

        <div id="carrito-info"></div>
        <nav class="navegacion">
            <a href="#">Envío</a>
            <a href="#">Contacto</a>
            <a href="#">Nuestras Tiendas</a>
        </nav>
    </div>
</header>
    
        


