<?php
require_once('vendor/autoload.php');
require_once('../system/connectivity_functions.php');

use Mpdf\Mpdf;

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

if($dl_data['typeofvehicle']=="MCWG,LMV,TRANS"){
    $mcwg = "MCWG";
    $lmv = "LMV";
    $trans = "TRANS";
    $truck = '<img src="assets/img/truck-v.png" alt="Hologram" height="35px" width="50px"/>';
    $nt = '<div class="class-a">T</div>
    <div class="t-nt">NT</div>
    <div class="expirydate">'.date('d/m/Y', strtotime($dl_data['expirydate'])).'</div>
    <div class="transportexpirydate">'.date('d/m/Y', strtotime($dl_data['transportexpirydate'])).'</div>
    <div class="UP-class-C">&nbsp;NT+T&nbsp;</div>
    <div class="trans-date">'.date('d/m/Y', strtotime($dl_data['idate'])).'</div>
    <div class="mcwg-date-1">'.date('d/m/Y', strtotime($dl_data['idate'])).'</div>
    <div class="mcwg-date-2">'.date('d/m/Y', strtotime($dl_data['idate'])).'</div>
    ';
}else{
    $mcwg = "MCWG";
    $lmv = "LMV";
    $t = '<div class="class-a">NT</div>
    <div class="expirydate">'.date('d/m/Y', strtotime($b['expirydate'])).'</div>
    <div class="UP-class-C">&nbsp;NT&nbsp;</div>
    <div class="mcwg-date-1">'.date('d/m/Y', strtotime($dl_data['idate'])).'</div>
    <div class="mcwg-date-2">'.date('d/m/Y', strtotime($dl_data['idate'])).'</div>';
}
$mpdf->imageVars['photo'] = file_get_contents($dl_data['photo']);

// Add a space between the word and number using regular expression
$dlnoWithSpace = substr_replace($dl_data['dlno'], ' ', 4, 0);
// Construct the HTML content
$html = '
<!DOCTYPE html>
<html>
<head>
  <title>DRIVING LICENCE - '.$dl_data['name'].'</title>
