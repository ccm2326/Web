<?php

include 'utilities/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:home.php');
};

if(isset($_POST['delete'])){
   $cart_id = $_POST['cart_id']; // Autoincremental en la base de datos
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
   $delete_cart_item->execute([$cart_id]);
   $message[] = 'Artículo eliminado';
}

if(isset($_POST['delete_all'])){
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
   $delete_cart_item->execute([$user_id]);
   // header('location:cart.php');
   $message[] = 'Se eliminaron todos los artículos';
}

if(isset($_POST['update_qty'])){
   $cart_id = $_POST['cart_id'];
   $qty = $_POST['qty'];
   $update_qty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
   $update_qty->execute([$qty, $cart_id]);
   $message[] = 'Cantidad actualizada';
}

$grand_total = 0;

?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Carrito</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'utilities/user_header.php'; ?>

<div class="heading">
   <h3> Carrito de compras </h3>
   <p><a href="home.php">Inicio</a> <span> / Carrito</span></p>
</div>

<section class="products">

   <h1 class="title">Mi carrito</h1>

   <div class="box-container">

      <?php
         $grand_total = 0;
         $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $select_cart->execute([$user_id]);
         if($select_cart->rowCount() > 0){
            while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
      ?>
      <form action="" method="post" class="box">
         <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>"> <!-- id oculta -->
         <a href="quick_view.php?pid=<?= $fetch_cart['pid']; ?>" class="fas fa-eye"></a> <!-- ref: ver artículo -->
         <button type="submit" class="fas fa-times" name="delete" onclick="return confirm('¿Eliminar artículo?');"></button> <!-- Botón eliminar un artículo -->
         <img src="uploaded_img/<?= $fetch_cart['image']; ?>" alt="">
         <div class="name"><?= $fetch_cart['name']; ?></div>
         <div class="flex">
            <div class="price"><span>$</span><?= $fetch_cart['price']; ?></div> <!-- Precio por unidad -->
            <input type="number" name="qty" class="qty" min="1" max="99" value="<?= $fetch_cart['quantity']; ?>" maxlength="2"> <!-- Cantidad para pedir -->
            <button type="submit" class="fas fa-edit" name="update_qty"></button> <!-- Botón para actualizar la cantidad -->
         </div>
         <div class="sub-total"> sub total : <span>S/.<?= $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']); ?>/-</span> </div> <!-- Precio por unidad x cantidad -->
      </form>
      <?php
               $grand_total += $sub_total;
            }
         }else{
            echo '<p class="empty">Ninguna prenda agregada</p>';
         }
      ?>

   </div>

   <!-- Muestra el total general de todos los artículos -->
   <div class="cart-total">
      <p>Total : <span>S/.<?= $grand_total; ?></span></p> <!-- Total general de todos los artículos -->
      <a href="checkout.php" class="btn <?= ($grand_total > 1)?'':'disabled'; ?>">Comprar</a> <!-- Redirección a checkout -->
   </div>

   <!-- Eliminar todos los artículos -->
   <div class="more-btn">
      <form action="" method="post">
         <!-- Desabilitado si no hay artículos -->
         <button type="submit" class="delete-btn <?= ($grand_total > 1)?'':'disabled'; ?>" name="delete_all" onclick="return confirm('¿Eliminar todos los artículos?');">Eliminar todos</button>
      </form>
      <a href="collection.php" class="btn">Seguir comprando</a>
   </div>

</section>


<?php include 'utilities/footer.php'; ?>
<script src="js/script.js"></script>

</body>
</html>