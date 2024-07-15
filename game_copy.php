<?php
include "./session_check.php";
include "./connection.php";
include "./populate_results.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game</title>
    <link rel="stylesheet" href="styles.css">
    <script>
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
            var nextResultTime = new Date(now.getTime() + (5 - now.getMinutes() % 5) * 60000);
            nextResultTime.setSeconds(0, 0);

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
                                // Update winner display
                                document.querySelector('.winner-text-cont .winning-number').innerText = data.winner;

                                // Get image URL based on result number
                                var image_url = `./images/${data.winner}.png`;

                                // Update winner image
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
                    document.querySelector('.winner-img-cont img').src = './images/' + previousWinner + '.png';
                    document.querySelector('.winner-text-cont .winning-number').innerText = 'Winner'; // Set to "Winner"
                } else {
                    document.querySelector('.winner-text-cont .winning-number').innerText = 'Waiting...';
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
                            // Update UI with new winner details
                            document.querySelector('.winner-img-cont img').src = './images/' + data.winner + '.png';
                            document.querySelector('.winner-text-cont .winning-number').innerText = data.winner;
                            updateResultsTable();

                            // Store winner in localStorage
                            localStorage.setItem('previousWinner', data.winner);
                        } else {
                            console.error('No winner data received');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }, 5 * 60 * 1000);
        });
    </script>
</head>

