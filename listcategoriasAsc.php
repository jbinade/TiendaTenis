<?php

include("seguridad.php");
include ('La-carta.php');

$rol = $_SESSION["rol"];

if ($rol == "usuario") {
    header("Location: index.php");
}
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
 
            <div class="contenido-tabla">

                <div class="tabla">

                    <h3>Listado Categorías</h3> 

                    <table>
                        <tr id="campos">
                            <th>Código</th>
                            <th><a href="listadocategorias.php">Nombre</a><br>Ord. ASC</th>
                            <th class="editarUser">Editar</th>
                            <th class="borrarUser">Borrar</th>
                        </tr>

<?php

                        try {

                            //Limito la búsqueda de cada página
                            $PAGS = 8;

                            //inicializamos la página y el inicio para el límite de SQL
                            $pagina = isset($_GET["pagina"]) ? $_GET["pagina"] : 1;
                            $inicio = ($pagina - 1) * $PAGS;

                            $con = new Conexion();
                            $conexion = $con->conectar_db();

                            $stmt = $conexion->prepare("SELECT * FROM categorias ORDER BY nombre ASC");
                            $stmt->execute();
                            //contar los registros y las páginas con la división entera
                            $num_total_registros = $stmt->rowCount();
                            $total_paginas = ceil($num_total_registros / $PAGS);

                            $stmt = $conexion->prepare("SELECT * FROM categorias WHERE activo = 1 AND codcategoriapadre IS NULL ORDER BY nombre ASC LIMIT ".$inicio."," .$PAGS);
                            $stmt->execute();
                            
                            while ($res = $stmt->fetch(PDO::FETCH_OBJ)) {
                                echo "<tr>";
                                echo "<td>" . $res->codigo . "</td>";
                                echo "<td>" . $res->nombre . "</td>";
                                echo "<td><a href='editarcategoria.php?codigo=" . $res->codigo . "'><img src='./images/editar.png' alt='Editar'></a></td>";
                                echo "<td><a href='borrarcategoria.php?codigo=" . $res->codigo . "'><img src='./images/borrar.jpg' alt='Borrar'></a></td>";
                                echo "</tr>";
                                echo "</tr>";
                            }
                    echo "</table>";
                            
                        } catch (PDOException $e) {
                            echo "Error al recuperar datos: " . $e->getMessage();

                        }

?>
                    <div class="botones-form">
                        <a class="btn-registro" href="categorias.php">Cancelar</a>
                    </div>
                </div>

                <div class="paginas">

<?php

                    if ($total_paginas > 1) {
                        for ($i = 1; $i <= $total_paginas; $i++) {
                            if ($pagina == $i) {
                                // Si muestro el índice de la página actual, no coloco enlace
                                echo $pagina . " ";
                            } else {
                                // Si el índice no corresponde con la página mostrada actualmente, coloco el enlace para ir a esa página
                                echo "<a href='listcategoriasAsc.php?pagina=$i'>$i</a> ";
                            }
                        }
                    }

?>
      
                </div>    

            </div>

        </main>

        <?php include("zona.php")?>

    </div>

    <?php include("footer.php");?>

    
</body>
</html>                               