<style>
    body {
     font-family: Helvetica, Lucida Sans, Franklin Gothic Medium, Arial;
}
 .base-image {
     background: url("assets/img/dl-PhotoRoom.jpg") no-repeat;
     position: absolute;
     width: 400px;
     height: 900px;
     left: 25%;
     top: 20%;
     transform: translate(-50%, -50%);
     overflow: visible;
     display: block;
     background-size: 100% auto;
}
 .dl-no{
     position: absolute;
     top: 277px;
     left: 297px;
     font-size: 13px;
     width: 200px;
     letter-spacing: 2px;
     font-family: Work Sans;
}
 .date-of-issue-hindi{
     position: absolute;
     top: 307px;
     left: 319px;
     font-size: 9px;
     width: 200px;
     color: #1a21a3;
}
 .date-of-issue-eng{
     position: absolute;
     top: 315px;
     left: 319px;
     font-size: 9px;
     width: 200px;
     color: #1a21a3;
}
 .issue-date{
     position: absolute;
     top: 325px;
     left: 319px;
     font-size: 11px;
     width: 200px;
}
 .dob-hindi{
     position: absolute;
     top: 346px;
     left: 319px;
     font-size: 9px;
     width: 200px;
     color: #1a21a3;
}
 .dob-eng{
     position: absolute;
     top: 355px;
     left: 319px;
     font-size: 9px;
     width: 200px;
     color: #1a21a3;
}
 .dob{
     position: absolute;
     top: 365px;
     left: 319px;
     font-weight: 300px;
     font-size: 11px;
     width: 200px;
}
 .validity-hi {
     position: absolute;
     top: 307px;
     left: 410px;
     font-size: 9px;
     width: 200px;
     color: #1a21a3;
}
 .class-a {
     position: absolute;
     top: 317px;
     left: 395px;
     font-weight: bold;
     width: 8px;
     height: 8px;
     border: 1px solid black;
     border-radius: 50%;
     padding: 1px;
     background-color: transparent;
     text-align: center;
}
 .class-b {
     position: absolute;
     top: 331px;
     left: 395px;
     font-weight: bold;
     width: 8px;
     height: 8px;
     border: 2px solid black;
     border-radius: 50%;
     padding: 1px;
     background-color: transparent;
     text-align: center;
}
 .t-nt {
     position: absolute;
     top: 331px;
     left: 395px;
     font-weight: bold;
     width: 8px;
     height: 8px;
     border: 1px solid black;
     border-radius: 50%;
     padding: 1px;
     background-color: transparent;
     text-align: center;
}
 .UP-class-b {
     position: absolute;
     top: 240px;
     left: 505px;
     font-weight: bold;
     width: 21px;
     height: 21px;
     border: 2px solid black;
     border-radius: 50%;
     padding: 1px;
     background-color: transparent;
     text-align: center;
}
 .UP-class-C {
     position: absolute;
     top: 240px;
     left: 537px;
     font-weight: bold;
     width: auto;
     height: 21px;
     border: 2px solid black;
     border-radius: 50%;
     padding: 1px;
     background-color: transparent;
     text-align: center;
}
 .expirydate {
     position: absolute;
     top: 316px;
     left: 408px;
     font-weight: 300px;
     font-size: 11px;
     width: 200px;
}
 .transportexpirydate {
     position: absolute;
     top: 330px;
     left: 408px;
     font-weight: 300px;
     font-size: 11px;
     width: 200px;
}
 .b-group{
     position: absolute;
     top: 346px;
     left: 410px;
     font-size: 9px;
     width: 200px;
     color: #1a21a3;
}
 .blood-group{
     position: absolute;
     top: 356px;
     left: 408px;
     font-weight: 300px;
     font-size: 11px;
     width: 200px;
}
 .name {
     position: absolute;
     top: 402px;
     left: 238px;
     font-weight: bold;
     font-size: 11px;
     width: 200px;
}
 .swd {
     position: absolute;
     top: 445px;
     left: 238px;
     font-weight: bold;
     font-size: 11px;
     width: 200px;
}
 .hologram-image {
     position: absolute;
     bottom: 392px;
     left: 495px;
}
 .dl-back {
     position: absolute;
     bottom: 451px;
     left: 217px;
     font-size: 13px;
     width: 200px;
     letter-spacing: 2px;
     font-family: Work Sans;
}
 .dl-dm {
     position: absolute;
     bottom: 456px;
     left: 505px;
     font-size: 13px;
     width: 200px;
     letter-spacing: -1px;
     color: #7b8494;
}
 .mcwg-date-1 {
     position: absolute;
     bottom: 399px;
     left: 215px;
     font-size: 11px;
     width: 200px;
}
 .mcwg-date-2 {
     position: absolute;
     bottom: 399px;
     left: 285px;
     font-size: 11px;
     width: 200px;
}
 .trans-date {
     position: absolute;
     bottom: 399px;
     left: 355px;
     font-size: 11px;
     width: 200px;
}
 .address-hindi {
     position: absolute;
     bottom: 338px;
     left: 216px;
     font-size: 9px;
     width: 200px;
     color: #1a21a3;
}
 .address {
     position: absolute;
     bottom: 308px;
     left: 216px;
     font-size: 11px;
     width: 200px;
}
 .signature{
     position: absolute;
     bottom: 270px;
     left: 235px;
     overflow: visible;
     display: block;
}
 .dl-image{
     position: absolute;
     top: 275px;
     left: 486;
}
 .card-chip{
     position: absolute;
     top: 288px;
     left: 220px;
}
 .bick-image{
     position: absolute;
     bottom: 416;
     left: 210;
}
 .car-image{
     position: absolute;
     bottom: 410;
     left: 280;
}
 .truck-image{
     position: absolute;
     bottom: 414;
     left: 354;
}
 .doc-signature{
     position: absolute;
     bottom: 275;
     left: 455;
}
 .state-round{
     position: absolute;
     bottom: 365;
     left: 517;
     font-weight: bold;
     width: 21px;
     height: 21px;
     border: 2px solid black;
     border-radius: 50%;
     padding: 1px;
     background-color: transparent;
     text-align: center;
}

  </style>
