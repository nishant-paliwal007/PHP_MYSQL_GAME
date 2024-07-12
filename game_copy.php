<?php
include "./connection.php";
include "./session_check.php";
$username = $_SESSION['username'];
$fetch_balance = mysqli_query($conn, "SELECT * FROM balance WHERE username= '$username'");
$balance_data = mysqli_fetch_assoc($fetch_balance);
$balance = $balance_data['balance'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game</title>
    <link rel="stylesheet" href="style.css">
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
            document.getElementById('totalButton').innerText = 'Total: ' + total.toFixed(2);
        }

        function updateTimeAndWinner() {
            var runningTimeElement = document.querySelector('.running-time');
            var nextResultTimeElement = document.querySelector('.next-result-time');
            var winnerImageElement = document.querySelector('.result-num-image');

            function updateNextDrawTime() {
                var now = new Date();
                var currentMinutes = now.getMinutes();
                var nextDrawMinutes = currentMinutes + (5 - (currentMinutes % 5));
                var nextDrawTime = new Date(now.getFullYear(), now.getMonth(), now.getDate(), now.getHours(), nextDrawMinutes, 0, 0);

                if (nextDrawTime < now) {
                    nextDrawTime.setHours(nextDrawTime.getHours() + 1);
                    nextDrawTime.setMinutes(0);
                }

                if (nextDrawTime.getHours() < 8) {
                    nextDrawTime.setHours(8);
                    nextDrawTime.setMinutes(0);
                } else if (nextDrawTime.getHours() >= 22 && nextDrawMinutes > 0) {
                    nextDrawTime.setDate(nextDrawTime.getDate() + 1);
                    nextDrawTime.setHours(8);
                    nextDrawTime.setMinutes(0);
                }

                return nextDrawTime;
            }

            function formatTime(date) {
                var hours = ('0' + date.getHours()).slice(-2);
                var minutes = ('0' + date.getMinutes()).slice(-2);
                var ampm = hours >= 12 ? 'pm' : 'am';
                hours = hours % 12;
                hours = hours ? hours : 12; // the hour '0' should be '12'
                return hours + ':' + minutes + ' ' + ampm;
            }

            var nextDrawTime = updateNextDrawTime();
            nextResultTimeElement.textContent = formatTime(nextDrawTime);

            function startCountdown() {
                var countDownTime = Math.floor((nextDrawTime - new Date()) / 1000); // Countdown in seconds
                var countdownTimer = setInterval(function() {
                    var minutes = Math.floor(countDownTime / 60);
                    var seconds = countDownTime % 60;
                    var displayTime = ('0' + minutes).slice(-2) + ':' + ('0' + seconds).slice(-2);
                    runningTimeElement.textContent = displayTime;

                    countDownTime--; // Decrease countdown

                    if (countDownTime < 0) {
                        clearInterval(countdownTimer);
                        // Generate random winner number between 1 and 12 (inclusive)
                        var randomNumber = Math.floor(Math.random() * 12) + 1;

                        // Update winner image
                        winnerImageElement.src = './images/' + randomNumber + '.png';

                        // Save result to the database
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", "store_result.php", true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                                // Refresh results table
                                fetchResults();
                            }
                        };
                        var now = new Date();
                        var res_time = formatTime(now);
                        var res_date = now.toISOString().slice(0, 10);
                        xhr.send("res=" + randomNumber + "&res_time=" + res_time + "&res_date=" + res_date);

                        // Update next draw time
                        nextDrawTime = updateNextDrawTime();
                        nextResultTimeElement.textContent = formatTime(nextDrawTime);
                        startCountdown(); // Restart countdown
                    }
                }, 1000); // Update every second
            }

            startCountdown();
        }

        function fetchResults() {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "fetch_results.php", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    document.querySelector(".result-table").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        document.addEventListener("DOMContentLoaded", function() {
            updateTimeAndWinner(); // Start the time and winner update function
            fetchResults(); // Fetch results initially
        });
    </script>
</head>

<body>
    <div class="game-container">
        <div class="balance-result-container">
            <div class="points-time-cont">
                <div class="balance-container">
                    <p class="points-text">POINTS</p>
                    <p class="balance"><?php echo $balance; ?></p>
                </div>
                <div class="time-container">
                    <p class="next-result-time">12:00 pm</p>
                    <p class="running-time"></p>
                </div>
            </div>
            <div class="win-res-container">
                <div class="winner-container">
                    <div class="winner-text-cont">
                        <p class="winner-text">Winner</p>
                        <p class="winning-number">0</p>
                    </div>
                    <div class="winner-display-container">
                        <div class="winner-img-cont">
                            <img class="result-num-image" src="./images/1.png" alt="Main Image">
                        </div>
                    </div>
                </div>
                <table class="result-table">
                    <?php
                    $sql = "SELECT * FROM result_single ORDER BY id DESC LIMIT 5";
                    $result = mysqli_query($conn, $sql);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<tr>';
                        echo '<td>' . $row['res'] . '</td>';
                        echo '<td>' . $row['res_time'] . '</td>';
                        echo '<td>' . $row['res_date'] . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </table>
            </div>
        </div>
        <div class="betting-points-buttons">
            <div class="bet-amt-buttons">
                <button class="bet-amt-btn" onclick="selectAmount(10)"><img src="./images/ten.png" class="bet-amt-image" alt=""></button>
                <button class="bet-amt-btn" onclick="selectAmount(20)"><img src="./images/twenty.png" class="bet-amt-image" alt=""></button>
                <button class="bet-amt-btn" onclick="selectAmount(50)"><img src="./images/fifty.png" class="bet-amt-image" alt=""></button>
                <button class="bet-amt-btn" onclick="selectAmount(100)"><img src="./images/eleven.png" class="bet-amt-image" alt=""></button>
            </div>

            <div class="bet-amt-buttons">
                <button class="bet-amt-btn" onclick="selectAmount(200)"><img src="./images/200.png" class="bet-amt-image" alt=""></button>
                <button class="bet-amt-btn" onclick="selectAmount(500)"><img src="./images/fiveh.png" class="bet-amt-image" alt=""></button>
                <button class="bet-amt-btn" onclick="selectAmount(800)"><img src="./images/800.png" class="bet-amt-image" alt=""></button>
                <button class="bet-amt-btn" onclick="selectAmount(34)"><img src="./images/34.png" class="bet-amt-image" alt=""></button>
            </div>
        </div>

        <div class="betting-numbers-container">
            <div class="bet-nums-cont">
                <div class="img-input-cont">
                    <img class="img" src="./images/1.png" alt="1" onclick="handleImageClick('input1')">
                    <input id="input1" class="input" type="text" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/2.png" alt="2" onclick="handleImageClick('input2')">
                    <input id="input2" class="input" type="text" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/3.png" alt="3" onclick="handleImageClick('input3')">
                    <input id="input3" class="input" type="text" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/4.png" alt="4" onclick="handleImageClick('input4')">
                    <input id="input4" class="input" type="text" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/5.png" alt="5" onclick="handleImageClick('input5')">
                    <input id="input5" class="input" type="text" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/6.png" alt="6" onclick="handleImageClick('input6')">
                    <input id="input6" class="input" type="text" value="">
                </div>
            </div>

            <div class="bet-nums-cont">
                <div class="img-input-cont">
                    <img class="img" src="./images/7.png" alt="7" onclick="handleImageClick('input7')">
                    <input id="input7" class="input" type="text" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/8.png" alt="8" onclick="handleImageClick('input8')">
                    <input id="input8" class="input" type="text" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/9.png" alt="9" onclick="handleImageClick('input9')">
                    <input id="input9" class="input" type="text" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/10.png" alt="10" onclick="handleImageClick('input10')">
                    <input id="input10" class="input" type="text" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/11.png" alt="11" onclick="handleImageClick('input11')">
                    <input id="input11" class="input" type="text" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/12.png" alt="12" onclick="handleImageClick('input12')">
                    <input id="input12" class="input" type="text" value="">
                </div>
            </div>
        </div>

        <div class="buttons-container">
            <form id="betAmountForm" method="post" action="">
                <div>
                    <button class="image-button" type="button">
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
                        <span id="totalButton" class="button-text"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
