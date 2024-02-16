<?php
    session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include("conectar_db.php");
    include ('La-carta.php');
    $con = new Conexion();

    //array para almacenar fallos
    $fallos = array();

    $dni = $_REQUEST["dni"];
    $nombre = $_REQUEST["nombre"];
    $apellidos = $_REQUEST["apellidos"];
    $email = $_REQUEST["email"];
    $contrasena = $_REQUEST["contrasena"];
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

            //$buscarDNI = $con->buscarDNI($dni);

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
            
            //se comprueba si el dni ya existe en la base de datos
            //if ($buscarDNI) {
                //$fallos["dni"] = "Este DNI ya se encuentra en la base de datos";
            //}
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
            
                    <form class="formulario" action="formRegistro.php" method="post">

                        <h2>Formulario de Registro</h2>

                        <div class="form-campos">
                            <label for="dni">DNI</label>
                            <input class="campo campo-dni" type="text" name="dni" id="dni" value="<?php echo $dni; ?>" required>
                            <?php 
                                if (isset($fallos["dni"])) { 
                                    echo "<span style='color: red;'>".$fallos["dni"]."</span>"; 
                                } 
                            ?>

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

                            <label for="email">Email</label>
                            <input class="campo campo-email" type="email" name="email" id="email" value="<?php echo $email; ?>" required>
                            <?php 
                                if (isset($fallos["email"])) { 
                                    echo "<span style='color: red;'>".$fallos["email"]."</span>"; 
                                } 
                            ?>

                            <label for="contraseña">Contraseña</label>
                            <input class="campo campo-contraseña" type="password" name="contrasena" id="contrasena" value="<?php echo $contrasena; ?>" required>
                            <?php 
                                if (isset($fallos["contrasena"])) { 
                                    echo "<span style='color: red;'>".$fallos["contrasena"]."</span>"; 
                                } 
                            ?>

                            <div class="botones-form">
                                <button class="btn-registro" type="submit">Crear Cuenta</button>
                                <a class="btn-registro" href="index.php">Cancelar</a>
                            </div>
                        </div>
                    </form>
                </main>

                <?php include("login.php");?>
            
            </div>

            <?php include("footer.php");?>

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
                        
                        $stmtCliente = $conexion->prepare("UPDATE clientes SET nombre = :nombre, apellidos = :apellidos, email = :email, contrasena = :contrasena, activo = 1 WHERE dni = :dni");
                        $stmtCliente->bindParam(':dni', $dni, PDO::PARAM_STR);
                        $stmtCliente->bindParam(':nombre', $nombre, PDO::PARAM_STR);
                        $stmtCliente->bindParam(':apellidos', $apellidos, PDO::PARAM_STR);
                        $stmtCliente->bindParam(':email', $email, PDO::PARAM_STR);
                        $stmtCliente->bindParam(':contrasena', $hashcontrasena, PDO::PARAM_STR);
                        $stmtCliente->execute();

                        header("Location: index.php?cliente=OK");

                    } else {
                        $stmtRegistro = $conexion->prepare(
                        'INSERT INTO clientes (dni, nombre, apellidos, email, contrasena, activo) VALUES (:dni, :nombre, :apellidos, :email, :contrasena, :activo)');
        
                        $stmtRegistro->bindParam(':dni', $dni, PDO::PARAM_STR);
                        $stmtRegistro->bindParam(':nombre', $nombre, PDO::PARAM_STR);
                        $stmtRegistro->bindParam(':apellidos', $apellidos, PDO::PARAM_STR);
                        $stmtRegistro->bindParam(':email', $email, PDO::PARAM_STR);
                        $stmtRegistro->bindParam(':contrasena', $hashcontrasena, PDO::PARAM_STR);
                        $stmtRegistro->bindParam(':activo', $activo, PDO::PARAM_STR);
                        
                        $stmtRegistro->execute();

                        header("Location: index.php?registro=OK");

                    }

                } catch(PDOException $e) {
                    echo 'Error al insertar el cliente: ' . $e->getMessage();
                }
        
            }

}

?>

<?php
include ('La-carta.php');
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
            
            <form class="formulario" action="formRegistro.php" method="post">

                <h2>Formulario de Registro</h2>

                <div class="form-campos">
                    <label for="dni">DNI</label>
                    <input class="campo campo-dni" type="text" name="dni" id="dni">
                
                    <div class="campo-nombre">
                        <div class="nombre">
                            <label for="nombre">Nombre</label>
                            <input class="campo" type="text" name="nombre" id="nombre" > 
                        </div>
                        
                        <div class="apellidos">
                            <label for="nombre">Apellidos</label>
                            <input class="campo" type="text" name="apellidos" id="apellidos" >
                        </div>
                    </div>
             
                    <label for="email">Email</label>
                    <input class="campo campo-email" type="email" name="email" id="email" >

                    <label for="contraseña">Contraseña</label>
                    <input class="campo campo-contraseña" type="password" name="contrasena" id="contrasena" >

                    <div class="botones-form">
                        <button class="btn-registro" type="submit">Crear Cuenta</button>
                        <a class="btn-registro" href="index.php">Cancelar</a>
                    </div>
                </div>
            </form>
           
        </main>

        <?php include("login.php");?>

    </div>

    <?php include("footer.php");?>

  
</body>
</html>              