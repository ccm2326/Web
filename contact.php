<?php

include 'utilities/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

if(isset($_POST['send'])){
   $name = $_POST['name'];
   $email = $_POST['email'];
   $number = $_POST['number'];
   $msg = $_POST['msg'];

   $select_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $select_message->execute([$name, $email, $number, $msg]);

   if($select_message->rowCount() > 0){
      $message[] = 'Mensaje repetido';
   }else{
      $insert_message = $conn->prepare("INSERT INTO `messages`(user_id, name, email, number, message) VALUES(?,?,?,?,?)");
      $insert_message->execute([$user_id, $name, $email, $number, $msg]);
      $message[] = 'Mensaje enviado correctamente';
   }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Contacto</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>

<body>

<?php include 'utilities/user_header.php'; ?>

<div class="heading">
   <h3>Contacto</h3>
   <p><a href="home.php">Inicio</a> <span> / Contacto</span></p>
</div>

<section class="contact">
   <div class="row">
      <form action="" method="post">
         <h3>Comunícate con nostros</h3>
         <input type="text" name="name" maxlength="50" class="box" placeholder="Nombre" required>
         <input type="number" name="number" min="0" max="999999999" class="box" placeholder="Número de teléfono" required maxlength="9">
         <input type="email" name="email" maxlength="50" class="box" placeholder="Email" required>
         <textarea name="msg" class="box" required placeholder="Mensaje" maxlength="500" cols="30" rows="10"></textarea>
         <input type="submit" value="Enviar mensaje" name="send" class="btn">
      </form>
   </div>
</section>

<?php include 'utilities/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>