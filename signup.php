<?php
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
  include('loggedin-user.php'); 
  include('connection.php');

  $errors = [];

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $email = trim($_POST["email"]);
      $phone = trim($_POST["phone"]);
      $password = $_POST["password"];
      $confirm = $_POST["confirm-password"];

      if ($password !== $confirm) {
          $errors[] = "Passwords do not match.";
      }

      if (empty($errors)) {
          // Hash password
          $hashed = password_hash($password, PASSWORD_DEFAULT);

          // Insert user
          $stmt = $connect->prepare("INSERT INTO users (`email`, `phone`, `password`) VALUES (?, ?, ?)");
          $stmt->bind_param("sss", $email, $phone, $hashed);

          if ($stmt->execute()) {
              $_SESSION['user_id'] = $stmt->insert_id;
              header("Location: address.php");
              exit();
          } else {
              $errors[] = "Email already exists or error occurred.";
          }
      }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Signup | Walmart</title>
  <link rel="stylesheet" href="./css/index.css" />
  <link rel="stylesheet" href="./css/login.css" />
</head>
<body>
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <img src="./assets/logo.png" alt="Walmart Logo" class="logo" />
        <h2>Signup</h2>
      </div>

      <?php if (!empty($errors)): ?>
        <div class="error-message"><?php echo implode("<br>", $errors); ?></div>
      <?php endif; ?>

      <form action="signup.php" method="POST">
        <label for="email">Email</label>
        <input type="text" name="email" required />

        <label for="phone">Phone Number</label>
        <input type="text" name="phone" required />

        <label for="password">Password</label>
        <input type="password" name="password" required />

        <label for="confirm-password">Confirm Password</label>
        <input type="password" name="confirm-password" required />

        <button type="submit" class="login-button">Signup</button>
      </form>
      <div class="signup-link">Already have an account? <a href="login.php">Login</a></div>
    </div>
  </div>
</body>
</html>
