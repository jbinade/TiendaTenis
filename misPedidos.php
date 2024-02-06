<?php

include("seguridad.php");

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
                            $stmtCategorias = $conexion->prepare("SELECT * FROM categorias WHERE codcategoriapadre IS NULL");
                            $stmtCategorias->execute();

                            // Iterar sobre las categorías principales
                            while ($categoria = $stmtCategorias->fetch(PDO::FETCH_ASSOC)) {
                                echo '<div class="categoria-desplegable">';
                                echo '<button class="btn-desplegable" onclick="menuDesplegable(\'' . $categoria['nombre'] . '\', this)">' . $categoria['nombre'] . ' +</button>';
                                echo '<ul class="enlaces-desplegable" id="' . $categoria['nombre'] . '-menu">';

                                // Realizar la consulta para obtener las subcategorías de esta categoría principal
                                $stmtSubcategorias = $conexion->prepare("SELECT * FROM categorias WHERE codcategoriapadre = :codcategoriapadre");
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

                    <h3>Mi Pedidos</h3> 

                    <table>
                        <tr id="campos">
                            <th>Número</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th class="editarUser">Ver Pedido</th>
                        </tr>

<?php

                        try {

                            //Limito la búsqueda de cada página
                            $PAGS = 4;

                            //inicializamos la página y el inicio para el límite de SQL
                            $pagina = isset($_GET["pagina"]) ? $_GET["pagina"] : 1;
                            $inicio = ($pagina - 1) * $PAGS;

                            $con = new Conexion();
                            $conexion = $con->conectar_db();
                            $codCliente = $_SESSION["dni"];

                            $stmt = $conexion->prepare("SELECT * FROM pedidos");
                            $stmt->execute();

                            //contar los registros y las páginas con la división entera
                            $num_total_registros = $stmt->rowCount();
                            $total_paginas = ceil($num_total_registros / $PAGS);
                            //LIMIT tiene dos argumentos, el primero es el registro por el que empezar los resultados y el segundo el número de resultados a recoger
                            $stmt = $conexion->prepare("SELECT * FROM pedidos WHERE codCliente = :codCliente AND activo = 1 LIMIT ".$inicio."," .$PAGS);
                            $stmt->bindParam(':codCliente', $codCliente, PDO::PARAM_STR);
                            $stmt->execute();

                            while ($res = $stmt->fetch(PDO::FETCH_OBJ)) {
                                echo "<tr>";
                                echo "<td>" . $res->idPedido . "</td>";
                                echo "<td>" . $res->fecha . "</td>";
                                echo "<td>" . $res->total . "</td>";
                                echo "<td>" . $res->estado . "</td>";
                                echo "<td>" . $res->localidad . "</td>";
                                echo "<td><a href='verPedidos.php?dni=" . $res->idPedido . "'><img src='./images/editar.png' alt='Editar'></a></td>";
                                echo "</tr>";
                            }
                    echo "</table>";
                            
                        } catch (PDOException $e) {
                            echo "Error al recuperar datos: " . $e->getMessage();

                        }

?>
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
                                echo "<a href='misPedidos.php?pagina=$i'>$i</a> ";
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

    <script src="js.js"></script>
</body>
</html>                                   