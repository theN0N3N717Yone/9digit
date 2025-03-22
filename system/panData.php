<?php
require_once('connectivity_functions.php');

// Fetch PAN card data based on input field and value
$field = $_POST['field']; // Field name (e.g., aadhaar_num, name_aadhaar, mob_num, order_id)
$value = $_POST['value']; // Field value

// Your database connection code here

// Prepare SQL query
$sql = "SELECT * FROM `nsdlpancard` WHERE `$field` = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$value]);
$pan_data = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if data found
if ($pan_data) {
    // Return data as JSON
    echo json_encode(['success' => true, 'data' => $pan_data]);
} else {
    // Return error message if data not found
    echo json_encode(['success' => false]);
}
?>
