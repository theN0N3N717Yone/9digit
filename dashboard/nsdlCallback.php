<?php
// URL to fetch the response from
$url = 'http://recharge.pockket.co.in/myrc_rsp'; // Replace with the actual URL

// Fetch the response
$response = file_get_contents($url);

// Output the response
echo $response;
?>
