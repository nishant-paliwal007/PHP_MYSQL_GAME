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
    inputs.forEach(function(input) {
        input.value = "";
    });
    betAmount = 0;
    updateTotal();
}

function updateTotal() {
    var inputs = document.querySelectorAll('input[type="text"]');
    var total = 0;
    inputs.forEach(function(input) {
        total += parseFloat(input.value || 0);
    });
    document.getElementById('totalButton').innerText = 'Total: ' + total.toFixed(2); // Display total
}

function updateCountdown() {
    var now = new Date();
    var nextResultTime;

    // Check if current time is between 08:00 AM and 10:00 PM
    if (now.getHours() >= 8 && now.getHours() < 22) {
        // Calculate next result time within the same day
        nextResultTime = new Date(now.getTime() + (5 - now.getMinutes() % 5) * 60000);
    } else {
        // Set next result time to undefined to indicate no active countdown
        nextResultTime = undefined;
    }

    // Ensure seconds and milliseconds are set to zero for precise timing
    if (nextResultTime) {
        nextResultTime.setSeconds(0, 0);
    }

    // Update UI based on next result time availability
    if (nextResultTime) {
        var countdown = (nextResultTime - now) / 1000;

        var interval = setInterval(function() {
            var minutes = Math.floor(countdown / 60);
            var seconds = Math.floor(countdown % 60);
            document.querySelector('.running-time').innerText = minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0');
            countdown--;

            if (countdown < 0) {
                clearInterval(interval);
                fetch('./update_result.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.winner !== undefined) {
                            // Update winner image
                            var image_url = `./images/${data.winner}.png`;
                            var winnerImage = document.querySelector('.result-num-image');
                            if (winnerImage) {
                                winnerImage.src = image_url;
                                winnerImage.alt = data.winner;
                            }

                            updateResultsTable();
                            // Store the new winner in localStorage
                            localStorage.setItem('previousWinner', data.winner);
                        } else {
                            console.error('No winner data received');
                        }
                    })
                    .catch(error => console.error('Error:', error));

                updateCountdown(); // Restart countdown for the next interval
            }
        }, 1000);

        // Update the next result time display
        document.querySelector('.next-result-time').innerText = nextResultTime.toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit'
        });
    } else {
        // No active countdown, set placeholders
        document.querySelector('.running-time').innerText = '--:--';
        document.querySelector('.next-result-time').innerText = '--:--';
    }
}

function updateResultsTable() {
    fetch('./fetch_latest_results.php')
        .then(response => response.text())
        .then(data => {
            document.querySelector('.result-table').innerHTML = data;
        })
        .catch(error => console.error('Error:', error));
}

document.addEventListener('DOMContentLoaded', function() {
    // Function to initialize winner display from localStorage or default text
    function initializeWinnerDisplay() {
        var previousWinner = localStorage.getItem('previousWinner');
        if (previousWinner) {
            // Update winner image
            var winnerImage = document.querySelector('.result-num-image');
            if (winnerImage) {
                winnerImage.src = `./images/${previousWinner}.png`;
                winnerImage.alt = previousWinner;
            }
        }
    }

    // Start the countdown and initial fetch
    initializeWinnerDisplay();
    updateCountdown();
    updateResultsTable();

    // Interval to update results every 5 minutes
    setInterval(function() {
        fetch('./update_result.php')
            .then(response => response.json())
            .then(data => {
                if (data.winner !== undefined) {
                    // Update winner image
                    var image_url = `./images/${data.winner}.png`;
                    var winnerImage = document.querySelector('.result-num-image');
                    if (winnerImage) {
                        winnerImage.src = image_url;
                        winnerImage.alt = data.winner;
                    }

                    updateResultsTable();
                    // Store the new winner in localStorage
                    localStorage.setItem('previousWinner', data.winner);
                } else {
                    console.error('No winner data received');
                }
            })
            .catch(error => console.error('Error:', error));
    }, 5 * 60 * 1000);
});
