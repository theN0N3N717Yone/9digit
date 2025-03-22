<?php
$pageName = "Rashan Print Advance"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');
?>
<?php

$getResult = false;
$resultSuccess = true;
if (isset($_POST['get_details']) && isset($_POST['rcno']) && !empty($_POST['rcno'])) {

$rcno = $_POST['rcno'];           
$amount = getUsersInfo('service_pricing_rashan');

    // Check if the amount is greater than the user's balance
    if ($amount > getUsersInfo('balance')) {
        echo '<script>toastr.error("Insufficient Balance. Please load balance.");</script>';
        redirect(3000, '');
    } else {
        
    $result = performRashanVerification($rcno);

    //echo $result;
    $response_data = json_decode($result, true);
    
            $ownerName = null;
            $family = [];
            $memberId = null;
            $releationship_name = null;
            $uid = null;
    
            foreach ($response_data['familyDetails'] as $member) {
                if ($response_data['releationship_name'] == "SELF") {
                    $ownerName = $response_data['ownerName'];
                    $memberId = $response_data['memberId'];
                    $releationship_name = $response_data['releationship_name'];
                    $uid = $response_data['uid'];
                } else {
                    $family[] = $member;
                }
            }
    if ($response_data['StatusCode'] === 200) {
        
    $getResult = false;
    $resultSuccess = true;
    
        echo '<script>toastr.info("Please enter valid details to fetch the Rashan Card number. Please try again later.");</script>';
        redirect(3000, '');
    
    } else {
        
    $getResult = true;
    $resultSuccess = false;

    $new_bal = getUsersInfo('balance') - $amount;
    $sqlu = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
    $sqlu->execute([$new_bal, getUsersInfo('id')]);
    // Debit
    $txnsql = "INSERT INTO `transactions`(`date_time`, `timestamp`, `userId`, `mode`, `type`, `amount`,`balance`, `reference`, `remark`, `status`)
         VALUES (:date_time,:timestamp,:userId,:mode,:type,:amount,:balance,:reference,:remark,:status)";
        $mode = 'Rashan Card Print';
        $type = 'debit';
        $remark = 'Rashan Print Transaction - Requested by: ' . $ownerName . ' (Rashan Card Number: ' . $response_data['rcno'] . ')';
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

}}}}
// Check if the form is submitted
if (isset($_POST['saveRashanData'])) {
    
    // Retrieve form data
    $name = $_POST['name'];
    $rcno = $_POST['rcno'];
    $print_type = "Rashan Card Print";
    $printData = $_POST['dataJson'];

    
    $Mremark = 'Name: '. $name." - Rashan Card Number: ".$rcno;

    $rashan_insert = "INSERT INTO `printRecords` (`name`, `idNumber`, `reqId`, `userId`, `print_type`, `date`, `time` , `printData`) VALUES (:name, :idNumber, :reqId, :userId, :print_type, :date, :time, :printData)";

    $rashanInsert = $conn->prepare($rashan_insert);
    $userIdd = getUsersInfo('id');
    $rashanInsert->bindParam(":name", $name);
    $rashanInsert->bindParam(":idNumber", $rcno);
    $rashanInsert->bindParam(":reqId", $reference);
    $rashanInsert->bindParam(":userId", $userIdd);
    $rashanInsert->bindParam(":date", $date);
    $rashanInsert->bindParam(":time", $timestamp);
    $rashanInsert->bindParam(":print_type", $print_type);
    $rashanInsert->bindParam(":printData", $printData);
    
    // Execute the query
    if ($rashanInsert->execute()) {
        // Display success toastr
        echo '<script>toastr.success("Rashan Download successful from '.$Mremark.'.");</script>';
        redirect(3000, 'printRecord');
    } else {
        // Display error toastr
        echo '<script>toastr.error("Form submission failed.");</script>';
        redirect(3000, '');
    }

}

?>

<div class="container-xxl flex-grow-1 container-p-y">

