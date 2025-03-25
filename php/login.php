<?php
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
  include('loggedin-user.php'); 
  include('connection.php');

  $login_error = "";

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $email = trim($_POST["email"]);
      $password = $_POST["password"];

      $stmt = $connect->prepare("SELECT user_id, password FROM users WHERE email = ?");
      $stmt->bind_param("s", $email);
      $stmt->execute();
      $stmt->store_result();
      
      if ($stmt->num_rows > 0) {
          $stmt->bind_result($user_id, $hashed_password);
          $stmt->fetch();

          if (password_verify($password, $hashed_password)) {
              $_SESSION['user_id'] = $user_id;
              header("Location: index.php");
              exit();
          } else {
              $login_error = "Invalid password.";
          }
      } else {
          $login_error = "User not found.";
      }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | Walmart</title>
  <link rel="stylesheet" href="../css/index.css" />
  <link rel="stylesheet" href="../css/login.css" />
</head>
<body>
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <img src="../assets/logo.png" alt="Walmart Logo" class="logo" />
        <h2>Login</h2>
      </div>

      <?php if ($login_error): ?>
        <div class="error-message"><?php echo $login_error; ?></div>
      <?php endif; ?>

      <form action="login.php" method="POST">
        <label>Email / Phone</label>
        <input type="text" name="email" required />

        <label>Password</label>
        <input type="password" name="password" required />

        <div class="forgot-password"><a href="#">Forgot Password?</a></div>

        <button type="submit" class="login-button">Login</button>
      </form>
      <div class="signup-link">Don't have an account? <a href="signup.php">Sign Up</a></div>
    </div>
  </div>
</body>
</html>
