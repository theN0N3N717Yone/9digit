<?php
$pageName = "Ayushman Print Advance"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');
?>

<?php
$getResult = true;
if(isset($_POST['p1'])) {
	$flno = $_POST['p1'];
	 $stid = $_POST['s1'];
	$mob = $_POST['p3'];		
	if($mob == "R"){
	    $type = "familyid";
	}else if($mob=="S"){
	    $type = "mob";
	}
	
//payment code hare    
    $amount = getUsersInfo('service_pricing_ayushman');

    // Check if the amount is greater than the user's balance
    if ($amount > getUsersInfo('balance')) {
        echo '<script>toastr.error("Insufficient Balance. Please load balance.");</script>';
        redirect(3000, '');
    } else {	
	
    $result = performAyushmanVerification($flno, $stid);	
    $rs = json_decode($result, true);
    $vk = $rs[0];
    $ayushData = $rs;
    if($vk['StatusCode'] === 100){

        // Debit the user's balance
        $new_bal = getUsersInfo('balance') - $amount;
        $sqlu = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
        $sqlu->execute([$new_bal, getUsersInfo('id')]);

        // Insert a transaction record
        $txnsql = "INSERT INTO `transactions`(`date_time`, `timestamp`, `userId`, `mode`, `type`, `amount`,`balance`, `reference`, `remark`, `status`)
         VALUES (:date_time,:timestamp,:userId,:mode,:type,:amount,:balance,:reference,:remark,:status)";
        $mode = 'Ayushman Print';
        $type = 'debit';
        $remark = 'Ayushman Print Transaction - Requested by: ' . $vk['ownerName'] . ' (Pmrssmid: ' . $vk['pmrssmId'] . ')';
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

        $getResult = 'true';
    }

} else {
    $getResult = 'false';
    echo '<script>toastr.info("Ayushman data not found / Ayushman server is currently down. Please try again later.");</script>';
    redirect(3000, '');
} 
}

}
// Check if the data saving form is submitted

