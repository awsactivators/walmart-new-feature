document.addEventListener("DOMContentLoaded", () => {
  const productGrid = document.getElementById("productGrid");
  const toggleButtons = document.querySelectorAll('.toggle-btn');
  const toggleBackground = document.querySelector('.toggle-background');
  const cartCountElement = document.querySelector('.cart-count');

  function loadProducts(type) {
      const file = type === "grocery" ? '../data/products.json' : '../data/others.json';

      fetch(file)
          .then(res => res.json())
          .then(data => {
              productGrid.innerHTML = ""; 
              data.forEach(product => {
                  const card = document.createElement("div");
                  card.classList.add("product-card");
                  card.dataset.id = product.id;
                  card.dataset.name = product.name;
                  card.dataset.image = product.image;
                  card.dataset.price = product.price;
                  card.dataset.priceUnit = product.price_per_unit || product.weight || '';
                  card.dataset.points = product.points || 0;

                  card.innerHTML = `
                       ${type === "grocery" ? `
                        <div class="points-tag">
                            <i class="fa-solid fa-circle"></i> ${product.points}
                        </div>` : ""}
                      <img src="${product.image}" alt="${product.name}">
                      <p class="product-name">
                          <a href="../php/product-detail.php?id=${product.id}&type=${type}">${product.name}</a>
                      </p>
                      <div class="price-container">
                          <div>
                              <p class="price">$${product.price.toFixed(2)}</p>
                              <p class="price-per-unit">${product.price_per_unit || product.weight || 'N/A'}</p>
                          </div>
                          <div class="cart-controls"></div>
                      </div>
                  `;
                  
                  productGrid.appendChild(card);
              });
              
              fetchCart();
          })
          .catch(err => console.error("Failed to load items.", err));
  }

  function fetchCart() {
      fetch("../php/clearance.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({ action: "fetch" })
      })
      .then(response => response.json())
      .then(data => {
          updateCartUI(data.cart);
          updateCartCount(data.cart_count);
      })
      .catch(error => console.error("Error fetching cart:", error));
  }

  function updateCartUI(cart) {
      document.querySelectorAll(".product-card").forEach(card => {
          const productId = card.dataset.id;
          const buttonContainer = card.querySelector(".cart-controls");
          if (!buttonContainer) return;

          buttonContainer.innerHTML = "";
          if (cart[productId]) {
              buttonContainer.innerHTML = `
                  <button class="decrement">-</button>
                  <span>${cart[productId].quantity}</span>
                  <button class="increment">+</button>
              `;
          } else {
              buttonContainer.innerHTML = `<button class="add-to-cart">+</button>`;
          }
      });
  }

  function updateCart(productId, action, productData = {}) {
      fetch("../php/clearance.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({ product_id: productId, action, ...productData })
      })
      .then(response => response.json())
      .then(data => {
          updateCartUI(data.cart);
          updateCartCount(data.cart_count);
      })
      .catch(error => console.error("Error updating cart:", error));
  }

  function updateCartCount(count) {
      cartCountElement.textContent = count;
  }

  function fetchCartCount() {
      fetch("../php/clearance.php?getCartCount=true")
          .then(response => response.json())
          .then(data => updateCartCount(data.cart_count))
          .catch(error => console.error("Error fetching cart count:", error));
  }

  function addToCart(productId) {
      const card = document.querySelector(`.product-card[data-id='${productId}']`);
      if (!card) return;

      updateCart(productId, "add", {
          name: card.dataset.name,
          image: card.dataset.image,
          price: parseFloat(card.dataset.price) || 0,
          price_per_unit: card.dataset.priceUnit,
          points: parseInt(card.dataset.points) || 0
      });
  }

  productGrid.addEventListener("click", (event) => {
      const button = event.target.closest("button");
      if (!button) return;

      const card = button.closest(".product-card");
      if (!card) return;

      const productId = card.dataset.id;
      if (!productId) return;

      if (button.classList.contains("add-to-cart")) {
          addToCart(productId);
      } else if (button.classList.contains("increment")) {
          updateCart(productId, "increment");
      } else if (button.classList.contains("decrement")) {
          updateCart(productId, "decrement");
      }
  });

  toggleButtons.forEach((btn, index) => {
    btn.addEventListener("click", () => {
        // Remove active from all buttons and add to clicked one
        toggleButtons.forEach(b => b.classList.remove("active"));
        btn.classList.add("active");

        // Load products based on category
        loadProducts(index === 0 ? "grocery" : "others");

        // ✅ Move the toggle background dynamically
        if (toggleBackground) {
            toggleBackground.style.left = index === 0 ? "5px" : "50%";
        }
    });
  });

  loadProducts("grocery");
  fetchCartCount(); // Fetch cart count on page load
});
