<?php
$pageName = "Driving Licence Print"; // Replace this with the actual page name
$_SESSION['userAuth'] = "Vehicle RC Print";
require_once('../layouts/mainHeader.php');

// Connect to the database
$conn = connectDB();

// Fetch distinct states from the 'stateDist' table
$query = "SELECT DISTINCT State FROM stateDist";
$stmt = $conn->prepare($query);
$stmt->execute();
$states = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Driving License Verification
$getResult = false;
$resultSuccess = true;

if (isset($_POST['get_details']) && isset($_POST['vehicleNo']) && !empty($_POST['vehicleNo'])) {
    $vehicleNo = $_POST['vehicleNo'];
    
        // Payment processing
        $amount = getUsersInfo('service_pricing_rc');

        if ($amount > getUsersInfo('balance')) {
            echo '<script>toastr.error("Insufficient Balance. Please load balance.");</script>';
            redirect(3000, '');
        } else {
            
        
            $response = performVehicleVerification($vehicleNo);
            //echo $response;
            $data = json_decode($response, true);
            
            if ($data['StatusCode'] === 100) {
                $getResult = false;
                $resultSuccess = true;
                echo '<script>toastr.info("Vehicle data not found / Vehicle server is currently down. Please try again later.");</script>';
                redirect(3000, '');
            } else {
                $uname = $data['owner'];
                $idNo = $data['rcno'];
                $getResult = true;
                $resultSuccess = false;

            $new_bal = getUsersInfo('balance') - $amount;
            $sqlu = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
            $sqlu->execute([$new_bal, getUsersInfo('id')]);

                // Insert a transaction record
                $txnsql = "INSERT INTO `transactions`(`date_time`, `timestamp`, `userId`, `mode`, `type`, `amount`,`balance`, `reference`, `remark`, `status`)
                 VALUES (:date_time,:timestamp,:userId,:mode,:type,:amount,:balance,:reference,:remark,:status)";
                $mode = 'RC Print';
                $type = 'debit';
                $remark = 'Vehicle RC Print Transaction - Requested by: ' . $uname . ' (Vehicle Number: ' . $idNo . ')';
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
                // Transaction success
            }
        }
    }
}

// Handling form submission
if (isset($_POST['savedata'])) {
    // Get the form data
    $rcno = $_POST['rcno'];
    $name = $_POST['owner'];
    $printData = $_POST['jsonData'];
    $print_type = "Vehicle RC";

    $Mremark = 'Name: ' . $name . " - Vehicle Number: " . $rcno;

    $rc_insert = "INSERT INTO `printRecords` (`name`, `idNumber`, `reqId`, `userId`, `print_type`, `date`, `time`, `printData`)
                    VALUES (:name, :idNumber, :reqId, :userId, :print_type, :date, :time, :printData)";
    $userIdd = getUsersInfo('id');
    $rcInsert = $conn->prepare($rc_insert);
    $rcInsert->bindParam(":name", $name);
    $rcInsert->bindParam(":idNumber", $rcno);
    $rcInsert->bindParam(":reqId", $reference);
    $rcInsert->bindParam(":userId", $userIdd);
    $rcInsert->bindParam(":date", $date);
    $rcInsert->bindParam(":time", $timestamp);
    $rcInsert->bindParam(":print_type", $print_type);
    $rcInsert->bindParam(":printData", $printData);

    // Execute the query
    if ($rcInsert->execute()) {
        // Display success toastr
        echo '<script>toastr.success("Vehicle RC Download successful from '.$Mremark.'.");</script>';
        redirect(3000, 'printRecord');
    } else {
        // Display error toastr
        echo '<script>toastr.error("Form submission failed.");</script>';
        redirect(3000, '');
    }
}
?>

<div class="container-xxl flex-grow-1 container-p-y">
<div class="row">

