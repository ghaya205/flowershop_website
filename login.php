<?php
session_start();
include 'connection.php';

$message = [];

if (isset($_POST['submit-btn'])) {

   $filter_email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
   $email = mysqli_real_escape_string($conn, $filter_email);

   $filter_password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
   $password = mysqli_real_escape_string($conn, $filter_password);

   $select_user = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'") or die('query failed');

   if (mysqli_num_rows($select_user) > 0) {

      $row = mysqli_fetch_assoc($select_user);

      
      if ($row['password'] === $password) {

         if ($row['user_type'] === 'admin') {

            $_SESSION['admin_name'] = $row['name'];
            $_SESSION['admin_email'] = $row['email'];
            $_SESSION['admin_id'] = $row['id'];

            header('Location: admin.php');
            exit();

         } elseif ($row['user_type'] === 'user') {

            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_email'] = $row['email'];
            $_SESSION['user_id'] = $row['id'];

            header('Location: index.php');
            exit();

         }

      } else {
         $message[] = 'Incorrect email or password!';
      }

   } else {
      $message[] = 'Incorrect email or password!';
   }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
<link rel="stylesheet" type="text/css" href="style.css">
  <title>Sign up</title>
</head>
<body>

<section class="form-container">

<?php
if (isset($message)) {
   foreach ($message as $msg) {
      echo '<div class="message">
      <span>'.$msg.'</span>
      <i class="bi bi-x-circle" onclick="this.parentElement.remove()"></i>
      </div>';
   }
}
?>

<form action="" method="POST">
  <h3>Log In</h3>
  <input type="email" name="email" placeholder="Email" required>
  <input type="password" name="password" placeholder="Password" required>
  <input type="submit" name="submit-btn" class="btn" value="Log In">
  <p> Do not have an account? <a href="signup.php">Sign Up</a></p>
</form>

</section>

</body>
</html>