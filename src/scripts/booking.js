document.addEventListener("DOMContentLoaded", () => {
  const roomCostDisplay = document.getElementById("room-cost");
  const featureCostDisplay = document.getElementById("feature-cost");
  const totalCostDisplay = document.getElementById("total-cost");

  const roomPricePerNight = Number(
    document.getElementById("price-per-night").dataset.price
  );

  const checkboxes = document.querySelectorAll('input[name="feature_ids[]"]');

  let roomTotal = 0;
  let featureTotal = 0;
  let arrivalDate = null;
  let departureDate = null;

  roomCostDisplay.innerText = roomTotal;
  featureCostDisplay.innerText = featureTotal;
  totalCostDisplay.innerText = 0;

  displayFeatureCost();
  displayRoomPrice();

  document
    .getElementById("arrival_date")
    .addEventListener("change", function () {
      arrivalDate = new Date(this.value);

      document.getElementById("departure_date").setAttribute("min", this.value);
      displayRoomPrice();

      // IF I WANT TO DISPLAY MSG FOR BOOKED ROOM

      // const calendar = document.getElementById("calendar");
      // const cells = Array.from(calendar.getElementsByClassName("booked"));
      // cells.forEach((cell) => {
      //   let bookedDate = new Date(cell.dataset.date);

      //   if (bookedDate.getTime() === arrivalDate.getTime()) {
      //   }
      // });
    });

  document
    .getElementById("departure_date")
    .addEventListener("change", function () {
      departureDate = new Date(this.value);
      displayRoomPrice();
    });

  function displayFeatureCost() {
    featureTotal = 0;
    checkboxes.forEach((checkbox) => {
      if (checkbox.checked) {
        featureTotal += Number(checkbox.dataset.price);
      }
    });
    featureCostDisplay.innerText = featureTotal;
    updateTotalCost();
  }

  checkboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", displayFeatureCost);
  });

  function displayRoomPrice() {
    if (!arrivalDate || !departureDate) return;
    // This will only work for this project as its locked to january
    const dateDiff = departureDate.getDate() - arrivalDate.getDate();
    if (dateDiff != 0) {
      roomTotal = dateDiff * roomPricePerNight;
      roomCostDisplay.innerText = roomTotal;
    } else {
      roomCostDisplay.innerText = 0;
    }
    updateTotalCost();
  }

  function updateTotalCost() {
    let total = roomTotal + featureTotal;
    totalCostDisplay.innerText = total;

    const discountDisplay = document.getElementById("discount-cost");
    const discountValue = Number(discountDisplay.innerHTML);

    let finalTotal = total;

    if (discountDisplay) {
      finalTotal = Math.max(total - discountValue, 0);
    }

    totalCostDisplay.innerText = finalTotal;
  }

  // ---- Toggle payment method
  document.querySelectorAll('input[name="payment_method"]').forEach((radio) => {
    radio.addEventListener("change", () => {
      const isApi = radio.value === "api_key";

      document.getElementById("api-key-field").style.display = isApi
        ? "flex"
        : "none";
      document.getElementById("transfer-code-field").style.display = isApi
        ? "none"
        : "flex";

      document.getElementById("api_key").required = isApi;
      document.getElementById("transfer_code").required = !isApi;
    });
  });
});
