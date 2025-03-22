<?php
require_once('connectivity_functions.php');

// Check if the required GET parameters are present
if (isset($_GET['txid'])) {
    // Retrieve the parameters from the GET request
    $txid = $_GET['txid'];
    $status = $_GET['status'];
    $opid = $_GET['opid'];

if($status === "Success"){
    $txid = $_GET['txid'];
    $sql = $conn->prepare("SELECT * FROM `nsdlTransaction` WHERE orderId = ?");
    $sql->execute([$txid]);
    $txndata = $sql->fetch(PDO::FETCH_ASSOC);

    
    if($txid === $txndata['orderId']){
        
            $remark = "PAN card processing completed successfully. Acknowledgment number: $opid. Applicant's mobile number: {$txndata['mobNumber']}.";
            $status = 'success';
            // Prepare and execute SQL statement to update the existing transaction record
            $stmt = $conn->prepare("UPDATE `nsdlTransaction` SET `nsdlAck` = ?, `status` = ? WHERE `orderId` = ?");
            $stmt->execute([$remark, $status, $txid]);

        
    }
} else if($status === "Failure"){
    $txid = $_GET['txid'];
    $sql = $conn->prepare("SELECT * FROM `nsdlTransaction` WHERE orderId = ?");
    $sql->execute([$txid]);
    $txndata = $sql->fetch(PDO::FETCH_ASSOC);

    
    if($txid === $txndata['orderId']){
        
        $txnuId = $txndata['userId'];
        $sql = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
        $sql->execute([$txnuId]);
        $uData = $sql->fetch(PDO::FETCH_ASSOC);
        
            $new_bal = $uData['balance'] + $uData['service_pricing_ekycpan'];

            // Use prepared statement for the update
            $stmt = $conn->prepare("UPDATE `users` SET balance = ? WHERE id = ?");
            $stmt->execute([$new_bal, $txnuId]);
        
            $mode = 'Refunded';
            $type = 'credit';
            $txn_type = 'e-Kyc Application Refund';
            $remark = "Refund initiated for Order ID: $txid. Applicant's mobile number: {$txndata['mobNumber']}.";
            $status = 'Refund';
            // Prepare and execute SQL statement to update the existing transaction record
            $stmt = $conn->prepare("UPDATE `transactions` SET `type` = ?, `mode` = ?, `balance` = ?, `remark` = ?, `status` = ? WHERE `reference` = ?");
            $stmt->execute([$type, $mode, $new_bal, $remark, $status, $txid]);

            $remark = $opid;
            $status = 'Failure';
            // Prepare and execute SQL statement to update the existing transaction record
            $stmt = $conn->prepare("UPDATE `nsdlTransaction` SET `nsdlAck` = ?, `status` = ? WHERE `orderId` = ?");
            $stmt->execute([$remark, $status, $txid]);


        
    }
} else if($status === "Pending"){
    
    $txid = $_GET['txid'];
    $sql = $conn->prepare("SELECT * FROM `nsdlTransaction` WHERE orderId = ?");
    $sql->execute([$txid]);
    $txndata = $sql->fetch(PDO::FETCH_ASSOC);

    
    if($txid === $txndata['orderId']){
        
            $did = $txndata['orderId'];
        
            $remark = "PAN card processing is pending. Applicant's mobile number: {$txndata['mobNumber']}.";
            $status = 'Incomplete';
            // Prepare and execute SQL statement to update the existing transaction record
            $stmt = $conn->prepare("UPDATE `nsdlTransaction` SET `nsdlAck` = ?, `status` = ? WHERE `orderId` = ?");
            $stmt->execute([$remark, $status, $did]);

        
    }
    
}
    

} else {
    // If any of the required parameters is missing, respond with an error message
    http_response_code(400); // Set HTTP response code to 400 (Bad Request)
    echo "Error: Incomplete callback data.";
}
?>

