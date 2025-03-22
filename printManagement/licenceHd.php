<?php

require_once('vendor/autoload.php');
require_once('../system/connectivity_functions.php');

use Mpdf\Mpdf;

require_once('phpqrcode/qrlib.php');

$mpdf = new Mpdf();
$mpdf->autoScriptToLang = true;
$mpdf->autoLangToFont = true;

if(!empty($_GET['token']) && $_GET['token']){
// Retrieve the 'dlno' value from the database

    // Assuming you want to use $_GET['access_token'] instead of $_GET['access_token']
    $access_token = base64_decode($_GET['access_token']);

    $stmt = $conn->prepare("SELECT * FROM `printRecords` WHERE idNumber = :idNumber");
    $stmt->bindParam(':idNumber', $access_token);
    $stmt->execute();
    $result = $stmt->fetch(); // Assuming only one row is expected

    $dl_data = json_decode($result['printData'], true);

if ($dl_data['typeofvehicle'] == "MCWG,LMV,TRANS") {
    $truck = '
        <div class="validityNtxt">Validity (NT)</div>
        <div class="validityNtDate">' . date('d/m/Y', strtotime($dl_data['expirydate'])) . '</div>
        <div class="validityTrtxt">Validity (TR)</div>
        <div class="validityTrDate">' . date('d/m/Y', strtotime($dl_data['transportexpirydate'])) . '</div>';
        $table = '<div class="table">
            <table>
                <tr>
                    <th>COV Category</th>
                    <th>Class of Vehicle</th>
                    <th>COV Issue Date</th>
                </tr>
                <tr>
                    <td><img src="css/img/bick.png" alt="Hologram" height="15px" width="15px"/></td>
                    <td>MCWG</td>
                    <td>' . date('d/m/Y', strtotime($dl_data['idate'])) . '</td>
                </tr>
                <tr>
                    <td><img src="css/img/car-v.png" alt="Hologram" height="15px" width="20px"/></td>
                    <td>LMV</td>
                    <td>' . date('d/m/Y', strtotime($dl_data['idate'])) . '</td>
                </tr>
                <tr>
                    <td><img src="css/img/truck-v.png" alt="Hologram" height="15px" width="25px"/></td>
                    <td>TRANS</td>
                    <td>' . date('d/m/Y', strtotime($dl_data['transportexpirydate'])) . '</td>
                </tr>
            </table>
        </div>';
} else if ($dl_data['typeofvehicle'] == "MCWG,LMV") {
    $truck = '
        <div class="validityNtxt">Validity (NT)</div>
        <div class="validityNtDate">' . date('d/m/Y', strtotime($dl_data['expirydate'])) . '</div>';
        $table = '<div class="table-nt">
            <table>
                <tr>
                    <th>COV Category</th>
                    <th>Class of Vehicle</th>
                    <th>COV Issue Date</th>
                </tr>
                <tr>
                    <td><img src="css/img/bick.png" alt="Hologram" height="15px" width="15px"/></td>
                    <td>MCWG</td>
                    <td>' . date('d/m/Y', strtotime($dl_data['idate'])) . '</td>
                </tr>
                <tr>
                    <td><img src="css/img/car-v.png" alt="Hologram" height="15px" width="20px"/></td>
                    <td>LMV</td>
                    <td>' . date('d/m/Y', strtotime($dl_data['idate'])) . '</td>
                </tr>
            </table>
        </div>';
} else if ($dl_data['typeofvehicle'] == "3W-CAB,LMV-TR,MCWG") {
    $truck = '
        <div class="validityNtxt">Validity (NT)</div>
        <div class="validityNtDate">' . date('d/m/Y', strtotime($dl_data['expirydate'])) . '</div>';
        $table = '<div class="table-cab">
            <table>
                <tr>
                    <th>COV Category</th>
                    <th>Class of Vehicle</th>
                    <th>COV Issue Date</th>
                </tr>
                <tr>
                    <td><img src="css/img/bick.png" alt="Hologram" height="15px" width="15px"/></td>
                    <td>MCWG</td>
                    <td>' . date('d/m/Y', strtotime($dl_data['idate'])) . '</td>
                </tr>
                <tr>
                    <td><img src="css/img/car-v.png" alt="Hologram" height="15px" width="20px"/></td>
                    <td>LMV-TR</td>
                    <td>' . date('d/m/Y', strtotime($dl_data['idate'])) . '</td>
                </tr>
                <tr>
                    <td><img src="css/img/3w-cab.png" alt="Hologram" height="15px" width="20px"/></td>
                    <td>3W-CAB</td>
                    <td>' . date('d/m/Y', strtotime($dl_data['idate'])) . '</td>
                </tr>
            </table>
        </div>';
}
// Link to be encoded in the QR code
$link = 'https://sarathi.parivahan.gov.in/sarathiservice/rsServices/sarathi/QRService/DLDetails/dlqrresult?dlnum=' . base64_encode($dl_data['dlno']) . '&dob=' . base64_encode($dl_data['dob']);

// Generate the QR code image with the specified size
ob_start();
QRcode::png($link, null, QR_ECLEVEL_L, 10, 0, false, 0xFFFFFF, 0x000000);
$qrCodeImage = ob_get_clean();

// Display the QR code image
$qrImg = '<img src="data:image/png;base64,' . base64_encode($qrCodeImage) . '"/>';

// Define a mapping of full state names to short codes
$stateMappings = array(
    'Andhra Pradesh' => 'AP',
    'Arunachal Pradesh' => 'AR',
    'Assam' => 'AS',
    'Bihar' => 'BR',
    'Chhattisgarh' => 'CG',
    'Chandigarh' => 'CH',
    'Goa' => 'GA',
    'Gujarat' => 'GJ',
    'Haryana' => 'HR',
    'Himachal Pradesh' => 'HP',
    'Jharkhand' => 'JH',
    'Karnataka' => 'KA',
    'Kerala' => 'KL',
    'Madhya Pradesh' => 'MP',
    'Maharashtra' => 'MH',
    'Manipur' => 'MN',
    'Meghalaya' => 'ML',
    'Mizoram' => 'MZ',
    'Nagaland' => 'NL',
    'Odisha' => 'OD',
    'Punjab' => 'PB',
    'Rajasthan' => 'RJ',
    'Sikkim' => 'SK',
    'Tamil Nadu' => 'TN',
    'Telangana' => 'TG',
    'Tripura' => 'TR',
    'Uttar Pradesh' => 'UP',
    'Uttarakhand' => 'UK',
    'West Bengal' => 'WB',
    // Add more states as needed
);
$fullState = $dl_data['state'];
// Check if the state is in the mapping, and if yes, get the short code
if (isset($stateMappings[$fullState])) {
    $stateShortCode = $stateMappings[$fullState];
} else {
    // Default to the full state name if no mapping is found
    $stateShortCode = $fullState;
}
$mpdf->imageVars['photo'] = file_get_contents($dl_data['photo']);
$mpdf->imageVars['sign'] = file_get_contents($dl_data['sign']);

// Add a space between the word and number using regular expression
$dlnoWithSpace = substr_replace($dl_data['dl_no'], ' ', 4, 0);
// Construct the HTML content
$html = '
<!DOCTYPE html>
<html>
<head>
  <title>DRIVING LICENCE - '.$dl_data['name'].'</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-source-segoe-ui@2.0.1/css/all.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;700&display=swap">
  <link rel="stylesheet" href="css/kavyaLicence.css">
</head>
<body>
<div class="base-imageFont"></div>
<div class="base-imageBack"></div>
<div class="satymevFont"> <img src="css/img/satymev_j.png" alt="applicationImage" height="170" width="100" style="opacity: 0.1;"/> </div>
<div class="holoGram"> <img src="css/img/holo.png" alt="applicationImage" height="40" width="40"/> </div>
<div class="state-name">Issued by Government of '.$dl_data['state'].'</div>
<div class="state-short">'.$stateShortCode.'</div>
<div class="dlNo-font">'.$dlnoWithSpace.'</div>
<div class="issueTxt">Issue Date</div>
<div class="issueDate">'.date('d/m/Y', strtotime($dl_data['idate'])).'</div>

'.$truck.'

<div class="applicationImage"> <img src="var:photo" alt="applicationImage" height="84px" width="72px"/> </div>
<div class="applicationSign"> <img src="var:sign" alt="Signature" height="20px" width="50px" style="filter: brightness(0) invert(1) sepia(1) saturate(5) hue-rotate(175deg);"> </div>
<div class="download-date">Date of first issued: '.date("d/m/y").'</div>
<div class="name">'.$dl_data['name'].'</div>
<div class="birth">'.date('d/m/Y', strtotime($dl_data['dob'])).'</div>
<div class="bloodG">'.$dl_data['bgroup'].'</div>
<div class="hofName">'.$dl_data['fathername'].'</div>
<div class="address">'.$dl_data['address'].' - '.$dl_data['pincode'].'</div>



<!-- Licence back -->
<div class="satymevBack"> <img src="css/img/satymev_j.png" alt="applicationImage" height="170" width="100" style="opacity: 0.1;"/> </div>
<div class="dlNo-back">DL No: '.$dlnoWithSpace.'</div>
<div class="qr-code">'.$qrImg.'</div>
<div class="regnNo">ADPVEH No.(Regn.Numbers)</div>
<div class="Hazardous">Hazardous Validity</div>
<div class="Hill">Hill Validity</div>

'.$table.'
<div class="ruleNo">Form 7 rule 16 (2)</div>
<div class="lauthReg">Licensing Authority</div>
<div class="lAuth">'.$dl_data['rto'].'</div>
<div class="verifiedTxt">Verified</div>
<div class="verifiedImg"><img src="css/img/approved-certified.png" alt="Verified icon" height="20px" width="20px"/></div>
<div class="note">This is not an official government-issued card</div>

</body>
</html>';

// Add the HTML content to the PDF
$mpdf->WriteHTML($html);

// Set password for the PDF
//$password = strtoupper(str_replace(' ', '', substr($b['votername'], 0, 5))) . $b['pincode'];
//$mpdf->SetProtection(array(), $password);
$pdfName = "DRIVING LICENCE - " . $dl_data['name'] . " - " . $dlnoWithSpace;

// Generate the PDF
$mpdf->Output($pdfName . ".pdf", 'D');

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