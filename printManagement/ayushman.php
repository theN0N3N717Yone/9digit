<?php
require_once('../system/connectivity_functions.php');

if(!empty($_GET['access_token']) && $_GET['token']){


$pmsid = base64_decode($_GET['access_token']);
$conn = connectDB();

$stmt = $conn->prepare("SELECT * FROM `printRecords` WHERE idNumber = :idNumber AND print_type='Ayushman Print'");
$stmt->bindParam(':idNumber', $pmsid);
$stmt->execute();
$a_data = $stmt->fetch(); // Assuming only one row is expected

// Uncomment the following line if you want to decode printData as JSON
$result = json_decode($a_data['printData'], true);


$familyid = $result['familyId'];
$id = $result['uidNumber'];
$stateid = $result['stateId'];
$apiKey = getPortalInfo('accessToken');
// Example Request using cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.sprintpan.in/serviceApi/V1/ayushmanPDF');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "apiKey=$apiKey&uidNumber=$id&familyId=$familyid&stateId=$stateid");

$pdfresult = curl_exec($ch);
curl_close($ch);
header('Content-Type: application/pdf');
echo $pdfresult;
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