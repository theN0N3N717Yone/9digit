<?php 
$pageName = "Add Wallet"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');
?>
<?php

if (isset($_GET['txnId'], $_GET['amount'], $_GET['utr'], $_GET['dateTime'])) {
    session_start(); // Start or resume the session

    $txnId = $_GET['txnId'];
    $amount = $_GET['amount'];
    $utr = $_GET['utr'];
    $dateTime = $_GET['dateTime'];

    // Assuming $_SESSION['paymentTime'] is set somewhere before
    if ($txnId == $_SESSION['paymentTime']) {
        $id = getUsersInfo('id');

        // Update user balance
        $credit = getUsersInfo('balance') + $amount;
        $updateQuery = "UPDATE users SET balance = :credit WHERE id = :id";
        $statement = $conn->prepare($updateQuery);
        $statement->bindParam(':credit', $credit, PDO::PARAM_STR);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        
        
        
        
        // Insert a transaction record
            $txnsql = "INSERT INTO `transactions`(`date_time`, `timestamp`, `userId`, `mode`, `type`, `amount`,`balance`, `reference`, `remark`, `status`)
             VALUES (:date_time,:timestamp,:userId,:mode,:type,:amount,:balance,:reference,:remark,:status)";
            $mode = 'QR Payment';
            $type = 'credit';
            $remark = 'balance credit with a reference to a UTR number '.$utr.'.';
            $status = 'success';
            $userIdd = getUsersInfo('id');
            $insertStatement = $conn->prepare($txnsql);
            $insertStatement->bindParam(":date_time", $date);
            $insertStatement->bindParam(":timestamp", $timestamp);
            $insertStatement->bindParam(":userId", $userIdd);
            $insertStatement->bindParam(":mode", $mode);
            $insertStatement->bindParam(":type", $type);
            $insertStatement->bindParam(":amount", $amount);
            $insertStatement->bindParam(":balance", $credit);
            $insertStatement->bindParam(":reference", $utr);
            $insertStatement->bindParam(":remark", $remark);
            $insertStatement->bindParam(":status", $status);

        $insertStatement->execute();
        unset($_SESSION['paymentTime']);
        echo "<script>alert('Balance updated successfully.', 'success');</script>";
        redirect(1500, 'addMoneyRecords');
    } else {
        echo "<script>alert('Order id mismatch', 'error');</script>";
    }
}
?>

<div class="container-xxl flex-grow-1 container-p-y">
   <div class="row">
      <!-- ---------------------  Add Wallet capacitor  ---------------- -->
      <div class="col-lg-10 d-flex align-items-strech m-auto <?php echo $getresult ? 'd-none' : ''; ?>">
         <div id="errors" class="card text-bg-primary border-0 w-100">
            <div class="card mx-2 mb-2 sprint-box mt-2">
               <div class="row">
                  <div class="col-md-6 mb-md-0 p-md-4">
                     <form class="was-validated" id="addWallet">
                        <div class="mb-3">
                           <input type="hidden" class="form-control" id="upiId" name="upiId" value="<?php echo getUpiDetails(1, 'upi_id')?>" required>
                           <input type="hidden" class="form-control" id="orderId" name="orderId" value="<?php echo $_SESSION['paymentTime'] = time();?>" required>
                           <input type="tel" id="amount" class="form-control" name="amount" placeholder="Enter amount" />

                        </div>
                        <div class="mb-3">
                           <input type="text" id="custId" class="form-control" name="custId" value="<?php echo getUsersInfo('username');?>" readonly required>
                           <input type="hidden" id="Id" class="form-control" name="Id" value="<?php echo getUsersInfo('id');?>" required>
                        </div>
                        <div class="mb-3">
                           <input type="text" id="mobile" class="form-control" name="mobile" value="<?php echo getUsersInfo('mobile_no');?>" readonly required>
                        </div>
                        <div class="mb-3">
                           <button type="button" id="generateQR" class="btn btn-primary" type="submit">Generate QR</button>
                        </div>
                     </form>
                  </div>
                  <div class="col-md-6 p-4 ps-md-0">
                     <h5 class="mt-0">Payment FAQs</h5>
                     <p style="color:black;">
                        You can load your wallet by depositing cash to the <span class="text-primary">Site Name bank account.</span> Additionally, you have the option to load through other digital modes such as UPI, net banking, and instant credit.
                     </p>

                     <a href="#" class="stretched">Read More FAQs</a>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- ---------------------  End Add Wallet capacitor  ---------------- -->

   </div>
</div>
<!-- Modal to display QR code -->
<div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="qrModalLabel" aria-hidden="true" data-bs-backdrop="static">
   <div class="modal-dialog modal-dialog-centered" role="document" style="width: 260px">
      <div class="modal-content">
         <div class="modal-body text-center">
            <h3 class="text-primary"><b><?= getPortalInfo('webName') ?></b></h3>
            <img id="qrCodeImage" class="img-fluid" alt="QR Code" height="250" width="250">
            <span class="d-flex justify-content-center" style="color:red; font-size:12px"><b>This QR code will expire in  <span id="upitimer"></span></b></span>
            <div class="mt-3 d-flex justify-content-center">
               <a href="javascript:void(0)" id="upilink" class="btn btn-sm btn-primary me-2 upilink">Pay UPI App</a><br>
               <a href="javascript:void(0)" id="cancelButton" class="btn btn-sm btn-danger">Cancel</a>
            </div>
            <div class="col-md-12 text-center">
               <img src="../assets/img/upiapp.png" alt="UPI" width="100%">
            </div>
         </div>
      </div>
   </div>
</div>
<?php 
require_once('../layouts/mainFooter.php');
?>