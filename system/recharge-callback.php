<?php
require_once('connectivity_functions.php');

$status = $_GET['status'];
$application_no = $_GET['opid'];
$OrderId = $_GET['txid'];
//$status = '1';
$conn = connectDB();

ob_start(); // Start output buffering

if($status === "Success"){
    
    $spl = $conn->prepare("select * from recharges WHERE order_id=?");
    $spl->execute([$application_no]);
    $usrredata=$spl->fetch();


    $disql = $conn->prepare("select * from users WHERE id = ?");
    $disql->execute([$usrredata['user_id']]);
    $dis_data=$disql->fetch();    
    
    $remark = "Recharge Success"; 
    $rsql = $conn->prepare("UPDATE recharges SET status=? , ref_id=? , remark=? WHERE order_id=?");
    $rsql->execute(['success' , $OrderId  , $remark, $application_no]);
        
}
if($status === "Failure"){
    
    $rsql = $conn->prepare("UPDATE recharges SET status=? , remark=? , ref_id=? WHERE order_id=?");
    $rsql->execute(['failed' , 'Recharge failed' , $OrderId , $application_no]);


    $spl = $conn->prepare("select * from recharges WHERE order_id=?");
    $spl->execute([$application_no]);
    $usrredata=$spl->fetch();


    $pdta = $conn->prepare("select * from transactions WHERE reference=?");
    $pdta->execute([$application_no]);
    $payment=$pdta->fetch();

    $disql = $conn->prepare("select * from users WHERE id = ?");
    $disql->execute([$usrredata['user_id']]);
    $dis_data=$disql->fetch();

    if ($payment['mode'] === "REFUNDED"){
        // Credit    
        $newbal = $dis_data['balance'] + $usrredata['amount'];
        $bsql = $conn->prepare("UPDATE users SET balance=?  WHERE id=?");
        $bsql->execute([$newbal,$usrredata['user_id']]);    
        // Credit  

        // Insert a transaction record
        $txnsql = "INSERT INTO `transactions`(`date_time`, `timestamp`, `userId`, `mode`, `type`, `amount`,`balance`, `reference`, `remark`, `status`)
        VALUES (:date_time,:timestamp,:userId,:mode,:type,:amount,:balance,:reference,:remark,:status)";
        $mode = 'REFUNDED';
        $type = 'credit';
        $remark = 'Recharge Failed Refund '.$usrredata['number'].', Opt: '.$usrredata['operator'].', Amt Rs. '.$usrredata['amount'];
        $status = 'success';
        $userIdd = getUsersInfo('id');
        $txn = $conn->prepare($txnsql);
        $txn->bindParam(":date_time", $date);
        $txn->bindParam(":timestamp", $timestamp);
        $txn->bindParam(":userId", $userIdd);
        $txn->bindParam(":mode", $mode);
        $txn->bindParam(":type", $type);
        $txn->bindParam(":amount", $usrredata['amount']);
        $txn->bindParam(":balance", $newbal);
        $txn->bindParam(":reference", $reference);
        $txn->bindParam(":remark", $remark);
        $txn->bindParam(":status", $status);
        if ($txn->execute()) {
            // Transaction successful
        }

    } else {  

    }
}

$response = ob_get_clean(); // Get the output and clear the buffer

// Save the response to a file
$file_path = 'response.txt';
if (file_put_contents($file_path, $response) !== false) {
    echo 'Response saved to ' . $file_path;
} else {
    echo 'Error occurred while saving the response.';
}
?>
