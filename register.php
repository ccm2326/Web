<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

if(isset($_POST['submit'])){

   $name = $_POST['name'];
   $email = $_POST['email'];
   $number = $_POST['number'];
   $pass = sha1($_POST['pass']);
   $cpass = sha1($_POST['cpass']);

   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? OR number = ?");
   $select_user->execute([$email, $number]);
   $row = $select_user->fetch(PDO::FETCH_ASSOC);

   if($select_user->rowCount() > 0){
      $message[] = 'El correo o número ya está siendo utilizado';
   }else{
      if($pass != $cpass){
         $message[] = 'Las contraseñas no coinciden';
      }else{
         $insert_user = $conn->prepare("INSERT INTO `users`(name, email, number, password) VALUES(?,?,?,?)");
         $insert_user->execute([$name, $email, $number, $cpass]);
         $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
         $select_user->execute([$email, $pass]);
         $row = $select_user->fetch(PDO::FETCH_ASSOC);
         if($select_user->rowCount() > 0){ // Verificar si se agregó el usuario correctamente
            $_SESSION['user_id'] = $row['id'];
            header('location:home.php'); // Redirigir
         }
      }
   }

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Registro</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- Cabecera -->
<?php include 'components/user_header.php'; ?>

<!-- Formulario registro -->
<section class="form-container">
   <form action="" method="post">
      <h3>Registrate</h3>
      <input type="text" name="name" required placeholder="Ingresa tu nombre" class="box" maxlength="50">
      <input type="email" name="email" required placeholder="Ingresa tu email" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')"> // Elimina espacios en blanco
      <input type="number" name="number" required placeholder="Ingresa tu número" class="box" min="0" max="999999999" maxlength="9">
      <input type="password" name="pass" required placeholder="Ingresa tu contraseña" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="cpass" required placeholder="Confirma tu contraseña" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="Registrar" name="submit" class="btn">
      <p>¿ya tienes una cuenta? <a href="login.php">Inicia sesión</a></p>
   </form>
</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>