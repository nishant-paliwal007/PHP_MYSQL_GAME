<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Buttons</title>
</head>

<body>
    <div class="buttons-container">
        <form id="betAmountForm" method="POST" action="bet_ok.php">
            <button onclick="betOk()" class="image-button" type="button">
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

</body>

</html>