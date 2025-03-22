<?php
require_once('connectivity_functions.php');

// Check if the form has been submitted
if(isset($_POST['callback'])) {
    // Check if the required POST parameters are set
    if(isset($_POST['mob']) && isset($_POST['message'])) {
        // Get mobile number and message from POST request
        $mob = $_POST['mob'];
        $message = $_POST['message'];

        // Call the function to send WhatsApp message
        $response = whatsappMessage($mob, $message);

        // Send response back to the client
        echo $response;
    } else {
        // Handle the case where required POST parameters are missing
        echo "Error: Required parameters are missing.";
    }
} else {
    // Handle the case where the form has not been submitted
    echo "Error: Form not submitted.";
}
?>