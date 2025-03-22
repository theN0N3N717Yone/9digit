<?php
//ini_set('display_errors', 1); error_reporting(E_ALL);
require_once('vendor/autoload.php');
require_once('phpqrcode/qrlib.php');
require_once('../system/connectivity_functions.php');

use Mpdf\Mpdf;

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
    

$iparr = explode (" ",$result['kanamelocal']); 
$aaaa =  $iparr[1];
$epic1 = $result['epicno']; // Replace this with the appropriate value

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


// Set the desired width and height of the barcode image
$epic2 = password_hash($result['epicno'], PASSWORD_DEFAULT);
// Generate the barcode image with the specified size
ob_start();
QRcode::png($epic2, null, QR_ECLEVEL_L, 10, 0, false, 0xFFFFFF, 0x000000);
$qrCodeImage = ob_get_clean();

// Display the QR code image
$barcodeImage = '<img src="data:image/png;base64,' . base64_encode($qrCodeImage) . '" height="172" widht="172">';

$barback = '<img src="data:image/png;base64,' . base64_encode($qrCodeImage) . '" height="82" widht="82">';


$name = str_replace("/", "_", $epic1); // Convert "/" to "_" in the name
$title = "e-EPIC_" . $name . "_" . date('Ymd');


// Function to apply blur effect to an image using GD library
function applyBlurToImage($imagePath, $outputPath, $blurAmount) {
    $image = imagecreatefromjpeg($imagePath);

    // Apply the blur filter multiple times for a stronger effect
    for ($i = 0; $i < $blurAmount; $i++) {
        imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);
    }

    imagejpeg($image, $outputPath);
    imagedestroy($image);
}

// Blur the voter image and save it to a temporary location
$blurAmount = 20; // Adjust the blur amount as needed
$originalImagePath = 'data:image/jpg;base64,'.$image_jpg_base64; // Replace with the path to your voter image
$tempImagePath = "Png/temp_image.jpg"; // Replace with the temporary path where the blurred image will be saved
applyBlurToImage($originalImagePath, $tempImagePath, $blurAmount);



// Construct the HTML content
$html = '
<!DOCTYPE html>
<html>
<head>
<title>'.$title.'</title>
<link href="css/kavya-voter-new.css" type="text/css" rel="stylesheet">
<style>

.state_url{
    position: absolute;
    top: 318px;
    left: 535px;
    font-size: 8px;
    font-weight: normal;
    width: 200px;
    font-family: ayar, Arial, sans-serif;
}
</style>
</head>
<body>
<img style="position: absolute; top: 0; left: 0; width: 1056px; height: 1056px; overflow: visible; padding: -60;" src="css/img/voter-kavya.jpg" alt="background image"/>

<!----************font************---->

<div class="font-epic-no1">'.$result['epicno'].'</div>
<div class="voter-image"><img src="data:image/jpg;base64,' . $image_jpg_base64 . '" alt="Voter Image" height="109px" width="81px"></div>
<div class="voter-image-rt"><img src="'.$tempImagePath.'" alt="Voter Image"></div>
<p class="ul">
<ul style="list-style: none; padding-left: 0;">
<li style="">'.$aaaa.': '. $result['namelocal'] .'</li>
<li style="">Name : '.strtoupper(substr($result['name'], 0, 1)) . strtolower(substr($result['name'], 1)).'</li>
<li style="">'.$result['spousenamelocal'].' '.$result['kanamelocal'].' : '.$result['fathernamelocal'].'</li>
<li style="">'.ucfirst($result['father/husband']).' s Name : '.strtoupper(substr($result['fathername'], 0, 1)) . strtolower(substr($result['fathername'], 1)).'</li>
<li style="">'.$result['sexlocal'].' / Gender : '.$result['genderlocal'].' / '.ucfirst($result['gender']).'</li>
<li style="">'.$result['birthtithilocal'].'</li>
<li style="">Date of Birth / Age : '.$result['dobadhar'].'</li>
</ul>
</p>
<div class="epic_ltr">'.$result['epicno'].'</div>

<!----************back************---->


<p class="ul-back">
<ul style="list-style: none; padding-left: 0; flex: 1;">
<li style="">'.$result['patalocal'].' : '.$result['addresslocal'].' -'.$result['pincode'].'</li>
<li style="">Address : HNo. '.strtoupper($result['address']).' - '.$result['pincode'].'</li>
</ul>
</p>
<div class="assembly-hindi">निर्वाचक रजिस्ट्रीकरण अधिकारी, '.$result['assemblyconnamenolocal'].'</div>
<div class="assembly-english">Electoral Registration Officer, '.strtoupper($result['assemblyconnameno']).'</div>
<div class="download-date">Download Date. '.date("d-m-Y").'</div>
<div class="epic_back">'.$result['epicno'].'</div>
<div class="br-back">'.$barback.'</div>
<div class="state_url">https://ceo'.strtolower($result['statename']).'.nic.in/</div>
<!----************down***********---->


<p class="ul_down">
<ul style="list-style: none; padding-left: 0; flex: 1;">
<li style="margin-bottom: 10px;">'.strtoupper($result['epicno']).'</li>
<li></li>
<li style="margin-top: 10px;">'.rand(000,999).'</li>
<li></li>
<li style="margin-top: 10px;"><b>'.$result['assemblyconnamenolocal'].'</b></li>
<li></li>
<li style="margin-top: 11px;">'.$result['assemblyconnameno'].'</li>
<li></li>
<li style="margin-top: 10px;">'.strtoupper($result['partno']).' '.strtoupper($result['partnamelocal']).'</li>
<li></li>
<li style="margin-top: 10.8px;">'.strtoupper($result['partno']).' '.strtoupper($result['partname']).'</li>
<li></li>
<li style="margin-top: 11.2px;">राजकीय उच्च माध्यमिक विद्यालय, '.strtoupper($result['partnamelocal']).'</li>  
<li></li>
<li style="margin-top: 11.4px;">GOVT SENIOR SECONDARY SCHOOL, '.strtoupper($result['partname']).'</li>
</ul>
</p>
<div class="download-down">'.date("d-m-Y").'</div>
<div class="br-down">'.$barcodeImage.'</div>
</body>
</html>
';

// Add the HTML content to the PDF

$mpdf->WriteHTML($html);

// Generate the PDF
$mpdf->Output("$title.pdf", 'I');

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