<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
};

if(isset($_POST['add_product'])){

   $name = $_POST['name'];
   $price = $_POST['price'];
   $category = $_POST['category'];

   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = '../uploaded_img/'.$image;

   $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
   $select_products->execute([$name]);

   if($select_products->rowCount() > 0){
      $message[] = '¡el nombre del producto ya existe!';
   }else{
      if($image_size > 2000000){
         $message[] = 'eL tamaño de la imagen es demasiado grande';
      }else{
         move_uploaded_file($image_tmp_name, $image_folder);

         $insert_product = $conn->prepare("INSERT INTO `products`(name, category, price, image) VALUES(?,?,?,?)");
         $insert_product->execute([$name, $category, $price, $image]);

         $message[] = '¡nuevo producto añadido!';
      }

   }

}

if(isset($_POST['update'])) {
   $pid = $_POST['pid'];
   $pid = htmlspecialchars($pid);
   
   // Validación y sanitización del nombre del producto
   $name = $_POST['name'];
   $name = htmlspecialchars($name); 

   // Validación y sanitización del precio
   $price = $_POST['price'];
   $price = filter_var($price, FILTER_VALIDATE_FLOAT);

   // Validación y sanitización de la categoría
   $category = $_POST['category'];
   $category = htmlspecialchars($category);

   if($price === false) {
      $message[] = '¡Precio no válido!';
   } else {
      // Actualización del producto en la base de datos
      $update_product = $conn->prepare("UPDATE `products` SET name = ?, category = ?, price = ? WHERE id = ?");
      $update_product->execute([$name, $category, $price, $pid]);

      $message[] = '¡Producto actualizado!';

      // Manejo de la imagen antigua y nueva
      $old_image = $_POST['old_image'];
      $image = $_FILES['image']['name'];
      $image = htmlspecialchars($image);  // Sanitización del nombre del archivo de imagen
      $image_size = $_FILES['image']['size'];
      $image_tmp_name = $_FILES['image']['tmp_name'];
      $image_folder = '../uploaded_img/'.$image;

      if(!empty($image)) {
         if($image_size > 2000000) {
            $message[] = '¡El tamaño de la imagen es demasiado grande!';
         } else {
            // Actualización de la nueva imagen en la base de datos
            $update_image = $conn->prepare("UPDATE `products` SET image = ? WHERE id = ?");
            $update_image->execute([$image, $pid]);
            
            // Movemos el nuevo archivo de imagen a la carpeta de destino
            move_uploaded_file($image_tmp_name, $image_folder);

            // Verificamos si la imagen antigua existe antes de eliminarla
            $old_image_path = '../uploaded_img/'.$old_image;
            if (file_exists($old_image_path)) {
               unlink($old_image_path);  // Eliminamos la imagen antigua solo si existe
               $message[] = '¡Imagen actualizada y la imagen antigua eliminada!';
            } else {
               $message[] = '¡Imagen actualizada, pero no se encontró la imagen antigua para eliminar!';
            }
         }
      }
   }
}

if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   $delete_product_image = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
   $delete_product_image->execute([$delete_id]);
   $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_img/'.$fetch_delete_image['image']);
   $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
   $delete_product->execute([$delete_id]);
   $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
   $delete_cart->execute([$delete_id]);
   header('location:products.php');

}

?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>productos</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body class="main-content">

<?php include '../components/admin_header.php' ?>

<!-- add products section starts  -->

<!-- <section class="add-products"> -->
<!-- 
<section class="">
   <form action="" method="POST" enctype="multipart/form-data">
      <h3>Añadir producto</h3>
      <input type="text" required placeholder="introduzca el nombre del producto" name="name" maxlength="100" class="box">
      <input type="number" min="0" max="9999999999" required placeholder="introduzca el precio del producto" name="price" onkeypress="if(this.value.length == 10) return false;" class="box">
      <select name="category" class="box" required>
         <option value="" disabled selected>seleccione categoría --</option>
         <option value="mujer">mujer</option>
         <option value="hombre">hombre</option>
         <option value="niño">niño</option>
         <option value="bebes">bebes</option>
      </select>
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp" required>
      <input type="submit" value="añadir producto" name="add_product" class="btn">
   </form>

</section> -->

<!-- add products section ends -->

<!-- show products section starts  -->

<!-- <section class="">
   <h3>Añadir producto</h3>

</section> -->
<div class="modal modal-add hidden">
   <form action="" method="POST" enctype="multipart/form-data">
      <h3>Añadir producto</h3>
      <input type="text" required placeholder="introduzca el nombre del producto" name="name" maxlength="100" class="box">
      <input type="number" min="0" max="9999999999" required placeholder="introduzca el precio del producto" name="price" onkeypress="if(this.value.length == 10) return false;" class="box">
      <select name="category" class="box" required>
         <option value="" disabled selected>seleccione categoría --</option>
         <option value="mujer">mujer</option>
         <option value="hombre">hombre</option>
         <option value="niño">niño</option>
         <option value="bebes">bebes</option>
      </select>
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp" required>
      <div style="display: flex; justify-content: center; gap: 20px">
         <input type="submit" value="añadir producto" name="add_product" class="btn">
         <button class="delete-btn  btn-close">Cancelar</button>
      </div>
   </form>
