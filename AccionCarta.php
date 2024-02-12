<?php

// Iniciamos la clase de la carta
include("conectar_db.php");
include ("claseCarrito.php");
$carrito = new Carrito();
$con = new Conexion();
// include database configuration file

if(isset($_REQUEST['action']) && !empty($_REQUEST['action'])){
    if($_REQUEST['action'] == 'addToCart' && !empty($_REQUEST['codigo'])){
        $codigo = $_REQUEST['codigo'];
        // get product details
        $conexion = $con->conectar_db();
        $stmt = $conexion->prepare("SELECT * FROM articulos WHERE codigo = :codigo");
        $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $itemData = array(
            'codigo' => $row['codigo'],
            'nombre' => $row['nombre'],
            'precio' => $row['precio'],
            'descuento' => $row['preciodest'],
            'cantidad' => 1
        );
        
        $insertItem = $carrito->insert($itemData);
        // Después de agregar el artículo al carrito, obtén la información actualizada del carrito
        $_SESSION['cart_contents'] = $carrito->contents();

       
        $redirectLoc = $insertItem?'index.php':'index.php';
        header("Location: ".$redirectLoc);

    } elseif ($_REQUEST['action'] == 'updateCartItem' && !empty($_REQUEST['codigo'])){
        $itemData = array(
            'codigo' => $_REQUEST['codigo'],
            'cantidad' => $_REQUEST['cantidad']

        );
        $updateItem = $carrito->update($itemData);

        $nuevoSubtotal = $carrito->get_item($item['codigo'])['subtotal'];

        echo 'ok:' . $nuevoSubtotal;

    } elseif ($_REQUEST['action'] == 'removeCartItem' && !empty($_REQUEST['codigo'])){
        $deleteItem = $carrito->remove($_REQUEST['codigo']);
        header("Location: vercesta.php");

    } elseif ($_REQUEST['action'] == 'placeOrder' && $cart->total_items() > 0 && !empty($_SESSION['sessCustomerID'])){
        // insert order details into database
        $insertOrder = $db->query("INSERT INTO orden (customer_id, total_price, created, modified) VALUES ('".$_SESSION['sessCustomerID']."', '".$cart->total()."', '".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."')");
        
        if($insertOrder){
            $orderID = $db->insert_id;
            $sql = '';
            // get cart items
            $cartItems = $cart->contents();
            foreach($cartItems as $item){
                $sql .= "INSERT INTO orden_articulos (order_id, product_id, quantity) VALUES ('".$orderID."', '".$item['id']."', '".$item['qty']."');";
            }
            // insert order items into database
            $insertOrderItems = $db->multi_query($sql);
            
            if($insertOrderItems){
                $cart->destroy();
                header("Location: OrdenExito.php?id=$orderID");
            }else{
                header("Location: Pagos.php");
            }
        }else{
            header("Location: Pagos.php");
        }
    }else{
        header("Location: index.php");
    }
}else{
    header("Location: index.php");
}