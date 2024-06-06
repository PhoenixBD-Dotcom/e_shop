<?php
if (isset($message)) {
   foreach ($message as $msg) {
      echo '
         <div class="message">
            <span>' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
   }
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
?>

<header class="header">

   <section class="flex">

      <a href="index.php" class="logo">E-Shop<span>.</span></a>

      <nav class="navbar">
         <a href="index.php">home</a>
         <a href="about.php">about</a>
         <a href="shop.php">shop</a>
         <a href="contact.php">contact</a>
      </nav>

      <div class="icons">
         <?php
         if (isset($conn) && $user_id) {
            // Count wishlist items
            $count_wishlist_items = $conn->prepare("SELECT COUNT(*) FROM `wishlist` WHERE user_id = ?");
            $count_wishlist_items->execute([$user_id]);
            $total_wishlist_counts = $count_wishlist_items->fetchColumn();

            // Count cart items
            $count_cart_items = $conn->prepare("SELECT COUNT(*) FROM `cart` WHERE user_id = ?");
            $count_cart_items->execute([$user_id]);
            $total_cart_counts = $count_cart_items->fetchColumn();
         } else {
            $total_wishlist_counts = 0;
            $total_cart_counts = 0;
         }
         ?>
         <div id="menu-btn" class="fas fa-bars"></div>
         <a href="search_page.php"><i class="fas fa-search"></i></a>
         <a href="wishlist.php"><i
               class="fas fa-heart"></i><span>(<?= htmlspecialchars($total_wishlist_counts, ENT_QUOTES, 'UTF-8'); ?>)</span></a>
         <a href="cart.php"><i
               class="fas fa-shopping-cart"></i><span>(<?= htmlspecialchars($total_cart_counts, ENT_QUOTES, 'UTF-8'); ?>)</span></a>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <div class="profile">
         <?php
         if (isset($conn) && $user_id) {
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$user_id]);
            if ($select_profile->rowCount() > 0) {
               $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
               ?>
               <p><?= htmlspecialchars($fetch_profile["name"], ENT_QUOTES, 'UTF-8'); ?></p>
               <a href="update_user.php" class="btn">update profile</a>
               <div class="flex-btn">
                  <a href="user_register.php" class="option-btn">register</a>
                  <a href="user_login.php" class="option-btn">login</a>
               </div>
               <a href="components/user_logout.php" class="delete-btn"
                  onclick="return confirm('logout from the website?');">logout</a>
               <?php
            } else {
               ?>
               <p>please login or register first!</p>
               <div class="flex-btn">
                  <a href="user_register.php" class="option-btn">register</a>
                  <a href="user_login.php" class="option-btn">login</a>
               </div>
               <?php
            }
         } else {
            ?>
            <p>please login or register first!</p>
            <div class="flex-btn">
               <a href="user_register.php" class="option-btn">register</a>
               <a href="user_login.php" class="option-btn">login</a>
            </div>
            <?php
         }
         ?>
      </div>

   </section>

</header>