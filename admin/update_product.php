<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
   exit; // Ensure no further execution
}

// Initialize $message array to store messages
$message = [];

if (isset($_POST['update'])) {

   // Fetch POST data and sanitize inputs
   $pid = $_POST['pid'];
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $price = filter_var($_POST['price'], FILTER_SANITIZE_STRING);
   $details = filter_var($_POST['details'], FILTER_SANITIZE_STRING);

   // Update product details
   $update_product = $conn->prepare("UPDATE `products` SET name = ?, price = ?, details = ? WHERE id = ?");
   $update_product->execute([$name, $price, $details, $pid]);

   // Add success message
   $message[] = 'Product updated successfully!';

   // Process image updates
   foreach (['image_01', 'image_02', 'image_03'] as $imageField) {
      $old_image = $_POST['old_' . $imageField];
      $image_name = $_FILES[$imageField]['name'];
      $image_size = $_FILES[$imageField]['size'];
      $image_tmp_name = $_FILES[$imageField]['tmp_name'];
      $image_folder = '../uploaded_img/' . $image_name;

      if (!empty($image_name)) {
         if ($image_size > 2000000) {
            $message[] = 'Image size is too large!';
         } else {
            // Update image in database
            $update_image = $conn->prepare("UPDATE `products` SET $imageField = ? WHERE id = ?");
            $update_image->execute([$image_name, $pid]);

            // Move uploaded file
            move_uploaded_file($image_tmp_name, $image_folder);

            // Remove old image
            unlink('../uploaded_img/' . $old_image);

            // Add success message
            $message[] = ucfirst($imageField) . ' updated successfully!';
         }
      }
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Product</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>

   <?php include '../components/admin_header.php'; ?>

   <section class="update-product">

      <h1 class="heading">Update Product</h1>

      <?php
      $update_id = $_GET['update'] ?? null; // Default to null if 'update' parameter is not set
      if ($update_id) {
         $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
         $select_products->execute([$update_id]);
         if ($select_products->rowCount() > 0) {
            $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC);
            ?>
            <form action="" method="post" enctype="multipart/form-data">
               <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
               <input type="hidden" name="old_image_01" value="<?= $fetch_products['image_01']; ?>">
               <input type="hidden" name="old_image_02" value="<?= $fetch_products['image_02']; ?>">
               <input type="hidden" name="old_image_03" value="<?= $fetch_products['image_03']; ?>">
               <div class="image-container">
                  <div class="main-image">
                     <img src="../uploaded_img/<?= $fetch_products['image_01']; ?>" alt="">
                  </div>
                  <div class="sub-image">
                     <img src="../uploaded_img/<?= $fetch_products['image_01']; ?>" alt="">
                     <img src="../uploaded_img/<?= $fetch_products['image_02']; ?>" alt="">
                     <img src="../uploaded_img/<?= $fetch_products['image_03']; ?>" alt="">
                  </div>
               </div>
               <span>Update Name</span>
               <input type="text" name="name" required class="box" maxlength="100" placeholder="Enter product name"
                  value="<?= $fetch_products['name']; ?>">
               <span>Update Price</span>
               <input type="number" name="price" required class="box" min="0" max="9999999999"
                  placeholder="Enter product price" value="<?= $fetch_products['price']; ?>">
               <span>Update Details</span>
               <textarea name="details" class="box" required cols="30" rows="10"><?= $fetch_products['details']; ?></textarea>
               <span>Update Image 01</span>
               <input type="file" name="image_01" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
               <span>Update Image 02</span>
               <input type="file" name="image_02" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
               <span>Update Image 03</span>
               <input type="file" name="image_03" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
               <div class="flex-btn">
                  <input type="submit" name="update" class="btn" value="Update">
                  <a href="products.php" class="option-btn">Go Back</a>
               </div>
            </form>

            <?php
         } else {
            echo '<p class="empty">No product found!</p>';
         }
      } else {
         echo '<p class="empty">Product ID is missing!</p>';
      }
      ?>

   </section>

   <script src="../js/admin_script.js"></script>

</body>

</html>