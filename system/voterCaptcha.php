<?php
require_once('connectivity_functions.php');
$url = getPortalInfo('apiUrl') . '/serviceApi/V1/voterCaptcha';

// Data to be sent in the POST request
$data = array(
    'apiKey' => getPortalInfo('accessToken'),
);

// HTTP options for the POST request
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    ),
);

// Create a stream context with the specified options
$context  = stream_context_create($options);

// Send the POST request and get the response
$response = file_get_contents($url, false, $context);

// Output the response
echo $response;
?>
