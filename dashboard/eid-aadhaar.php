<?php
$pageName = "EID TO AADHAAR NUMBER"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');
?>
<?php
$successresult = true;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
        $amount = 50;

        if ($amount > getUsersInfo('balance')) {
             echo '<script>toastr.error("Insufficient Balance. Please load balance.");</script>';
             redirect(3000, '');
        } else {
        // Retrieve form data
        $eid = $_POST['eid'];



        $url = "https://getnewapi.in/api_service/eidtoaadhar_api.php?eid=$eid&token=1cbc77726a6e81ff&domain=successprint.in";
        $result = file_get_contents($url);

        //$result = '{"statusCode":200,"statusMessage":"Success","uid":312904929326,"eid":"1149500972582120130819125750","resMessage":"Your Aadhaar has been generated. While your Aadhaar is being printed and posted to you, please download eAadhaar from www.UIDAI.gov.in"}';
            
        //echo  $result;
        

        $responseData = json_decode($result, true);
        // Check if the API request was successful
        if ($responseData['statusCode'] === 200) {
        
        $successresult = false;

        // Access relevant data from the response
        $uid = $responseData['uid'];
        $eid = $responseData['eid'];
        $resMessage = $responseData['resMessage'];
        $statusMessage = $responseData['statusMessage'];

            // Debit the user's balance
         $new_bal = getUsersInfo('balance') - $amount;
         $sqlu = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
         $sqlu->execute([$new_bal, getUsersInfo('id')]);

        // Insert a transaction record
        $txnsql = "INSERT INTO `transactions`(`date_time`, `timestamp`, `userId`, `mode`, `type`, `amount`,`balance`, `reference`, `remark`, `status`)
         VALUES (:date_time,:timestamp,:userId,:mode,:type,:amount,:balance,:reference,:remark,:status)";
         $idtxn = getUsersInfo('id');
         $mode = 'Get UID Number';
         $type = 'debit';
         $remark = 'EID TO UID Transaction - Requested by: EID:' . $eid . ' (Fetch UID Number: ' . $uid . ')';
         $status = 'success';
         $txn = $conn->prepare($txnsql);
         $txn->bindParam(":date_time", $date);
         $txn->bindParam(":timestamp", $timestamp); // Change $today to $timestamp
         $txn->bindParam(":userId", $idtxn);
         $txn->bindParam(":mode", $mode);
         $txn->bindParam(":type", $type);
         $txn->bindParam(":amount", $amount);
         $txn->bindParam(":balance", $new_bal);
         $txn->bindParam(":reference", $reference);
         $txn->bindParam(":remark", $remark);
         $txn->bindParam(":status", $status);
         if($txn->execute()){

         $Mremark = "UID Number: ".$uid;
         $idpan = getUsersInfo('id');
            $adhar_insert = "INSERT INTO `printRecords` (`idNumber`, `userId`, `print_type`, `date`, `time` , `printData`) VALUES (:idNumber, :userId, :print_type, :date, :time, :printData)";

                $adhar = $conn->prepare($adhar_insert);
                $adhar->bindParam(":idNumber", $eid);
                $adhar->bindParam(":userId", $idpan);
                $adhar->bindParam(":date", $date);
                $adhar->bindParam(":time", $timestamp);
                $adhar->bindParam(":print_type", $mode);
                $adhar->bindParam(":printData", $uid);
                

                if ($adhar->execute()) {
                echo '<script>toastr.success("Your Aadhaar has been generated. While your Aadhaar is being printed and posted to you! UID '.$uid.'.");</script>';
                redirect(3000, 'eid-aadhaar-list');   
            } else {
                echo '<script>toastr.error("Form submission failed.");</script>';
                redirect(3000, '');
            }

             
             
         }
        }else{
            echo '<script>toastr.error("Oops...","Please check eid or try again");</script>';
            redirect(3000, '');    
    }
    }
}
?>
<? if(getUsersInfo('usertype') !== "demo"){ 
$class = "disabled";
}
?>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
   <!-- eid to uid capacitor -->
   <div class="col-6 m-auto">
        <div class="alert alert-danger alert-dismissible mb-2" role="alert">
          <h6 class="alert-heading d-flex align-items-center mb-1 text-dark">Notice!!</h6>
          <p class="mb-0">This portal does not allow anyone to search their personal information. If someone's data is incorrect, if you are found doing so then legal action can be taken against you. This portal will not take any responsibility.</p>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
          </button>
        </div>
      <div id="errors" class="card text-bg-primary border-0 w-100">
         <div class="card mx-2 mb-2 sprint-box mt-2">
            <div class="card-body">
               <div class="mb-3">
                  <form method="post" action="">
                     <div class="d-flex align-items-center">
                        <h5 class="mb-0 mt-0 uidai">Enter EID number <span class="text-danger">*</span></h5>
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
                           <i class="bx bx-hash"></i>
                        </button>
                        <input class="form-control" name="eid" type="text" value="" required placeholder="EID Number" maxlenth="10">
                     </div>
               </div>
               <div class="input-group mt-2">
                   <button type="button" class="btn btn-danger active">Fees ₹ 50</button>
                   <button type="submit" id="sendOTP" name="sendOTP" class="btn btn-primary active">Get UID</button>
                </div>
               <?php endif;?>
            </div>
         </div>
      </div>
   </div></div>
   <!-- End eid to uid capacitor -->
</div>
</div>
<?php
require_once('../layouts/mainFooter.php');
?>