</head>
<body>
<div class="base-image"></div>
<div style="position: absolute; top: 248; left: 297; font-size: 11px; width: 220px; ">UNION OF INDIA <span style="font-size: 16px"><b>Driving Licence</b></span></div>
<div class="UP-class-b">'.$dl_data['state'].'</div>
<div class="card-chip">
<img src="assets/img/card-chip-ad.png" alt="Card Chip" height="100px" width="100px">
</div>
<div class="dl-no">'.$dlnoWithSpace .'</div>
<div class="date-of-issue-hindi">जारी करने की तिथि</div>
<div class="date-of-issue-eng">Date of Issue</div>
<div class="issue-date">'.date('d/m/Y', strtotime($dl_data['edate'])).'</div>
<div class="dob-hindi">जन्म तिथि</div>
<div class="dob-eng">Date of Birth</div>
<div class="dob">'.date('d/m/Y', strtotime($dl_data['dob'])).'</div>
<div class="validity-hi">वैधता / Validity</div>
'.$t.'
'.$nt.'
<div class="b-group">Blood Group</div>
<div class="blood-group">'.$dl_data['bgroup'].'</div>
<div style="position: absolute; top: 378px; left: 238px; font-size: 9px; width: 200px; color: #1a21a3; ">नाम / Name</div>
<div class="name">'.$dl_data['name'].'</div>
<div style="position: absolute; top: 422px; left: 238px; font-size: 9px; width: 200px; color: #1a21a3; ">पिता/पति का नाम / Son/Daughter/Wife of</div>
<div class="swd">'.$dl_data['fathername'].'</div>
<div class="dl-image">
<img src="var:photo" alt="DL Image" height="114px" width="89px"/>
</div>
<!----------------------BACK------------------------>
<div class="bick-image">
<img src="assets/img/bick.png" alt="Hologram" height="35px" width="35px"/>
</div>
<div style="position: absolute; bottom: 410; left: 215; font-size: 9px; width: 200px; ">'.$mcwg.'</div>
<div class="car-image">
<img src="assets/img/car-v.png" alt="Hologram" height="40px" width="55px"/>
</div>
<div style="position: absolute; bottom: 410; left: 287; font-size: 9px; width: 200px; ">'.$lmv.'</div>
<div class="truck-image">
'.$truck.'
</div>
<div style="position: absolute; bottom: 410; left: 357; font-size: 9px; width: 200px; ">'.$trans.'</div>
<div class="hologram-image">
<img src="assets/img/hologram-stick.png" alt="Hologram" height="80px" width="80px"/>
</div>
<div class="state-round">'.$dl_data['state'].'</div>
<div class="dl-back">'.$dlnoWithSpace .'</div>
<div class="dl-dm">D'.rand(11111111,99999999).'M</div>
<div class="address-hindi">पता / Permanent Address</div>
<div class="address">'.$dl_data['address'].' - '.$dl_data['pincode'].'</div>
<div class="signature">
<img src="https://pansprint.in/downloads.php?files='.$dl_data['dlno'].'.png" alt="Signature" height="15px" width="45px" style="filter: brightness(0) invert(1) sepia(1) saturate(5) hue-rotate(175deg);">
</div>
<div style="position: absolute; bottom: 255px; left: 215px; font-size: 10px; width: 200px; ">Holder`s Signature</div>
<div class="doc-signature">
<img src="assets/img/dl-doc-signature.png" alt="Hologram" height="35px" width="100"/>
</div>
<div style="position: absolute; bottom: 380; left: 435; font-size: 9px; width: 200px;"><b>Badge No:1<br>0/stock</b></div>
<div style="position: absolute; bottom: 262px; left: 440px; font-size: 9px; width: 200px; ">जारीकर्ता / Issuing Authority Sign</div>
<div style="position: absolute; bottom: 250px; left: 445px; font-size: 10px; width: 200px; ">DTO &nbsp;'.$dl_data['dto-dist'].'</div>
<div style="position: absolute; bottom: 335; left: 578; font-size: 7px; width: 200px; rotate: -90;">Form 7 Rule 16(2)</div>
</body>
</html>';

// Add the HTML content to the PDF
$mpdf->WriteHTML($html);

// Set password for the PDF
//$password = strtoupper(str_replace(' ', '', substr($b['votername'], 0, 5))) . $b['pincode'];
//$mpdf->SetProtection(array(), $password);

// Generate the PDF
$mpdf->Output("pdffpf.pdf", 'D');
}else{
echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Unauthorized Access</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100vh;
            }
    
            .container {
                text-align: center;
            }
    
            h1 {
                color: #333;
            }
    
            p {
                color: #777;
            }
        </style>
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