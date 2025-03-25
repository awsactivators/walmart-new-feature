<?php
    include('auth.php'); 
    include('connection.php'); 

    $user_id = $_SESSION['user_id'] ?? 1;

    $stmt = $connect->prepare("
      SELECT u.email, a.first_name, a.postal_code, a.street_address, a.province
      FROM users u
      LEFT JOIN addresses a ON u.user_id = a.user_id
      WHERE u.user_id = ?
      LIMIT 1
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($email, $first_name, $postal_code, $street_address, $province);
    $stmt->fetch();
    $stmt->close();

    // fallback values
    $first_name = $first_name ?: "User";
    $postal_code = $postal_code ?: "L4Y 1N6";
    $street_address = $street_address ?: "1234 Address st";
    $province = $province ?: "Toronto, ON";
    $full_address = "$street_address, $province, $postal_code";
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
  <link rel="stylesheet" href="../css/index.css">

  <title>Walmart Home</title>
</head>
<body>
  <header>
    <nav>
      <section class="top-nav">
      <div class="user-greeting">
        <h1>Hi, <span><?php echo htmlspecialchars($first_name); ?></span></h1>
      </div>

        <div class="store-title">
          <h2>Walmart</h2>
        </div>
        <div class="cart">
          <a href="#"><i class="fa-solid fa-cart-shopping"></i><span class="cart-count">5</span></a>
        </div>
      </section>

      <section class="search-bar">
        <div class="search-input">
          <i class="fa-solid fa-search"></i>
          <input type="text" placeholder="Search everything at Walmart">
          <i class="fa-solid fa-barcode"></i>
        </div>
        <div class="location">
          <p>How do you want your items? | <span class="postal-code"><?php echo htmlspecialchars($postal_code); ?></span></p>
          <i class="fa-solid fa-caret-down"></i>
        </div>
      </section>
    </nav>
  </header>

  <main>
    <section class="pickup-delivery">
      <p>Pickup and delivery</p>
      <button class="see-times">See times</button>
    </section>
  
    <!-- Grocery Clearance Ad Card -->
    <section class="clearance-card">
      <img src="../assets/grocerybag.png" alt="Grocery Clearance">
      <div class="clearance-details">
        <div class="clearance-text">
          <h4>Grocery Clearance</h4>
          <p>Get Points when you purchase from Grocery Clearance</p>
        </div>
        <button class="shop-now">Shop Now</button>
      </div>
    </section>
  
    <!-- Categories -->
    <section class="categories">
      <div class="category-item">
        <i class="fa-solid fa-cart-shopping"></i> Grocery
      </div>
      <div class="category-item">
        <i class="fa-solid fa-receipt"></i> Weekly Flyer
      </div>
      <div class="category-item">
        <i class="fa-solid fa-ticket"></i> Coupon Centre
      </div>
      <div class="category-item">
        <i class="fa-solid fa-leaf"></i> Made in Canada
      </div>
      <div class="category-item">
        <i class="fa-solid fa-tags"></i> Walmart Brands
      </div>
      <div class="category-item selected">
        <i class="fa-solid fa-percent"></i> <a href="clearance.php" style="text-decoration: none;">Clearance</a>
      </div>
    </section>
  
    <!-- Groceries -->
    <section class="groceries">
      <h3>Groceries <span class="item-count">(12.3k)</span></h3>
      <div class="products">
        <div class="product">
          <img src="../assets/orange.jpg" alt="Oranges">
          <div class="product-name">
            <p class=""><a href="product-detail.php" style="text-decoration: none;">Oranges</a></p>
          </div>
          <div class="price-container">
            <div class="price-col">
              <p class="price">$2.39</p>
              <p class="price-per-unit">11.9¢/oz</p>
            </div>
            <div>
              <button class="add-to-cart">+</button>
            </div>
          </div>
        </div>
        <div class="product">
          <img src="../assets/kiwi.jpg" alt="Kiwi">
          <div class="product-name">
            <p>Kiwi</p>
          </div>
          <div class="price-container">
            <div class="price-col">
              <p class="price">$3.19</p>
              <p class="price-per-unit">11.9¢/oz</p>
            </div>
            <div>
              <button class="add-to-cart">+</button>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  

  <footer class="bottom-nav">
    <div>
      <a href="#"><i class="fa-solid fa-house"></i></a>
      <p>Home</p>
    </div>
    <div>
      <a href="#"><i class="fa-solid fa-shapes"></i></a>
      <p>Categories</p>
    </div>
    <div>
      <a href="./points.php"><i class="fa-solid fa-piggy-bank"></i></a>
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