<!-- ---------------------  Voter Print Start  ---------------- -->
      <div class="col-lg-8 d-flex align-items-strech m-auto <?php echo $getResult ? 'd-none' : ''; ?> ">
         <div id="errors" class="card text-bg-primary border-0 w-100 sprint">
            <div class="card mx-2 mb-2 mt-2">
               <div class="">
                  <div class="card-body m-auto mx-auto">
                     <form action="" name="vform" method="POST">
                        <div class="form-body">
                           <div class="row">
                              <div class="col-md-6">
                                 <div class="mb-3">
                                    <label class="form-label">Rashan Card Number</label>
                                    <input type="text" class="form-control" name="rcno" id="rcno" placeholder="Rashan Card Number" autofocus/>
                                 </div>
                              </div>
                              <div class="col-md-6">
                                 <!-- Added text-center class to center form elements -->
                                 <div class="mb-3">
                                    <label class="form-label">Mobile Number</label>
                                    <input type="text" class="form-control" placeholder="Enter mobile number" name="mobile" id="mobile"/>
                                    <input class="form-control" name="get_details" id="get_details" type="hidden"/>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="input-group">
                           <button type="button" class="btn btn-danger active">Fees ₹ <?= getUsersInfo('service_pricing_rashan')?></button>
                           <button type="button" class="btn btn-primary font-medium" onclick="Print_pay()"> Get Details </button>
                        </div>
                     </form>
                     <p class="mt-4 text-danger mb-0"><b>Keep in mind that the specific details and format of a ration card print can vary based on the policies and practices of the government issuing the card. If you have a specific format or requirement in mind, it's advisable to refer to the guidelines provided by the relevant government authority.</b></p>
                  </div>
               </div>
            </div>
         </div>
      </div>
    <div class="col-lg-11 d-flex align-items-strech m-auto <?php echo $resultSuccess ? 'd-none' : ''; ?> ">
         <div id="errors" class="card text-bg-primary border-0 w-100">
            <div class="card mx-2 mb-2 mt-2">
                <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">Your e-EPIC has been Fetch Successfully</h5>
      </div>
        <div class="card-body">
        <form action="" method="POST">
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label" for="basic-default-name">Rashan Number :</label>
            <div class="col-sm-5">
              <input type="text" class="form-control" name="rcno" value="<?=$response_data['rcno']?>" readonly>
            </div>
            <label class="col-sm-2 col-form-label" for="basic-default-name">State :</label>
            <div class="col-sm-2">
              <input class="form-control border-danger" id="houseNo" value="<?=$response_data['homeStateName']?>" type="text" title="Please fill houseNo Number" readonly>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label" for="basic-default-company">Name :</label>
            <div class="col-sm-5">
              <input class="form-control " type="text" name="name" value="<?=$ownerName?>" readonly>
            </div>
            <label class="col-sm-2 col-form-label" for="basic-default-company">Dist :</label>
            <div class="col-sm-2">
              <input class="form-control border-danger" placeholder="Pin Code" type="text" value="<?=$response_data['homeDistName']?>" readonly>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label" for="basic-default-email">scheme :</label>
              <div class="col-sm-5">
                <input class="form-control" type="text" name="scheme" value="<?=$response_data['schemeId']?>" readonly>
                <textarea id="w3review" name="dataJson" rows="4" cols="50" style="display:none"><?=$result?></textarea>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label" for="basic-default-email">Download :</label>
              <div class="col-sm-5">
                <button type="submit" name="saveRashanData" class="btn btn-primary">Submit</button>
            </div>
          </div>
        </form>
      </div>
    </div>
 </div>
</div>

<script>
// Add an event listener to the input fields
document.getElementById("rcno").addEventListener("input", handleInput);
document.getElementById("mobile").addEventListener("input", handleInput);

function handleInput() {
    // Get the values of the input fields
    var rcno = document.getElementById("rcno").value;
    var mobile = document.getElementById("mobile").value;

    // Check if both fields are not empty
    if (rcno !== "" && mobile !== "") {
        // Clearing errors and changing class to 'text-bg-primary'
        $('.sprint').removeClass('text-bg-danger').addClass('text-bg-primary');
    } else {
        // If any field is empty, add 'text-bg-danger' class
        $('.sprint').addClass('text-bg-danger');
    }
}

// Original function
function Print_pay() {
    var rcno = document.getElementById("rcno").value;
    var mobile = document.getElementById("mobile").value;

    if (rcno === "") {
        toastr.error('Please enter your Rashan Card Number.');
    } else if (mobile === "") {
        toastr.error('Please enter your mobile number.');
    } else {
        // Submit the form
        document.vform.submit();
    }
}


</script>
</div>
<?php
require_once('../layouts/mainFooter.php');
?>