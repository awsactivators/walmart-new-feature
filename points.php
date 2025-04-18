<?php
  include('auth.php'); 
  include('connection.php');

  $user_id = $_SESSION['user_id'] ?? 1;

  // Get user points
  $stmt = $connect->prepare("SELECT point FROM users WHERE user_id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $stmt->bind_result($user_points);
  $stmt->fetch();
  $stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="./css/index.css">
  <link rel="stylesheet" href="./css/points.css">

  <title>My Points</title>
</head>
<body>
  <header>
    <nav>
      <section class="top-nav">
        <a href=""><img src="./assets/Back.png" alt="Back arrow"></a>
        <h1>My Points</h1>
      </section>
      <section class="location-bar">
        <p>How do you want your items? | <span class="postal-code">L4Y 1N6</span></p>
        <i class="fa-solid fa-caret-down"></i>
      </section>
    </nav>
  </header>

  <main>
    <!-- Points Balance Section -->
    <section class="points-balance">
      <h2><?php echo $user_points; ?></h2>
      <p>Points Balance</p>
    </section>



    <!-- Membership ID & History -->
    <section class="membership">
      <div class="membership-row">
        <p>Your Membership ID</p>
        <a href="./comeback.php">View</a>
      </div>
      <div class="membership-row">
        <p>Points History</p>
        <a href="./comeback.php">View</a>
      </div>
    </section>

    <!-- How it works -->
     <section class="how-it-works">
      <h2>How it works</h2>
      <div class="how-it-works-grid">
        <div>
          <img src="./assets/add-to-cart.png" alt="Add to Cart">
          <p>Buy grocery from clearance</p>
        </div>

        <div>
          <img src="./assets/order-complete.png" alt="Order Complete">
          <p>Checkout your order</p>
        </div>

        <div>
          <img src="./assets/Cashback.png" alt="Cashback">
          <span>Hooray!!</span>
          <p>Earn Points</p>
        </div>
      </div>
     </section>

    <!-- Offers Section -->
    <section class="offers">
      <h3>Earn Points</h3>
      <div class="offer-card">
        <p class="offer-title">Free Delivery</p>
        <p class="offer-detail">Thank you for downloading the Walmart App!</p>
        <div class="offer-actions">
          <a href="./clearance.php" class="blue-btn">Order Now</a>
          <img src="./assets/Gift.png" alt="Gift Box" id="gift-box">
        </div>
        
      </div>
      <div class="offer-card">
        <p class="offer-title">Earn 2x Points</p>
        <p class="offer-detail">Purchase 5 items to earn 2X Points</p>
        <div class="offer-actions">
          <a href="./clearance.php" class="blue-btn">Order Now</a>
          <img src="./assets/Gift.png" alt="Gift Box" id="gift-box">
        </div>
      </div>
      <div class="offer-card">
        <p class="offer-title">Earn 500 Points</p>
        <p class="offer-detail">500 Bonus Points on your first order</p>
        <div class="offer-actions">
          <a href="./clearance.php" class="blue-btn">Order Now</a>
          <img src="./assets/Gift.png" alt="Gift Box" id="gift-box">
        </div>
      </div>
    </section>

    <!-- Ways to Earn Section -->
    <section class="ways-to-earn">
      <h3>Ways to Earn</h3>
      <div class="offer-card">
        <p class="offer-title">Purchase Clearance Grocery</p>
        <p class="offer-detail">Earn points with every clearance grocery purchase</p>
        <div class="offer-actions">
          <a href="./clearance.php" class="blue-btn">Order Now</a>
          <img src="./assets/Gift.png" alt="Gift Box" id="gift-box">
        </div>
      </div>
      
      <div class="offer-card">
        <p class="offer-title">Refer a Friend</p>
        <p class="offer-detail">You get points on every successful referral</p>
        <div class="offer-actions">
          <a href="./clearance.php" class="blue-btn">Order Now</a>
          <img src="./assets/Gift.png" alt="Gift Box" id="gift-box">
        </div>
      </div>
    </section>
  </main>

  <footer class="bottom-nav">
    <div>
      <a href="./index.php"><i class="fa-solid fa-house"></i></a>
      <p>Home</p>
    </div>
    <div>
      <a href="./index.php"><i class="fa-solid fa-shapes"></i></a>
      <p>Categories</p>
    </div>
    <div>
      <a href="#" class="active"><i class="fa-solid fa-piggy-bank"></i></a>
      <p>My Points</p>
    </div>
    <div>
      <a href="./cart.php"><i class="fa-solid fa-cart-shopping"></i></a>
      <p>Cart</p>
    </div>
    <div>
      <a href="./profile.php"><i class="fa-solid fa-user"></i></a>
      <p>Profile</p>
    </div>
  </footer>
</body>
</html>

