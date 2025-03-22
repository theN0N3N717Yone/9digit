<?php

$cookieFile = sys_get_temp_dir() . '/cookies.txt';

$ch = curl_init();
$captchaUrl = 'https://www.ieiletds.com/ETDS/default.aspx';
$userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36';

// Define the Cache-Control headers
curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIESESSION, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, "Login_Txt=F5044001&Pass_Txt=Alwar@123&ctl00=Submit&__VIEWSTATEGENERATOR=3ACED193&__VIEWSTATE=/wEPDwUJMTQ3OTMwODkxZGQm0vsitWqXdFFlI/UsBJPQXzCLi1llHxzBj6uxOE2XYA==");

curl_setopt($ch, CURLOPT_COOKIE, '');
curl_setopt($ch, CURLOPT_URL, $captchaUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$headers = array();
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
}
curl_close($ch);

$cookies = file_get_contents($cookieFile);
$jsid = '';
$JSESSIONID = '/ASP.NET_SessionId\s+(.*?)\s+/';

if (preg_match($JSESSIONID, $cookies, $matches)) {
    $s = $matches[1];
    $b = urldecode($s);
    $jsid = rtrim($b, '=');
}

// Check if acknowledgment number is received via GET
if(isset($_POST['ack_no'])) {
    // Get the acknowledgment number from the GET request
    $ack_no = $_POST['ack_no'];

    // URL to which you want to send the request
    $url = 'https://www.ieiletds.com/ETDS/PAN/PAN_STATUS/PAN_STATUS_CHCK_SCREENAjax.aspx?Req=1&AckNum=' . $ack_no;

    // Cookie string
    $cookies = 'ASP.NET_SessionId='.$jsid; // Replace with your actual cookies

    // Initialize cURL session
    $ch = curl_init();

    // Set the cURL options
    curl_setopt($ch, CURLOPT_URL, $url); // Set the URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
    curl_setopt($ch, CURLOPT_COOKIE, $cookies); // Set the cookies

    // Execute the cURL request
    $response = curl_exec($ch);

    // Check for errors
    if(curl_errno($ch)) {
        // Handle cURL error
        echo 'Error: ' . curl_error($ch);
    } else {
        // Print the response
        echo $response;
    }

    // Close cURL session
    curl_close($ch);
} else {
    // If acknowledgment number is not received
    echo "Acknowledgment number is not received.";
}
?>
