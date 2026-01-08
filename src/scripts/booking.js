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
  // ---- Restore dates on reload
  const arrivalInput = document.getElementById("arrival_date");
  const departureInput = document.getElementById("departure_date");

  if (arrivalInput.value) {
    arrivalDate = new Date(arrivalInput.value);
  }

  if (departureInput.value) {
    departureDate = new Date(departureInput.value);
  }

  // ---- Re-apply min departure date if arrival exists
  document
    .getElementById("arrival_date")
    .addEventListener("change", function () {
      arrivalDate = new Date(this.value);

      // Departure date must be bigger than arrival
      const minDeparture = new Date(
        arrivalDate.getFullYear(),
        arrivalDate.getMonth(),
        arrivalDate.getDate() + 2
      );
      // Set min departure date
      document
        .getElementById("departure_date")
        .setAttribute("min", minDeparture.toISOString().split("T")[0]);
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

  displayFeatureCost();
  displayRoomPrice();

  const checkedPayment = document.querySelector(
    'input[name="payment_method"]:checked'
  );

  if (
    document.getElementById("api-key-field") &&
    document.getElementById("transfer-code-field")
  ) {
    updatePaymentFields(checkedPayment.value);
  }

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

    let finalTotal = total;

    if (discountDisplay) {
      const discountValue = Number(discountDisplay.innerHTML);
      finalTotal = Math.max(total - discountValue, 0);
    }

    totalCostDisplay.innerText = finalTotal;
  }

  // ---- Toggle payment method fields
  function updatePaymentFields(selectedValue) {
    const isApi = selectedValue === "api_key";

    document.getElementById("api-key-field").style.display = isApi
      ? "flex"
      : "none";

    document.getElementById("transfer-code-field").style.display = isApi
      ? "none"
      : "flex";

    document.getElementById("api_key").required = isApi;
    document.getElementById("transfer_code").required = !isApi;
  }
  // ---- Toggle on change
  document.querySelectorAll('input[name="payment_method"]').forEach((radio) => {
    radio.addEventListener("change", () => {
      updatePaymentFields(radio.value);
    });
  });

  /* ---- ALTERNATING IMG */
  const images = document.querySelectorAll(".booking-img");
  let current = 0;

  setInterval(() => {
    images[current].classList.remove("active");
    current = (current + 1) % images.length;
    images[current].classList.add("active");
  }, 6000); // 4 seconds
});
