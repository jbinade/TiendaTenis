<?php

include("seguridad.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include("conectar_db.php");

    $con = new Conexion();

    $nombre = $_REQUEST["nombre"];
    $categoriaPadre = $_REQUEST["categoria"];
    $activo = 1;

    $fallos = array();
  
    if (empty($nombre)) {
        $fallos["nombre"] = "El nombre de la subcategoría es obligatorio";

    }

    if (empty($categoriaPadre)) {
        $fallos["categoria"] = "La categoria del artículo es obligatoria";
    }

    try {

        $conexion = $con->conectar_db();
        $stmt = $conexion->prepare("SELECT * FROM categorias WHERE nombre = :nombre");
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->execute();

        $nombreSubcategoria = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($nombreSubcategoria) {
                $fallos["nombre"] = "Esta subcategoría ya se encuentra en la base de datos";
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
                    <form class="formulario" action="nuevasubcategoria.php" method="post">

                        <h3>Nueva Subcategoría</h3>
                        
                        <div class="form-campos form-cambio-contraseña">

                            <label for="categoria">Categoria</label>
                            <select class="opcion-desplegable" name="categoria" id="categoria">
                                <option value="">Selecciona una categoría</option>
                                <?php
                                $conexion = $con->conectar_db();
                                $stmtCategorias = $conexion->prepare("SELECT * FROM categorias WHERE codcategoriapadre IS NULL AND activo = 1");
                                $stmtCategorias->execute();

                                while ($categoria = $stmtCategorias->fetch(PDO::FETCH_ASSOC)) {
                                     echo '<option value="' . $categoria["codigo"] . '">' . $categoria["nombre"] . '</option>';
                                }
                                ?>
                                <?php
                                if (isset($fallos["categoria"])) { 
                                    echo "<span style='color: red;'>". $fallos["categoria"]."</span>"; 
                                } 
                                ?>
                            </select>

                            <label for="nombre">Nombre</label>
                            <input class="campo nueva-contraseña" type="text" name="nombre" id="nombre" pattern="[A-Za-z]+" title="Solo se permiten letras" required> 
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
                    $stmt = $conexion->prepare(
                        'INSERT INTO categorias (nombre, activo, codcategoriapadre) VALUES (:nombre, :activo, :codcategoriapadre)');
        
                        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
                        $stmt->bindParam(':activo', $activo, PDO::PARAM_STR);
                        $stmt->bindParam(':codcategoriapadre', $categoriaPadre, PDO::PARAM_STR);
                        
                    $stmt->execute();

                    header("Location: subcategorias.php?subcategoria=OK");
        
                } catch(PDOException $e) {
                    echo 'Error al insertar la subcategoria: ' . $e->getMessage();
                }
        
            
            }

}

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
            
            <form class="formulario" action="nuevasubcategoria.php" method="post">

                <h3>Nueva Subcategoría</h3>

                <div class="form-campos form-cambio-contraseña">

                    <label for="categoria">Categoria</label>
                        <select class="opcion-desplegable" name="categoria" id="categoria">
                            <option value="">Selecciona una categoría</option>
                            <?php
                            $conexion = $con->conectar_db();
                            $stmtCategorias = $conexion->prepare("SELECT * FROM categorias WHERE activo = 1 AND codcategoriapadre IS NULL");
                            $stmtCategorias->execute();

                            while ($categoria = $stmtCategorias->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="' . $categoria["codigo"] . '">' . $categoria["nombre"] . '</option>';
                            }
                            ?>
                        </select>

                    <label for="nombre">Nombre</label>
                    <input class="campo nueva-contraseña" type="text" name="nombre" id="nombre" required> 
                        
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