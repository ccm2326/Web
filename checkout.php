<?php

include 'utilities/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:home.php');
};

if(isset($_POST['submit'])){
   $name = $_POST['name'];
   $number = $_POST['number'];
   $email = $_POST['email'];
   $method = $_POST['method'];
   $address = $_POST['address'];
   $total_products = $_POST['total_products'];
   $total_price = $_POST['total_price'];

   $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $check_cart->execute([$user_id]);

   if($check_cart->rowCount() > 0){ // Verifica si hay productos (forzado)
      if($address == ''){  // Verifica la dirección
         $message[] = 'Actualice su dirección';
      }else{
         $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price) VALUES(?,?,?,?,?,?,?,?)");
         $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $total_price]);

         $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
         $delete_cart->execute([$user_id]);

         $message[] = 'Pedido realizado con éxito';
      }
   }else{
      $message[] = 'Su carrito está vacío';
   }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Pagar</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'utilities/user_header.php'; ?>

<div class="heading">
   <h3>Pagar</h3>
   <p><a href="home.php">Inicio</a> <span> / Pago</span></p>
</div>

<section class="checkout">

   <h1 class="title">Resumen de la compra</h1>

<form action="" method="post">

   <div class="cart-items">
      <h3>Artículos</h3>
      <?php
         $grand_total = 0; // Total de la compra
         $cart_items[] = ''; // Almacena los productos del carrito
         $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $select_cart->execute([$user_id]);
         if($select_cart->rowCount() > 0){ // Verifica si el carrito tiene algún producto
            while($fetch_cart = $select_cart->fetch(mode: PDO::FETCH_ASSOC)){
               $cart_items[] = $fetch_cart['name'].' ('.$fetch_cart['price'].' x '. $fetch_cart['quantity'].') - '; // Añade un elemento al array [] -> Producto A (10 x 2)
               $total_products = implode($cart_items); // Convertir en cadena todo el array
               $grand_total += ($fetch_cart['price'] * $fetch_cart['quantity']);
      ?>
      <!-- Mostrar cada prenda -->
      <p><span class="name"><?= $fetch_cart['name']; ?></span><span class="price">S/.<?= $fetch_cart['price']; ?> x <?= $fetch_cart['quantity']; ?></span></p>
      <?php
            }
         }else{
            echo '<p class="empty">Ninguna prenda agregada</p>';
         }
      ?>
      <p class="grand-total"><span class="name">Total:</span><span class="price">S/.<?= $grand_total; ?></span></p>
      <a href="cart.php" class="btn"> Revisar carrito </a>
   </div>

   <input type="hidden" name="total_products" value="<?= $total_products; ?>">
   <input type="hidden" name="total_price" value="<?= $grand_total; ?>" value="">
   <input type="hidden" name="name" value="<?= $fetch_profile['name'] ?>">
   <input type="hidden" name="number" value="<?= $fetch_profile['number'] ?>">
   <input type="hidden" name="email" value="<?= $fetch_profile['email'] ?>">
   <input type="hidden" name="address" value="<?= $fetch_profile['address'] ?>">

   <div class="user-info">
      <!-- DATOS -->
      <h3>Datos</h3>
      <p><i class="fas fa-user"></i><span><?= $fetch_profile['name'] ?></span></p>
      <p><i class="fas fa-phone"></i><span><?= $fetch_profile['number'] ?></span></p>
      <p><i class="fas fa-envelope"></i><span><?= $fetch_profile['email'] ?></span></p>
      <a href="update_profile.php" class="btn">Actualizar</a>
      
      <!-- DIRECCIÓN -->
      <h3>Dirección de entrega</h3>
      <p><i class="fas fa-map-marker-alt"></i><span><?php if($fetch_profile['address'] == ''){echo 'Actualice su dirección';}else{echo $fetch_profile['address'];} ?></span></p>
      <a href="update_address.php" class="btn">Actualizar</a>

      <!-- MÉTODO DE PAGO -->
      <select name="method" class="box" required>
         <option value="" disabled selected>Método de pago</option>
         <option value="tarjeta de crédito">Tarjeta de crédito</option>
         <option value="efectivo">Efectivo al entregar</option>
      </select>
      <input type="submit" value="Realizar pedido" class="btn <?php if($fetch_profile['address'] == ''){echo 'disabled';} ?>" style="width:100%; background:var(--red); color:var(--white);" name="submit">
   </div>

</form>
   
</section>

<?php include 'utilities/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>