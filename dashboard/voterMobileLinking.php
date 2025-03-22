<?php
$pageName = "Voter mobile link"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');

?>
<?php
if (isset($_POST['epicNumber']) && isset($_POST['mob']) && isset($_POST['otp'])) {
    $sid = $_POST['captchaSID'];
    $scaptcha = $_POST['captcha'];
    $epicNumber = $_POST["epicNumber"];
    $otp = $_POST["otp"];
    $mob = $_POST['mob'];

    $response = voterMobileLinking($epicNumber, $sid, $scaptcha, $mob, $otp);

    $jsonResponse = json_decode($response, true);

    if ($jsonResponse['statusCode'] === 200) {
        $mobileNumber = $jsonResponse['mobileNumber'];
        $epicNumber = $jsonResponse['epicNumber'];
        $name = $jsonResponse['name'];
        
        $amount = getUsersInfo('service_pricing_vmLink');

            // Debit the user's balance
            $new_bal = getUsersInfo('balance') - $amount;
            $sqlu = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
            $sqlu->execute([$new_bal, getUsersInfo('id')]);

            // Insert a transaction record
            $txnsql = "INSERT INTO `transactions`(`date_time`, `timestamp`, `userId`, `mode`, `type`, `amount`,`balance`, `reference`, `remark`, `status`)
             VALUES (:date_time,:timestamp,:userId,:mode,:type,:amount,:balance,:reference,:remark,:status)";
            $mode = 'Voter mobile link';
            $type = 'debit';
            $remark = 'Voter mobile link Transaction - Requested by: ' . $name . ' (e-Epic Number: ' . $epicNumber . ')';

            $status = 'success';
            $userIdd = getUsersInfo('id');
            $txn = $conn->prepare($txnsql);
            $txn->bindParam(":date_time", $date);
            $txn->bindParam(":timestamp", $timestamp);
            $txn->bindParam(":userId", $userIdd);
            $txn->bindParam(":mode", $mode);
            $txn->bindParam(":type", $type);
            $txn->bindParam(":amount", $amount);
            $txn->bindParam(":balance", $new_bal);
            $txn->bindParam(":reference", $reference);
            $txn->bindParam(":remark", $remark);
            $txn->bindParam(":status", $status);
            if ($txn->execute()) {
                
            // Set a remark for the transaction
            $Mremark = 'Name: ' . $name . " - e-Epic Number: " . $epicNumber;
        
            // Insert print record into the database
            $adhar_insert = "INSERT INTO `printRecords` (`name`, `idNumber`, `userId`, `print_type`, `date`, `time` , `printData`) 
                             VALUES (:name, :idNumber, :userId, :print_type, :date, :time, :printData)";
        
            $adhar = $conn->prepare($adhar_insert);
            $userIdd = getUsersInfo('id');
            // Bind parameters
            $adhar->bindParam(":name", $name);
            $adhar->bindParam(":idNumber", $epicNumber);
            $adhar->bindParam(":userId", $userIdd);
            $adhar->bindParam(":date", $date);
            $adhar->bindParam(":time", $timestamp);
            $adhar->bindParam(":print_type", $mode);
            $adhar->bindParam(":printData", $response);
        
            // Execute the query
            if ($adhar->execute()) {
                // Display success toastr
                echo '<script>toastr.success("Voter Mobile Number Linked Successfully. Mobile no ' . $mobileNumber . ' Epic ' . $epicNumber . '");</script>';
                redirect(3000, '');
            } else {
                // Display error toastr
                echo '<script>toastr.error("Form submission failed.");</script>';
                redirect(3000, '');
            }
        }
        
    } else {
        echo '<script>toastr.error("Unable to fetch server, please try again.");</script>';
        redirect(3000, '');
    }
} else if (isset($_POST['sendOTP'])) {
    $mobile = $_POST['mobile'];
    $sendotp = false;

    $amount = getUsersInfo('service_pricing_vmLink');

        // Check if the amount is greater than the user's balance
        if ($amount > getUsersInfo('balance')) {
            echo '<script>toastr.error("Insufficient Balance. Please load balance.");</script>';
            redirect(3000, '');
            $getresult = false;
        } else {


    $response = voterMobileLinkSendOTP($mobile);

    $apiResponse = json_decode($response, true);
    $statusMessage = $apiResponse['statusMessage'];

    if ($statusMessage == 'Success') {
        $captchaSID = $apiResponse['captchaSID'];
        $captcha = $apiResponse['captcha'];
        $mobileNumber = $apiResponse['mobileNumber'];
        $sendotp = true;

        echo '<script>toastr.success("OTP sent successfully to mobile number: ' . $mobile . '");</script>';
    } else {
        echo '<script>toastr.error("Failed to send OTP, please try again.");</script>';
        redirect(3000, '');
    }
}
}
?>

<div class="container-xxl flex-grow-1 container-p-y">
   <!-- Voter mobile link capacitor -->
   <div class="col-lg-5 d-flex align-items-strech m-auto">
      <div id="errors" class="card text-bg-primary border-0 w-100">
         <div class="card mx-2 mb-2 sprint-box mt-2">
            <div class="card-body">
               <div class="mb-3">
                  <form method="post" action="">
                     <div class="d-flex align-items-center">
                        <h5 class="mb-0 mt-0 uidai">Enter your mobile number <span class="text-danger">*</span></h5>
                     </div>
                     <div class="mt-1">
                        <?php if($sendotp):?>
                        <div class="input-group">
                           <button class="btn bg-danger rounded-start" type="button" style="color: #fff">
                              <i class="bx bx-id-card"></i>
                           </button>
                           <input class="form-control" name="epicNumber" type="text" value="" required placeholder="e-Epic Number" maxlenth="10">
                        </div>
                        <div class="input-group mt-2">
                           <button class="btn bg-danger rounded-start" type="button" style="color: #fff">
                              <i class="bx bx-dots-horizontal"></i>
                           </button>
                           <input class="form-control" name="otp" type="text" value="" required placeholder="OTP Number" maxlenth="6">
                           <input type="hidden" name="captchaSID" value="<?=$captchaSID?>">
                           <input type="hidden" name="captcha" value="<?=$captcha?>">
                           <input type="hidden" name="mob" value="<?=$mobileNumber?>">
                        </div>
                        <div class=" mt-2">
                           <button type="submit" id="submit" name="submit" class="btn btn-danger active">Submit</button>
                        </div>
                     </div>
                     <?php else: ?>
                     <div class="input-group mt-2">
                        <button class="btn bg-danger rounded-start" type="button" style="color: #fff">
                           <i class="bx bx-phone"></i>
                        </button>
                        <input class="form-control" name="mobile" type="text" value="" required placeholder="Mobile Number" maxlenth="10">
                     </div>
               </div>
               <div class="input-group mt-2">
                   <button type="button" class="btn btn-danger active">Processing Fees ₹ <?= getUsersInfo('service_pricing_vmLink') ?></button>
                   <button type="submit" id="sendOTP" name="sendOTP" class="btn btn-primary active">Send OTP</button>
                </div>
               <?php endif;?>
            </div>
         </div>
      </div>
   </div>
   <!-- End Voter mobile link capacitor -->
</div>
</div>
<?php
require_once('../layouts/mainFooter.php');
?>