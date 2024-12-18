<?php

include 'utilities/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

include 'utilities/add_cart.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Inicio</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'utilities/user_header.php'; ?>

<section class="banner">
   <div class="banner-item">
      <div class="content">
         <span>Camisa de Lino</span>
         <h3>Edición Costa</h3>
         <a href="collection.php" class="btn">Ver colección</a>
      </div>
      <div class="image">
         <img src="https://hmperu.vtexassets.com/assets/vtex.file-manager-graphql/images/5720d11c-0e24-49a0-a705-500c8977b733___d868ae9a90eed8dbe78ac5a923a41780.webp" alt="">
      </div>
   </div>
</section>

<section class="category">

   <h1 class="title">Categorías</h1>

   <div class="box-container">

      <a href="category.php?category=mujer" class="box">
         <img src="https://hmperu.vtexassets.com/arquivos/ids/4221203-600-900?v=638419935270700000&width=600&height=900&aspect=true" alt="">
         <h3>Mujer</h3>
      </a>

      <a href="category.php?category=hombre" class="box">
         <img src="https://hmperu.vtexassets.com/arquivos/ids/4540315-294-450?v=638536955227770000&width=294&height=450&aspect=true" alt="">
         <h3>Hombre</h3>
      </a>

      <a href="category.php?category=bebes" class="box">
         <img src="https://hmperu.vtexassets.com/arquivos/ids/4473390-600-900?v=638507588853500000&width=600&height=900&aspect=true" alt="">
         <h3>Bebé</h3>
      </a>

      <a href="category.php?category=niño" class="box">
         <img src="https://hmperu.vtexassets.com/arquivos/ids/4237350-600-900?v=638420047141130000&width=600&height=900&aspect=true" alt="">
         <h3>Niños</h3>
      </a>

   </div>

</section>


<section class="products">

   <h1 class="title">Última temporada</h1>

   <div class="box-container">

      <?php
         $select_products = $conn->prepare("SELECT * FROM `products` ORDER BY `id` DESC LIMIT 4");
         $select_products->execute();
         if($select_products->rowCount() > 0){
            while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){
      ?>
      <form action="" method="post" class="box">
         <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
         <input type="hidden" name="name" value="<?= $fetch_products['name']; ?>">
         <input type="hidden" name="price" value="<?= $fetch_products['price']; ?>">
         <input type="hidden" name="image" value="<?= $fetch_products['image']; ?>">
         <a href="quick_view.php?pid=<?= $fetch_products['id']; ?>" class="fas fa-eye"></a>
         <button type="submit" class="fas fa-shopping-cart" name="add_to_cart"></button>
         <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
         <a href="category.php?category=<?= $fetch_products['category']; ?>" class="cat"><?= $fetch_products['category']; ?></a>
         <div class="name"><?= $fetch_products['name']; ?></div>
         <div class="flex">
            <div class="price"><span>S/.</span><?= $fetch_products['price']; ?></div>
            <input type="number" name="qty" class="qty" min="1" max="99" value="1" maxlength="2">
         </div>
      </form>
      <?php
            }
         }else{
            echo '<p class="empty">Aún no se han agregado productos</p>';
         }
      ?>
   </div>

   <div class="more-btn">
      <a href="collection.php" class="btn">Ver</a>
   </div>
</section>

<?php include 'utilities/footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>