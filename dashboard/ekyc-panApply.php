<?php
$pageName = "NSDL Pan Management"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');

if (base64_decode($_GET['accessToken']) === "KAVYAINFOTECH"){

if(isset($_GET['tryAgain']) && isset($_GET['orderId'])){
        
        $orderid = $_GET['orderId'];
        $response = performIncompleteApplication($orderid);
        
        $errorHndl = json_decode($response, true);
        if($errorHndl['status'] !== "Failure"){
        
        echo $response;
        die;    
        } else {
           echo '<script>toastr.error("' . $errorHndl['message'] . '");</script>';
            redirect(3000, 'ekyc-panApply?accessToken=S0FWWUFJTkZPVEVDSA=='); 
        }
}

    
if(isset($_POST['newPan']) && isset($_POST['number']) && isset($_POST['mode']) && isset($_POST['orderId'])){
    
    $number = $_POST['number'];
    $panMode = $_POST['mode'];
    $orderid = $_POST['orderId'];

    $amount = getUsersInfo('service_pricing_ekycpan');

    // Check if the amount is greater than the user's balance
    if ($amount > getUsersInfo('balance')) {
        echo '<script>toastr.error("Insufficient Balance. Please load balance.");</script>';
        redirect(3000, '');
    } else {    
        $response = performEkycNewApplication($number, $panMode, $orderid);
        
        $errors = json_decode($response, true);
        //echo     $response;
        if ($errors['status'] !== "Failure") {
        
        echo $response;
        
        // Debit the user's balance
        $new_bal = getUsersInfo('balance') - $amount;
        $sqlu = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
        $sqlu->execute([$new_bal, getUsersInfo('id')]);

        // Insert a transaction record
        $txnsql = "INSERT INTO `transactions`(`date_time`, `timestamp`, `userId`, `mode`, `type`, `amount`,`balance`, `reference`, `remark`, `status`)
         VALUES (:date_time,:timestamp,:userId,:mode,:type,:amount,:balance,:reference,:remark,:status)";
        $mode = 'Instan new pan';
        $type = 'debit';
        $remark = 'Instan New Pan Transaction - Requested by: ' . $number . ' (Reference Number: ' . $orderid . ')';
    
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
        $txn->bindParam(":reference", $orderid);
        $txn->bindParam(":remark", $remark);
        $txn->bindParam(":status", $status);
        if ($txn->execute()) {
        
        // Insert print record into the database
        $panInsert = "INSERT INTO `nsdlTransaction` (`userId`, `mobNumber`, `orderId`, `nsdlAck`, `time`, `date`, `status`) 
                         VALUES (:userId, :mobNumber, :orderId, :nsdlAck, :time, :date, :status)";
                         
        $nsdlAck = "NULL";
        $status = "process";
        
        $statementPan = $conn->prepare($panInsert);
        $userIdd = getUsersInfo('id');
        // Bind parameters
        $statementPan->bindParam(":userId", $userIdd);
        $statementPan->bindParam(":mobNumber", $number);
        $statementPan->bindParam(":orderId", $orderid);
        $statementPan->bindParam(":nsdlAck", $nsdlAck);
        $statementPan->bindParam(":time", $timestamp);
        $statementPan->bindParam(":date", $date);
        $statementPan->bindParam(":status", $status);
        $statementPan->execute();
        
        }
        
            
        } else {
           echo '<script>toastr.error("' . $errors['message'] . '");</script>';
           redirect(5000, '');
        }
        

}    
    
    
}
if(isset($_POST['changePan']) && isset($_POST['number']) && isset($_POST['mode']) && isset($_POST['orderId'])){
    
    $number = $_POST['number'];
    $panMode = $_POST['mode'];
    $orderid = $_POST['orderId'];

    $amount = getUsersInfo('service_pricing_ekycpan');

    // Check if the amount is greater than the user's balance
    if ($amount > getUsersInfo('balance')) {
        echo '<script>toastr.error("Insufficient Balance. Please load balance.");</script>';
        redirect(3000, '');
    } else {    

        $response = performCorrectionApplication($number, $panMode, $orderid);
        
        $errors = json_decode($response, true);
        //echo $response;
        if ($errors['status'] !== "Failure") {
        
        echo $response;
        
        // Debit the user's balance
        $new_bal = getUsersInfo('balance') - $amount;
        $sqlu = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
        $sqlu->execute([$new_bal, getUsersInfo('id')]);

        // Insert a transaction record
        $txnsql = "INSERT INTO `transactions`(`date_time`, `timestamp`, `userId`, `mode`, `type`, `amount`,`balance`, `reference`, `remark`, `status`)
         VALUES (:date_time,:timestamp,:userId,:mode,:type,:amount,:balance,:reference,:remark,:status)";
        $mode = 'Instan correction pan';
        $type = 'debit';
        $remark = 'Instan Correction Pan Transaction - Requested by: ' . $number . ' (Reference Number: ' . $orderid . ')';
    
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
        $txn->bindParam(":reference", $orderid);
        $txn->bindParam(":remark", $remark);
        $txn->bindParam(":status", $status);
        if ($txn->execute()) {
        
        // Insert print record into the database
        $panInsert = "INSERT INTO `nsdlTransaction` (`userId`, `mobNumber`, `orderId`, `nsdlAck`, `time`, `date`, `status`) 
                         VALUES (:userId, :mobNumber, :orderId, :nsdlAck, :time, :date, :status)";
                         
        $nsdlAck = "NULL";
        $status = "process";
        
        $statementPan = $conn->prepare($panInsert);
        $userIdd = getUsersInfo('id');
        // Bind parameters
        $statementPan->bindParam(":userId", $userIdd);
        $statementPan->bindParam(":mobNumber", $number);
        $statementPan->bindParam(":orderId", $orderid);
        $statementPan->bindParam(":nsdlAck", $nsdlAck);
        $statementPan->bindParam(":time", $timestamp);
        $statementPan->bindParam(":date", $date);
        $statementPan->bindParam(":status", $status);
        $statementPan->execute();
        echo $response;
        }
        
            
        } else {
           echo '<script>toastr.error("' . $errors['message'] . '");</script>';
           redirect(5000, '');
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
      <div class="col-xl-12">
         <div class="nav-align-top mb-4">
            <ul class="nav nav-tabs nav-fill" role="tablist">
               <li class="nav-item" role="presentation">
                  <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#new-ekyc-pan" aria-controls="new-ekyc-pan" aria-selected="true"><span class="d-none d-sm-block">New PAN Apply</span></button>
               </li>
               <li class="nav-item" role="presentation">
                  <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#change-pan-ekyc" aria-controls="change-pan-ekyc" aria-selected="false" tabindex="-1"><span class="d-none d-sm-block">Correction PAN Apply</span></button>
               </li>
               <li class="nav-item" role="presentation">
                  <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#Incomplete-pan" aria-controls="Incomplete-pan" aria-selected="false" tabindex="-1"><span class="d-none d-sm-block">Incomplete PAN</span></button>
               </li>
               <li class="nav-item" role="presentation">
                  <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#record-pan" aria-controls="record-pan" aria-selected="false" tabindex="-1"><span class="d-none d-sm-block">PAN History</span></button>
               </li>
            </ul>
            <div class="tab-content">
               <div class="tab-pane fade active show" id="new-ekyc-pan" role="tabpanel">
                  <form action="" method="POST">
                     <div class="row g-3">
                        <div class="col-md-4">

                           <input type="tel" name="number" id="number" placeholder="Enter Customer Mobile Number" class="form-control" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onKeyPress="if(this.value.length==10) return false;" required>

                           <div id="onactive" class="">
                              <select name="mode" id="mode" class="form-select select2 mt-3" required="">
                                 <option value="">Application Type</option>
                                 <option value="EKYC" selected>NSDL EKYC PAN (Instant Pan)</option>
                                 <option value="ESIGN">NSDL ESIGN PAN (Scan Based with photo and signature)</option>
                              </select>

                              <div class="input-group">
                                 <input class="form-control" type="hidden" placeholder="Order ID" name="orderId" value="<?= date('YmdHis') ?>" required>
                              </div>
                           </div>
                           <div class="pt-4">
                              <button type="submit" class="btn btn-primary me-sm-3 me-1" name="newPan">Submit</button>
                              <button type="reset" class="btn btn-label-secondary">Reset</button>
                           </div>
                        </div>
                        <div class="col-md-12 col-lg-8" id="card-block">
                           <div class="card h-100">
                              <h3 class="card-body text-center" id="blankPage"><img src="../assets/img/backgrounds/nsdl-kavya-banner.png" height="100%" width="550"></h3>
                              <div class="alert alert-dark mb-0" role="alert" style="border-radius: 0; font-size:12px">
                                 <span style="color:black"><b>Disclaimer</b></span> : While we facilitate PAN card applications, we strongly advise users to ensure the accuracy of their details and the authenticity of the process with the relevant authorities before proceeding. This includes verifying information provided during the NSDL eKYC process for PAN card applications.
                              </div>
                           </div>
                        </div>
                     </div>
                  </form>
               </div>
               <div class="tab-pane fade" id="change-pan-ekyc" role="tabpanel">
                  <form action="" method="POST">
                     <div class="row g-3">
                        <div class="col-md-4">

                           <input type="tel" name="number" id="number" placeholder="Enter Customer Mobile Number" class="form-control" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onKeyPress="if(this.value.length==10) return false;" required>

                           <div id="onactive" class="">
                              <select name="mode" id="mode" class="form-select select2 mt-3" required="">
                                 <option value="">Application Type</option>
                                 <option value="CEKYC" selected>NSDL EKYC PAN CORRECTION (Instant Pan)</option>
                                 <option value="CEKYC">NSDL ESIGN PAN CORRECTION (Scan Based with photo and signature)</option>
                              </select>

                              <div class="input-group">
                                 <input class="form-control" type="hidden" placeholder="Order ID" name="orderId" value="<?= date('YmdHis') ?>" required>
                              </div>
                           </div>
                           <div class="pt-4">
                              <button type="submit" class="btn btn-primary me-sm-3 me-1" name="changePan">Submit</button>
                              <button type="reset" class="btn btn-label-secondary">Reset</button>
                           </div>
                        </div>
                        <div class="col-md-12 col-lg-8" id="card-block">
                           <div class="card h-100">
                              <h3 class="card-body text-center" id="blankPage"><img src="../assets/img/backgrounds/nsdl-kavya-banner.png" height="100%" width="550"></h3>
                              <div class="alert alert-dark mb-0" role="alert" style="border-radius: 0; font-size:12px">
                                 <span style="color:black"><b>Disclaimer</b></span> : While we facilitate PAN card applications, we strongly advise users to ensure the accuracy of their details and the authenticity of the process with the relevant authorities before proceeding. This includes verifying information provided during the NSDL eKYC process for PAN card applications.
                              </div>
                           </div>
                        </div>
                     </div>
                  </form>
               </div>
               <div class="tab-pane fade" id="Incomplete-pan" role="tabpanel">
                  <div id="demo_info" class="p">
                     <table id="example" class="datatables-basic table border-top" style="width:100%">
                        <thead style="background: #000cad;">
                           <tr>
                              <th style="display: none;">#</th>
                              <th style="color: #fff;">ID / REF</th>
                              <th style="color: #fff;">Date</th>
                              <th style="color: #fff;">Mobile_No</th>
                              <th style="color: #fff;">Status</th>
                              <th style="color: #fff;">Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php
                            $conn = connectDB();
                            
                            $stmt = $conn->prepare("SELECT * FROM nsdlTransaction WHERE status != 'Complited' AND status != 'Failure' AND userId = ? ORDER BY id DESC");
                            $stmt->execute([getUsersInfo('id')]);

                            $sl=1;
                            while($row=$stmt->fetch()) {
                                
        		            // Display the table row
                                echo "<tr>
                                    <td style='display:none'>{$sl}</td>
                                    <td>{$row['orderId']}<br>" . getUsersInfo('username') . "</td>
                                    <td>" . date('d M Y : h:i A', strtotime($row['time'])) . "</td>
                                    <td>{$row['mobNumber']}</td>
                                    <td>{$row['status']}</td>
                                    <td>
                                    <form class='GET' action=''>
                                    <input type='hidden' name='accessToken' value='" . base64_encode('KAVYAINFOTECH') . "'>
                                    <input type='hidden' name='orderId' value='{$row['orderId']}'>
                                    <button name='tryAgain' type='submit' class='btn btn-info btn-sm'>Try again</button>
                                    </form>
                                    </td>

                                </tr>";
                                $sl++;
                            }
                            
                            ?>
                        </tbody>
                     </table>
                  </div>
               </div>
               <div class="tab-pane fade" id="record-pan" role="tabpanel">
                  <div id="demo_info" class="p">
                     <form method="post" action="" id="filterForm">
                        <div class="row">
                           <div class="col-3">
                              <div class="form-group">
                                 <label class="form-label" style="color: black;">Filter</label>
                                 <input type="text" class="form-control flatpickr-input" name="daterange" placeholder="<?php echo $date; ?> to YYYY-MM-DD" id="flatpickr-range" readonly="readonly">
                              </div>
                           </div>
                           <div class="col-2">
                              <div class="form-group">
                                 <label class="form-label" style="color: black;">By Status</label>
                                 <select name="status" class="form-select">
                                    <option value="">Select Status</option>
                                    <option value="process">Pending</option>
                                    <option value="success">Completed</option>
                                    <option value="rejected">Failed</option>
                                 </select>
                              </div>
                           </div>
                           <div class="col-7">
                              <div class="form-group">
                                 <label class="form-label" style="color: black;">Search</label>
                                 <input type="search" autocomplete="off" name="search" class="form-control border-danger" placeholder="Search By:- Reference Number / Mobile Number / Ack Number" autofocus />
                              </div>
                           </div>
                           <div class="col-5 mt-2 mb-2">
                              <div class="dt-action-buttons text-start pt-3 pt-md-0">
                                 <div class="dt-buttons">
                                    <!-- Modify the button to set the active state based on form submission -->
                                    <button class="dt-button create-new btn btn-primary <?php if(isset($_POST['daterange']) || isset($_POST['status']) || isset($_POST['search'])) echo 'active'; ?>" type="submit" name="submitBtn">Filter</button>

                                 </div>
                              </div>
                           </div>
                        </div>
                     </form>
                     <table id="example" class="datatables-basic table border-top" style="width:100%">
                        <thead style="background: #000cad;">
                           <tr>
                              <th style="display: none;">#</th>
                              <th style="color: #fff;">ID / REF</th>
                              <th style="color: #fff;">Date</th>
                              <th style="color: #fff;">Mobile_No</th>
                              <th style="color: #fff;">Ack_No</th>
                              <th style="color: #fff;">Status</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr class='hidden-item text-center text-danger'>
                              <td colspan='8' class='p-4'><b>Provide filter criteria exmp Mobile Number / Acknowledgment Number / Date /Reference Number</b></td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<script>
   // Function to handle form submission
   function handleFormSubmission(event) {
      event.preventDefault(); // Prevent the default form submission behavior

      // Fetch form data
      var formData = new FormData(document.getElementById("filterForm"));

      // Add a flag to indicate whether to apply filters or not
      formData.append('applyFilters', 'true');

      // Make an AJAX request
      $.ajax({
         url: "../system/ePan-data.php", // Specify the URL to send the request
         type: "POST", // Set the request type
         data: formData, // Set the data to send
         processData: false,
         contentType: false,
         success: function(response) {
            if (response !== undefined && response.trim() !== '') {
               // Update the table body with the fetched data
               $("#example tbody").html(response);
               $('.hidden-item').addClass('d-none');
            } else {
               $("#example tbody").html("<tr class='text-center text-danger'><td colspan='8' class='p-4'>Oops...! data not found</td></tr>");
            }
         },

         error: function(xhr, status, error) {
            // Handle error response
            console.error(error);
         }
      });
   }

   // Add event listener to the form submit button
   document.getElementById('filterForm').addEventListener('submit', handleFormSubmission);
</script>

<? } else {
        echo '<div class="misc-wrapper text-center" style="margin-top: 100px">
    <span class="text-danger"><i class="bi bi-exclamation-triangle display-1 text-primary"></i></span>
    <h2 class="mb-2 mx-2">Method Not Allowed</h2>
    <p class="mb-4 mx-2 text-danger">You do not have permission to view this page using the credentials that you have provided while logging in. <br> Please contact your site administrator.</p>
    <a href="index" class="btn btn-primary">Back to Home</a>
</div>
';
    } ?>
<!-- Add this JavaScript code after your HTML content -->

<?php
require_once('../layouts/mainFooter.php');
?>