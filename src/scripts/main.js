document.addEventListener("DOMContentLoaded", () => {
  const bookingForm = document.getElementById("booking-form");
  let roomSelect = bookingForm.elements["room-id"];
  let priceOutput = document.getElementById("price");

  function displayPrice() {
    const roomPrice = roomSelect.value;
    if (roomPrice) {
      priceOutput.innerText = roomPrice + " $";
    }
  }

  roomSelect.addEventListener("change", displayPrice);
});
