<?php
require_once('connectivity_functions.php'); // Make sure to provide the correct file extension

try {
    // Get user ID from session
    $username = getUsersInfo('id');
    $uname = getUsersInfo('username');
    $service_name = 'active'; // Service name for PAN status
    $debit_amount = 199; // Amount to debit for service activation
    
    if ($debit_amount > getUsersInfo('balance')) {
        echo 'low balance';
    } else {
    
    $conn = connectDB();

    // Start transaction
    $conn->beginTransaction();

    // Activate service and debit balance
    $stmt1 = $conn->prepare("UPDATE users SET ekycPan_status = :status WHERE id = :id AND ekycPan_status = 'inactive'");
    $stmt1->bindParam(':id', $username);
    $stmt1->bindParam(':status', $service_name);
    $stmt1->execute();

    $new_bal = getUsersInfo('balance') - $debit_amount;

    $stmt2 = $conn->prepare("UPDATE users SET balance = balance - :debit_amount WHERE id = :id");
    $stmt2->bindParam(':id', $username);
    $stmt2->bindParam(':debit_amount', $debit_amount);
    $stmt2->execute();
    
    // Insert a transaction record
    $txnsql = "INSERT INTO `transactions`(`date_time`, `timestamp`, `userId`, `mode`, `type`, `amount`,`balance`, `reference`, `remark`, `status`)
     VALUES (:date_time,:timestamp,:userId,:mode,:type,:amount,:balance,:reference,:remark,:status)";
    $mode = 'Instant Pan Service Activation';
    $type = 'debit';
    $remark = 'Instant Pan Service Activation Transaction - Requested by: ' . $uname . ' (Amount: ' . $debit_amount . ')';
    $status = 'success';
    $txn = $conn->prepare($txnsql);
    $txn->bindParam(":date_time", $date);
    $txn->bindParam(":timestamp", $timestamp);
    $txn->bindParam(":userId", $username);
    $txn->bindParam(":mode", $mode);
    $txn->bindParam(":type", $type);
    $txn->bindParam(":amount", $debit_amount);
    $txn->bindParam(":balance", $new_bal);
    $txn->bindParam(":reference", $reference);
    $txn->bindParam(":remark", $remark);
    $txn->bindParam(":status", $status);
    if ($txn->execute()) {
        echo "success"; // Return success response
    }
            
    // Commit transaction
    $conn->commit();

}
} catch(PDOException $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo "error"; // Return error response
}
?>
