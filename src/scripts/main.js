document.addEventListener("DOMContentLoaded", () => {
  const bookingForm = document.getElementById("booking-form");
  let roomSelect = bookingForm.elements["room-id"];
  let priceOutput = document.getElementById("price");

  function displayPrice() {
    const roomId = roomSelect.value;
    const room = rooms.find((r) => r.id == roomId);
    if (room) {
      priceOutput.textContent = room.price_per_night + " $";
    }
  }

  roomSelect.addEventListener("change", displayPrice);
});
