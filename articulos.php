<?php
session_start();
include ('La-carta.php');

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
                <?php

                        try {

                            $categoriaSeleccionada = isset($_GET['cod']) ? $_GET['cod'] : null;

                            if ($categoriaSeleccionada) {
                                // Consulta para obtener los códigos de las subcategorías
                                $con = new Conexion();
                                $conexion = $con->conectar_db();

                                $stmtSubcategorias = $conexion->prepare("SELECT codigo FROM categorias WHERE codcategoriapadre = :cod");
                                $stmtSubcategorias->bindParam(':cod', $categoriaSeleccionada);
                                $stmtSubcategorias->execute();
                        
                                // Recuperar códigos de subcategorías
                                $subcategorias = [$categoriaSeleccionada];
                                while ($filaSubcategoria = $stmtSubcategorias->fetch(PDO::FETCH_ASSOC)) {
                                    $subcategorias[] = $filaSubcategoria['codigo'];
                                }

                                //Limito la búsqueda de cada página
                                $PAGS = 4;

                                //inicializamos la página y el inicio para el límite de SQL
                                $pagina = isset($_GET["pagina"]) ? $_GET["pagina"] : 1;
                                $inicio = ($pagina - 1) * $PAGS;
                                //examino la página a mostrar y la muestro si existe
                                
                                // Consulta para obtener el número total de registros
                                $stmtTotalRegistros = $conexion->prepare("SELECT COUNT(*) as total FROM articulos WHERE categoria IN (" . implode(',', $subcategorias) . ") AND activo = 1");
                                $stmtTotalRegistros->execute();
                                $total_registros = $stmtTotalRegistros->fetch(PDO::FETCH_ASSOC)['total'];

                                // Calcular el total de páginas
                                $total_paginas = ceil($total_registros / $PAGS);

                            
                                // Consulta para obtener los artículos de la categoría y subcategorías
                                $placeholders = str_repeat('?, ', count($subcategorias) - 1) . '?';
                                $stmtArticulos = $conexion->prepare("SELECT codigo, nombre, descripcion, precio, preciodest, imagen FROM articulos WHERE categoria IN ($placeholders) AND activo = 1 LIMIT $inicio, $PAGS");
                                $stmtArticulos->execute($subcategorias);
                        

                        echo '<div class="contenedor-articulos">';   
                            echo '<div class="articulos">';
                                while ($fila = $stmtArticulos->fetch(PDO::FETCH_ASSOC)) {
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
                            echo '</div>';

                            echo '<div class="paginas">';
                    
                                //muestro los distintos índices de las páginas, si es que hay varias páginas
                                if ($total_paginas > 1) {
                                
                                    for ($i=1;$i<=$total_paginas;$i++){  
                                        if ($pagina == $i) {
                                        //si muestro el índice de la página actual, no coloco enlace
                                            echo $pagina . " ";
                                        } else {       
                                        //si el índice no corresponde con la página mostrada actualmente, coloco el enlace para ir a esa página
                                            echo "<a href='articulos.php?cod=$categoriaSeleccionada&pagina=". $i ."'>" . $i . "</a> ";
                                        }
                                
                                    }
                                }

                            } else {
                                echo "Por favor, selecciona una categoría.";
                            }

                        } catch (PDOException $e) {
                            echo "Error al recuperar datos: " . $e->getMessage();
                        }
                        echo '</div>';         

                    echo '</div>'; 

                ?>
                    
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

</body>
</html>