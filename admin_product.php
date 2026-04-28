<?php
include 'connection.php';
session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
   header('location:login.php');
}
if (isset($_POST['logout'])) {
   session_destroy();
   header('location:login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css"
    />
    <link rel="stylesheet" type="text/css" href="style.css" />
  <title>products</title>
</head>
<body>
  <?php include 'admin_header.php'; ?>
  <?php
if (isset($message)) {
   foreach ($message as $msg) {
      echo '<div class="message">
      <span>'.$msg.'</span>
      <i class="bi bi-x-circle" onclick="this.parentElement.remove()"></i>
      </div>';
   }}
   /* adding products to flowershop_db */
   if (isset($_POST['add_product'])) {
      $product_name = mysqli_real_escape_string($conn, $_POST['name']);
      $product_price = mysqli_real_escape_string($conn, $_POST['price']);
      $product_detail = mysqli_real_escape_string($conn, $_POST['detail']);
      $image = $_FILES['image']['name'];
      $image_size = $_FILES['image']['size'];
      $image_tmp_name = $_FILES['image']['tmp_name'];
      $image_folder = 'image/'.$image;
      $select_product_name = mysqli_query($conn, "SELECT name FROM `products` WHERE name = '$product_name'") or die('query failed');
      if (mysqli_num_rows($select_product_name) > 0) {
         $message[] = 'product name already exists!';
      } elseif ($image_size > 2000000) {
         $message[] = 'image size is too large!';
      } else {
         $insert_product = mysqli_query($conn, "INSERT INTO `products`(name, price, product_detail, image) VALUES('$product_name', '$product_price', '$product_detail', '$image')") or die('query failed');
         if ($insert_product) {
          if ($image_size > 2000000) {
            $message[] = 'image size is too large!';
         } else {
            move_uploaded_file($image_tmp_name, $image_folder);
            $message[] = 'product added successfully!';
         }
      }
   }


}
?>
  <section class="add-products">
<form method="post" action="" enctype="multipart/form-data">
      <h1 class="title">add a new product</h1>
    <div class="input-field">
      <label>product name</label>
      <input type="text" name="name" required>
    </div>
    <div class="input-field">
      <label>product price</label>
      <input type="text" name="price" min="0" required>
    </div>
        <div class="input-field">
      <label>product detail</label>
      <textarea name="detail" required></textarea>
    </div>
    <div class="input-field">
      <label>product image</label>
      <input type="file" name="image" accept="image/jpg, image/jpeg, image/png image/webp" required>
    </div>
    <input type="submit" value="add product" name="add_product" class="btn">
  </form>
</section>

</body>
</html>