   <?php

   include '../components/connect.php';

   session_start();

   $admin_id = $_SESSION['admin_id'];

   if(!isset($admin_id)){
      header('location:admin_login.php');
   }

   if(isset($_GET['delete'])){
      $delete_id = $_GET['delete'];
      $delete_message = $conn->prepare("DELETE FROM `messages` WHERE id = ?");
      $delete_message->execute([$delete_id]);
      header('location:messages.php');
   }

   ?>

   <!DOCTYPE html>
   <html lang="es">
   <head>
      <meta charset="UTF-8">
      
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>mensajes</title>

      <!-- font awesome cdn link  -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

      <!-- custom css file link  -->
      <link rel="stylesheet" href="../css/admin_style.css">

   </head>
   <body class="main-content">

   <?php include '../components/admin_header.php' ?>

   <!-- messages section starts  -->

   <div class="container-2">

      <h1 class="heading">mensajes</h1>

      <div class="box-container">

      <?php
         $select_messages = $conn->prepare("SELECT * FROM `messages`");
         $select_messages->execute();
         if($select_messages->rowCount() > 0){
            while($fetch_messages = $select_messages->fetch(PDO::FETCH_ASSOC)){
      ?>
      <div class="box">
         <h3 style="font-size: 1.8em">Mensaje N° <?= $fetch_messages['id']; ?></h3>
         <p> Nombre : <span><?= $fetch_messages['name']; ?></span> </p>
         <p> Número : <span><?= $fetch_messages['number']; ?></span> </p>
         <p> Email : <span><?= $fetch_messages['email']; ?></span> </p>
         <p> Mensaje : <span><?= $fetch_messages['message']; ?></span> </p>
         <a href="messages.php?delete=<?= $fetch_messages['id']; ?>" class="delete-btn" onclick="return confirm('¿Borrar este mensaje?');">Borrar</a>
      </div>
      <?php
            }
         }else{
            echo '<p class="empty">No tienes mensajes</p>';
         }
      ?>

      </div>

      </div>

   <!-- messages section ends -->









   <!-- custom js file link  -->
   <script src="../js/admin_script.js"></script>

   </body>
   </html>