</div>
<!-- Modal para editar producto -->
<div class="modal modal-edit hidden" style="top: 10%">
   <form id="edit-form" method="POST" enctype="multipart/form-data">
      <h3>Editar producto</h3>
      <input type="hidden" name="pid" id="edit_pid">
      <input type="hidden" name="old_image" id="edit_old_image">
      <img id="edit_image_preview" src="" alt="" style="width: 30%">
      <input type="text" required placeholder="Introduzca el nombre del producto" name="name" id="edit_name" maxlength="100" class="box">
      <span>Actualizar precio</span>
      <input type="number" min="0" max="9999999999" required placeholder="Introduzca el precio del producto" name="price" id="edit_price" onkeypress="if(this.value.length == 10) return false;" class="box">
      <span>Actualizar categoría</span>
      <select name="category" id="edit_category" class="box" required>
         <option value="hombre">Hombre</option>
         <option value="mujer">Mujer</option>
         <option value="niño">Niño</option>
         <option value="bebes">Bebés</option>
      </select>
      <span>Actualizar imagen</span>
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp">
      <div class="flex-btn">
         <input type="submit" value="Actualizar" class="btn" name="update">
         <button type="button" class="delete-btn btn-close-edit">Cancelar</button>
      </div>
   </form>
</div>

<div class="overlay hidden"></div>
<div class="">
   <h1 class="heading">Productos</h1>
   <button class="btn btn-open">Añadir Producto</button>
</div>

<section class="show-products" style="padding-top: 3em;">

   <div class="box-container">
      <?php
         $show_products = $conn->prepare("SELECT * FROM `products`");
         $show_products->execute();
         if($show_products->rowCount() > 0){
            while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
      ?>
      <div class="box">
         <img src="../uploaded_img/<?= $fetch_products['image']; ?>" alt="">
         <div class="flex">
            <div class="price"><span>Precio: $</span><?= $fetch_products['price']; ?><span>/-</span></div>
            <div class="category">Categoría: <?= $fetch_products['category']; ?></div>
            <div class="name">Producto: <?= $fetch_products['name']; ?></div>
         </div>
         <div class="flex-btn">
            <a href="#" class="option-btn" 
               data-id="<?= $fetch_products['id']; ?>" 
               data-name="<?= $fetch_products['name']; ?>" 
               data-price="<?= $fetch_products['price']; ?>" 
               data-category="<?= $fetch_products['category']; ?>" 
               data-image="<?= $fetch_products['image']; ?>">
               Editar
            </a>
            <a href="products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('¿Eliminar este producto?');">Borrar</a>
         </div>
      </div>
      <?php
            }
         }else{
            echo '<p class="empty">Aún no se han agregado productos</p>';
         }
      ?>
   </div>

</section>

<!-- show products section ends -->
<!-- custom js file link  -->

<script>
   const modal = document.querySelector(".modal-add");
   const overlay = document.querySelector(".overlay");
   const openModalBtn = document.querySelector(".btn-open");
   const closeModalBtn = document.querySelector(".btn-close");
   const openModal = function () {
      modal.classList.remove("hidden");
      overlay.classList.remove("hidden");
   };
   openModalBtn.addEventListener("click", openModal);
   const closeModal = function () {
      modal.classList.add("hidden");
      overlay.classList.add("hidden");
   };
   closeModalBtn.addEventListener("click", closeModal);
   overlay.addEventListener("click", closeModal);

   // EDIT MODAL
   const editModal = document.querySelector(".modal-edit");
   const editOverlay = document.querySelector(".overlay");
   const editButtons = document.querySelectorAll(".option-btn"); // Botones de "editar"

   // Función para abrir el modal de edición con los datos del producto
   const openEditModal = (id, name, price, category, image) => {
      document.getElementById("edit_pid").value = id;
      document.getElementById("edit_name").value = name;
      document.getElementById("edit_price").value = price;
      document.getElementById("edit_category").value = category;
      document.getElementById("edit_old_image").value = image;
      document.getElementById("edit_image_preview").src = '../uploaded_img/' + image;
      
      editModal.classList.remove("hidden");
      editOverlay.classList.remove("hidden");
   };

   // Asociar eventos a los botones de edición
   editButtons.forEach(button => {
      button.addEventListener("click", function(event) {
         event.preventDefault();

         // Obtener los datos del producto desde los atributos data-
         const productId = this.getAttribute("data-id");
         const productName = this.getAttribute("data-name");
         const productPrice = this.getAttribute("data-price");
         const productCategory = this.getAttribute("data-category");
         const productImage = this.getAttribute("data-image");

         openEditModal(productId, productName, productPrice, productCategory, productImage);
      });
   });

   // Cerrar el modal de edición
   const closeEditModal = function () {
      editModal.classList.add("hidden");
      editOverlay.classList.add("hidden");
   };
   document.querySelector(".btn-close-edit").addEventListener("click", closeEditModal);
   editOverlay.addEventListener("click", closeEditModal);

</script>

<script src="../js/admin_script.js"></script>

</body>
</html>