<?php

include("seguridad.php");
include ('La-carta.php');

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


    if(isset($_FILES['imagen']['name']) && $_FILES['imagen']['size'] > 0) {
        $imagen = $_FILES['imagen'] ['name'];
        $tamano = $_FILES['imagen']['size'];
        $ruta = "./images/" . $imagen;

       
        $imagentemporal = $_FILES['imagen']['tmp_name'];

        //$extension = pathinfo($imagen, PATHINFO_EXTENSION);

        // Verificar si la extensión está en la lista de tipos permitidos
        $tipos = ["jpg", "jpeg", "gif", "png"];
        //if (!in_array(strtolower($extension), $tipos_permitidos)) {
            //$fallos["tipos"] = "El tipo de archivo es incorrecto";
        //}
        list($tipos) = getimagesize($imagentemporal);

        if (!$tipos) {
            $fallos["tipos"] = "El tipo de archivo es incorrecto";

        }

        if ($tamano > 300000) {
            $fallos["tamano"] = "Imagen demasiado pesada";
                
        }
    
    } else {
        $fallos["imagen"] = "Es obligatorio subir una imagen";
    }

  
    // Obtengo las tres primeras letras del codigo para verificar si es correcto
    $categoriaCodigo = substr($codigo, 0, 3);
    $categoriaCodigo = strtoupper($categoriaCodigo);

    // Verifico si la categoría del código existe en la base de datos
    try {

        if (empty($codigo)) {
            $fallos["nombre"] = "El código es obligatorio";
        }
    
        $conexion = $con->conectar_db();
        $stmt = $conexion->prepare("SELECT * FROM categorias WHERE LEFT(nombre, 3) = :categoriaCodigo");
        $stmt->bindParam(':categoriaCodigo', $categoriaCodigo, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            // Si no se ha encontrado ninguna categoría con las tres primeras letras del código muestro mensaje 
            $fallos["codigo"] = "Código Incorrecto";
        } else {
            // Si se ha encontrado una categoría con las tres primeras letras del código se verifica si existe o no
            $stmt = $conexion->prepare("SELECT * FROM articulos WHERE codigo = :codigo");
            $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // Si ya existe un artículo con el mismo código muestro mensaje
                $fallos["codigo"] = "El código ya se encuentra en la base de datos";
            }
        }
    } catch(PDOException $e) {
        echo 'Error al verificar el código: ' . $e->getMessage();
    }

  
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
                    <form class="formulario" action="nuevoarticulo.php" method="post" enctype="multipart/form-data">

                        <h3>Nuevo Artículo</h3>

                        <div class="form-campos">

                            <div class="campo-nombre">
                                <div class="nombre">
                                    <label for="codigo">Código</label>
                                    <input class="campo" type="text" name="codigo" id="codigo" value="<?php echo $codigo; ?>" required> 
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
                                    <option value="">Selecciona una categoría</option>
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

                            <div class="campo-nombre">
                                <div class="nombre">
                                    <label for="imagen">Imagen</label>
                                    <input class="campo" type="file" name="imagen" id="imagen" required value="<?php $imagen?>"> 
                                    <?php 
                                    if (isset($fallos["tipos"])) { 
                                        echo "<span style='color: red;'>". $fallos["tipos"]."</span>"; 
                                    }

                                    if (isset($fallos["tamano"])) { 
                                        echo "<span style='color: red;'>". $fallos["tamano"]."</span>"; 
                                    }

                                    if (isset($fallos["imagen"])) { 
                                        echo "<span style='color: red;'>". $fallos["imagen"]."</span>"; 
                                    }

                                    ?>
                                </div>
                                
                            </div>

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

         
        </body>
        </html>
        
<?php
        
            } else {
                
                if (move_uploaded_file($imagentemporal, $ruta)) {
        
                    try {
            
                        $conexion = $con->conectar_db();
            
                        $stmt = $conexion->prepare(
                            "INSERT INTO articulos (codigo, nombre, precio, preciodest, categoria, descripcion, imagen, activo) VALUES (:codigo, :nombre, :precio, :preciodest, :categoria, :descripcion, :imagen, :activo)");
            
                        $stmt->execute(array(':codigo'=>$codigo, ':nombre'=>$nombre, ':precio'=>$precio, ':preciodest'=>$descuento, ':categoria'=>$categoria, ':descripcion'=>$descripcion, ':imagen'=>$ruta, ':activo'=>$activo));
                        header("Location: menuarticulos.php?articulo=OK");

                    } catch(PDOException $e) {
                        echo 'Error al insertar el articulo ' . $e->getMessage();
                    }
            
                } else {
                    //Si no se ha podido subir la imagen, mostramos un mensaje de error
                    echo '<div><b>Ocurrió algún error al subir el fichero. No pudo guardarse.</b></div>';
                    
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
            
            <form class="formulario" action="nuevoarticulo.php" method="post" enctype="multipart/form-data">

                <h3>Nuevo Artículo</h3>

                <div class="form-campos">

                    <div class="campo-nombre">
                        <div class="nombre">
                            <label for="codigo">Código</label>
                            <input class="campo" type="text" name="codigo" id="codigo" required> 
                        </div>
                        
                        <div class="apellidos">
                            <label for="nombre">Nombre</label>
                            <input class="campo" type="text" name="nombre" id="nombre" required>
                        </div>
                    </div>

                    <div class="campo-nombre">
                        <div class="nombre">
                            <label for="precio">Precio</label>
                            <input class="campo" type="number" name="precio" id="precio" required> 
                        </div>
                        
                        <div class="apellidos">
                            <label for="descuento">Precio Descuento</label>
                            <input class="campo" type="number" name="descuento" id="descuento">
                        </div>
                    </div>

                 
                     <label for="categoria">Categoria</label>
                        <select class="opcion-desplegable" name="categoria" id="categoria">
                            <option value="">Selecciona una categoría</option>
                            <?php
                            $conexion = $con->conectar_db();
                            $stmtCategorias = $conexion->prepare("SELECT * FROM categorias WHERE codcategoriapadre IS NOT NULL AND activo = 1");
                            $stmtCategorias->execute();

                            while ($categoria = $stmtCategorias->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="' . $categoria["codigo"] . '">' . $categoria["nombre"] . '</option>';
                            }
                            ?>
                        </select>
          

                    

                    <label for="descripcion">Descripción</label>
                    <textarea class="campo direccion" name="descripcion" id="descripcion" required></textarea>

                    <div class="campo-nombre">
                        <div class="nombre">
                            <label for="imagen">Imagen</label>
                            <input class="campo" type="file" name="imagen" id="imagen" required> 
                        </div>
                        
                    </div>

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

</body>
</html>              