const addressRadios = document.querySelectorAll('input[name="address"]');
const altInput = document.querySelector('.alt-address-input');

addressRadios.forEach(radio => {
  radio.addEventListener('change', () => {
    if (radio.value === "custom" && radio.checked) {
      altInput.disabled = false;
      altInput.focus();
    } else {
      altInput.disabled = true;
    }
  });
});


document.addEventListener("DOMContentLoaded", () => {
  const { subtotal, tax, userPoints } = window.orderData;

  const redeemCheckbox = document.getElementById("redeem-check");
  const redeemedVal = document.getElementById("redeemed-val");
  const finalTotal = document.getElementById("final-total");
  const hiddenTotal = document.getElementById("hidden-total");
  const hiddenPoints = document.getElementById("redeemed-points");

  redeemCheckbox.addEventListener("change", () => {
    let redeemableDollars = Math.min(userPoints / 1000, subtotal);
    let redeemablePoints = Math.floor(redeemableDollars * 1000);

    let newTotal = (subtotal - (redeemCheckbox.checked ? redeemableDollars : 0)) + tax;

    redeemedVal.innerText = "$" + (redeemCheckbox.checked ? redeemableDollars.toFixed(2) : "0.00");
    finalTotal.innerText = "$" + newTotal.toFixed(2);
    hiddenTotal.value = newTotal.toFixed(2);
    hiddenPoints.value = redeemCheckbox.checked ? redeemablePoints : 0;
  });
});


