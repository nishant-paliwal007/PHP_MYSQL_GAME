
function confirmLogout() {
  var result = confirm("Are you sure you want to logout?");
  if (result) {
    window.location.href = "./logout.php?logout=true";
  }
}

function betOk() {
  // Validate and prepare data before submitting
  let totalAmount = 0;
  let tickets = [];
  let inputs = document.querySelectorAll('.bet-nums-cont input[type="text"]');
  let form = document.getElementById("betAmountForm");

  inputs.forEach((input) => {
    let amount = parseInt(input.value);
    let number = input.id.split("_")[2]; // Get the number from the input id, e.g., bet_input_1 -> 1
    if (!isNaN(amount) && amount > 0) {
      totalAmount += amount;
      tickets.push(amount + "*" + number); // Format as amount*number
    }
  });

  if (totalAmount < 10) {
    alert("Minimum bet amount should be 10.");
    // Clear inputs and total amount display
    inputs.forEach((input) => (input.value = ""));
    document.getElementById("totalButton").textContent = "Total: 0";
    return;
  }

  // Get total bet amount from the span element
  let totalButton = document.getElementById("totalButton").textContent;
  let totalBetAmount = parseInt(totalButton.replace("Total: ", ""));

  // Create form data object
  let formData = new FormData();
  formData.append("total_bet_amount", totalBetAmount);
  formData.append("tickets", tickets.join(", "));
  formData.append("bet_ok", "1");

  // Send AJAX request to bet_ok.php
  fetch("bet_ok.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        // Show success message and update balance dynamically
        alert("Bet placed successfully. Ticket_amt: " + totalBetAmount);
        updateBalance(); // Function to update balance dynamically
        // Clear inputs and total amount display
        inputs.forEach((input) => (input.value = ""));
        document.getElementById("totalButton").textContent = "Total: 0";
      } else {
        // Show error message
        if (data.message === "Insufficient balance.") {
          alert("Insufficient balance!");
          // Clear inputs and total amount display on insufficient balance
          inputs.forEach((input) => (input.value = ""));
          document.getElementById("totalButton").textContent = "Total: 0";
        } else {
          alert("Error: " + data.message);
        }
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("An error occurred while placing the bet.");
    });
}

// Function to update balance dynamically
function updateBalance() {
  fetch("get_balance.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        document.getElementById("currentBalance").textContent = data.balance; // Update balance on the UI
      } else {
        console.error("Failed to fetch updated balance:", data.message);
      }
    })
    .catch((error) => {
      console.error("Error fetching updated balance:", error);
    });
}

var betAmount = 0; // Default bet amount

function selectAmount(amount) {
  betAmount = amount;
}

function handleImageClick(inputId) {
  var inputBox = document.getElementById(inputId);
  var currentValue = parseFloat(inputBox.value || 0);
  inputBox.value = currentValue + betAmount;
  updateTotal();
}

function clearInputs() {
  var inputs = document.querySelectorAll('input[type="text"]');
  inputs.forEach(function (input) {
    input.value = "";
  });
  betAmount = 0;
  updateTotal();
}

function updateTotal() {
  var inputs = document.querySelectorAll('input[type="text"]');
  var total = 0;
  inputs.forEach(function (input) {
    total += parseFloat(input.value || 0);
  });
  document.getElementById("totalButton").innerText =
    "Total: " + total.toFixed(2); // Display total
}

