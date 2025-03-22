<?php
// Uncomment the following line to display errors
ini_set('display_errors', 1); // error_reporting(E_ALL);

// Include necessary files
require_once('vendor/autoload.php');
require_once('../system/connectivity_functions.php');

// Create an instance of mPDF
use Mpdf\Mpdf;
require_once('phpqrcode/qrlib.php');

// Check if access token and token are provided in the GET parameters
if (!empty($_GET['access_token']) && $_GET['token']) {
    // Decode the access token
    $vNo = base64_decode($_GET['access_token']);

    // Connect to the database
    $conn = connectDB();

    // Prepare and execute a query to fetch data based on the decoded access token
    $stmt = $conn->prepare("SELECT * FROM `printRecords` WHERE idNumber = :idNumber");
    $stmt->bindParam(':idNumber', $vNo);
    $stmt->execute();

    // Fetch the result (assuming only one row is expected)
    $result = $stmt->fetch();

    // Uncomment the following line if you want to decode printData as JSON
    $a_data = json_decode($result['printData'], true);

    // Create a SimpleXMLElement
    $simplexml = new SimpleXMLElement('<root></root>');

    // Add elements to XML
    $book = $simplexml->addChild('book');
    $printLetterBarcodeData = $book->addChild('PrintLetterBarcodeData');
    $printLetterBarcodeData->addAttribute('Regn Number', $vNo);
    $printLetterBarcodeData->addAttribute('Chassis Number', $a_data['chassis']);
    $printLetterBarcodeData->addAttribute('Engine / Motor Number', $a_data['engine']);
    $printLetterBarcodeData->addAttribute('Owner Name', $a_data['owner']);
    $printLetterBarcodeData->addAttribute('Owner Father Name', $a_data['ownerFatherName']);
    $printLetterBarcodeData->addAttribute('Address', $a_data['permanentAddress']);
    $printLetterBarcodeData->addAttribute('Date of Regn.', $a_data['regDate']);
    $printLetterBarcodeData->addAttribute('RC Expiry Date', $a_data['rcExpiryDate']);

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

    // PDF file name
    $p_name = 'PVCeAadhaar_' . date('dYmdhs') . '_' . $a_data['rcno'];

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
            <link href="css/kavyaRc.css" type="text/css" rel="stylesheet">
        </head>
        <body>
            <!-- PDF content goes here -->
            <div class="base-image-font"></div>
            <div class="base-image-back"></div>
            
            
            <!-- ************ FONT ************* -->
            <div class="vType">NT</div>
            <div class="stateShort">' . $a_data['stateShort'] . '</div>
            <div class="govtOf">Government of ' . $a_data['state'] . '</div>
            <div class="badge"><img src="css/img/holo.png" alt="applicationImage" height="33" width="40"/></div>
            <div class="chip"><img src="css/img/sim-chip.png" alt="applicationImage" height="100%" width="40"/></div>
            <div class="rgNumber">Regn. Number</div>
            <div class="rgNumberdt">' . $a_data['rcno'] . '</div>
            <div class="rgDate">Date of Regn.</div>
            <div class="rgDatedt">' . $a_data['regDate'] . '</div>
            <div class="rgValidity">Regn. Validity*</div>
            <div class="rgValiditydt">' . $a_data['rcExpiryDate'] . '</div>
            <div class="ownerSr">Owner</div>
            <div class="ownSerial">Serial</div>
            <div class="ownSerialRound">' . $a_data['ownerCount'] . '</div>
            <div class="chassisNumber">Chassis Number</div>
            <div class="chassisNumberdt">' . $a_data['chassis'] . '</div>
            <div class="engineNumber">Engine / Motor Number</div>
            <div class="engineNumberdt">' . $a_data['engine'] . '</div>
            <div class="ownerName">Owner Name</div>
            <div class="ownerNamedt">' . $a_data['owner'] . '</div>
            <div class="ownerFatherName">Son / Daughter / Wife of <span style="font-size: 7px">(In case of Individual Owner)</span></div>
            <div class="ownerFatherNamedt">' . $a_data['ownerFatherName'] . '</div>
            <div class="Address">Address</div>
            <div class="Addressdt">' . strtoupper($a_data['permanentAddress']) . '</div>
            <div class="Fuel">Fuel</div>
            <div class="Fueldt">' . strtoupper($a_data['type']) . '</div>
            <div class="emissionNorms">Emission Norms</div>
            <div class="emissionNormsdt">' . strtoupper($a_data['normsType']) . '</div>
            <div class="cardIssue">Card Issue Date (' . strtoupper($a_data['monthYearmfg']) . ')</div>
            <!-- ************ FONT END ************* -->
            
            <!-- ************ BACK ************* -->
            <div class="vehicleClass">VEHICAL CLASS : ' . strtoupper($a_data['vehicleClass']) . '</div>
            <div class="vTypeBack">NT</div>
            <div class="stateShortback">' . $a_data['stateShort'] . '</div>
            <div class="baseQrCode">' . $img . '</div>
            <div class="regnNumberBack">Regn. Number</div>
            <div class="regnNumberBackdt">' . $a_data['rcno'] . '</div>
            <div class="mfd">Month - Year of Mfg.</div>
            <div class="mfddt">' . $a_data['monthYearmfg'] . '</div>
            <div class="noCylinders">No of Cylinders : ' . $a_data['vehicleCylindersNo'] . '</div>
            <div class="Maker">Maker:</div>
            <div class="Makerdt">' . $a_data['vehicleManufacturerName'] . '</div>
            <div class="Model">Model:</div>
            <div class="Modeldt">' . $a_data['model'] . '</div>
            <div class="Color">Color:</div>
            <div class="Colordt">' . $a_data['Color'] . '</div>
            <div class="bodyType">/ Body Type</div>
            <div class="bodyTypedt">' . $a_data['bodyType'] . '</div>
            <div class="siaCapacity">Seating (in all) Capacity</div>
            <div class="siaCapacitydt">' . $a_data['vehicleSeatCapacity'] . '</div>
            <div class="wnladenWeight">Unladen Weight (Kg)</div>
            <div class="wnladenWeightdt">' . $a_data['unladenWeight'] . '</div>
            <div class="cubicCap">Cubic Cap.</div>
            <div class="cubicCapdt">' . $a_data['vehicleCubicCapacity'] . '</div>
            <div class="hpkw">/ Horse Power (BHP/Kw)</div>
            <div class="hpkwdt">NA</div>
            <div class="wbm">/ Wheel Base</div>
            <div class="wbmdt">' . $a_data['wheelbase'] . '</div>
            <div class="Financier">Financier</div>
            <div class="Financierdt">' . $a_data['rcFinancer'] . '</div>
            <div class="registrationAuthority">Registration Authority</div>
            <div class="registrationAuthoritydt">' . $a_data['regAuthority'] . '</div>
            <div class="fnamea">Form : 23A</div>
            <!-- ************ BACK END ************* -->

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
