<?php
// Set the page name
$pageName = "Know PAN"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";

require_once('../layouts/mainHeader.php');
?>
<?php
$successresult = true;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
        $amount = getUsersInfo('service_pricing_panFind');

        if ($amount > getUsersInfo('balance')) {
             echo '<script>toastr.error("Insufficient Balance. Please load balance.");</script>';
             redirect(3000, '');
        } else {
        // Retrieve form data
        $uidNumber = $_POST['uid_number'];
        
        $result = performPanFIND($uidNumber);
        
        //echo $result;
        $responseData = json_decode($result, true);
        // Check if the API request was successful
        if ($responseData['StatusCode'] === 100) {
        
        $successresult = false;

        // Access relevant data from the response
        $pan = $responseData['panNumber'];
        $resMessage = $responseData['message'];

            // Debit the user's balance
         $new_bal = getUsersInfo('balance') - $amount;
         $sqlu = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
         $sqlu->execute([$new_bal, getUsersInfo('id')]);

        // Insert a transaction record
        $txnsql = "INSERT INTO `transactions`(`date_time`, `timestamp`, `userId`, `mode`, `type`, `amount`,`balance`, `reference`, `remark`, `status`)
         VALUES (:date_time,:timestamp,:userId,:mode,:type,:amount,:balance,:reference,:remark,:status)";
         $idtxn = getUsersInfo('id');
         $mode = 'Get Pan Number';
         $type = 'debit';
         $remark = 'Lost Pan FIND Transaction - Requested by: UID:' . $uidNumber . ' (Pan Number: ' . $pan . ')';
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

         $Mremark = "Pan Number: ".$pan;
         $idpan = getUsersInfo('id');
            $adhar_insert = "INSERT INTO `printRecords` (`idNumber`, `userId`, `print_type`, `date`, `time` , `printData`) VALUES (:idNumber, :userId, :print_type, :date, :time, :printData)";

                $adhar = $conn->prepare($adhar_insert);
                $adhar->bindParam(":idNumber", $uidNumber);
                $adhar->bindParam(":userId", $idpan);
                $adhar->bindParam(":date", $date);
                $adhar->bindParam(":time", $timestamp);
                $adhar->bindParam(":print_type", $mode);
                $adhar->bindParam(":printData", $pan);
                

                if ($adhar->execute()) {
                echo '<script>toastr.success("You have retrieved the PAN number linked to the given Aadhaar number! from '.$Mremark.'.");</script>';
                redirect(3000, 'printRecord');   
            } else {
                echo '<script>toastr.error("Form submission failed.");</script>';
                redirect(3000, '');
            }

             
             
         }
        }else{
            echo '<script>toastr.error("Oops...","'.$resMessage.'");</script>';
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
      <!-- ---------------------  Pan Find capacitor  ---------------- -->
      <div class="col-lg-8 d-flex align-items-strech m-auto">
         <div id="errors" class="card text-bg-primary border-0 w-100">
            <div class="card mx-2 sprint-box mb-2 mt-2">
               <form action="" method="POST">
                  <div class="card-body">
                     <div class="mb-3">
                        <div class="d-flex align-items-center">
                           <h5 class="mb-0 mt-0 uidai">Enter your aadhaar number <span class="text-danger">*</span></h5>
                        </div>
                        <div class="mt-1">
                           <div class="input-group">
                              <button class="btn bg-danger text-light rounded-start" type="button">
                                 UID
                              </button>
                              <input name="uid_number" aria-label="uid_number" aria-describedby="basic-addon-search31" type="text" autofocus maxlength="12" oninput="verify()" id="txtUID" class="form-control vd_Required A_AadharNo" aria-describedby="Help" autocomplete="off" autofocus placeholder="9999 XXXX XXXX" required/>

                           </div>
                           <span style="font-size:12px; color:red;" id="basic-addon-search32"></span>
                        </div>
                     </div>
                     <div class="">
                        <div class="input-group">
                           <button type="button" class="btn btn-danger active">Processing Fees ₹ <?= getUsersInfo('service_pricing_panFind') ?></button>
                           <button type="submit" class="btn btn-primary active">Find</button>
                        </div>
                        <p class="mt-4 text-danger"><b>Please note that your Aadhaar and PAN details should match for successful linking or retrieval. Additionally, always use official and secure websites to avoid potential fraud or misuse of personal information. If you are facing difficulties or have specific concerns, it's advisable to contact the relevant authorities or seek professional advice.</b></p>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
      <!-- ---------------------  End Pan Find capacitor ---------------- -->
   </div>
</div>
<?php
require_once('../layouts/mainFooter.php');
?>
 <script type="text/javascript">
 $('#basic-addon-search32').removeClass('d-none');
    const d = [
        [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
        [1, 2, 3, 4, 0, 6, 7, 8, 9, 5],
        [2, 3, 4, 0, 1, 7, 8, 9, 5, 6],
        [3, 4, 0, 1, 2, 8, 9, 5, 6, 7],
        [4, 0, 1, 2, 3, 9, 5, 6, 7, 8],
        [5, 9, 8, 7, 6, 0, 4, 3, 2, 1],
        [6, 5, 9, 8, 7, 1, 0, 4, 3, 2],
        [7, 6, 5, 9, 8, 2, 1, 0, 4, 3],
        [8, 7, 6, 5, 9, 3, 2, 1, 0, 4],
        [9, 8, 7, 6, 5, 4, 3, 2, 1, 0]
    ];

    const p = [
        [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
        [1, 5, 7, 6, 2, 8, 3, 0, 9, 4],
        [5, 8, 0, 3, 7, 9, 6, 1, 4, 2],
        [8, 9, 1, 6, 0, 4, 3, 5, 2, 7],
        [9, 4, 5, 3, 1, 2, 6, 8, 7, 0],
        [4, 2, 8, 6, 5, 7, 3, 9, 0, 1],
        [2, 7, 9, 3, 8, 0, 6, 4, 1, 5],
        [7, 0, 4, 6, 9, 1, 3, 2, 5, 8]
    ];

    function validate(aadharNumber) {
        let c = 0;
        let invertedArray = aadharNumber.split('').map(Number).reverse();

        invertedArray.forEach((val, i) => {
            c = d[c][p[(i % 8)][val]];
        });

        return c === 0;
    }
    
    function verify() {
        var message = document.getElementById("message");
        var aadharNo = document.getElementById("txtUID").value;
        
        if (validate(aadharNo)) {
                $('#txtUID').removeClass('border border-primary');
		        $('.uidai').removeClass('text-danger');
		        $('#errors').removeClass('text-bg-danger');
                $('#basic-addon-search32').html('');
        } else {
            $('#basic-addon-search32').html('Please Enter Valid Aadhar Number');
            $('#errors').addClass('text-bg-danger');

        }
    }
</script>
