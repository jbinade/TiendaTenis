<?php

include("seguridad.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include("conectar_db.php");

    $con = new Conexion();

    //array para almacenar fallos
    $fallos = array();

    $codigo = $_REQUEST["codigo"];
    $nombre = $_REQUEST["nombre"];
    $precio = $_REQUEST["precio"];
    $descuento = $_REQUEST["descuento"];
    $categoria = $_REQUEST["categoria"];
    $descripcion = $_REQUEST["descripcion"];
    $activo = 1;

    $conexion = $con->conectar_db();
    $stmt = $conexion->prepare('SELECT * FROM articulos WHERE codigo = :codigo');
    $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
    $stmt->execute();
    $res = $stmt->fetch(PDO::FETCH_OBJ);

    $stmt = $conexion->prepare("SELECT * FROM categorias WHERE codigo = :codigo");
    $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
    $stmt->execute();
    $res2 = $stmt->fetch(PDO::FETCH_OBJ);
  
    if (empty($nombre)) {
        $fallos["nombre"] = "El nombre es obligatorio";
    }

    if (empty($precio)) {
        $fallos["precio"] = "El precio del artículo es obligatorio";
    }

    if (empty($categoria)) {
        $fallos["categoria"] = "La categoria del artículo es obligatoria";
    }

    if (empty($descripcion)) {
        $fallos["descripcion"] = "La descripción del artículo es obligatoria";
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
                    <form class="formulario" action="editararticulo.php" method="post" enctype="multipart/form-data">

                            <h3>Editar Artículo</h3>

                            <div class="form-campos">

                                    
                                    
                                   

                                <div class="campo-nombre">
                                    <div class="nombre">
                                        <label for="codigo">Código: <?php echo $codigo; ?></label>
                                        <input class="campo" type="hidden" name="codigo" id="codigo" value="<?php echo $codigo; ?>" required> 
                                        <?php 
                                        if (isset($fallos["codigo"])) { 
                                            echo "<span style='color: red;'>". $fallos["codigo"]."</span>"; 
                                        } 
                                        ?>
                                    </div>
                                    
                                    <div class="apellidos">
                                        <label for="nombre">Nombre</label>
                                        <input class="campo" type="text" name="nombre" id="nombre" value="<?php echo $nombre; ?>" required>
                                        <?php if (isset($fallos["nombre"])) { 
                                            echo "<span style='color: red;'>".$fallos["nombre"]."</span>"; 
                                        } 
                                        ?>
                                    </div>
                                </div>

                                <div class="campo-nombre">
                                    <div class="nombre">
                                        <label for="precio">Precio</label>
                                        <input class="campo" type="number" name="precio" id="precio" value="<?php echo $precio; ?>" required> 
                                        <?php 
                                        if (isset($fallos["precio"])) { 
                                            echo "<span style='color: red;'>". $fallos["precio"]."</span>"; 
                                        } 
                                        ?>
                                    </div>
                                    
                                    <div class="apellidos">
                                        <label for="descuento">Precio Descuento</label>
                                        <input class="campo" type="number" name="descuento" id="descuento" value="<?php echo $descuento; ?>">
                                    </div>
                                </div>

                            
                                <label for="categoria">Categoria</label>
                                <select class="opcion-desplegable" name="categoria" id="categoria">
                                    <option value="<?php echo $categoria; ?>"><?php echo $res2->nombre ?></option>
                                    <?php
                                    $conexion = $con->conectar_db();
                                    $stmtCategorias = $conexion->prepare("SELECT * FROM categorias WHERE codcategoriapadre IS NOT NULL");
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

                                <label for="descripcion">Descripción</label>
                                <textarea class="campo direccion" name="descripcion" id="descripcion" required><?php echo $descripcion; ?></textarea>
                                <?php if (isset($fallos["descripcion"])) { 
                                    echo "<span style='color: red;'>".$fallos["descripcion"]."</span>"; 
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
                            "UPDATE articulos SET nombre = :nombre, precio = :precio, preciodest = :preciodest, categoria = :categoria, descripcion = :descripcion WHERE codigo = :codigo");
            
                            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
                            $stmt->bindParam(':precio', $precio, PDO::PARAM_STR);
                            $stmt->bindParam(':preciodest', $descuento, PDO::PARAM_STR);
                            $stmt->bindParam(':categoria', $categoria, PDO::PARAM_STR);
                            $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
                            $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);

                            $stmt->execute();

                        header("Location: index.php?articuloactualizado=OK");

                    } catch(PDOException $e) {
                        echo 'Error al insertar el articulo ' . $e->getMessage();
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
$stmt = $conexion->prepare('SELECT * FROM articulos WHERE codigo = :codigo');
$stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
$stmt->execute();

$res = $stmt->fetch(PDO::FETCH_OBJ);

$stmtDesplegable = $conexion->prepare("SELECT * FROM categorias WHERE codcategoriapadre IS NOT NULL");
$stmtDesplegable->execute();
$res2 = $stmtDesplegable->fetch(PDO::FETCH_OBJ);
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
            
            <form class="formulario" action="editararticulo.php" method="post" enctype="multipart/form-data">

                <h3>Editar Artículo</h3>

                <div class="form-campos">

                    

                    <div class="campo-nombre">
                        <div class="nombre">
                            <label for="codigo">Código: <?php echo $res->codigo; ?></label>
                            <input class="campo" type="hidden" name="codigo" id="codigo" value="<?php echo $res->codigo; ?>"> 
                        </div>
                        
                        <div class="apellidos">
                            <label for="nombre">Nombre</label>
                            <input class="campo" type="text" name="nombre" id="nombre" value="<?php echo $res->nombre; ?>" required>
                        </div>
                    </div>

                    <div class="campo-nombre">
                        <div class="nombre">
                            <label for="precio">Precio</label>
                            <input class="campo" type="number" name="precio" id="precio" value="<?php echo $res->precio; ?>" required> 
                        </div>
                        
                        <div class="apellidos">
                            <label for="descuento">Precio Descuento</label>
                            <input class="campo" type="number" name="descuento" id="descuento" value="<?php echo $res->preciodest; ?>">
                        </div>
                    </div>

                    <label for="categoria">Categoria</label>
                            <select class="opcion-desplegable" name="categoria" id="categoria">
                                    <option value="<?php echo $res->categoria; ?>"><?php echo $res2->nombre ?></option>
                                    <?php
                                    $conexion = $con->conectar_db();
                                    $stmtCategorias = $conexion->prepare("SELECT * FROM categorias WHERE codcategoriapadre IS NOT NULL");
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
       
                    <label for="descripcion">Descripción</label>
                    <textarea class="campo direccion" name="descripcion" id="descripcion" required><?php echo $res->descripcion; ?></textarea>

                    

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