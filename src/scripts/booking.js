document.addEventListener("DOMContentLoaded", () => {
  // const bookingForm = document.getElementById('booking-form');
  const roomCostDisplay = document.getElementById("room-cost");
  const roomPrice = document.getElementById("price-per-night").innerText;
  // console.log(roomPrice);

  let totalCost = 0;
  roomCostDisplay.innerText = totalCost;
  let arrivalDate = null;
  let departureDate = null;

  document
    .getElementById("arrival_date")
    .addEventListener("change", function () {
      arrivalDate = new Date(this.value);
      console.log(arrivalDate);
      displayRoomPrice();
    });

  document
    .getElementById("departure_date")
    .addEventListener("change", function () {
      departureDate = new Date(this.value);
      console.log(departureDate);
      displayRoomPrice();
    });

  function displayRoomPrice() {
    if (!arrivalDate || !departureDate) return;
    // This will only work for this project as its locked to january
    const dateDiff = departureDate.getDate() - arrivalDate.getDate();
    // console.log(dateDiff);
    if (dateDiff != 0) {
      totalCost = dateDiff * roomPrice;
      roomCostDisplay.innerText = totalCost;
    } else {
      roomCostDisplay.innerText = 0;
    }
  }

  // roomCostDisplay.addEventListener("change", displayPrice);
});
