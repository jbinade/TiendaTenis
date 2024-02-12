<?php
// initializ shopping cart class
include("conectar_db.php");
include ("claseCarrito.php");
$carrito = new Carrito;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Cart - PHP Shopping Cart Tutorial</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style>
    .container{padding: 20px;}
    input[type="number"]{width: 20%;}
    </style>
    <script>
    function updateCartItem(obj,id){
        $.get("AccionCarta.php", {action:"updateCartItem", id:id, qty:obj.value}, function(data){
            if(data == 'ok'){
                location.reload();
            }else{
                alert('Cart update failed, please try again.');
            }
        });
    }
    </script>
</head>
</head>
<body>
<div class="container">
<div class="panel panel-default">
<div class="panel-heading"> 

<ul class="nav nav-pills">
  <li role="presentation"><a href="index.php">Inicio</a></li>
  <li role="presentation" class="active"><a href="VerCarta.php">Ver Carta</a></li>
  <li role="presentation"><a href="Pagos.php">Pagos</a></li>
</ul>
</div>

<div class="panel-body">


    <h1>Carrito de compras</h1>
    <table class="table">
    <thead>
        <tr>
            <th>Producto</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Sub total</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $total_items = $carrito->total_items();
        if($total_items > 0){
            //get cart items from session
            $cartItems = $carrito->contents();
            foreach($cartItems as $item){
        ?>
        <tr>
    <td><?php echo isset($item["nombre"]) ? $item["nombre"] : ""; ?></td>
    <td><?php echo isset($item["precio"]) ? '$'.$item["precio"].' USD' : ""; ?></td>
    <td><input type="number" class="form-control text-center" value="<?php echo isset($item['cantidad']) ? $item['cantidad'] : ''; ?>" onchange="updateCartItem(this, '<?php echo isset($item['rowcodigo']) ? $item['rowcodigo'] : ''; ?>')"></td>
    <td><?php echo isset($item["subtotal"]) ? '$'.$item["subtotal"].' USD' : ""; ?></td>
    <td>
        <a href="AccionCarta.php?action=removeCartItem&codigo=<?php echo isset($item["rowcodigo"]) ? $item["rowcodigo"] : ""; ?>" class="btn btn-danger" onclick="return confirm('Confirma eliminar?')"><i class="glyphicon glyphicon-trash"></i></a>
    </td>
</tr>
<?php } }else{ ?>
<tr><td colspan="5"><p>Tu carta está vacía.....</p></td>
<?php } ?>
    </tbody>
    <tfoot>
        <tr>
            <td><a href="index.php" class="btn btn-warning"><i class="glyphicon glyphicon-menu-left"></i> Continue Comprando</a></td>
            <td colspan="2"></td>
            <?php if($carrito->total_items() > 0){ ?>
            <td class="text-center"><strong>Total <?php echo '$'.$carrito->total().' USD'; ?></strong></td>
            <td><a href="Pagos.php" class="btn btn-success btn-block">Pagos <i class="glyphicon glyphicon-menu-right"></i></a></td>
            <?php } ?>
        </tr>
    </tfoot>
    </table>
    
    </div>
 <div class="panel-footer">BaulPHP</div>
 </div><!--Panek cierra-->
 
</div>
</body>
</html>