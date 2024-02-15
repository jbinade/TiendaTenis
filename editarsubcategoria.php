<?php

include("seguridad.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include("conectar_db.php");

    $con = new Conexion();

    $codigo = $_REQUEST["codigo"];
    $nombre = $_REQUEST["nombre"];

    $fallos = array();
  
    if (empty($nombre)) {
        $fallos["nombre"] = "El nombre de la subcategoría es obligatorio";
    }

    try {

        $conexion = $con->conectar_db();
        $stmt = $conexion->prepare("SELECT * FROM categorias WHERE nombre = :nombre");
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->execute();

        $nombreSubcategoria = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($nombreSubcategoria) {
                $fallos["nombre"] = "Esta categoría ya se encuentra en la base de datos";
        }
        

    } catch(PDOException $e) {
            echo 'Error al insertar el nombre: ' . $e->getMessage();
    }


    //si hay fallos al introducir el fomulario se vuelve a mostrar indicando el error en color rojo
    if (count($fallos) > 0) {
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
                    <form class="formulario" action="editarsubcategoria.php" method="post">

                        <h3>Editar Subcategoría</h3>
                        
                        <div class="form-campos form-cambio-contraseña">

                            <input type="hidden" name="codigo" id="codigo" value="<?php echo $codigo; ?>">

                            <label for="nombre">Nombre</label>
                            <input class="campo nueva-contraseña" type="text" name="nombre" id="nombre" value="<?php echo $nombre;?>" required> 
                            <?php 
                            if (isset($fallos["nombre"])) { 
                                echo "<span style='color: red;'>".$fallos["nombre"]."</span>"; 
                            } 
                            ?>
                                
                            <div class="botones-form dni-empleado">
                                <button class="btn-registro" type="submit">Enviar</button>
                                <a class="btn-registro" href="index.php">Cancelar</a>
                            </div>
                        </div>
                    </form>

                </main>

                <?php include("zona.php");?>
            
            </div>

            <?php include("footer.php");?>

            <script src="js.js"></script>
        </body>
        </html>
        
<?php
        
            } else {
                
                try {
        
                    $conexion = $con->conectar_db();
                    $stmt = $conexion->prepare('UPDATE categorias SET nombre = :nombre WHERE codigo = :codigo');
        
                        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
                        $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
                        
                    $stmt->execute();

                    header("Location: subcategorias.php?subcategoriaActualizada=OK");
        
                } catch(PDOException $e) {
                    echo 'Error al editar la categoria: ' . $e->getMessage();
                }
        
            
            }

}

?>

<?php

include("conectar_db.php");

if(isset($_REQUEST["codigo"])) {
    $codigo = $_REQUEST["codigo"];

}

$con = new Conexion();
$conexion = $con->conectar_db();
$stmt = $conexion->prepare('SELECT * FROM categorias WHERE codigo = :codigo');
$stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
$stmt->execute();

$res = $stmt->fetch(PDO::FETCH_OBJ);
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
            
            <form class="formulario" action="editarsubcategoria.php" method="post">

                <h3>Editar Subcategoría</h3>

                <div class="form-campos form-cambio-contraseña">

                    <input type="hidden" name="codigo" id="codigo" value="<?php echo $res->codigo; ?>">
                
                    <label for="nombre">Nombre</label>
                    <input class="campo nueva-contraseña" type="text" name="nombre" id="nombre" value="<?php echo $res->nombre; ?>" required> 
                        
                    <div class="botones-form dni-empleado">
                        <button class="btn-registro" type="submit">Enviar</button>
                        <a class="btn-registro" href="index.php">Cancelar</a>
                    </div>
                </div>
            </form>
           
        </main>

        <?php include("zona.php");?>

    </div>

    <?php include("footer.php");?>

    <script src="js.js"></script>
</body>
</html>         