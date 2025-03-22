<?php
require_once('vendor/autoload.php');
require_once('../system/connectivity_functions.php');

    if(!empty($_GET['access_token']) && $_GET['token']){

$rcno = base64_decode($_GET['access_token']);
$conn = connectDB();

$stmt = $conn->prepare("SELECT * FROM `printRecords` WHERE idNumber = :idNumber");
$stmt->bindParam(':idNumber', $rcno);
$stmt->execute();
$result = $stmt->fetch(); // Assuming only one row is expected

// Uncomment the following line if you want to decode printData as JSON
$data = json_decode($result['printData'], true);
        // Extract information from the decoded data
        $ownerName = null;
        $family = [];
        $memberId = null;
        $releationship_name = null;
        $uid = null;

        // Separate owner and family members
        foreach ($data['familyDetails'] as $member) {
                $ownerName = $data['ownerName'];
                $memberId = $data['memberId'];
                $releationship_name = $data['releationship_name'];
                $uid = $data['uid'];
                // Add serial number to family members
                $family[] = [
                    'sn' => $key + 1,  // Serial number starting from 1
                    'memberName' => $member['memberName'],
                    'memberId' => $member['memberId'],
                    'releationship_name' => $member['releationship_name'],
                    'uid' => $member['uid'],
                ];
            }

        // Create PDF using mPDF
        $mpdf = new \Mpdf\Mpdf();
        

        $statestr = $data['homeStateName'];
        $statestr = trim($statestr);
        $State = ucfirst(strtolower($statestr));
        if ($data['fullAddress'] === "") {
            $address = 'N/A';
        } else {
            $address = $data['fullAddress'];
        }
        
        
        $st = $data['homeStateName'];
        
        if($st === "RAJASTHAN"){
            $img = "css/img/rajasthan.png";
        } else if ($st === "BIHAR"){
            $img = "css/img/bihar.png";
        } else if ($st === "PUNJAB"){
            $img = "css/img/punjab.png";
        } else if ($st === "Chhattisgarh"){
            $img = "css/img/chhattisgarh.png";
        } else if ($st === "Jharkhand"){
            $img = "css/img/jharkhand.png";
        } else if ($st === "Madhya Pradesh"){
            $img = "css/img/madhya-pradesh.png";
        } else if ($st === "UTTAR PRADESH"){
            $img = "css/img/uttar-pradesh.png";
        } else{
           $img = "css/img/satyamev-jayate.png"; 
        }

        // HTML content with header
$html = '<!DOCTYPE html>
    <html>
    <head>
        <title>NFSA Beneficiary - ' . $ownerName . '</title>
        <link href="css/kavyaRashan.css" type="text/css" rel="stylesheet">
    </head>
    <body>

    <!-- Font -->
    
    <div class="header">Department Of Food & Public Distribution</div>
    <div class="header-dis">Ministry Of Consumer Affairs, Food & Public Distribution</div>
    <div class="base-image"></div>
    <div class="st1-image"><img src="css/img/satyamev-jayate.png" /></div>
    <div class="st2-image"><img src="'.$img.'" style="opacity: 0.1;"/></div>
    <div class="st3-image"><img src="'.$img.'" style="opacity: 0.1;"/></div>
    <div class="card-header">Department Of Food & Public Distribution</div>
    <div class="card-header-dis">Ministry Of Consumer Affairs, Food & Public Distribution</div>
    <div class="govt-of">Government Of ' . $State . '</div>
    
    <div class="rashan-font">
        <div>Ration Card No.</div>
    	<div>Member ID</div>
    	<div>Name</div>
    	<div>State</div>
    	<div>District</div>
    	<div>EPDS FPS Code</div>
        <div>Address</div>
    </div>
    
    <div class="dd">
        <div>:</div>
    	<div>:</div>
    	<div>:</div>
    	<div>:</div>
    	<div>:</div>
    	<div>:</div>
        <div>:</div>
    </div>
    
    <div class="font-details">
        <div>' . $data['rcno'] . '</div>
    	<div>' . $memberId . '</div>
    	<div>' . strtoupper($ownerName) . '</div>
    	<div>' . strtoupper($State) . '</div>
    	<div>' . strtoupper($data['homeDistName']) . '</div>
    	<div>' . $data['fpsId'] . '</div>
        <div style="width: 200px">' . $address . '</div>
    </div>

    <!-- \Font -->
    
<!-- Back -->
<div class="card-header-back">Family Members</div>

<!-- Family Members -->
<div class="table-data">
    <table>
        <tbody>
            <tr>
                <td>SN.</td>
                <td>MEMBER NAME</td>
                <td>MEMBER ID</td>
                <td>RELATION</td>
            </tr>';

foreach ($family as $key => $member) {
    $memberId = $member['memberId'];

// Convert to string
$memberId_str = (string) $memberId;

// Get last 6 digits
$last_12_digits = substr($memberId_str, -18);
    $html .= '
        <tr>
            <td style="width:20px">' . ($key + 1) . '</td>
            <td style="width:150px">' . strtoupper($member['memberName']) . '</td>
            <td>' . $last_12_digits . '</td>
            <td style="text-align: end;">' . ($member['releationship_name'] === "NOT AVAILABLE" ? 'Null' : $member['releationship_name']) . '</td>
        </tr>';
}

$html .= '
        </tbody>
    </table>
</div>

<div class="urlfficial">
    <div>One Nation One Ration Card | https://impds.nic.in</div>
</div>
</body>
</html>';

// Output details in the PDF
$mpdf->WriteHTML($html);
$mpdf->Output('NFSA' . $data['rcno'] . '.pdf', 'I');
} else {
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