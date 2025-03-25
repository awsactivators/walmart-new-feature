document.addEventListener("DOMContentLoaded", () => {
  const productGrid = document.getElementById("productGrid");
  const toggleButtons = document.querySelectorAll('.toggle-btn');

  function loadProducts(type) {
    const file = type === "grocery" ? '../data/products.json' : '../data/others.json';

    fetch(file)
      .then(res => res.json())
      .then(data => {
        productGrid.innerHTML = ""; 
        data.forEach(product => {
          const card = document.createElement("div");
          card.classList.add("product-card");

          if (type === "grocery") {
            card.innerHTML = `
              <div class="points-tag">
                <i class="fa-solid fa-circle"></i> ${product.points}
              </div>
              <img src="${product.image}" alt="${product.name}">
              <p class="product-name"><a href="../php/product-detail.php?id=${product.id}&type=${type}">${product.name}</a></p>
              <div class="price-container">
                <div>
                  <p class="price">$${product.price.toFixed(2)}</p>
                  <p class="price-per-unit">${product.price_per_unit}</p>
                </div>
                <button class="add-to-cart">
                  <a href="cart.html"><i class="fa-solid fa-plus"></i></a>
                </button>
              </div>
            `;
          } else {
            card.innerHTML = `
              <img src="${product.image}" alt="${product.name}">
              <p class="product-name">
                <a href="../php/product-detail.php?id=${product.id}&type=${type}">${product.name}</a>
              </p>
              <div class="price-container">
                <div>
                  <p class="price">$${product.price.toFixed(2)}</p>
                  <p class="price-per-unit">${product.weight}</p>
                </div>
                <button class="add-to-cart">
                  <a href="cart.html"><i class="fa-solid fa-plus"></i></a>
                </button>
              </div>
            `;
          }

          productGrid.appendChild(card);
        });
      })
      .catch(err => {
        productGrid.innerHTML = "<p>Failed to load items.</p>";
        console.error(err);
      });
  }

  loadProducts("grocery");

  // Toggle buttons
  toggleButtons.forEach((btn, index) => {
    btn.addEventListener("click", function () {
      toggleButtons.forEach(b => b.classList.remove('active'));
      this.classList.add('active');

      let toggleBg = document.querySelector('.toggle-background');
      toggleBg.style.left = index === 0 ? "5px" : "49%";

      const category = index === 0 ? "grocery" : "others";
      loadProducts(category);
    });
  });
});
