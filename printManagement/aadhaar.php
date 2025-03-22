<?php
// Uncomment the following line to display errors
// ini_set('display_errors', 1); // error_reporting(E_ALL);

// Include necessary files
require_once('vendor/autoload.php');
require_once('../system/connectivity_functions.php');

// Create an instance of mPDF
use Mpdf\Mpdf;
require_once('phpqrcode/qrlib.php');

// Check if access token and token are provided in the GET parameters
if (!empty($_GET['access_token']) && $_GET['token']) {
    // Decode the access token
    $aadhar = base64_decode($_GET['access_token']);

    // Connect to the database
    $conn = connectDB();

    // Prepare and execute a query to fetch data based on the decoded access token
    $stmt = $conn->prepare("SELECT * FROM `printRecords` WHERE idNumber = :idNumber");
    $stmt->bindParam(':idNumber', $aadhar);
    $stmt->execute();

    // Fetch the result (assuming only one row is expected)
    $result = $stmt->fetch();

    // Uncomment the following line if you want to decode printData as JSON
    $a_data = json_decode($result['printData'], true);

    // Format Aadhaar number with spaces
    $aadharno = $aadhar;
    $spacedAadharno = "";
    for ($i = 0; $i < strlen($aadharno); $i++) {
        $spacedAadharno .= $aadharno[$i];
        if (($i + 1) % 4 == 0) {
            $spacedAadharno .= " ";
        }
    }

    // Create a SimpleXMLElement
    $simplexml = new SimpleXMLElement('<root></root>');

    // Add elements to XML
    $book = $simplexml->addChild('book');
    $printLetterBarcodeData = $book->addChild('PrintLetterBarcodeData');
    $printLetterBarcodeData->addAttribute('uid', $aadhar);

    // Generate XML string
    $xmlString = $simplexml->asXML();

    // Create mPDF instance
    $mpdf = new Mpdf();

    // Generate QR code image
    ob_start();
    QRcode::png($xmlString, null, QR_ECLEVEL_L, 10, 0, false, 0xFFFFFF, 0x000000);
    $qrCodeImage = ob_get_clean();

    // Display the QR code image
    $img = '<img src="data:image/png;base64,' . base64_encode($qrCodeImage) . '"/>';

    // Process the base64-encoded image data
    $base64_image = $a_data['imgdata'];
    $base64_image = str_replace('data:image/png;base64,', '', $base64_image);
    $image_data = base64_decode($base64_image);
    $image = imagecreatefromstring($image_data);
    $image_jpg = imagecreatetruecolor(imagesx($image), imagesy($image));
    imagecopy($image_jpg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
    ob_start();
    imagejpeg($image_jpg);
    $image_jpg_base64 = ob_get_clean();
    $image_jpg_base64 = base64_encode($image_jpg_base64);

    // PDF file name
    $p_name = 'EAadhaar_' . date('dYmdhs') . '_' . $a_data['aadharno'];

    // Random serial number
    $srno = rand(1, 9);

    // Start creating the PDF content
    $mpdf->autoScriptToLang = true;
    $mpdf->autoLangToFont = true;

    $mpdf->WriteHTML('
        <!DOCTYPE html>
        <html>
        <head>
            <title>' . $p_name . '</title>
            <link href="css/kavyaAadhaar.css" type="text/css" rel="stylesheet">
        </head>
        <body>
            <!-- PDF content goes here -->
            <div class="base-image"></div>
            <div class="enrollment"><span>नामांकन / Enrollment No.: 1429/70044/0027'. $srno.'</span></div>
            <div class="to">To</div>
            <div class="an-hindi-1">'.$a_data['namelocal'].'</div>  
            <div class="an-english-1">'.$a_data['name'].'</div> 
            <div class="address-font">'.$a_data['address'].'</div>   
            <div class="qr-code">'.$img.'</div>
            <div class="aadhar-number-1">'.$spacedAadharno.'</div>
            
            <!-----FONT------>
            <div class="aadhar-img"><img src="data:image/jpg;base64,' . $image_jpg_base64 . '" width="68" height="85"> </div>
            <div class="an-hindi">'.$a_data['namelocal'].'</div> 
            <div class="an-english">'.$a_data['name'].'</div>
            <div class="dob">'.$a_data['birthtithilocal'].' / DOB: '.$a_data['dobadhar'].'</div> 
            <div class="gender">'.$a_data['genderlocal'].' / '.$a_data['gender'].'</div>
            <div class="download-date">Aadhaar no. issued: '.date("d/m/y").'</div>
            <div class="aadhar-number-2">'.$spacedAadharno.'</div>
            
            <!-----BACK------>
            
            <div class="pata">'.$a_data['patalocal'].':</div>
            <div class="pata-eng">Address:</div>
            <div width="180" class="address-back-eng">'.$a_data['address'].'</div>
            <div width="180" class="address-back-hnd">'.$a_data['addresslocal'].'</div>
            <div class="qr-code-back">'.$img.'</div>
            <div class="aadhar-number-b">'.$spacedAadharno.'</div>

            <!-- Add more content as needed -->

        </body>
        </html>'
    );

    // Output the PDF with a specific name
    $mpdf->Output("$p_name.pdf", 'D');
} else {
    // Display an unauthorized access message
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
                <a href="/" class="btn btn-primary mb-4">Back to home</a>
            </div>
        </div>
        <!-- /Not Authorized -->
    </body>
    </html>';
}
?>
