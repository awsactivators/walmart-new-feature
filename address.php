<?php
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
  include('loggedin-user.php'); 
  include('connection.php');

  if (!isset($_SESSION['user_id'])) {
      header("Location: signup.php");
      exit();
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $user_id = $_SESSION['user_id'];
      $first = $_POST["first-name"];
      $last = $_POST["last-name"];
      $street = $_POST["street-address"];
      $apt = $_POST["apt"];
      $province = $_POST["province"];
      $postal = $_POST["postal-code"];

      $stmt = $connect->prepare("INSERT INTO addresses 
          (user_id, first_name, last_name, street_address, apt_suite, province, postal_code) 
          VALUES (?, ?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("issssss", $user_id, $first, $last, $street, $apt, $province, $postal);
      
      if ($stmt->execute()) {
          session_destroy(); 
          header("Location: login.php");
          exit();
      } else {
          $error = "Failed to save address.";
      }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Address | Walmart</title>
  <link rel="stylesheet" href="./css/index.css" />
  <link rel="stylesheet" href="./css/login.css" />
</head>
<body>
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <img src="./assets/logo.png" alt="Walmart Logo" class="logo" />
        <h2>Address</h2>
      </div>

      <?php if (isset($error)): ?>
        <div class="error-message"><?php echo $error; ?></div>
      <?php endif; ?>

      <form action="address.php" method="POST">
        <label>First Name</label>
        <input type="text" name="first-name" required />

        <label>Last Name</label>
        <input type="text" name="last-name" required />

        <label>Street Address</label>
        <input type="text" name="street-address" required />

        <label>Apt, Suite (optional)</label>
        <input type="text" name="apt" />

        <label>Province</label>
        <input type="text" name="province" required />

        <label>Postal Code</label>
        <input type="text" name="postal-code" required />

        <button type="submit" class="login-button">Continue</button>
      </form>
    </div>
  </div>
</body>
</html>
