<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Sign up</title>
</head>
<body>
  <section class = "form-container">
    
    <form action="register.php" method="POST">
      <h3>Sign Up</h3>
      <input type="text" name="username" placeholder="Username" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
     <input type="submit" name="submit-btn" class ="btn" value="Sign Up">
      <p>Already have an account? <a href="login.php">Log in</a></p>
    </form>
  </section>
</body>
</html>