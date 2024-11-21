<?php

include '../utilities/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
};

if(isset($_POST['update_payment'])){
   if (isset($_POST['payment_status']) && !empty($_POST['payment_status'])) {
      $order_id = $_POST['order_id'];
      $payment_status = $_POST['payment_status'];
      
      // Actualizamos el estado del pago en la base de datos
      $update_status = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
      $update_status->execute([$payment_status, $order_id]);
      $message[] = '¡Estado de pago actualizado!';
   } else {
      $message[] = '¡Por favor, seleccione un estado de pago!';
   }

}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_order = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
   $delete_order->execute([$delete_id]);
   header('location:placed_orders.php');
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Pedidos realizados</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body class="main-content">

<?php include '../utilities/admin_header.php' ?>

<div class="container-2">

   <h1 class="heading">Pedidos realizados</h1>

   <div class="box-container">

   <?php
      $select_orders = $conn->prepare("SELECT * FROM `orders`");
      $select_orders->execute();
      if($select_orders->rowCount() > 0){
         while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <h3 style="font-size: 1.8em">Pedido N° <?= $fetch_orders['id']; ?></h3>
      <p> Usuario id : <span><?= $fetch_orders['user_id']; ?></span> </p>
      <p> Colocado en : <span><?= $fetch_orders['placed_on']; ?></span> </p>
      <p> Nombre : <span><?= $fetch_orders['name']; ?></span> </p>
      <p> Email : <span><?= $fetch_orders['email']; ?></span> </p>
      <p> Número : <span><?= $fetch_orders['number']; ?></span> </p>
      <p> Dirección : <span><?= $fetch_orders['address']; ?></span> </p>
      <p> Productos totales : <span><?= $fetch_orders['total_products']; ?></span> </p>
      <p> Precio total : <span>$<?= $fetch_orders['total_price']; ?>/-</span> </p>
      <p> Método de pago : <span><?= $fetch_orders['method']; ?></span> </p>
      <form action="" method="POST">
         <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
         <select name="payment_status" class="box" required>
            <option value="Pendiente" <?= ($fetch_orders['payment_status'] == 'Pendiente') ? 'selected' : '' ?>>Pendiente</option>
            <option value="Completado" <?= ($fetch_orders['payment_status'] == 'Completado') ? 'selected' : '' ?>>Completado</option>
         </select>
         <div class="flex-btn">
            <input type="submit" value="Actualizar" class="option-btn" name="update_payment">
            <a href="placed_orders.php?delete=<?= $fetch_orders['id']; ?>" class="delete-btn" onclick="return confirm('¿Eliminar este pedido?');">Borrar</a>
         </div>
      </form>
   </div>
   <?php
      }
   }else{
      echo '<p class="empty">¡Aún no hay pedidos realizados!</p>';
   }
   ?>

   </div>

</div>

<script src="../js/admin_script.js"></script>

</body>
</html>