<?php
include('auth.php'); 

$earned = $_SESSION['earned_points'] ?? 0;
// $earned = $_GET['earned'] ?? 0;

if (isset($_GET['clear']) && $_GET['clear'] == 1) {
  unset($_SESSION['cart']);
  unset($_SESSION['clearance_cart']);
  header('Location: index.php');
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="./css/success.css" />
  <title>Order Success</title>
</head>
<body>
  <main class="success-page">
    <img src="./assets/complete-img.png" alt="Success Basket" class="success-img" />

    <h2>Order placed successfully!</h2>

    <p class="success-message">
      You’ve earned <span id="totalPoints"><strong><?php echo $earned; ?> Points</strong></span>
      for making a sustainable choice. Thanks for helping the planet!
    </p>

    <div class="success-actions">
      <div class="home-btn"><a href="success.php?clear=1">Home</a></div>
      <a href="./points.php" class="view-points-link">View Points</a>
    </div>
  </main>
</body>
</html>