if (isset($_POST['saveayushman'])) {
    // Retrieve form data
    $stid = $_POST['stateId'];
    $uidNumber = $_POST['uidNumber'];
    $userid = $_POST['userid'];
    $name = $_POST['name'];
    $id = $_POST['familyId'];
    // ... (other form fields)

    // Define print type
    $print_type = "Ayushman Print";

    // Create an array with print data
     $printData = array(
        'stateId' => $stid,
        'uidNumber' => $uidNumber,
        'userid' => $userid,
        'name' => $name,
        'familyId' => $id
    );

    // Set a remark for the transaction
    $Mremark = 'Name: ' . $name . " - Family Id: " . $uidNumber;

    // Insert print record into the database
    $ayushman_insert = "INSERT INTO `printRecords` (`name`, `idNumber`, `reqId`, `userId`, `print_type`, `date`, `time` , `printData`) 
                     VALUES (:name, :idNumber, :reqId, :userId, :print_type, :date, :time, :printData)";

    $ayushmanInsert = $conn->prepare($ayushman_insert);
    $userIdd = getUsersInfo('id');
    // Bind parameters
    $ayushmanInsert->bindParam(":name", $name);
    $ayushmanInsert->bindParam(":idNumber", $uidNumber);
    $ayushmanInsert->bindParam(":reqId", $reference);
    $ayushmanInsert->bindParam(":userId", $userIdd);
    $ayushmanInsert->bindParam(":date", $date);
    $ayushmanInsert->bindParam(":time", $timestamp);
    $ayushmanInsert->bindParam(":print_type", $print_type);
    $ayushmanInsert->bindParam(":printData", json_encode($printData, JSON_UNESCAPED_UNICODE));
    
    
    // Execute the query
    if ($ayushmanInsert->execute()) {
        // Display success toastr
        echo '<script>toastr.success("Ayushman Download successful from '.$Mremark.'.");</script>';
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
        <!-- Ayushman Card List -->
        <div class="col-lg-10 d-flex align-items-stretch m-auto">
            <div id="errors" class="card text-bg-primary border-0 w-100">
                <div class="card balance-box mx-2 mb-2 mt-2">
                    <div class="">
                        <div class="card-body m-auto mx-auto">
                            <!-- Ayushman Card List Form -->
                            <form method="post" name="f1">
                                <!-- Form Body -->
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Select State</label>
                                                <select name="s1" id="s1" class="form-select">
                                                    <option value="">Select State</option>
                                                    <option value="35">ANDAMAN AND NICOBAR ISLANDS</option>
                                                    <option value="28">ANDHRA PRADESH</option>
                                                    <option value="12">ARUNACHAL PRADESH</option>
                                                    <option value="18">ASSAM</option>
                                                    <option value="10">BIHAR</option>
                                                    <option value="4">CHANDIGARH</option>
                                                    <option value="22">CHHATTISGARH</option>
                                                    <option value="26">DADRA AND NAGAR HAVELI</option>
                                                    <option value="25">DAMAN AND DIU</option>
                                                    <option value="7">DELHI</option>
                                                    <option value="30">GOA</option>
                                                    <option value="24">GUJARAT</option>
                                                    <option value="6">HARYANA</option>
                                                    <option value="2">HIMACHAL PRADESH</option>
                                                    <option value="1">JAMMU AND KASHMIR</option>
                                                    <option value="20">JHARKHAND</option>
                                                    <option value="29">KARNATAKA</option>
                                                    <option value="32">KERALA</option>
                                                    <option value="31">LAKSHADWEEP</option>
                                                    <option value="23">MADHYA PRADESH</option>
                                                    <option value="27">MAHARASHTRA</option>
                                                    <option value="14">MANIPUR</option>
                                                    <option value="17">MEGHALAYA</option>
                                                    <option value="15">MIZORAM</option>
                                                    <option value="13">NAGALAND</option>
                                                    <option value="21">ODISHA</option>
                                                    <option value="34">PUDUCHERRY</option>
                                                    <option value="3">PUNJAB</option>
                                                    <option value="8">RAJASTHAN</option>
                                                    <option value="11">SIKKIM</option>
                                                    <option value="33">TAMIL NADU</option>
                                                    <option value="36">TELANGANA</option>
                                                    <option value="16">TRIPURA</option>
                                                    <option value="5">UTTARAKHAND</option>
                                                    <option value="9">UTTAR PRADESH</option>
                                                    <option value="19">WEST BENGAL</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Select Proof</label>
                                                  <select name="p3" id="p3" required="" class="form-select">
                                                    <option value="">Select</option>
                                                    <option selected value="S">Aadhar Number</option>
                                                  </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Enter parameter</label>
                                                <input type="text" name="p1" id="p1" placeholder="Enter No parameter" class="form-control" maxlength="12">
                                                <input type="hidden" name="submit1" value="submit">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Input Group for Buttons -->
                                <div class="input-group">
                                    <button type="button" class="btn btn-danger active">Fees ₹ <?= getUsersInfo('service_pricing_ayushman') ?></button>
                                    <button type="button" class="btn btn-primary font-medium" onclick="myFunction()"> Get Details </button>
                                </div>
                            </form>
                            <!-- Error Message -->
                            <p class="mt-4 text-danger"><b>Keep in mind that the format and specific details on a voter card can vary between countries and regions. If you are looking for information specific to a particular location, it's recommended to refer to the guidelines provided by the relevant election commission or electoral authority in that area.</b></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="success_alert_modal" data-bs-backdrop="static" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-body p-4">
                <div class="figure d-block">
                    <blockquote class="blockquote">
                        <b style="color:black">Your Ayushman Card has been Fetch Successfully</b>
                    </blockquote>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead style="background: #13027d;">
                            <tr>
                                <th style="color: #ffffff;">pmrssmId</th>
                                <th style="color: #ffffff;">Name</th>
                                <th style="color: #ffffff;">Father Name</th>
                                <th style="color: #ffffff;">Created</th>
                                <th style="color: #ffffff;">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <?php
                            // Empty array to store unique identifiers
                            $uniqueIdentifiers = array();
                            
                            // Loop through each entry in the data
                            foreach ($ayushData as $ayushF) {
                                // Create a unique identifier
                                $uniqueIdentifier = $ayushF['pmrssmId'] . '_' . $ayushF['ownerName'];
                                
                                // Check if this identifier already exists
                                if (!in_array($uniqueIdentifier, $uniqueIdentifiers)) {
                                    // If it doesn't exist, add it to the list of unique identifiers
                                    $uniqueIdentifiers[] = $uniqueIdentifier;
                            
                                    // Extract relevant information
                                    $pmrssmid = $ayushF['pmrssmId'];
                                    $userName = $ayushF['ownerName'];
                                    $fatherName = $ayushF['fatherName'];
                                    $createdOn = $ayushF['createdOn'];
                            
                                    // Display the information in a table row
                                    echo '<tr>
                                            <td>' . $pmrssmid . '</td>
                                            <td>' . $userName . '</td>
                                            <td>' . $fatherName . '</td>
                                            <td>' . $createdOn . '</td>
                                            <td>
                                                <form action="" method="post">
                                                    <input type="hidden" name="stateId" value="' . $stid . '">
                                                    <input type="hidden" name="uidNumber" value="' . $flno . '">
                                                    <input type="hidden" name="userid" value="' . getUsersInfo('id') . '">
                                                    <input type="hidden" name="name" value="' . $userName . '">
                                                    <input type="hidden" name="familyId" value="' . $pmrssmid . '">
                                                    <input type="submit" name="saveayushman" class="btn btn-primary" value="Print Card">
                                                </form>
                                            </td>
                                        </tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-outline-danger active" data-bs-dismiss="modal" aria-label="Close">Close</button>
            </div>
        </div>
    </div>
</div>
</div>
<!-- JavaScript for Modal -->
    <script>
        function myFunction() {
            var state = document.getElementById("s1").value;
            var type = document.getElementById("p3").value;
            var value = document.getElementById("p1").value;
            if (state == '') {
                toastr.error("Please Select state");
            } else if (type == '') {
                toastr.error('Please select Proof');
            } else if (value == '') {
                if (type == 'R') {
                    var ss = 'Family Id';
                    toastr.error('Please enter ' + ss + ' ');
                } else if (type == 'A') {
                    var ss1 = 'AB PMJAY ID';
                    toastr.error('Please enter ' + ss1 + ' ');
                } else {
                    var ss2 = 'Aadhaar Number';
                    toastr.error('Please enter ' + ss2 + ' ');
                }
            } else {
                $("#proc_modal").modal('show');
                document.f1.submit();
            }
        }
    </script>


<?php
require_once('../layouts/mainFooter.php');
?>
<script>
    $(document).ready(function () {
        <?php if ($getResult === "true") : ?>
            $("#success_alert_modal").modal("show");
        <?php endif; ?>    
        <?php if ($getResult === "false") : ?>
            $("#error_alert_modal").modal("show");
        <?php endif; ?>
    });
</script>