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
        document.querySelector("main").innerHTML = "<p>Product not found.</p>";
        return;
      }

      // Update product image and info
      document.querySelector(".product-image img").src = product.image;
      document.querySelector(".product-image img").alt = product.name;
      document.querySelector(".product-name-price h2").textContent = product.name;
      document.querySelector(".product-name-price .price").textContent = `$${product.price.toFixed(2)}`;

      // Insert Ingredients
      const ingredientsSection = document.getElementById("ingredients");
      ingredientsSection.innerHTML = `
        <h3>Ingredients</h3>
        <ul>${(product.ingredients || []).map(i => `<li>${i}</li>`).join('')}</ul>
      `;

      // Insert Recipe
      const recipeSection = document.getElementById("recipes");
      recipeSection.innerHTML = `
        <h3>Recipe</h3>
        <p>${product.recipe || "No recipe available."}</p>
      `;
    });

  // Toggle between Ingredients and Recipes
  const ingredientsBtn = document.getElementById("ingredients-btn");
  const recipesBtn = document.getElementById("recipes-btn");
  const ingredientsContent = document.getElementById("ingredients");
  const recipesContent = document.getElementById("recipes");

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
