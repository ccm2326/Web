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
   $address = $_POST['calle'] .' '.$_POST['numero'].' '.$_POST['distrito'].' '.$_POST['provincia'] .', '. $_POST['departamento'];

   $update_address = $conn->prepare("UPDATE `users` set address = ? WHERE id = ?");
   $update_address->execute([$address, $user_id]);

   $message[] = 'Dirección guardada correctamente';

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Actualizar dirección</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<?php include 'utilities/user_header.php' ?>

<section class="form-container">
   <form action="" method="post">
      <h3>Mi dirección</h3>
      <input type="text" class="box" placeholder="Departamento" required max length="50" name="departamento">
      <input type="text" class="box" placeholder="Provincia" required max length="50" name="provincia">
      <input type="text" class="box" placeholder="Distrito" required max length="50" name="distrito">
      <input type="text" class="box" placeholder="Calle" required max length="50" name="calle">
      <input type="text" class="box" placeholder="Número" required max length="10" name="numero">
      <input type="submit" value="Guardar" name="submit" class="btn">
   </form>
</section>

<?php include 'utilities/footer.php' ?>

<script src="js/script.js"></script>

</body>
</html>