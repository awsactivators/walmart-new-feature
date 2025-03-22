
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

