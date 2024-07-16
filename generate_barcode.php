<?php
function generateBarcode() {
    $barcode = "";
    for($i = 0; $i < 10; $i++) {
        $barcode .= (string) rand(0, 9);
    }

    echo $barcode;
}

generateBarcode();
?>