<!-- -------------------- Get Vehicle Details ------------------------ -->
<div class="col-lg-9 d-flex align-items-strech m-auto <?php echo $getResult ? 'd-none' : ''; ?> ">
   <div id="errors" class="card text-bg-primary border-0 w-100">
      <div class="card mx-2 mb-2 mt-2">
         <div class="">
            <div class="card-body m-auto mx-auto">
               <form action="" name="vform" method="POST">
                  <div class="form-body">
                     <div class="row">
                        <div class="col-md-6">
                           <div class="mb-3">
                              <label class="form-label">Vehicle No</label>
                              <input type="text" class="form-control" name="vehicleNo" id="vehicleNo" placeholder="Enter your vehicle number" autofocus oninput="this.value = this.value.toUpperCase()" />
                           </div>
                        </div>
                        <div class="col-md-6">
                           <!-- Added text-center class to center form elements -->
                           <div class="mb-3">
                              <label class="form-label">Mobile Number</label>
                              <input type="text" class="form-control" placeholder="Enter mobile number" name="mob" id="mob" maxlength="10" />
                              <input class="form-control" name="get_details" id="get_details" type="hidden" />
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="input-group">
                     <button type="button" class="btn btn-danger active">Fees ₹ <?= getUsersInfo('service_pricing_rc') ?></button>
                     <button type="button" class="btn btn-primary font-medium" onclick="Print_pay()"> Get Details </button>
                  </div>
               </form>
               <p class="mt-4"><b>
                     <li class="text-danger">If you encounter any issues or have specific questions about obtaining a printed copy of your driving license, it's advisable to contact the customer support or helpline of the relevant transportation authority.</li>
                     <li class="text-danger">Remember, the specific steps and requirements can vary, so it's crucial to check the guidelines provided by the transportation authority in your jurisdiction. Always use official and secure channels to avoid potential scams or misinformation.</li>
                  </b></p>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- -------------------- Get Vehicle Details End ------------------------ -->

<!-- -------------------- Vehicle Success Details --------------------- -->
<div class="col-lg-12 d-flex align-items-strech m-auto <?php echo $resultSuccess ? 'd-none' : ''; ?> ">
   <div id="errors" class="card text-bg-primary border-0 w-100">
      <div class="card mx-2 mb-2 mt-2">
         <div class="card-body">
             <div class="figure d-block">
                <blockquote class="blockquote"> 
                <b style="color:black">Your vehicle details has been Fetch Successfully</b>
                </blockquote>
              </div>
            <form method="post" action="">
               <div class="row">
                  <label class="col-sm-2 col-form-label" for="basic-default-name">Vehicle Number :</label>
                  <div class="col-sm-5">
                     <input type="text" class="form-control" placeholder="Vehicle Number" value="<?php echo $data['rcno']; ?>" name="rcno" readonly>
                  </div>
               </div>
               <div class="row mb-3" style="margin-top: 17px">
                  <label class="col-sm-2 col-form-label" for="basic-default-company">Name :</label>
                  <div class="col-sm-5">
                     <input class="form-control " type="text" value="<?php echo strtoupper($data['owner']); ?>" name="owner" readonly>
                  </div>
               </div>
               <div class="mb-3">
                  <div class="row">
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-sm-4 col-form-label" for="basic-default-email">Father Name :</label>
                           <div class="col-md-8">
                              <input class="form-control" type="text" value="<?php echo strtoupper($data['ownerFatherName']); ?>" readonly>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-sm-2 col-form-label" for="basic-default-phone">State :</label>
                           <div class="col-md-8">
                               <textarea name="jsonData" style="display:none;"><?= $response  ?></textarea>
                              <input type="text" class="form-control" value="<?php echo strtoupper($data['state']); ?>" name="state" required>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="mb-3">
                  <div class="row">
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-sm-4 col-form-label" for="basic-default-email">RTO Office :</label>
                           <div class="col-md-8">
                              <input type="text" class="form-control" value="<?php echo strtoupper($data['rto']); ?>" name="rto" required>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-sm-2 col-form-label" for="basic-default-phone">Add :</label>
                           <div class="col-md-8">
                              <input type="text" class="form-control" value="<?php echo strtoupper($data['permanentAddress']); ?>" name="permanentAddress" required>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-sm-3">
                     <button type="submit" class="btn btn-primary active" name="savedata">PDF Download</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
<!-- -------------------- Vehicle Success Details End --------------------- -->
</div>
</div>
<?php
require_once('../layouts/mainFooter.php');
?>
<script>
function Print_pay() {
    var vehicleNo = document.getElementById("vehicleNo").value;
    var mob = document.getElementById("mob").value;
    if (vehicleNo === "") {
        toastr.error("Please enter your vehicle number.");
    } else if (mob === "") {
        toastr.error("Please enter your mobile number.");
    } else {

        document.vform.submit();
    }
}
</script>