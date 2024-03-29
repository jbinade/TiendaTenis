<?php

include("seguridad.php");
include ('La-carta.php');



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include("conectar_db.php");
    include("funciones.php");
    //array para almacenar fallos
    $fallos = array();

    $dni = $_SESSION["dni"];
    $nombre = $_REQUEST["nombre"];
    $apellidos = $_REQUEST["apellidos"];
    $direccion = $_REQUEST["direccion"];
    $localidad = $_REQUEST["localidad"];
    $provincia = $_REQUEST["provincia"];
    $telefono = $_REQUEST["telefono"];

    //verificar los campos

    if (empty($nombre)) {
        $fallos["nombre"] = "El nombre es obligatorio";
    }

    if (empty($direccion)) {
        $fallos["direccion"] = "La direccion es obligatoria";
    }

    if (empty($localidad)) {
        $fallos["localidad"] = "La localidad es obligatoria";
    }

    if (empty($provincia)) {
        $fallos["provincia"] = "La provincia es obligatoria";
    }

    if (empty($apellidos)) {
        $fallos["apellidos"] = "Por favor, introduce el nombre completo";
    }

    if (!empty($telefono)) {

        if (strlen($telefono) != 9 || !is_numeric($telefono)) {
            $fallos["telefono"] = "Teléfono incorrecto";
        }
    }

    //se busca el cliente a partir del dni
    $con = new Conexion();
    $datos = $con->buscarCliente($dni);

    //si hay fallos al introducir el fomulario se vuelve a mostrar indicando el error en color rojo
    if (count($fallos) > 0) {
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
            
                    <form class="formulario" action="datosenvio.php" method="post">

                    <h3>Datos de Envío</h3>

                        <div class="form-campos">
                            <label for="dni">DNI</label>
                            <input class="campo campo-dni" type="text" name="dni" id="dni" value="<?php echo $datos->dni; ?>" disabled>

                            <label for="email">Email</label>
                            <input class="campo campo-email" type="email" name="email" id="email" value="<?php echo $datos->email; ?>" disabled>
                          
                            <div class="campo-nombre">
                                <div class="nombre">
                                    <label for="nombre">Nombre</label>
                                    <input class="campo" type="text" name="nombre" id="nombre" value="<?php echo $nombre;?>" required>
                                    <?php 
                                        if (isset($fallos["nombre"])) { 
                                            echo "<span style='color: red;'>". $fallos["nombre"]."</span>"; 
                                        } 
                                    ?>
                                </div>
                                
                                <div class="apellidos">
                                    <label for="nombre">Apellidos</label>
                                    <input class="campo" type="text" name="apellidos" id="apellidos" value="<?php echo $apellidos;?>" required>
                                    <?php 
                                        if (isset($fallos["apellidos"])) { 
                                            echo "<span style='color: red;'>". $fallos["apellidos"]."</span>"; 
                                        } 
                                    ?>
                                </div>
                            </div>

                            <label for="dni">Dirección</label>
                            <input class="campo campo-dni" type="text" name="direccion" id="direccion" value="<?php echo $direccion; ?>" required>
                            <?php 
                                        if (isset($fallos["direccion"])) { 
                                            echo "<span style='color: red;'>". $fallos["direccion"]."</span>"; 
                                        } 
                                    ?>
                        
                            <div class="campo-nombre">
                                <div class="nombre">
                                    <label for="localidad">Localidad</label>
                                    <input class="campo" type="text" name="localidad" id="localidad" value="<?php echo $localidad; ?>" required> 
                                    <?php 
                                        if (isset($fallos["localidad"])) { 
                                            echo "<span style='color: red;'>". $fallos["localidad"]."</span>"; 
                                        } 
                                    ?>
                                </div>
                                
                                <div class="apellidos">
                                    <label for="provincia">Provincia</label>
                                    <input class="campo" type="text" name="provincia" id="provincia" value="<?php echo $provincia; ?>" required> 
                                    <?php 
                                        if (isset($fallos["provincia"])) { 
                                            echo "<span style='color: red;'>". $fallos["provincia"]."</span>"; 
                                        } 
                                    ?>
                                </div>
                            </div>

                            <label for="telefono">Telefono</label>
                            <input class="campo campo-contraseña" type="tel" name="telefono" id="telefono" pattern="[0-9]{9}" value="<?php echo $telefono; ?>" required>
                            <?php 
                                if (isset($fallos["telefono"])) { 
                                     echo "<span style='color: red;'>". $fallos["telefono"]."</span>"; 
                                } 
                            ?>

                            <div class="botones-form">
                                <button class="btn-registro" type="submit">Continuar</button>
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
                
                try {
        
                    $conexion = $con->conectar_db();
                    $stmt = $conexion->prepare(
                        'UPDATE clientes SET nombre = :nombre, apellidos = :apellidos, direccion = :direccion, localidad = :localidad, provincia = :provincia, telefono = :telefono WHERE dni = :dni');
        
                        $stmt->bindParam(':dni', $dni, PDO::PARAM_STR);
                        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
                        $stmt->bindParam(':apellidos', $apellidos, PDO::PARAM_STR);
                        $stmt->bindParam(':direccion', $direccion, PDO::PARAM_STR);
                        $stmt->bindParam(':localidad', $localidad, PDO::PARAM_STR);
                        $stmt->bindParam(':provincia', $provincia, PDO::PARAM_STR);
                        $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
                        
                        
                    $stmt->execute();
        
                } catch(PDOException $e) {
                    echo 'Error al insertar el cliente: ' . $e->getMessage();
                }
        
                header("Location: tramitacionpago.php");


            }
}

