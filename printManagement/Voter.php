<?php
require_once('vendor/autoload.php');
require_once('spliteQR/autoload.php');
require_once('../system/connectivity_functions.php');

use Mpdf\Mpdf;
use Picqer\Barcode\BarcodeGeneratorPNG;

$mpdf = new Mpdf();

$mpdf->autoScriptToLang = true;
$mpdf->autoLangToFont = true;
if(!empty($_GET['access_token']) && $_GET['token']){


$epicNo = base64_decode($_GET['access_token']);
$conn = connectDB();

$stmt = $conn->prepare("SELECT * FROM `printRecords` WHERE idNumber = :idNumber");
$stmt->bindParam(':idNumber', $epicNo);
$stmt->execute();
$a_data = $stmt->fetch(); // Assuming only one row is expected

// Uncomment the following line if you want to decode printData as JSON
$result = json_decode($a_data['printData'], true);

$iparr = explode (" ", $result['kanamelocal']); 
$aaaa =  $iparr[1];
$epic1 = $result['epicno']; // Replace this with the appropriate value
$generator = new BarcodeGeneratorPNG();

// Set the desired width and height of the barcode image

// Generate the barcode image with the specified size
$barcodeData = $generator->getBarcode($epic1, BarcodeGeneratorPNG::TYPE_CODE_128, 1, 16);
$barcodeImage = '<img src="data:image/png;base64,' . base64_encode($barcodeData) . '" height="15px" width="105px" >';

$base64_image = $result['image_data']; // Replace this with your base64-encoded image data

// Remove data:image/png;base64, from the base64 string
$base64_image = str_replace('data:image/jpeg;base64,', '', $base64_image);

// Decode the base64 string
$image_data = base64_decode($base64_image);
// Remove data:image/png;base64, from the base64 string
$base64_image = str_replace('data:image/jpeg;base64,', '', $base64_image);

// Decode the base64 string
$image_data = base64_decode($base64_image);

// Create an image resource from the decoded data
$image = imagecreatefromstring($image_data);

// Create a new image in JPG format
$image_jpg = imagecreatetruecolor(imagesx($image), imagesy($image));
imagecopy($image_jpg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));

// Output the new JPG image as base64
ob_start();
imagejpeg($image_jpg);
$image_jpg_base64 = ob_get_clean();
$image_jpg_base64 = base64_encode($image_jpg_base64);



$name = str_replace("/", "_", $epic1); // Convert "/" to "_" in the name
$title = "e-EPIC_" . $name . "_" . date('Ymd');

// Construct the HTML content
$html = '
<!DOCTYPE html>
<html>
<head>
  <title>'.$title.'</title>
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="css/kavya-voter-old.css" type="text/css" rel="stylesheet">
</head>
<body>

<div class="base-image"></div>
<div class="epic-no">'.$result['epicno'].'</div>
<div class="qr-image">'.$barcodeImage.'</div>

<div class="voter-image"><img src="data:image/jpg;base64,' . $image_jpg_base64 . '" alt="Voter Image" height="125px" width="95px"></div>
<div class="name-hindi">'.$aaaa.' : '.$result['namelocal'].'</div>
<div class="name-english">NAME : '.$result['name'].'</div>
<div class="father-name-hindi">'.$result['spousenamelocal'].' '.$result['kanamelocal'].' : '.$result['fathernamelocal'].'</div>
<div class="father-name-english">'.strtoupper($result['father/husband']).' NAME : '.mb_strtoupper($result['fathername'], 'UTF-8').'</div>

<!----------------------BACK--------------------->

<div class="gender">'.$result['sexlocal'].' / Sex</div>
<div class="gender-type">: '.$result['genderlocal'].' / '.strtoupper($result['gender']).'</div>
<div class="bob-hindi">'.$result['birthtithilocal'].'</div>
<div class="bob-english">Date Of Birth / Age</div>
<div class="years">: '.$result['dobadhar'].' Years</div>
<div class="address-hindi">'.$result['patalocal'].' : म. नं '.$result['addresslocal'].'</div>
<div class="address-english">Address : HNo. '.strtoupper($result['address']).'</div>
<div class="signature">
    <img src="css/img/signature.png" alt="Signature" height="30px" width="90px">
</div>
<div class="officer-hindi">'.$result['signlocal'].'</div>
<div class="officer-english">Electoral Registration Officer</div>
<div class="download-date">Date : '.date("d/m/Y").'</div>
<div class="assembly-hindi">'.$result['assconnamenolocal'].' : '.$result['assemblyconnamenolocal'].'</div>
<div class="assembly-english">'.strtoupper('Assembly Constituency No. & Name').' : '.strtoupper($result['assemblyconnameno']).'</div>
<div class="port-hindi">'.strtoupper($result['partnoandnamelocal']).' : '.strtoupper($result['partno']).' '.strtoupper($result['partnamelocal']).'</div>
<div class="port-english">PORT NO. AND NAME : '.strtoupper($result['partno']).'  '.strtoupper($result['partname']).'</div>
</body>
</html>';

// Add the HTML content to the PDF
$mpdf->WriteHTML($html);

// Generate the PDF
$pdfFilePath = $title;
$mpdf->Output($pdfFilePath, 'I');


}else{
echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Unauthorized Access</title>
        <link href="css/unauthorized.css" type="text/css" rel="stylesheet">
    </head>
    <body>
    <!-- Not Authorized -->
    <div class="container container-p-y">
      <div class="misc-wrapper">
        <h1 class="mb-1 mx-2">You are not authorized!</h1>
        <p class="mb-4 mx-2">You do not have permission to view this page using the credentials that you have provided while login. <br> Please contact your site administrator.</p>
        <a href="\" class="btn btn-primary mb-4">Back to home</a>
      </div>
    </div>
    <!-- /Not Authorized -->
    </body>
    </html>';
}