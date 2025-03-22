<?php

$payload = array(
    "PAN" => "EVJPD1473E"
);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.cashfree.com/verification/marketing/pan');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

$headers = array(
    'Accept: application/json',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
    'Content-Type: application/json' // Add Content-Type header
);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Disable SSL verification
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

// Set up proxy
//$proxy = 'http://your-proxy-ip:port';
//curl_setopt($ch, CURLOPT_PROXY, $proxy);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

echo $result;
?>
