<?php
require_once('../system/connectivity_functions.php');
$mid = getUpiDetails(1, 'mid');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Retrieve order ID from the query parameters
    $orderId = isset($_GET['ORDERID']) ? $_GET['ORDERID'] : '';

    if (!empty($orderId)) {

    function curl_get($url, $headers) {
        $ch = curl_init();
    
        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
        // Execute cURL session and get the response
        $response = curl_exec($ch);
    
        // Check for cURL errors
        if (curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch);
        }
    
        // Close cURL session
        curl_close($ch);
    
        return $response;
    }
    
    // Example usage
    $JsonData = json_encode(array("MID" => $mid, "ORDERID" => $orderId));
    
    $encodedJsonData = urlencode($JsonData);
    
    // Add any additional headers if needed
    $headers = array(
        'Content-Type: application/json',
        // Add other headers if required
    );
    
    // Make GET request
    $url = "https://securegw.paytm.in/order/status?JsonData=$encodedJsonData";
    $response = curl_get($url, $headers);
    
    echo $response;
    } else {
        echo 'Invalid or missing ORDERID parameter.';
    }
} else {
    echo 'Invalid request method.';
}    