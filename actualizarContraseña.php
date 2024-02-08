<?php

    include("conectar_db.php");
    // Recuperar los valores del formulario
    $dni = $_REQUEST["dni"];
    $email = $_REQUEST["email"];
    $fallos = array();
    
    // Verificar en la base de datos si existe el DNI
    $con = new Conexion();
    $conexion = $con->conectar_db();

    // Realizar la consulta para verificar el DNI
    $stmt = $conexion->prepare("SELECT * FROM clientes WHERE dni = :dni AND email = :email");
    $stmt->bindParam(':dni', $dni, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    // Verificar si se encontraron registros para el DNI
    if ($stmt->rowCount() > 0) {

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
                        <form class="formulario" action="nuevaContraseña.php" method="post">

                            <h2>Restablecer Contraseña</h2>

                            <div class="form-campos form-cambio-contraseña">

                                <input type="hidden" name="dni" value="<?php echo $dni ?>">
                                <input type="hidden" name="email" value="<?php echo $email ?>">
                            
                                <label for="contraseña">Introduce tu nueva contraseña</label>
                                <input class="campo nueva-contraseña" type="password" name="contrasena" id="contrasena" required>                        

                                <div class="botones-form">
                                    <button class="btn-registro" type="submit">Enviar</button>
                                    <a class="btn-registro" href="index.php">Cancelar</a>
                                </div>
                            </div>
                        </form>
                    </main>

                    <?php include("login.php");?>

                </div>

                <?php include("footer.php");?>

                <script src="js.js"></script>
            </body>
            </html>

<?php


    } else {
        $datosIncorrectos = "Datos no encontrados";

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
                    <form class="formulario" action="actualizarContraseña.php" method="post">

                        <h2>Restablecer Contraseña</h2>

                        <div class="form-campos">
                            <label for="dni">DNI</label>
                            <input class="campo campo-dni" type="text" name="dni" id="dni" required>
                
                            <label for="email">Email</label>
                            <input class="campo campo-email" type="email" name="email" id="email" required>
                            <?php
                            if (isset($datosIncorrectos)) {
                                echo '<p class="error-mensaje">' . $datosIncorrectos . '</p>';
                            }
                            ?>

                            <div class="botones-form">
                                <button class="btn-registro" type="submit">Enviar</button>
                                <a class="btn-registro" href="index.php">Cancelar</a>
                            </div>
                        </div>
                    </form>
                </main>

                <?php include("login.php");?>

            </div>

            <?php include("footer.php");?>

            <script src="js.js"></script>
        </body>
        </html>

<?php

    }

?>
   

    























    

