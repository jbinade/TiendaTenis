<?php

// Iniciamos la clase de la carta
include("conectar_db.php");
include ("claseCarrito.php");
$carrito = new Carrito();
$con = new Conexion();
// include database configuration file


$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'addToCart' && isset($_GET['codigo'])) {
    $codigo = $_GET['codigo'];

    $conexion = $con->conectar_db();
    $stmt = $conexion->prepare("SELECT * FROM articulos WHERE codigo = :codigo");
    $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
    $stmt->execute();
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);

    $nombre = $fila['nombre'];
    $precio = $fila['precio']; 
    $descuento = $fila['preciodest']; 

    // Agregar el artículo al carrito
    $carrito->add($codigo, $nombre, $precio, $descuento, 1); // La cantidad es 1 por defecto

    // Redirigir de vuelta a la página anterior o a donde sea necesario
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

