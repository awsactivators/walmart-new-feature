document.addEventListener("DOMContentLoaded", function() {
  fetchCartData();
});

function fetchCartData() {
  fetch('index.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'fetch_cart=1'
  })
  .then(response => response.json())
  .then(data => {
      let cart = data.cart;
      let cartCount = data.cart_count;
      Object.keys(cart).forEach(productId => {
        const quantity = cart[productId].quantity || 0;
        updateCartButton(productId, cart[productId].quantity || 0);
      });
      updateCartCount(cartCount);
  })
  .catch(error => console.error('Error fetching cart:', error));
}

function updateCart(productId, action) {
let productElement = document.querySelector(`#cart-btn-${productId}`);
if (!productElement) {
  console.error(`Product container not found for product ID: ${productId}`);
  return;
}

let productContainer = productElement.closest('.product');
if (!productContainer) {
  console.error(`Could not find product container for product ID: ${productId}`);
  return;
}

// Try alternative selectors if elements are missing
let productNameElement = productContainer.querySelector('.product-name a') || 
                       productContainer.querySelector('.product-name p');
let productImageElement = productContainer.querySelector('img');
let productPriceElement = productContainer.querySelector('.price');
let productPointsElement = productContainer.querySelector('.points-tag');

if (!productNameElement || !productImageElement || !productPriceElement) {
  console.error(`Missing product details for product ID: ${productId}`);
  console.log({
      productNameElement,
      productImageElement,
      productPriceElement,
      productPointsElement
  });
  return;
}

// let productName = productNameElement.textContent.trim();
// let productImage = productImageElement.getAttribute('src');
// let productPrice = productPriceElement.textContent.replace('$', '').trim();
// let productPoints = productPointsElement ? productPointsElement.textContent.trim() : "0";

let productName = productContainer.querySelector(`[onclick*="updateCart(${productId},"]`)?.dataset.name;
let productImage = productContainer.querySelector(`[onclick*="updateCart(${productId},"]`)?.dataset.image;
let productPrice = productContainer.querySelector(`[onclick*="updateCart(${productId},"]`)?.dataset.price;
let productPoints = productContainer.querySelector(`[onclick*="updateCart(${productId},"]`)?.dataset.points || "0";


let formData = new URLSearchParams();
formData.append("product_id", productId);
formData.append("action", action);
formData.append("product_name", productName);
formData.append("product_image", productImage);
formData.append("product_price", productPrice);
formData.append("product_points", productPoints);

fetch('index.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
  body: formData.toString()
})
.then(response => response.text())
.then(text => {
  console.log("Raw Response:", text);
  let data = JSON.parse(text);
  updateCartButton(productId, data.cart[productId]?.quantity || 0);
  updateCartCount(data.cart_count);
})
.catch(error => console.error('JSON Parse Error:', error));
}



function updateCartButton(productId, quantity) {
  let buttonContainer = document.getElementById(`cart-btn-${productId}`);
  if (!buttonContainer) {
      console.error(`Button container not found for product ${productId}`);
      return;
  }
  if (quantity > 0) {
      buttonContainer.innerHTML = `
          <button class="add-to-cart" onclick="updateCart(${productId}, 'decrease')">-</button>
          <span>${quantity}</span>
          <button class="add-to-cart"  onclick="updateCart(${productId}, 'increase')">+</button>
      `;
  } else {
      buttonContainer.innerHTML = `<button class="add-to-cart" onclick="updateCart(${productId}, 'increase')">+</button>`;
  }
}

function updateCartCount(count) {
  let cartCountElement = document.querySelector(".cart-count");
  if (cartCountElement) {
      cartCountElement.textContent = count;
  }
}