<?php

session_start();

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

            <div class="contenedor-articulos">
                <div class="articulos">

                    <?php

                        try {

                            //Limito la búsqueda de cada página
                            $PAGS = 4;

                            //inicializamos la página y el inicio para el límite de SQL
                            $pagina = isset($_GET["pagina"]) ? $_GET["pagina"] : 1;
                            $inicio = ($pagina - 1) * $PAGS;
                           
                            $con = new Conexion();
                            $conexion = $con->conectar_db();

                            // Verificar si se realizó una búsqueda
                            if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
                                $buscar = '%' . $_GET['buscar'] . '%';

                                // Consulta para obtener los resultados de la búsqueda
                                $stmt = $conexion->prepare("SELECT codigo, imagen, nombre, descripcion, precio, preciodest FROM articulos WHERE nombre LIKE :buscar AND activo = 1 LIMIT $inicio, $PAGS");
                                $stmt->bindParam(':buscar', $buscar);

                            } else {
                                // Consulta para obtener todos los artículos
                                $stmt = $conexion->prepare("SELECT codigo, imagen, nombre, descripcion, precio, preciodest FROM articulos WHERE activo = 1 LIMIT $inicio, $PAGS");
                            }

                            $stmt->execute();

                            if ($stmt->rowCount() > 0) {
                                while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $descuento = $fila['precio'] * $fila['preciodest'];
                                    $precioConDescuento = $fila['precio'] - $descuento;

                                    echo '<div class="articulo">';
                                    echo '<img src="' . $fila['imagen'] . '"alt="imagen">';
                                    echo '<div class="info-articulo">';
                                    echo '<a href=""><h3>' . $fila['nombre'] . '</h3></a>';
                                    echo '<p class="descripcion">' . $fila['descripcion'] . '</p>';
                                    echo '<div class="precio">';
                                    echo '<div class="precio">';
                                    if ($descuento > 0) {
                                        echo '<p class="precio-original">' . $fila['precio'] . '€</p>';
                                        echo '<p class="preciodest">' . $precioConDescuento . '€</p>'; 
                                    } else {
                                        echo '<p class="">' . $fila['precio'] . '€</p>'; 
                                    }
                                    echo '</div>';
                                    echo '<a class="btn-articulo" href="AccionCarta.php?action=addToCart&codigo='. $fila['codigo'] .'">Añadir a la cesta</a>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';

                                }
                            } else {
                                // Mostrar mensaje de "Artículo no encontrado"
                                echo '<p class="error-mensaje">Artículo no encontrado</p>';
                            }


                        } catch (PDOException $e) {
                                echo "Error al recuperar datos: " . $e->getMessage();

                        }

                    ?>

                </div>

                <div class="paginas">

                    <?php

                        if (isset($buscar)) {
                            // Mostrar los enlaces de paginación si hay búsqueda
                            $stmtTotalRegistros = $conexion->prepare("SELECT COUNT(*) as total FROM articulos WHERE nombre LIKE :buscar AND activo = 1");
                            $stmtTotalRegistros->bindParam(':buscar', $buscar);
                            $stmtTotalRegistros->execute();
                            $total_registros = $stmtTotalRegistros->fetch(PDO::FETCH_ASSOC)['total'];
                            $total_paginas = ceil($total_registros / $PAGS);

                            if ($total_paginas > 1) {
                                for ($i = 1; $i <= $total_paginas; $i++) {
                                    if ($pagina == $i) {
                                        // Si muestro el índice de la página actual, no coloco enlace
                                        echo $pagina . " ";
                                    } else {
                                        // Si el índice no corresponde con la página mostrada actualmente, coloco el enlace para ir a esa página
                                        echo "<a href='index.php?buscar=$buscar&pagina=$i'>$i</a> ";
                                    }
                                }
                            }
                            
                        } else {
                            // Mostrar los enlaces de paginación si no hay búsqueda
                            $stmtTotalRegistros = $conexion->prepare("SELECT COUNT(*) as total FROM articulos WHERE activo = 1");
                            $stmtTotalRegistros->execute();
                            $total_registros = $stmtTotalRegistros->fetch(PDO::FETCH_ASSOC)['total'];
                            $total_paginas = ceil($total_registros / $PAGS);
                           
                            if ($total_paginas > 1) {
                                for ($i = 1; $i <= $total_paginas; $i++) {
                                    if ($pagina == $i) {
                                        // Si muestro el índice de la página actual, no coloco enlace
                                        echo $pagina . " ";
                                    } else {
                                        // Si el índice no corresponde con la página mostrada actualmente, coloco el enlace para ir a esa página
                                        echo "<a href='index.php?pagina=$i'>$i</a> ";
                                    }
                                }
                            }
                        }

                    ?>
      
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
















    


