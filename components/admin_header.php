<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Panel</title>
   <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css"> <!-- Asegúrate de incluir Font Awesome -->
   <link rel="stylesheet" href="styles.css"> <!-- Asegúrate de incluir tu CSS -->
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
               <a href="dashboard.php">home</a>
               <a href="products.php">products</a>
               <a href="placed_orders.php">orders</a>
               <a href="admin_accounts.php">admins</a>
               <a href="users_accounts.php">users</a>
               <a href="messages.php">messages</a>
            </nav>

            <div class="icons">
               <div id="menu-btn" class="fas fa-bars"></div>
               <div id="user-btn" class="fas fa-user"></div>
            </div>

            <div class="profile">
               <?php
                $select_profile = $conn->prepare("SELECT * FROM `admin` WHERE id = ?");
               $select_profile->execute([$admin_id]);
               $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
               ?>
               <p><?= $fetch_profile['name']; ?></p>
               <a href="update_profile.php" class="btn">update profile</a>
               <div class="flex-btn">
                  <a href="admin_login.php" class="option-btn">login</a>
                  <a href="register_admin.php" class="option-btn">register</a>
               </div>
               <a href="../components/admin_logout.php" onclick="return confirm('logout from this website?');" class="delete-btn">logout</a>
            </div>

      </section>
   </header>

   <!-- Contenido Principal -->
   <div class="main-content">
      <!-- Aquí va el contenido principal de tu página -->
      <h1>Welcome to the Admin Panel</h1>
      <!-- Más contenido aquí -->
   </div>

</body>
</html>