document.addEventListener("DOMContentLoaded", function() {
  // Toggle between Ingredients and Recipes
  const ingredientsBtn = document.getElementById("ingredients-btn");
  const recipesBtn = document.getElementById("recipes-btn");
  const ingredientsContent = document.getElementById("ingredients");
  const recipesContent = document.getElementById("recipes");

  ingredientsBtn.addEventListener("click", function() {
    ingredientsContent.classList.add("active");
    recipesContent.classList.remove("active");
    ingredientsBtn.classList.add("active");
    recipesBtn.classList.remove("active");
  });

  recipesBtn.addEventListener("click", function() {
    recipesContent.classList.add("active");
    ingredientsContent.classList.remove("active");
    recipesBtn.classList.add("active");
    ingredientsBtn.classList.remove("active");
  });

  // Star Rating System
  const stars = document.querySelectorAll(".star");

  stars.forEach((star, index) => {
    star.addEventListener("click", function() {
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
