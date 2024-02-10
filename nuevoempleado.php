<?php

include("seguridad.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include("conectar_db.php");

    $con = new Conexion();

    //array para almacenar fallos
    $fallos = array();

    $dni = $_REQUEST["dni"];
    $nombre = $_REQUEST["nombre"];
    $apellidos = $_REQUEST["apellidos"];
    $direccion = $_REQUEST["direccion"];
    $localidad = $_REQUEST["localidad"];
    $provincia = $_REQUEST["provincia"];
    $telefono = $_REQUEST["telefono"];
    $email = $_REQUEST["email"];
    $contrasena = $_REQUEST["contrasena"];
    $rol = "empleado";
    $activo = 1;

    $hashcontrasena = password_hash($contrasena, PASSWORD_DEFAULT);

    //funcion para validar el dni
    function validarDNI($dni, $con) {
        $fallos = array();

        if (strlen($dni) == 9) {
            $numeros = substr($dni, 0, 8);
            $letra = strtoupper(substr($dni, 8, 1));

            $comprobarNumeros = ord($numeros);
            
            //se comprueba si hay numeros
            if ($comprobarNumeros >= 48 && $comprobarNumeros <= 57) {
                $comprobarLetra = ord($letra);
                
                //se comprueba que la letra es valida
                if (($comprobarLetra >= 65 && $comprobarLetra <= 90) || ($comprobarLetra >= 97 && $comprobarLetra <= 122)) {
                    $letrasDNI = "TRWAGMYFPDXBNJZSQVHLCKE";
                    $numDni = $numeros % 23;
                    $comprobarDni = $letrasDNI[$numDni];

                    if ($letra == $comprobarDni) {
                        $letra = strtoupper($letra);
                    } else {
                        $fallos["dni"] = "Letra no válida";
                    }
                } else {
                    $fallos["dni"] = "No has introducido una letra";
                }
            } else {
                $fallos["dni"] = "No has introducido números";
            }

            $buscarDNI = $con->buscarDNI($dni);
            
            //se comprueba si el dni ya existe en la base de datos
            //if ($buscarDNI) {

                try {

                    $conexion = $con->conectar_db();
                    $stmt = $conexion->prepare("SELECT * FROM clientes WHERE dni = :dni");
                    $stmt->bindParam(':dni', $dni, PDO::PARAM_STR);
                    $stmt->execute();
    
                   
                    $dni_existe = $stmt->fetch(PDO::FETCH_ASSOC);
    
                    if ($dni_existe) {
                        if ($dni_existe['activo'] == 0) {
                            // Si el usuario existe pero está inactivo, permitir el registro
                            $fallos = array();
                        } else {
                            // Si el usuario existe y está activo, mostrar mensaje de error
                            $fallos["dni"] = "Este DNI ya se encuentra en la base de datos";
                        }
                    }
    
                } catch(PDOException $e) {
                        echo 'Error al insertar el email: ' . $e->getMessage();
                }
            
        } else {
            $fallos["dni"] = "El DNI es obligatorio";
        }

        return $fallos;
    }

    //verificar los campos
    if (empty($nombre)) {
        $fallos["nombre"] = "El nombre es obligatorio";
    }

    if (empty($apellidos)) {
        $fallos["apellidos"] = "Por favor, introduce el nombre completo";
    }

    if (empty($contrasena)) {
        $fallos["contrasena"] = "La contraseña es obligatoria";
    }

    if (empty($email) || strpos($email, " ") !== false) {
        $fallos["email"] = "El email no puede estar en blanco ni tener espacios";

    } else {
        if ($email) {

            try {

                $conexion = $con->conectar_db();
                $stmt = $conexion->prepare("SELECT * FROM clientes WHERE email = :email");
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();

               
                $email_existe = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($email_existe) {
                    if ($email_existe['activo'] == 0) {
                        // Si el usuario existe pero está inactivo, permitir el registro
                        $fallos = array();
                    } else {
                        // Si el usuario existe y está activo, mostrar mensaje de error
                        $fallos["email"] = "Este email ya se encuentra en uso";
                    }
                }

            } catch(PDOException $e) {
                    echo 'Error al insertar el email: ' . $e->getMessage();
            }

        }

    } 

    if (empty($direccion)) {
        $fallos["direccion"] = "La direccion es obligatoria";
    }

    if (empty($localidad)) {
        $fallos["localidad"] = "La localidad es obligatoria";
    }

    if (empty($provincia)) {
        $fallos["provincia"] = "La provinicia es obligatoria";
    }

    if  (empty($telefono)) {
        $fallos["telefono"] = "El teléfono es obligatorio";
        

    } else {

        if (strlen($telefono) != 9 || !is_numeric($telefono)) {
            $fallos["telefono"] = "Teléfono incorrecto";
        }
    }


        
    $fallos = array_merge($fallos, validarDNI($dni, $con));

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
                    <form class="formulario" action="nuevoempleado.php" method="post">

                            <h3>Datos Empleado</h3>

                            <div class="form-campos">

                            
                                <div class="nombre">
                                    <label for="dni">DNI</label>
                                    <input class="campo" type="text" name="dni" id="dni" value="<?php echo $dni; ?>" required>
                                    <?php 
                                        if (isset($fallos["dni"])) { 
                                            echo "<span style='color: red;'>". $fallos["dni"]."</span>"; 
                                        } 
                                    ?>
                                </div>

                            
                                <div class="apellidos email-empleado">
                                    <label for="email">Email</label>
                                    <input class="campo" type="email" name="email" id="email" value="<?php echo $email; ?>" required>
                                    <?php 
                                        if (isset($fallos["email"])) { 
                                            echo "<span style='color: red;'>". $fallos["email"]."</span>"; 
                                        } 
                                    ?>
                                </div>

                                <div class="apellidos">
                                    <label for="contraseña">Contraseña</label>
                                    <input class="campo campo-contraseña" type="password" name="contrasena" id="contrasena" value="<?php echo $contrasena; ?>" required>
                                    <?php 
                                        if (isset($fallos["contrasena"])) { 
                                            echo "<span style='color: red;'>". $fallos["contrasena"]."</span>"; 
                                        } 
                                    ?>
                                </div>
                                
                                <div class="campo-nombre">
                                    <div class="nombre">
                                        <label for="nombre">Nombre</label>
                                        <input class="campo" type="text" name="nombre" id="nombre" value="<?php echo $nombre; ?>" required> 
                                        <?php 
                                        if (isset($fallos["nombre"])) { 
                                            echo "<span style='color: red;'>". $fallos["nombre"]."</span>"; 
                                        } 
                                    ?>
                                    </div>
                                    
                                    <div class="apellidos">
                                        <label for="nombre">Apellidos</label>
                                        <input class="campo" type="text" name="apellidos" id="apellidos" value="<?php echo $apellidos; ?>" required>
                                        <?php 
                                        if (isset($fallos["apellidos"])) { 
                                            echo "<span style='color: red;'>". $fallos["apellidos"]."</span>"; 
                                        } 
                                    ?>
                                    </div>
                                </div>

                                <label for="dni">Dirección</label>
                                <input class="campo direccion" type="text" name="direccion" id="direccion" value="<?php echo $direccion; ?>" required>
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
                                <input class="campo campo-contraseña" type="tel" name="telefono" id="telefono" value="<?php echo $telefono; ?>" pattern="[0-9]{9}" required>
                                <?php 
                                        if (isset($fallos["telefono"])) { 
                                            echo "<span style='color: red;'>". $fallos["telefono"]."</span>"; 
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
                    $stmt = $conexion->prepare("SELECT * FROM clientes WHERE dni = :dni");
                    $stmt->bindParam(':dni', $dni, PDO::PARAM_STR);
                    $stmt->execute();

                    if ($stmt->rowCount() > 0) {
                        
                        $stmtCliente = $conexion->prepare("UPDATE clientes SET activo = 1 WHERE dni = :dni");
                        $stmtCliente->bindParam(':dni', $dni, PDO::PARAM_STR);
                        $stmtCliente->execute();

                        header("Location: index.php?cliente=OK");
                    
                    } else {
                        $stmt = $conexion->prepare(
                        'INSERT INTO clientes (dni, nombre, apellidos, telefono, rol, direccion, localidad, provincia, email, contrasena, activo) VALUES (:dni, :nombre, :apellidos, :telefono, :rol, :direccion, :localidad, :provincia, :email, :contrasena, :activo)');
        
                        $stmt->bindParam(':dni', $dni, PDO::PARAM_STR);
                        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
                        $stmt->bindParam(':apellidos', $apellidos, PDO::PARAM_STR);
                        $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
                        $stmt->bindParam(':rol', $rol, PDO::PARAM_STR);
                        $stmt->bindParam(':direccion', $direccion, PDO::PARAM_STR);
                        $stmt->bindParam(':localidad', $localidad, PDO::PARAM_STR);
                        $stmt->bindParam(':provincia', $provincia, PDO::PARAM_STR);
                        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                        $stmt->bindParam(':contrasena', $hashcontrasena, PDO::PARAM_STR);
                        $stmt->bindParam(':activo', $activo, PDO::PARAM_STR);
                        
                    $stmt->execute();

                    header("Location: index.php?empleado=OK");
        
                    }

                } catch(PDOException $e) {
                    echo 'Error al insertar el empleado: ' . $e->getMessage();
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
            
            <form class="formulario" action="nuevoempleado.php" method="post">

                <h3>Datos Empleado</h3>

                <div class="form-campos">

                   
                    <div class="nombre">
                        <label for="dni">DNI</label>
                        <input class="campo" type="text" name="dni" id="dni" required>
                    </div>

                  
                    <div class="apellidos email-empleado">
                        <label for="email">Email</label>
                        <input class="campo" type="email" name="email" id="email" required>
                    </div>

                    <div class="apellidos">
                        <label for="contraseña">Contraseña</label>
                        <input class="campo campo-contraseña" type="password" name="contrasena" id="contrasena" required>
                    </div>
                    
                    <div class="campo-nombre">
                        <div class="nombre">
                            <label for="nombre">Nombre</label>
                            <input class="campo" type="text" name="nombre" id="nombre" required> 
                        </div>
                        
                        <div class="apellidos">
                            <label for="nombre">Apellidos</label>
                            <input class="campo" type="text" name="apellidos" id="apellidos" required>
                        </div>
                    </div>

                    <label for="dni">Dirección</label>
                    <input class="campo direccion" type="text" name="direccion" id="direccion" required>

                    <div class="campo-nombre">
                        <div class="nombre">
                            <label for="localidad">Localidad</label>
                            <input class="campo" type="text" name="localidad" id="localidad" required> 
                        </div>
                        
                        <div class="apellidos">
                            <label for="provincia">Provincia</label>
                            <input class="campo" type="text" name="provincia" id="provincia" required> 
                        </div>
                    </div>

                    <label for="telefono">Telefono</label>
                    <input class="campo campo-contraseña" type="tel" name="telefono" id="telefono" pattern="[0-9]{9}" required>

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