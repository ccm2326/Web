<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Panel</title>
   <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css"> 
   <link rel="stylesheet" href="styles.css"> 
</head>
<body>
   <!-- Mensajes -->
   <?php
   if (isset($message)) {
      foreach ($message as $message) {
            echo '
            <div class="message">
               <span>' . $message . '</span>
               <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
            </div>
            ';
      }
   }
   ?>

   <!-- Encabezado -->
   <header class="header">
      <section class="flex">
            <a href="dashboard.php" class="logo">Admin<span>Panel</span></a>

      <nav class="navbar">
         <a href="dashboard.php">Inicio</a>
         <a href="products.php">Productos</a>
         <a href="placed_orders.php">Pedidos</a>
         <a href="admin_accounts.php">Administradores</a>
         <a href="users_accounts.php">Usuarios</a>
         <a href="messages.php">Mensajes</a>
      </nav>

            <div class="icons">
               <div id="collection-btn" class="fas fa-bars"></div>
               <div id="user-btn" class="fas fa-user"></div>
            </div>

            <div class="profile">
               <?php
                $select_profile = $conn->prepare("SELECT * FROM `admin` WHERE id = ?");
               $select_profile->execute([$admin_id]);
               $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
               ?>
               <p><?= $fetch_profile['name']; ?></p>
               <a href="update_profile.php" class="btn">Actualizar perfil</a>
               <div class="flex-btn">
                  <a href="admin_login.php" class="option-btn">Iniciar sesión</a>
                  <a href="register_admin.php" class="option-btn">Registrarse</a>
               </div>
               <a href="../utilities/admin_logout.php" onclick="return confirm('¿Desea cerrar sesión?');" class="delete-btn">Cerrar sesión</a>
            </div>

      </section>
   </header>

   <!-- Contenido Principal -->
   <div>
      <!-- Aquí va el contenido principal de tu página -->
      <!-- <h1>Welcome to the Admin Panel</h1> -->
      <!-- Más contenido aquí -->
   </div>

</body>
</html>