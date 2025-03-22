<?php
header("Content-type: application/json; charset=utf-8");
session_start();

// Check user authentication
if (!isset($_SESSION['userAuth'])) {
    session_destroy(); // Destroy session
    header('Location: index.php'); // Redirect to index.php if user not authenticated
}

// Fetch data based on city_aoCode parameter
if (isset($_REQUEST['city_aoCode'])) {
    $city_aoCode = preg_replace('/[^a-zA-Z0-9 ]/', '', filter_var($_REQUEST["city_aoCode"], FILTER_SANITIZE_STRING));
    require_once('system/connectivity_functions.php'); // Include connectivity functions
    
    // Prepare and execute SQL query
    $aosql = $conn->prepare("SELECT * FROM aocode_list WHERE city = ?");
    $aosql->execute([$city_aoCode]);
    $results = $aosql->fetchAll(PDO::FETCH_ASSOC); // Fetch all rows
    echo json_encode($results); // Output JSON-encoded results
}

// Fetch data based on city_Code parameter
if (isset($_REQUEST['city_Code'])) {
    $city_Code = preg_replace('/[^a-zA-Z0-9 ]/', '', filter_var($_REQUEST["city_Code"], FILTER_SANITIZE_STRING));
    require_once('system/connectivity_functions.php'); // Include connectivity functions
    
    // Prepare and execute SQL query
    $aosql = $conn->prepare("SELECT * FROM aocode_list WHERE city = ?");
    $aosql->execute([$city_Code]);
    $results = $aosql->fetchAll(PDO::FETCH_ASSOC); // Fetch all rows
    echo json_encode($results); // Output JSON-encoded results
}
?>
