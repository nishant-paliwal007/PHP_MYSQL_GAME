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
    </script>
</head>

<body>

    <div class="game-container">
        <div class="balance-result-container">
            <div class="points-time-cont">
                <div class="balance-container">
                    <p class="points-text">POINTS</p>
                    <p class="balance">1000</p>
                </div>

                <div class="time-container">
                    <p class="next-result-time">12:00 pm</p>
                    <p class="running-time">running time</p>
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
                    <tr>
                        <td>
                            <div>
                                <span>12:00pm</span>
                                <img class="result-num-img-tb" src="./images/1.png" alt="">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div>
                                <span>12:05pm</span>
                                <img class="result-num-img-tb" src="./images/2.png" alt="">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div>
                                <span>12:10pm</span>
                                <img class="result-num-img-tb" src="./images/3.png" alt="">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div>
                                <span>12:15pm</span>
                                <img class="result-num-img-tb" src="./images/4.png" alt="">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div>
                                <span>12:20pm</span>
                                <img class="result-num-img-tb" src="./images/5.png" alt="">
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>



        <div class="betting-points-buttons">
            <div class="bet-amt-buttons">
                <button class="bet-amt-btn"><img src="./images/ten.png" class="bet-amt-image" alt=""></button>
                <button class="bet-amt-btn"><img src="./images/twenty.png" class="bet-amt-image" alt=""></button>
                <button class="bet-amt-btn"><img src="./images/fifty.png" class="bet-amt-image" alt=""></button>
                <button class="bet-amt-btn"><img src="./images/eleven.png" class="bet-amt-image" alt=""></button>
            </div>

            <div class="bet-amt-buttons">
                <button class="bet-amt-btn"><img src="./images/200.png" class="bet-amt-image" alt=""></button>
                <button class="bet-amt-btn"><img src="./images/fiveh.png" class="bet-amt-image" alt=""></button>
                <button class="bet-amt-btn"><img src="./images/800.png" class="bet-amt-image" alt=""></button>
                <button class="bet-amt-btn"><img src="./images/34.png" class="bet-amt-image" alt=""></button>
            </div>
        </div>

        <div class="betting-numbers-container">
            <div class="bet-nums-cont">
                <div class="img-input-cont">
                    <img class="img" src="./images/1.png" alt="1">
                    <input class="input" type="text" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/2.png" alt="2">
                    <input class="input" type="text" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/3.png" alt="3">
                    <input class="input" type="text" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/4.png" alt="4">
                    <input class="input" type="text" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/5.png" alt="5">
                    <input class="input" type="text" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/6.png" alt="6">
                    <input class="input" type="text" value="">
                </div>
            </div>

            <div class="bet-nums-cont">
                <div class="img-input-cont">
                    <img class="img" src="./images/7.png" alt="7">
                    <input class="input" type="text" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/8.png" alt="8">
                    <input class="input" type="text" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/9.png" alt="9">
                    <input class="input" type="text" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/10.png" alt="10">
                    <input class="input" type="text" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/11.png" alt="11">
                    <input class="input" type="text" value="">
                </div>
                <div class="img-input-cont">
                    <img class="img" src="./images/12.png" alt="12">
                    <input class="input" type="text" value="">
                </div>
            </div>
        </div>

        <div class="buttons-container">

            <button class="image-button" type="button">
                <img src="./images/button.png" alt="Button Image">
                <span class="button-text">Bet Ok</span>
            </button>

            <button class="image-button" type="button">
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
                <span class="button-text"></span>
            </button>
        </div>
    </div>

</body>

</html>