?>

<?php
include("conectar_db.php");
$dni = $_SESSION["dni"];
$con = new Conexion();
$datos = $con->buscarCliente($dni);

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
            
            <form class="formulario" action="datosenvio.php" method="post">

                <h3>Datos de Envío</h3>

                <div class="form-campos">

                    <div class="campo-nombre">
                        <div class="nombre">
                            <label for="dni">DNI</label>
                            <input class="campo dni" type="text" name="dni" id="dni" value="<?php echo $datos->dni; ?>" disabled>
                        </div>

                        <div class="apellidos">
                            <label for="email">Email</label>
                            <input class="campo dni" type="email" name="email" id="email" value="<?php echo $datos->email; ?>" disabled>
                        </div>
                    </div>

                    <div class="campo-nombre">
                        <div class="nombre">
                            <label for="nombre">Nombre</label>
                            <input class="campo" type="text" name="nombre" id="nombre" value="<?php echo $datos->nombre; ?>" required> 
                        </div>
                        
                        <div class="apellidos">
                            <label for="nombre">Apellidos</label>
                            <input class="campo" type="text" name="apellidos" id="apellidos" value="<?php echo $datos->apellidos; ?>" required>
                        </div>
                    </div>

                    <label for="dni">Dirección</label>
                    <input class="campo direccion" type="text" name="direccion" id="direccion" value="<?php echo $datos->direccion; ?>" required>

                    <div class="campo-nombre">
                        <div class="nombre">
                            <label for="localidad">Localidad</label>
                            <input class="campo" type="text" name="localidad" id="localidad" value="<?php echo $datos->localidad; ?>" required>  
                        </div>
                        
                        <div class="apellidos">
                            <label for="provincia">Provincia</label>
                            <input class="campo" type="text" name="provincia" id="provincia" value="<?php echo $datos->provincia; ?>" required> 
                        </div>
                    </div>

                    <label for="telefono">Telefono</label>
                    <input class="campo campo-contraseña" type="tel" name="telefono" id="telefono" pattern="[0-9]{9}" value="<?php echo $datos->telefono; ?>" required>

                    <div class="botones-form">
                        <button class="btn-registro" type="submit">Continuar</button>
                    </div>
                </div>
            </form>
           
        </main>

        <?php include("zona.php");?>

    </div>

    <?php include("footer.php");?>

    
</body>
</html>              

