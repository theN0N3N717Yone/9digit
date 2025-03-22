<?php
require_once('connectivity_functions.php');

// Get the user ID and OTP status from the AJAX request
$userId = $_POST['userId'];
$otpStatus = $_POST['otpStatus'];

// Perform any necessary validation on user input

// Update the OTP status in the database
$updateResult = updateUserOtpStatus($userId, $otpStatus);

// Check if the update was successful
if ($updateResult) {
    echo "OTP status updated successfully";
} else {
    echo 'Failed to update OTP status';
}

// Function to update the OTP status in the database
function updateUserOtpStatus($userId, $otpStatus) {
    // Establish a database connection using PDO
    $conn = connectDB(); // Replace with your actual function to establish a connection

    try {
        // Prepare the SQL statement
        $query = "UPDATE users SET otp = :otpStatus WHERE id = :userId";
        $stmt = $conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':otpStatus', $otpStatus);
        $stmt->bindParam(':userId', $userId);

        // Execute the statement
        $result = $stmt->execute();

        // Close the database connection
        $conn = null;

        return $result;
    } catch (PDOException $e) {
        // Handle any errors that occur during the database operation
        return false;
    }
}
?>