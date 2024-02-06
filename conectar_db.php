<?php



    //clase cliente
    class Cliente {

        public $dni;
        public $nombre;
        public $apellidos;
        public $telefono;
        public $rol;
        public $direccion;
        public $localidad;
        public $provincia;
        public $email;
        public $contrasena;


        public function __construct($dni, $nombre, $apellidos, $telefono, $rol, $direccion, $localidad, $provincia, $email, $contrasena) {
            $this->dni = $dni;
            $this->nombre = $nombre;
            $this->apellidos = $apellidos;
            $this->telefono = $telefono;
            $this->rol = $rol;
            $this->direccion = $direccion;
            $this->localidad = $localidad;
            $this->provincia = $provincia;
            $this->email = $email;
            $this->$contrasena = $contrasena;

        }
 
    }

    class Articulo {

        public $codigo;
        public $marca;
        public $nombre;
        public $descripcion;
        public $precio;
        public $preciodest;
        public $categoria;
        public $sexo;
        public $imagen;


        public function __construct($codigo, $marca, $nombre, $descripcion, $precio, $preciodest, $categoria, $sexo, $imagen) {
            $this->codigo = $codigo;
            $this->marca = $marca;
            $this->nombre = $nombre;
            $this->descripcion = $descripcion;
            $this->precio = $precio;
            $this->preciodest = $preciodest;
            $this->categoria = $categoria;
            $this->sexo = $sexo;
            $this->imagen = $imagen;
            
        }

    }


    class Conexion {

        private $host = "localhost";
        private $baseDatos = "tiendatenis";
        private $usuario = "root";
        private $password = "";


        public function conectar_db() {

            $dsn = "mysql:host=$this->host;dbname=$this->baseDatos";

            $opciones = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {
                $con = new PDO($dsn, $this->usuario, $this->password, $opciones);
                return $con;

            } catch(PDOException $e) {
                echo 'Error: '.$e->getMessage();
            }

        }

        public function mostrarArticulos() {
            
            try {

                $con = new Conexion();
                $conexion = $con->conectar_db();

                $stmt = $conexion->query("SELECT imagen, nombre, descripcion, precio, preciodest FROM articulos");

                while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<div class="articulo">';
                    echo '<img src="' . $fila['imagen'] . '"alt="imagen">';
                    echo '<div class="info-articulo">';
                    echo '<a href=""><h3>' . $fila['nombre'] . '</h3></a>';
                    echo '<p class="descripcion">' . $fila['descripcion'] . '</p>';
                    echo '<div class="precio">';
                    echo '<div class="precio">';
                    echo '<p class="precio-original">' . $fila['precio'] . '€</p>';
                    echo '<p class="preciodest">' . $fila['preciodest'] . '€</p>';
                    echo '</div>';
                    echo '<button class="btn-articulo">Añadir a la cesta</button>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';

                }

            } catch (PDOException $e) {
                echo "Error al recuperar datos: " . $e->getMessage();
            }
        }

        public function buscarDNI($dni) {

            $con = $this->conectar_db();

                try {
                    $stmt = $con->prepare('SELECT * FROM clientes WHERE dni = :dni');
                    $stmt->bindParam(':dni', $dni, PDO::PARAM_STR);
                    $stmt->execute();

                    return $stmt->rowCount() > 0;

                } catch(PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }

        }

        public function buscarCliente($dni) {

            $con = $this->conectar_db();

            try {
                $stmt = $con->prepare('SELECT * FROM clientes WHERE dni = :dni');
                $stmt->bindParam(':dni', $dni, PDO::PARAM_STR);
                $stmt->execute();

                $res = $stmt->fetch(PDO::FETCH_OBJ);

                if($res) {
                    $cliente = new Cliente(
                        $res->dni,
                        $res->nombre,
                        $res->apellidos,
                        $res->telefono,
                        $res->rol,
                        $res->direccion,
                        $res->localidad,
                        $res->provincia,
                        $res->email,        
                        $res->contrasena
                    );

                    return $cliente;
                } else {
                    return null;
                }

            } catch(PDOException $e) {
                echo 'Error al buscar el cliente: ' . $e->getMessage();
            }

        }

        public function verificarLogin($email) {

            $con = $this->conectar_db();

            try {
                $stmt = $con->prepare('SELECT dni, nombre, rol, contrasena FROM clientes WHERE email = :email AND activo = 1');
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();

                return $stmt->fetch(PDO::FETCH_ASSOC);

              

            } catch(PDOException $e) {
                echo "Error: " . $e->getMessage();
            }

        }



    }

?>