<?php
require_once('../system/connectivity_functions.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the form was submitted

    // Retrieve password and validate
    $password = $_POST['password'];

    // Retrieve uploaded file
    $uploadedFile = $_FILES['pdfFile'];

    // Check if file upload succeeded
    if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
        echo "File upload failed with error code: " . $uploadedFile['error'];
        exit;
    }

    // Get API key
    $api_key = getPortalInfo('accessToken');

    // Set up API request
    $url = getPortalInfo('apiUrl') . '/serviceApi/idMaker/aadhaaridMaker';
    $postData = array(
        'apiKey' => $api_key,
        'order_id' => time(), // You can use time as the order_id, or any other suitable value
        'aadhaarPdf' => curl_file_create($uploadedFile['tmp_name'], $uploadedFile['type'], $uploadedFile['name']),
        'password' => $password
    );

    // Initialize cURL session
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

    // Execute cURL session and get the response
    $result = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
        exit;
    }

    // Close cURL session
    curl_close($ch);

    // Display the response
    echo $result;
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Upload Form</title>
</head>
<body>

<h2>Upload PDF and Enter Password</h2>

<form action="" method="post" enctype="multipart/form-data">
    <label for="pdfFile">Select PDF File:</label>
    <input type="file" name="pdfFile" id="pdfFile" accept=".pdf" required><br>

    <label for="password">Enter Password:</label>
    <input type="password" name="password" id="password" required><br>

    <input type="submit" value="Upload and Submit">
</form>

</body>
</html>
