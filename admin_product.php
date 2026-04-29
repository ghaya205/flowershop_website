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
  <?php include 'admin_header.php'; ?>
 <?php

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
/* update products */
if (isset($_POST['update_product'])) {
    $update_id     = mysqli_real_escape_string($conn, $_POST['update_p_id']);
    $update_name   = mysqli_real_escape_string($conn, $_POST['update_name']);
    $update_price  = mysqli_real_escape_string($conn, $_POST['update_price']);
    $update_detail = mysqli_real_escape_string($conn, $_POST['update_p_detail']);
 
    
    if (!empty($_FILES['update_p_image']['name'])) {
        $new_image          = $_FILES['update_p_image']['name'];
        $new_image_size     = $_FILES['update_p_image']['size'];
        $new_image_tmp_name = $_FILES['update_p_image']['tmp_name'];
 
        if ($new_image_size > 2000000) {
            $message[] = 'Image size is too large! (max 2MB)';
        } else {
            
            $old_image_query = mysqli_query($conn, "SELECT image FROM `products` WHERE id = '$update_id'") or die('query failed');
            $old_image_row   = mysqli_fetch_assoc($old_image_query);
            if ($old_image_row && file_exists('image/' . $old_image_row['image'])) {
                unlink('image/' . $old_image_row['image']);
            }
 
            move_uploaded_file($new_image_tmp_name, 'image/' . $new_image);
 
            mysqli_query($conn, "UPDATE `products`
                SET name='$update_name', price='$update_price',
                    product_detail='$update_detail', image='$new_image'
                WHERE id='$update_id'") or die('query failed');
            $message[] = 'Product updated successfully!';
        }
    } else {
        
        mysqli_query($conn, "UPDATE `products`
            SET name='$update_name', price='$update_price',
                product_detail='$update_detail'
            WHERE id='$update_id'") or die('query failed');
        $message[] = 'Product updated successfully!';
    }
 
    
    header('location:admin_product.php');
    exit;
}
/* delete products from flowershop_db */
if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $select_delete_image = mysqli_query($conn, "SELECT image FROM `products` WHERE id = '$delete_id'") or die('query failed');

    $fetch_delete_image = mysqli_fetch_assoc($select_delete_image);
    unlink('image/'.$fetch_delete_image['image']);
    mysqli_query($conn, "DELETE FROM `products` WHERE id = '$delete_id'") or die('query failed');
    mysqli_query($conn, "DELETE FROM `cart` WHERE pid = '$delete_id'") or die('query failed');
    mysqli_query($conn, "DELETE FROM `wishlist` WHERE pid = '$delete_id'") or die('query failed');

   
}

/*  display messages  */
if (isset($message)) {
   foreach ($message as $msg) {
      echo '<div class="message">
      <span>'.$msg.'</span>
      <i class="bi bi-x-circle" onclick="this.parentElement.remove()"></i>
      </div>';
   }
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


<!--show products-->
<section class="show-products">
    <h1 class="title">Products</h1>
    <div class="box-container">
        <?php
        $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
        if (mysqli_num_rows($select_products) > 0) {
            while ($fetch_product = mysqli_fetch_assoc($select_products)) {
        ?>
        <div class="box">
            <img src="image/<?php echo $fetch_product['image']; ?>" alt="">
            <p class="price">Price: <?php echo $fetch_product['price']; ?> dt</p>
            <h4><?php echo $fetch_product['name']; ?></h4>
            <p class="detail"><?php echo $fetch_product['product_detail']; ?></p>
            
            <a href="admin_product.php?edit=<?php echo $fetch_product['id']; ?>" class="edit">Edit</a>
            <a href="admin_product.php?delete=<?php echo $fetch_product['id']; ?>" class="delete"
               onclick="return confirm('Delete this product?');">Delete</a>
        </div>
        <?php
            }
        }
        ?>
    </div>
</section>
<section class="update-container" style="display:none;">
    <?php
    if (isset($_GET['edit'])) {
        $edit_id    = mysqli_real_escape_string($conn, $_GET['edit']);
        $edit_query = mysqli_query($conn, "SELECT * FROM `products` WHERE id = '$edit_id'") or die('query failed');
 
        if (mysqli_num_rows($edit_query) > 0) {
            $fetch_edit = mysqli_fetch_assoc($edit_query);
    ?>
    <form method="post" action="" enctype="multipart/form-data">
        <img src="image/<?php echo $fetch_edit['image']; ?>" alt="" style="max-width:150px;">
 
        <input type="hidden" name="update_p_id" value="<?php echo $fetch_edit['id']; ?>">
 
        
        <input type="text"   name="update_name"     value="<?php echo $fetch_edit['name']; ?>" required>
        <input type="number" name="update_price" min="0" step="0.01" value="<?php echo $fetch_edit['price']; ?>" required>
        <textarea name="update_p_detail" required><?php echo $fetch_edit['product_detail']; ?></textarea>
        <input type="file"   name="update_p_image"  accept="image/jpg, image/jpeg, image/png, image/webp">
        <small>Leave image blank to keep the current one.</small>
 
        <input type="submit" value="Update"  name="update_product" class="edit">
        <a href="admin_product.php" class="option-btn btn">Cancel</a>
    </form>
    <?php
        }
        echo "<script>document.querySelector('.update-container').style.display='block';</script>";
    }
    ?>
</section>
 
<script type="text/javascript" src="script.js"></script>
</body>
</html>
 