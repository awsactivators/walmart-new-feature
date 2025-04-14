<?php
  include('auth.php'); 
  include('connection.php');

  if (!isset($_SESSION['clearance_cart'])) {
    $_SESSION['clearance_cart'] = [];
}


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

  if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
  }
  if (!isset($_SESSION['clearance_cart'])) {
      $_SESSION['clearance_cart'] = [];
  }

  function getTotalCartCount() {
      $cartCount = array_sum(array_column($_SESSION['cart'], 'quantity'));
      $clearanceCount = array_sum(array_column($_SESSION['clearance_cart'], 'quantity'));
      return $cartCount + $clearanceCount;
  }
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? null;
    $action = $_POST['action'] ?? null;
    $name = $_POST['name'] ?? '';
    $image = $_POST['image'] ?? '';
    $price = $_POST['price'] ?? 0;
    $price_per_unit = $_POST['price_per_unit'] ?? '';
    $points = $_POST['points'] ?? 0;

    if ($product_id && $action) {
        switch ($action) {
            case 'add':
                if (!isset($_SESSION['clearance_cart'][$product_id])) {
                    $_SESSION['clearance_cart'][$product_id] = [
                        'id' => $product_id,
                        'name' => $name,
                        'image' => $image,
                        'price' => $price,
                        'price_per_unit' => $price_per_unit,
                        'points' => $points,
                        'quantity' => 1
                    ];
                } else {
                    $_SESSION['clearance_cart'][$product_id]['quantity'] += 1;
                }
                break;

            case 'increment':
                if (isset($_SESSION['clearance_cart'][$product_id])) {
                    $_SESSION['clearance_cart'][$product_id]['quantity'] += 1;
                }
                break;

            case 'decrement':
                if (isset($_SESSION['clearance_cart'][$product_id])) {
                    $_SESSION['clearance_cart'][$product_id]['quantity'] -= 1;
                    if ($_SESSION['clearance_cart'][$product_id]['quantity'] <= 0) {
                        unset($_SESSION['clearance_cart'][$product_id]);
                    }
                }
                break;
        }
    }

    echo json_encode([
        'cart' => $_SESSION['clearance_cart'],
        'cart_count' => getTotalCartCount()
    ]);
    exit;
}

// Handle cart count request separately
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['getCartCount'])) {
    echo json_encode(['cart_count' => getTotalCartCount()]);
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clearance Store | Walmart</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="./css/clearance.css">
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
              <!-- <h2>Walmart</h2> -->
            </div>
            <div class="cart">
              <a href="./cart.php"><i class="fa-solid fa-cart-shopping"></i><span class="cart-count">0</span></a>
            </div>
          </section>
    
          <section class="search-bar">
            <div class="search-input">
              <input type="text" placeholder="&#128269;    Search everything at Walmart">
              <i class="fa-solid fa-barcode"></i>
            </div>
            <div class="location">
              <p>How do you want your items? | <span class="postal-code"><?php echo htmlspecialchars($postal_code); ?></span></p>
              <i class="fa-solid fa-caret-down"></i>
            </div>
          </section>
        </nav>
      </header>
    

    <!-- Clearance Store Section -->
    <main>
        <section>
            <div class="clearance-banner">
                <h1>Clearance Store</h1>
                <p class="learn-points">Learn about Points</p>
            </div>
        </section>
        <section class="category-toggle">
            <div class="toggle-container">
                <div class="toggle-background"></div>
                <button class="toggle-btn active">Grocery</button>
                <button class="toggle-btn">Others</button>
            </div>
        </section>

        <!-- Filter Buttons -->
        <section class="filter-buttons">
            <button class="filter-btn">
                <i class="fa-solid fa-sliders"></i> Sort & Filter <i class="fa-solid fa-chevron-down"></i>
            </button>
            <button class="filter-btn">
                In-Store <i class="fa-solid fa-chevron-down"></i>
            </button>
            <button class="filter-btn">
                Price <i class="fa-solid fa-chevron-down"></i>
            </button>
        </section>


        <!-- Product Grid -->
        <section class="product-grid" id="productGrid">
            <!-- Products will be loaded here via JS -->
        </section>          

    </main>

    <!-- Footer (Reused from index.html) -->
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

    <script src="./js/load-clearance-products.js"></script>
</body>
</html>
