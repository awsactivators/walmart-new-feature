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

    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      try {
          if (isset($_POST['fetch_cart'])) {
            $cart = $_SESSION['cart'] ?? [];
            $clearance_cart = $_SESSION['clearance_cart'] ?? [];
        
            $combined_cart = $cart + $clearance_cart; // Merge both (no worry if keys overlap)
        
            $cart_count = array_sum(array_column($cart, 'quantity')) + array_sum(array_column($clearance_cart, 'quantity'));
        
            echo json_encode([
                'cart' => $combined_cart,
                'cart_count' => $cart_count
            ]);
            exit;
          }
  
          if (isset($_POST['product_id'], $_POST['action'], $_POST['product_name'], $_POST['product_image'], $_POST['product_price'], $_POST['product_points'])) {
              $product_id = $_POST['product_id'];
              $action = $_POST['action'];
              $product_name = $_POST['product_name'];
              $product_image = $_POST['product_image'];
              $product_price = $_POST['product_price'];
              $product_points = $_POST['product_points'];
  
              if (!isset($_SESSION['cart'][$product_id])) {
                  $_SESSION['cart'][$product_id] = [
                      'quantity' => 0,
                      'name' => $product_name,
                      'image' => $product_image,
                      'price' => $product_price,
                      'points' => $product_points
                  ];
              }
  
              if ($action === 'increase') {
                  $_SESSION['cart'][$product_id]['quantity'] += 1;
              } elseif ($action === 'decrease' && isset($_SESSION['cart'][$product_id])) {
                  $_SESSION['cart'][$product_id]['quantity'] -= 1;
                  if ($_SESSION['cart'][$product_id]['quantity'] <= 0) {
                      unset($_SESSION['cart'][$product_id]);
                  }
              }
  
              $cart = $_SESSION['cart'] ?? [];
              $clearance_cart = $_SESSION['clearance_cart'] ?? [];

              $combined_cart = $cart + $clearance_cart;
              $cart_count = array_sum(array_column($cart, 'quantity')) + array_sum(array_column($clearance_cart, 'quantity'));

              echo json_encode([
                  'cart' => $combined_cart,
                  'cart_count' => $cart_count
              ]);

              exit;
          }
  
          // If no valid action was taken, return a default JSON response
          echo json_encode(["status" => "error", "message" => "Invalid request"]);
          exit;
  
      } catch (Exception $e) {
          echo json_encode(['error' => $e->getMessage()]);
          exit;
      }
  }
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
          <img src="./assets/walmart-logo.png" alt="Walmart Logo">
        </div>
        <div class="cart">
          <a href="./cart.php"><i class="fa-solid fa-cart-shopping"></i><span class="cart-count">0</span></a>
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
      <a href="./comeback.php">
        <button class="see-times">See times</button>
      </a>
    </section>
  
    <!-- Grocery Clearance Ad Card -->
    <section class="clearance-card">
      <img src="./assets/grocerybag.png" alt="Grocery Clearance">
      <div class="clearance-details">
        <div class="clearance-text">
          <h4>Grocery Clearance</h4>
          <p>Get Points when you purchase from Grocery Clearance</p>
        </div>
        <a href="./clearance.php">
          <button class="shop-now">Shop Now</button>
        </a>
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

    <!-- Points Detail -->
    <section class="points-banner">
      <div class="points-new">
        <h2>My Points</h2>
        <span>NEW</span>
      </div>
      <div class="points-details-link">
        <h2>Track your rewards and see how close you are to your next benefits</h2>
        <a href="./points.php">My Points</a>
      </div>
    </section>
  
    <!-- Groceries -->
    <?php
      // Load grocery data
      $grocery_data = json_decode(file_get_contents('./data/products.json'), true);

      // Get all cart item names (case-insensitive)
      $cart_names = [];
      foreach (['cart', 'clearance_cart'] as $cart_type) {
        if (!empty($_SESSION[$cart_type])) {
          foreach ($_SESSION[$cart_type] as $item) {
            $cart_names[] = strtolower($item['name'] ?? $item['product_name']);
          }
        }
      }

      // Remove items already in the cart
      $filtered_groceries = array_filter($grocery_data, function ($item) use ($cart_names) {
        return !in_array(strtolower($item['name']), $cart_names);
      });

      // Pick exactly 2 random grocery items
      $random_items = [];
      if (count($filtered_groceries) >= 2) {
        $keys = array_rand($filtered_groceries, 2);
        foreach ((array)$keys as $key) {
          $random_items[] = $filtered_groceries[$key];
        }
      }
    ?>

    <section class="groceries">
      <h3>Groceries <span class="item-count">(<?php echo count($grocery_data); ?>)</span></h3>
      <div class="products">
        <?php foreach ($random_items as $item): ?>
          <div class="product">
            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
            <div class="product-name">
              <p>
                <a href="product-detail.php?id=<?php echo $item['id']; ?>&type=grocery" style="text-decoration: none;">
                  <?php echo htmlspecialchars($item['name']); ?>
                </a>
              </p>
            </div>
            <div class="price-container">
              <div class="price-col">
                <p class="price">$<?php echo number_format($item['price'], 2); ?></p>
                <p class="price-per-unit"><?php echo htmlspecialchars($item['price_per_unit']); ?></p>
              </div>
              <div id="cart-btn-<?php echo $item['id']; ?>">
                <button 
                  class="add-to-cart" 
                  onclick="updateCart(<?php echo $item['id']; ?>, 'increase')"
                  data-name="<?php echo htmlspecialchars($item['name']); ?>"
                  data-image="<?php echo $item['image']; ?>"
                  data-price="<?php echo $item['price']; ?>"
                >+</button>
              </div>
            </div>

          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </main>
  

  <footer class="bottom-nav">
    <div>
      <a href="#" class="active"><i class="fa-solid fa-house"></i></a>
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

  <script src="./js/index.js"></script>
</body>
</html>
