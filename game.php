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
    <link rel="stylesheet" href="style.css">
    <script src="./script.js"></script>

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
                    <p class="balance" id="currentBalance"><?php echo $balance; ?></p>
                </div>
                <div class="time-container">
                    <div class="next-result-time"></div>
                    <div class="running-time"></div>
                </div>
            </div>
            <div>
                <?php echo "<h1 style='font-family: Roboto; font-style: italic; color:red;'>Welcome {$_SESSION['username']}!&#128522;</h1>"; ?>
            </div>
            <div class="win-res-container">
                <div class="winner-container">
                    <div class="winner-text-cont">
                        <!-- <p class="winner-text">Winner</p> -->
                        <!-- later display total betting amout in place of Winner -->
                        <p class="total-bet-amt" style="background-color: #303030;height: 
                        35px;width: 280px;border-radius: 10px;margin-top: 0px;color: #ffffff;
                        display: flex;justify-content: center;align-items: center;">Winner</p>
                        <p class="total-bet-amt" id="totalWin" style="background-color: darkgrey;height: 
                        35px;width: 280px;border-radius: 10px;margin-top: 0px;color: #ffffff;
                        display: flex;justify-content: center;align-items: center;">Total Winning: 0</p>

                    </div>
                    <div class="winner-display-container">
                        <div class="winner-img-cont">
                            <img class="result-num-image" src="./images/1.png" alt="Main Image">
                        </div>
                    </div>
                </div>
                <table class="result-table">
              <!-- table data is shown here dynamically by using AJAX -->
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
        <?php
        include "./game_buttons.php";
        ?>
    </div>
    <script>
        // Update total after DOM loaded
        document.addEventListener('DOMContentLoaded', function() {
            updateTotal();
        });
    </script>
</body>

</html>