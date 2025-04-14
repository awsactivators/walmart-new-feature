document.addEventListener("DOMContentLoaded", function () {
  const urlParams = new URLSearchParams(window.location.search);
  const id = parseInt(urlParams.get("id"));
  const type = urlParams.get("type");

  const file = type === "others" ? "../data/others.json" : "../data/products.json";

  fetch(file)
    .then(res => res.json())
    .then(data => {
      const product = data.find(p => p.id === id);
      if (!product) {
        const mainEl = document.querySelector("main");
        if (mainEl) mainEl.innerHTML = "<p>Product not found.</p>";
        return;
      }

      const productImg = document.querySelector(".product-image img");
      const productName = document.querySelector(".product-name-price h2");
      const productPrice = document.querySelector(".product-name-price .price");

      if (productImg) {
        productImg.src = product.image;
        productImg.alt = product.name;
      }
      if (productName) productName.textContent = product.name;
      if (productPrice) productPrice.textContent = `$${product.price.toFixed(2)}`;

      const ingredientsSection = document.getElementById("ingredients");
      if (ingredientsSection) {
        ingredientsSection.innerHTML = `
          <h3>Ingredients</h3>
          <ul>${(product.ingredients || []).map(i => `<li>${i}</li>`).join('')}</ul>
        `;
      }

      const recipeSection = document.getElementById("recipes");
      if (recipeSection) {
        recipeSection.innerHTML = `
          <h3>Recipe</h3>
          <p>${product.recipe || "No recipe available."}</p>
        `;
      }
    });

  // Toggle buttons (wrap with safety check)
  const ingredientsBtn = document.getElementById("ingredients-btn");
  const recipesBtn = document.getElementById("recipes-btn");
  const ingredientsContent = document.getElementById("ingredients");
  const recipesContent = document.getElementById("recipes");

  if (ingredientsBtn && recipesBtn && ingredientsContent && recipesContent) {
    ingredientsBtn.addEventListener("click", function () {
      ingredientsContent.classList.add("active");
      recipesContent.classList.remove("active");
      ingredientsBtn.classList.add("active");
      recipesBtn.classList.remove("active");
    });

    recipesBtn.addEventListener("click", function () {
      recipesContent.classList.add("active");
      ingredientsContent.classList.remove("active");
      recipesBtn.classList.add("active");
      ingredientsBtn.classList.remove("active");
    });
  }

  // Star Rating System
  const stars = document.querySelectorAll(".star");
  stars.forEach((star, index) => {
    star.addEventListener("click", function () {
      stars.forEach((s, i) => {
        if (i <= index) {
          s.classList.add("checked");
        } else {
          s.classList.remove("checked");
        }
      });
    });
  });
});