<body>
    <div class="game-container">
        <div class="balance-result-container">
            <div class="points-time-cont">
                <div class="balance-container">
                    <p class="points-text">POINTS</p>
                    <!-- Fetch the balance from PHP -->
                    <?php
                    include "./connection.php";
                    $username = $_SESSION['username'];
                    $fetch_balance = mysqli_query($conn, "SELECT * FROM balance WHERE username= '$username'");
                    $balance_data = mysqli_fetch_assoc($fetch_balance);
                    $balance = $balance_data['balance'];

                    ?>
                    <p class="balance"><?php echo $balance; ?></p>
                </div>
                <div class="time-container">
                    <div class="next-result-time"></div>
                    <div class="running-time"></div>
                </div>
            </div>
            <div class="win-res-container">
                <div class="winner-container">
                    <div class="winner-text-cont">
                        <!-- <p class="winner-text">Winner</p> -->
                        <!-- later display total betting amout in place of Winner -->
                        <p class="winning-number" id="winningNumber"><?php echo htmlspecialchars($_SESSION['winning-number'] ?? '0'); ?></p>
                        <p class="total-bet-amt" style="background-color: darkgrey;height: 
                        35px;width: 280px;border-radius: 10px;margin-top: 0px;color: #ffffff;
                        display: flex;justify-content: center;align-items: center;"
                        >Total Bet Amt: 0</p>

                    </div>
                    <div class="winner-display-container">
                        <div class="winner-img-cont">
                            <img class="result-num-image" src="./images/1.png" alt="Main Image">
                        </div>
                    </div>
                </div>
                <table class="result-table">

                </table>
            </div>
        </div>
        <div class="betting-points-buttons">
            <div class="bet-amt-buttons">
                <button type="button" class="bet-amt-btn" onclick="selectAmount(10)">
                    <img src="./images/ten.png" class="bet-amt-image" alt="">
                </button>
                <button type="button" class="bet-amt-btn" onclick="selectAmount(20)">
                    <img src="./images/twenty.png" class="bet-amt-image" alt="">
                </button>
                <button type="button" class="bet-amt-btn" onclick="selectAmount(50)">
                    <img src="./images/fifty.png" class="bet-amt-image" alt="">
                </button>
                <button type="button" class="bet-amt-btn" onclick="selectAmount(100)">
                    <img src="./images/eleven.png" class="bet-amt-image" alt="">
                </button>
            </div>
            <div class="bet-amt-buttons">
                <button type="button" class="bet-amt-btn" onclick="selectAmount(200)">
                    <img src="./images/200.png" class="bet-amt-image" alt="">
                </button>
                <button type="button" class="bet-amt-btn" onclick="selectAmount(500)">
                    <img src="./images/fiveh.png" class="bet-amt-image" alt="">
                </button>
                <button type="button" class="bet-amt-btn" onclick="selectAmount(800)">
                    <img src="./images/800.png" class="bet-amt-image" alt="">
                </button>
                <button type="button" class="bet-amt-btn" onclick="selectAmount(1000)">
                    <img src="./images/34.png" class="bet-amt-image" alt="">
                </button>
            </div>
        </div>
        <div class="betting-numbers-container">
            <div class="bet-nums-cont">
                <div class="img-input-cont">
                    <img class="img" src="./images/1.png" alt="1" onclick="handleImageClick('bet_input_1')">
                    <input id="bet_input_1" class="input" type="text" name="bet_input_1" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/2.png" alt="2" onclick="handleImageClick('bet_input_2')">
                    <input id="bet_input_2" class="input" type="text" name="bet_input_2" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/3.png" alt="3" onclick="handleImageClick('bet_input_3')">
                    <input id="bet_input_3" class="input" type="text" name="bet_input_3" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/4.png" alt="4" onclick="handleImageClick('bet_input_4')">
                    <input id="bet_input_4" class="input" type="text" name="bet_input_4" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/5.png" alt="5" onclick="handleImageClick('bet_input_5')">
                    <input id="bet_input_5" class="input" type="text" name="bet_input_5" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/6.png" alt="6" onclick="handleImageClick('bet_input_6')">
                    <input id="bet_input_6" class="input" type="text" name="bet_input_6" value="">
                </div>
            </div>
            <div class="bet-nums-cont">
                <div class="img-input-cont">
                    <img class="img" src="./images/7.png" alt="7" onclick="handleImageClick('bet_input_7')">
                    <input id="bet_input_7" class="input" type="text" name="bet_input_7" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/8.png" alt="8" onclick="handleImageClick('bet_input_8')">
                    <input id="bet_input_8" class="input" type="text" name="bet_input_8" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/9.png" alt="9" onclick="handleImageClick('bet_input_9')">
                    <input id="bet_input_9" class="input" type="text" name="bet_input_9" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/10.png" alt="10" onclick="handleImageClick('bet_input_10')">
                    <input id="bet_input_10" class="input" type="text" name="bet_input_10" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/11.png" alt="11" onclick="handleImageClick('bet_input_11')">
                    <input id="bet_input_11" class="input" type="text" name="bet_input_11" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/12.png" alt="12" onclick="handleImageClick('bet_input_12')">
                    <input id="bet_input_12" class="input" type="text" name="bet_input_12" value="">
                </div>
            </div>
        </div>
        <div class="buttons-container">
            <form id="betAmountForm" method="post" action="">
                <button class="image-button" type="submit" form="betAmountForm" name="bet_ok">
                    <img src="./images/button.png" alt="Button Image">
                    <span class="button-text">Bet Ok</span>
                </button>
                <button class="image-button" type="button" onclick="clearInputs()">
                    <img src="./images/button.png" alt="Button Image">
                    <span class="button-text">Clear</span>
                </button>
                <button class="image-button" type="button">
                    <img src="./images/button.png" alt="Button Image">
                    <span class="button-text">Report</span>
                </button>
                <button class="image-button" type="button" onclick="confirmLogout()">
                    <img src="./images/button.png" alt="Button Image">
                    <span class="button-text">Logout</span>
                </button>
                <button class="image-button" type="button">
                    <img src="./images/button.png" alt="Button Image">
                    <span id="totalButton" class="button-text" style="color: black;"></span>
                </button>
            </form>
        </div>
    </div>
    <script>
        // Update total after DOM loaded
        document.addEventListener('DOMContentLoaded', function() {
            updateTotal();
        });
    </script>
</body>

</html>