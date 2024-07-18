function confirmLogout() {
  var result = confirm("Are you sure you want to logout?");
  if (result) {
    window.location.href = "./logout.php?logout=true";
  }
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

  if (nextResultTime) {
    var countdown = (nextResultTime - now) / 1000;

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
            console.log("Fetched data on countdown end:", data); // Add this line for debugging
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
      nextResultTime.toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
      });
  } else {
    document.querySelector(".running-time").innerText = "--:--";
    document.querySelector(".next-result-time").innerText = "--:--";
  }
}

function updateWinnerImage(winner) {
  console.log("Updating winner image to:", winner); // Add this line for debugging
  var image_url = `./images/${winner}.png`;
  var winnerImage = document.querySelector(".result-num-image");
  if (winnerImage) {
    winnerImage.src = image_url;
    winnerImage.alt = winner;
  } else {
    console.error("Winner image element not found");
  }
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

// Initialize winner display and start countdown
document.addEventListener("DOMContentLoaded", function () {
  // Function to initialize winner display from localStorage or default text
  function initializeWinnerDisplay() {
    var previousWinner = localStorage.getItem("previousWinner");
    console.log("Previous winner from localStorage:", previousWinner); // Add this line for debugging
    if (previousWinner) {
      updateWinnerImage(previousWinner);
    }
  }

  // Start the countdown and initial fetch
  initializeWinnerDisplay();
  updateCountdown();
  updateResultsTable();

  // Interval to update results every 5 minutes
  setInterval(function () {
    fetch("./update_result.php")
      .then((response) => response.json())
      .then((data) => {
        console.log("Fetched data on interval:", data); // Add this line for debugging
        if (data.winner !== undefined) {
          updateWinnerImage(data.winner);
          updateResultsTable();
          localStorage.setItem("previousWinner", data.winner);
        } else {
          console.error("No winner data received");
        }
      })
      .catch((error) => console.error("Error:", error));
  }, 5 * 60 * 1000); // 5 minutes interval
});
