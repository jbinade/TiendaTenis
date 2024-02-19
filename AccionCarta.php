<?php

// Iniciamos la clase de la carta
session_start();
include 'La-carta.php';
$cart = new Cart;

// include database configuration file
include 'conectar_db.php';
$con = new Conexion();
$conexion = $con->conectar_db();
if(isset($_REQUEST['action']) && !empty($_REQUEST['action'])){
    if($_REQUEST['action'] == 'addToCart' && !empty($_REQUEST['codigo'])){
        $productID = $_REQUEST['codigo'];
        // get product details
        $stmt = $conexion->prepare("SELECT * FROM articulos WHERE codigo = :codigo");
        $stmt->bindParam(':codigo', $productID, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $itemData = array(
            'id' => $row['codigo'],
            'name' => $row['nombre'],
            'price' => $row['precio'],
            'descuento' => $row['preciodest'],
            'qty' => 1
        );
        
        $insertItem = $cart->insert($itemData);
        $redirectLoc = $insertItem?'index.php':'index.php';
        header("Location: ".$redirectLoc);
    }elseif($_REQUEST['action'] == 'updateCartItem' && !empty($_REQUEST['id'])){
        $itemData = array(
            'rowid' => $_REQUEST['id'],
            'qty' => $_REQUEST['qty']
        );
        $updateItem = $cart->update($itemData);
        echo $updateItem?'ok':'err';die;
    }elseif($_REQUEST['action'] == 'removeCartItem' && !empty($_REQUEST['id'])){
        $deleteItem = $cart->remove($_REQUEST['id']);
        header("Location: vercesta.php");
    }elseif($_REQUEST['action'] == 'placeOrder' && $cart->total_items() > 0 && !empty($_REQUEST['dni'])){
        // insert order details into database
        $dni = $_REQUEST["dni"];
        $estado = "Creado";
        $activo = 1;

        $stmt = $conexion->prepare(
            'INSERT INTO pedidos (fecha, total, estado, codCliente, activo) VALUES (:fecha, :total, :estado, :codCliente, :activo)');
            
        $fecha = date('Y-m-d H:i:s'); // Almacena la fecha actual en una variable
        $total = $cart->total(); // Almacena el total del carrito en una variable

        $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR); // Pasa la variable $fecha por referencia
        $stmt->bindParam(':total', $total, PDO::PARAM_STR); // Pasa la variable $total por referencia
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindParam(':codCliente', $dni, PDO::PARAM_STR);
        $stmt->bindParam(':activo', $activo, PDO::PARAM_STR);
            
        $insertOrder = $stmt->execute();

        if($insertOrder){
            $orderID = $conexion->lastInsertId();
        
            // get cart items
            $cartItems = $cart->contents();
            foreach($cartItems as $item){

                $porcentajeDescuento = isset($item["descuento"]) ? $item["descuento"] * 100 : 0;
                if (isset($item["price"], $item["descuento"], $item["qty"])) {
                    $precioConDescuento = $item["price"] * (1 - $item["descuento"]);
                    $subtotalConDescuento = $precioConDescuento * $item["qty"];
                } else {
                    $subtotalConDescuento = 0;
                }

                $stmt = $conexion->prepare(
                    'INSERT INTO lineapedido (numPedido, codArticulo, cantidad, precio, descuento, preciototal) VALUES (:numPedido, :codArticulo, :cantidad, :precio, :descuento, :preciototal)');
                
                $stmt->bindParam(':numPedido', $orderID, PDO::PARAM_STR);
                $stmt->bindParam(':codArticulo', $item['id'], PDO::PARAM_STR);
                $stmt->bindParam(':cantidad', $item['qty'], PDO::PARAM_STR);
                $stmt->bindParam(':precio', $item['price'], PDO::PARAM_STR);
                $stmt->bindParam(':descuento', $porcentajeDescuento, PDO::PARAM_STR);
                $stmt->bindParam(':preciototal', $subtotalConDescuento, PDO::PARAM_STR);

                $stmt->execute();

            }
            // insert order items into database
            
            $cart->destroy();
            header("Location: confirmacionpedido.php?id=$orderID");
        } else {
            header("Location: tramitacionpago.php");
        }
    }else{
        header("Location: index.php");
    }
}else{
    header("Location: index.php");
}