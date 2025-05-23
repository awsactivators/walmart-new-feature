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

  $first_name = $first_name ?: "User";
  $postal_code = $postal_code ?: "L4Y 1N6";
  $street_address = $street_address ?: "1234 Address st";
  $province = $province ?: "Toronto, ON";
  $full_address = "$street_address, $province, $postal_code";

  // Get product info
  $type = strtolower($_GET['type'] ?? 'grocery');
  $id = $_GET['id'] ?? null;
  $json_file = $type === 'grocery' ? './data/products.json' : './data/others.json';
  $products = json_decode(file_get_contents($json_file), true);

  $product = null;
  foreach ($products as $p) {
    if ($p['id'] == $id) {
      $product = $p;
      break;
    }
}

if (!$product) {
  echo "<p>Product not found</p>";
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Product Page</title>
  <link rel="stylesheet" href="./css/index.css" />
  <link rel="stylesheet" href="./css/product.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        <?php
          $nav_cart_count = 0;
          foreach (['cart', 'clearance_cart'] as $cartKey) {
            if (!empty($_SESSION[$cartKey])) {
              foreach ($_SESSION[$cartKey] as $item) {
                $nav_cart_count += $item['quantity'] ?? 1;
              }
            }
          }
        ?>

        <div class="cart">
          <a href="./cart.php">
            <i class="fa-solid fa-cart-shopping"></i>
            <span class="cart-count"><?php echo $nav_cart_count; ?></span>
          </a>
        </div>

      </section>

      <section class="search-bar">
        <div class="search-input">
          <input type="text" placeholder="🔍 Search everything at Walmart" />
          <i class="fa-solid fa-barcode"></i>
        </div>

        <div class="location">
          <p>How do you want your items? | <span class="postal-code"><?php echo htmlspecialchars($postal_code); ?></span></p>
          <i class="fa-solid fa-caret-down"></i>
        </div>
      </section>
    </nav>
  </header>

  <main class="product-detail-page">
    <section class="points-banner">
      <p><strong>"Buy now & claim <?php echo $product['points'] ?? '0'; ?> points instantly!"</strong></p>
    </section>

    <section class="product-image">
      <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
    </section>

    <section class="product-info">
      <div class="product-name-price">
        <h2><?php echo htmlspecialchars($product['name']); ?></h2>
        <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
      </div>

      <div class="rating-container">
        <div class="rating">
          <?php for ($i = 1; $i <= 5; $i++): ?>
            <span class="star" data-value="<?php echo $i; ?>">&#9733;</span>
          <?php endfor; ?>
        </div>
      </div>
    </section>

    <?php
      echo "<!-- Type: $type -->";
      echo "<!-- Ingredients: " . print_r($product['ingredients'] ?? 'none', true) . " -->";
      echo "<!-- Recipe: " . ($product['recipe'] ?? 'none') . " -->";
    ?>


    <?php if ($type === 'grocery'): ?>
      <section class="toggle-buttons">
        <button class="toggle-btn active" id="ingredients-btn">Ingredients</button>
        <button class="toggle-btn" id="recipes-btn">Recipes</button>
      </section>

      <section class="product-details">
        <div id="ingredients" class="details-content active">
          <h3>Ingredients</h3>
          <ul>
            <?php foreach ($product['ingredients'] as $ingredient): ?>
              <li><?php echo htmlspecialchars($ingredient); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>

        
        <div id="recipes" class="details-content">
          <h3>Recipe</h3>
          <p><?php echo htmlspecialchars($product['recipe'] ?? "No recipe provided."); ?></p>
        </div>

      </section>
    <?php endif; ?>

    <button 
      class="add-to-cart-btn" 
      data-id="<?php echo strtolower(str_replace(' ', '_', $product['name'])); ?>"
      data-name="<?php echo htmlspecialchars($product['name']); ?>"
      data-image="<?php echo $product['image']; ?>"
      data-price="<?php echo $product['price']; ?>"
      data-price-per-unit="<?php echo $product['price_per_unit']; ?>"
      data-points="<?php echo $product['points']; ?>"
    >
      Add to Cart
    </button>

    <a href="./clearance.php" class="back-to-grocery">Back to Clearance</a>
  </main>

  <footer class="bottom-nav">
    <div><a href="./index.php"><i class="fa-solid fa-house"></i></a><p>Home</p></div>
    <div><a href="./index.php"><i class="fa-solid fa-shapes"></i></a><p>Categories</p></div>
    <div><a href="./points.php"><i class="fa-solid fa-piggy-bank"></i></a><p>My Points</p></div>
    <div><a href="./cart.php"><i class="fa-solid fa-cart-shopping"></i></a><p>Cart</p></div>
    <div><a href="./profile.php"><i class="fa-solid fa-user"></i></a><p>Profile</p></div>
  </footer>

  <script src="./js/product.js"></script>
  <script defer>
  document.querySelector('.add-to-cart-btn').addEventListener('click', function () {
    const btn = this;

    const product_id = btn.dataset.id;
    const name = btn.dataset.name;
    const image = btn.dataset.image;
    const price = btn.dataset.price;
    const pricePerUnit = btn.dataset.pricePerUnit || '';
    const points = btn.dataset.points || 0;

    const formData = new FormData();
    formData.append('product_id', product_id);
    formData.append('name', name);
    formData.append('image', image);
    formData.append('price', price);
    formData.append('price_per_unit', pricePerUnit);
    formData.append('points', points);

    fetch('cart.php', {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        btn.textContent = "Added to Cart";
        btn.disabled = true;
        btn.classList.add('added');

        // Increment cart count visually
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
          cartCount.textContent = parseInt(cartCount.textContent || "0") + 1;
        }
      }
    })
    .catch(err => {
      console.error("Add to cart error:", err);
      alert("Failed to add item. Please try again.");
    });
  });
</script>

</body>
</html>
