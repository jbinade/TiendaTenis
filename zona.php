<?php 

    if (session_status() == PHP_SESSION_NONE) {
        include("seguridad.php");
    }

    

    $nombreusuario = $_SESSION["nombre"];
    $dni = $_SESSION["dni"];
    //$rol = $_SESSION["rol"]
 

    if (($_SESSION["rol"] == "usuario")) {

?>         

        <aside class="asidedca">
            <div class="form-login">
                <h3>Hola, <?php echo $nombreusuario?></h3>";

                <div class="form-login enlaces-user">
                    <a href="miCuenta.php">Mi cuenta</a>
                    <a href="misPedidos.php">Mis Pedidos</a>
                    <a href="resetpassword.php">Actualizar Contraseña</a>
                    <a href="salir.php">Cerrar Sesión</a>
                </div>

                <?php
                if (isset($_GET['resetcontraseña']) && $_GET['resetcontraseña'] == 'OK') {
                    echo '<p class="mensaje-contraseña">Contraseña actualizada correctamente</p>';
                }
                ?>
            </div>
        </aside>

<?php

    } else  if (($_SESSION["rol"] == "administrador")) {

?>

        <aside class="asidedca">
            <div class="form-login">
                <h3>Hola, <?php echo $nombreusuario?></h3>

                <div class="form-login enlaces-user">
                    <a href="clientes.php">Clientes</a>
                    <a href="empleados.php">Empleados</a>
                    <a href="menucategorias.php">Categorías</a>
                    <a href="menuarticulos.php">Artículos</a>
                    <a href="">Pedidos</a>
                    <a href="resetpassword.php">Actualizar Contraseña</a>
                    <a href="salir.php">Cerrar Sesión</a>
                </div>

                <?php
                if (isset($_GET['articulo']) && $_GET['articulo'] == 'OK') {
                    echo '<p class="mensaje-contraseña">Artículo añadido correctamente</p>';
                }

                if (isset($_GET['articuloactualizado']) && $_GET['articuloactualizado'] == 'OK') {
                    echo '<p class="mensaje-contraseña">Artículo actualizado correctamente</p>';
                }

                if (isset($_GET['empleado']) && $_GET['empleado'] == 'OK') {
                    echo '<p class="mensaje-contraseña">Empleado añadido correctamente</p>';
                }

                if (isset($_GET['articuloeliminado']) && $_GET['articuloeliminado'] == 'OK') {
                    echo '<p class="mensaje-contraseña">Artículo eliminado correctamente</p>';
                }

                if (isset($_GET['articuloactivado']) && $_GET['articuloactivado'] == 'OK') {
                    echo '<p class="mensaje-contraseña">Artículo activado correctamente</p>';
                }

                if (isset($_GET['categoria']) && $_GET['categoria'] == 'OK') {
                    echo '<p class="mensaje-contraseña">Categoría añadida correctamente</p>';
                }

                if (isset($_GET['categoriaActualizada']) && $_GET['categoriaActualizada'] == 'OK') {
                    echo '<p class="mensaje-contraseña">Categoría añadida correctamente</p>';
                }

                if (isset($_GET['categoriaeliminada']) && $_GET['categoriaeliminada'] == 'OK') {
                    echo '<p class="mensaje-contraseña">Categoría eliminada correctamente</p>';
                }

                if (isset($_GET['categoriaActivada']) && $_GET['categoriaActivada'] == 'OK') {
                    echo '<p class="mensaje-contraseña">Categoría activada correctamente</p>';
                }

                if (isset($_GET['subcategoria']) && $_GET['subcategoria'] == 'OK') {
                    echo '<p class="mensaje-contraseña">Categoría añadida correctamente</p>';
                }

                if (isset($_GET['subcategoriaActualizada']) && $_GET['subcategoriaActualizada'] == 'OK') {
                    echo '<p class="mensaje-contraseña">Subategoría actualizada correctamente</p>';
                }
                
                if (isset($_GET['subcategoriaActivada']) && $_GET['subcategoriaActivada'] == 'OK') {
                    echo '<p class="mensaje-contraseña">Subcategoría activada correctamente</p>';
                }

                if (isset($_GET['subcategoriaeliminada']) && $_GET['subcategoriaeliminada'] == 'OK') {
                    echo '<p class="mensaje-contraseña">Subcategoría eliminada correctamente</p>';
                }
                ?>
            </div>
        </aside>
     
        
<?php

    } else {

?>

        <aside class="asidedca">
            <div class="form-login">
                <h3>Hola, <?php echo $nombreusuario?></h3>

                <div class="form-login enlaces-user">
                    <a href="clientes.php">Clientes</a>
                    <a href="menucategorias.php">Categorías</a>
                    <a href="menuarticulos.php">Artículos</a>
                    <a href="">Pedidos</a>
                    <a href="resetpassword.php">Actualizar Contraseña</a>
                    <a href="salir.php">Cerrar Sesión</a>
                </div>
                <?php
                if (isset($_GET['articulo']) && $_GET['articulo'] == 'OK') {
                    echo '<p class="mensaje-contraseña">Artículo añadido correctamente</p>';
                }

                if (isset($_GET['articuloactualizado']) && $_GET['articuloactualizado'] == 'OK') {
                    echo '<p class="mensaje-contraseña">Artículo actualizado correctamente</p>';
                }

                if (isset($_GET['articuloeliminado']) && $_GET['articuloeliminado'] == 'OK') {
                    echo '<p class="mensaje-contraseña">Artículo eliminado correctamente</p>';
                }

                if (isset($_GET['articuloactivado']) && $_GET['articuloactivado'] == 'OK') {
                    echo '<p class="mensaje-contraseña">Artículo activado correctamente</p>';
                }

                if (isset($_GET['categoria']) && $_GET['categoria'] == 'OK') {
                    echo '<p class="mensaje-contraseña">Categoría añadida correctamente</p>';
                }

                if (isset($_GET['categoriaActualizada']) && $_GET['categoriaActualizada'] == 'OK') {
                    echo '<p class="mensaje-contraseña">Categoría añadida correctamente</p>';
                }

                if (isset($_GET['categoriaeliminada']) && $_GET['categoriaeliminada'] == 'OK') {
                    echo '<p class="mensaje-contraseña">Categoría eliminada correctamente</p>';
                }

                if (isset($_GET['categoriaActivada']) && $_GET['categoriaActivada'] == 'OK') {
                    echo '<p class="mensaje-contraseña">Categoría activada correctamente</p>';
                }

                if (isset($_GET['subcategoria']) && $_GET['subcategoria'] == 'OK') {
                    echo '<p class="mensaje-contraseña">Subcategoría añadida correctamente</p>';
                }

                if (isset($_GET['subcategoriaActualizada']) && $_GET['subcategoriaActualizada'] == 'OK') {
                    echo '<p class="mensaje-contraseña">Subategoría actualizada correctamente</p>';
                }

                if (isset($_GET['subcategoriaActivada']) && $_GET['subcategoriaActivada'] == 'OK') {
                    echo '<p class="mensaje-contraseña">Subcategoría activada correctamente</p>';
                }

                if (isset($_GET['subcategoriaeliminada']) && $_GET['subcategoriaeliminada'] == 'OK') {
                    echo '<p class="mensaje-contraseña">Subcategoría eliminada correctamente</p>';
                }
                ?>
            </div>


        </aside>


    

<?php

    }

?>

        