// Function to update countdown timer
function updateCountdown() {
  var now = new Date();
  var nextResultTime;

  // Calculate next result time within the same day
  if (now.getHours() >= 8 && now.getHours() < 22) {
    nextResultTime = new Date(
      now.getTime() + (5 - (now.getMinutes() % 5)) * 60000
    );
  } else {
    nextResultTime = undefined; // No active countdown outside 08:00 AM to 10:00 PM
  }

  if (nextResultTime) {
    nextResultTime.setSeconds(0, 0);
  }

  var countdown = nextResultTime ? (nextResultTime - now) / 1000 : 0;

  var interval = setInterval(function () {
    var minutes = Math.floor(countdown / 60);
    var seconds = Math.floor(countdown % 60);
    document.querySelector(".running-time").innerText =
      minutes.toString().padStart(2, "0") +
      ":" +
      seconds.toString().padStart(2, "0");
    countdown--;

    if (countdown < 0) {
      clearInterval(interval);
      fetch("./update_result.php")
        .then((response) => response.json())
        .then((data) => {
          if (data.winner !== undefined) {
            updateWinnerImage(data.winner);
            updateResultsTable();
            localStorage.setItem("previousWinner", data.winner);
          } else {
            console.error("No winner data received");
          }
        })
        .catch((error) => console.error("Error:", error));

      updateCountdown();
    }
  }, 1000);

  document.querySelector(".next-result-time").innerText =
    nextResultTime ? nextResultTime.toLocaleTimeString([], {
      hour: "2-digit",
      minute: "2-digit",
    }) : "--:--";
}

// Function to update results table
function updateResultsTable() {
  fetch("./fetch_latest_results.php")
    .then((response) => response.text())
    .then((data) => {
      document.querySelector(".result-table").innerHTML = data;
    })
    .catch((error) =>
      console.error("Error fetching fetch_latest_results.php:", error)
    );
}

// Function to update winner image dynamically
function updateWinnerImage(winner) {
  console.log("Updating winner image to:", winner); // For debugging
  var image_url = `./images/${winner}.png`;
  var winnerImage = document.querySelector(".result-num-image");

  if (winnerImage) {
    winnerImage.src = image_url;
    winnerImage.alt = winner;
  } else {
    console.error("Winner image element not found");
  }
}

function checkDrawTime() {
  fetch("check_draw_time.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        console.log("Winning Number:", data.winning_number);
        console.log("Total Winning Amount:", data.winning_amount);

        const totalWinElement = document.getElementById("totalWin");

        // Calculate the delay until 2 seconds after the draw time
        const now = new Date();
        const drawTime = new Date();
        drawTime.setSeconds(Math.ceil(now.getSeconds() / 5) * 5);
        const delay = Math.max(2000 - (now - drawTime), 0); // Ensure non-negative delay

        // Update the element after 2 seconds
        setTimeout(() => {
          totalWinElement.textContent = "You Won: " + data.winning_amount;
          totalWinElement.style.color = "#FFFF00";
          totalWinElement.style.fontWeight = "bold";

          // Fetch and update the balance after 10 seconds
          setTimeout(() => {
            totalWinElement.textContent = "";
            totalWinElement.style.color = "#ffffff";

            fetch("get_balance.php", {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
            })
              .then((response) => response.json())
              .then((balanceData) => {
                if (balanceData.status === "success") {
                  document.getElementById("currentBalance").textContent =
                    balanceData.balance;
                } else {
                  console.error("Error:", balanceData.message);
                }
              })
              .catch((error) => {
                console.error("Fetch Error:", error);
              });
          }, 10000); // 10 seconds delay
        }, delay); // 2 seconds delay
      } else {
        console.error("Error:", data.message);
      }
    })
    .catch((error) => {
      console.error("Fetch Error:", error);
    });
}

function scheduleNextCheck() {
  const now = new Date();
  const nextDrawMinutes = Math.ceil(now.getMinutes() / 5) * 5;
  const nextDrawTime = new Date(
    now.getFullYear(),
    now.getMonth(),
    now.getDate(),
    now.getHours(),
    nextDrawMinutes,
    0,
    0
  );

  if (now > nextDrawTime) {
    nextDrawTime.setMinutes(nextDrawTime.getMinutes() + 5);
  }

  const timeUntilNextDraw = nextDrawTime - now;

  setTimeout(() => {
    checkDrawTime();
    scheduleNextCheck();
  }, timeUntilNextDraw);
}

function initializeWinnerDisplay() {
  fetch("./update_result.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.winner) {
        updateWinnerImage(data.winner);
      } else {
        console.error("No winner data received from server");
      }
    })
    .catch((error) => console.error("Error fetching latest winner:", error));
}

// Initialize
document.addEventListener("DOMContentLoaded", function () {
  updateCountdown();
  updateResultsTable();
  initializeWinnerDisplay();
  scheduleNextCheck();
});
