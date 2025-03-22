<?php
header("Access-Control-Allow-Origin: *");

// Get the PAN number from the query string
$pan = $_GET['pan'];

// Make the request to the API endpoint
$url = "https://getnewapi.in/api_service/pan_api.php?pan=" . $pan . "&token=2b4cf893f766b9c3&domain=PanSprint.in";
$response = file_get_contents($url);

// Output the response
echo $response;
?>
