<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
require 'phpqrcode/qrlib.php';

$comName = 'PANSPRINT INFOTECH';

// Function to merge logo with QR code
function mergeLogo($qrPath, $logoPath, $outputPath)
{
    // Load QR code image
    $qrImg = imagecreatefrompng($qrPath);

    // Load logo image
    $logoImg = imagecreatefrompng($logoPath);

    // Get QR code dimensions
    $qrWidth = imagesx($qrImg);
    $qrHeight = imagesy($qrImg);

    // Get logo dimensions
    $logoWidth = '50';
    $logoHeight = '50';

    // Calculate logo position to center it on the QR code
    $x = ($qrWidth - $logoWidth) / 2;
    $y = ($qrHeight - $logoHeight) / 2;

    // Border width (adjust as needed)
    $borderWidth = 5;

    // Create a new image with the same size as the logo plus border
    $newLogoImg = imagecreatetruecolor($logoWidth + 2 * $borderWidth, $logoHeight + 2 * $borderWidth);

    // Allocate a color for the border (white in this example)
    $borderColor = imagecolorallocate($newLogoImg, 255, 255, 255);

    // Fill the entire image with the border color
    imagefilledrectangle($newLogoImg, 0, 0, $logoWidth + 2 * $borderWidth, $logoHeight + 2 * $borderWidth, $borderColor);

    // Copy the original logo onto the new image with border
    imagecopy($newLogoImg, $logoImg, $borderWidth, $borderWidth, 0, 0, $logoWidth, $logoHeight);

    // Merge QR code with logo (including the border)
    imagecopy($qrImg, $newLogoImg, $x - $borderWidth, $y - $borderWidth, 0, 0, $logoWidth + 2 * $borderWidth, $logoHeight + 2 * $borderWidth);

    // Save the final image
    imagepng($qrImg, $outputPath);

    // Free up memory
    imagedestroy($qrImg);
    imagedestroy($logoImg);
    imagedestroy($newLogoImg);
}

// Check if all required fields are set
if (
    isset($_POST['upiId'], $_POST['orderId'], $_POST['amount'], $_POST['custId']) &&
    !empty($_POST['upiId']) &&
    !empty($_POST['orderId']) &&
    !empty($_POST['amount']) &&
    !empty($_POST['custId'])
) {
    // UPI payment details
    $upiId = $_POST['upiId'];
    $orderId = $_POST['orderId'];
    $amount = $_POST['amount'];
    $custId = $_POST['custId'];
    
    // Path to the logo image
    $logoPath = 'phpqrcode/pansprint.png';

    // Output image file path in the "qrImages" folder
    $outputPath = 'qrImages/' . rand(0000000, 9999999) . 'output.png';

    // Construct UPI payment details
    $upiDetails = "upi://pay?pa=$upiId&pn=$comName&tr=$orderId&tn=$custId&am=$amount&cu=INR&mam=$amount";

    // Generate QR code
    QRcode::png($upiDetails, $outputPath, QR_ECLEVEL_L, 10);

    // Merge QR code with logo
    mergeLogo($outputPath, $logoPath, $outputPath);

    // Output JSON response with the image path
    $response = array('status' => 'success', 'qrImage' => $outputPath);
    echo json_encode($response);

    // Unlink (delete) the generated QR code image
    //unlink($outputPath);

} else {
    // If any of the required parameters are missing or empty, send an error response
    $response = array('status' => 'error', 'message' => 'Please enter the amount value.');
    echo json_encode($response);
}
?>