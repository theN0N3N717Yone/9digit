<?php
$pageName = "Driving Licence Print"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
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

if (isset($_POST['get_details']) && isset($_POST['dlno']) && !empty($_POST['dlno']) && isset($_POST['dob']) && !empty($_POST['dob'])) {
    $dlNumber = $_POST['dlno'];
    $dob = $_POST['dob'];
    
        // Payment processing
        $amount = getUsersInfo('service_pricing_driving_licence');

        if ($amount > getUsersInfo('balance')) {
            echo '<script>toastr.error("Insufficient Balance. Please load balance.");</script>';
            redirect(3000, '');
        } else {
            
        
            $response = performLicenceVerification($dlNumber, $dob);

            $data = json_decode($response, true);
            echo $response;
            if ($data['StatusCode'] === 200) {
                $getResult = false;
                $resultSuccess = true;
                echo '<script>toastr.info("Licence data not found / Licence server is currently down. Please try again later.");</script>';
                //redirect(3000, '');
            } else {
                $uname = $data['ownerName'];
                $idNo = $data['dlno'];
                $getResult = true;
                $resultSuccess = false;

            
            
            
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://api.remove.bg/v1.0/removebg');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            $post = array(
                'image_file_b64' => $data['sign'],
                'size' => 'auto'
            );
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            
            $headers = array();
            $headers[] = 'X-Api-Key: GJDTSrZw7Xd7nejFf7o6UYkh';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            
            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
            $fp=fopen('../printManagement/printBackground/'.$data["dlNumber"].'.png','wb');
            fwrite($fp,$result);
            fclose($fp);
            
            $file = $data["dlNumber"].'.png';
            $file_link = '../printManagement/printBackground/' . $file; 
            
            
            
            $new_bal = getUsersInfo('balance') - $amount;
            $sqlu = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
            $sqlu->execute([$new_bal, getUsersInfo('id')]);

                // Insert a transaction record
                $txnsql = "INSERT INTO `transactions`(`date_time`, `timestamp`, `userId`, `mode`, `type`, `amount`,`balance`, `reference`, `remark`, `status`)
                 VALUES (:date_time,:timestamp,:userId,:mode,:type,:amount,:balance,:reference,:remark,:status)";
                $mode = 'Licence Print';
                $type = 'debit';
                $remark = 'Licence Print Transaction - Requested by: ' . $uname . ' (Driving Licence Number: ' . $idNo . ')';
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
    $dlno = $_POST['dl_no'];
    $name = $_POST['name'];
    $photo = $_POST['photo'];
    $printData = $_POST;
    $print_type = "Driving Licence";

    $Mremark = 'Name: ' . $name . " - Driving Licence Number: " . $dlno;

    $licence_insert = "INSERT INTO `printRecords` (`name`, `idNumber`, `reqId`, `userId`, `print_type`, `photo`, `date`, `time`, `printData`)
                    VALUES (:name, :idNumber, :reqId, :userId, :print_type, :photo, :date, :time, :printData)";
    $userIdd = getUsersInfo('id');
    $licenceInsert = $conn->prepare($licence_insert);
    $licenceInsert->bindParam(":name", $name);
    $licenceInsert->bindParam(":idNumber", $dlno);
    $licenceInsert->bindParam(":reqId", $reference);
    $licenceInsert->bindParam(":userId", $userIdd);
    $licenceInsert->bindParam(":date", $date);
    $licenceInsert->bindParam(":time", $timestamp);
    $licenceInsert->bindParam(":print_type", $print_type);
    $licenceInsert->bindParam(":photo", $photo);
    $licenceInsert->bindParam(":printData", json_encode($printData, JSON_UNESCAPED_UNICODE));

    // Execute the query
    if ($licenceInsert->execute()) {
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

<!-- -------------------- Get Driving Licence Details ------------------------ -->
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
                              <label class="form-label">Driving Licence No</label>
                              <input type="text" class="form-control" name="dlno" id="dlno" placeholder="Enter your driving licence number" autofocus oninput="this.value = this.value.toUpperCase()" />
                           </div>
                        </div>
                        <div class="col-md-6">
                           <!-- Added text-center class to center form elements -->
                           <div class="mb-3">
                              <label class="form-label">Date Of Birth</label>
                              <input class="form-control" id="dob" name="dob" tabindex="9" oninput="formatDate(this)" maxlength="10" required type="text" autocomplete="off" placeholder="XX-XX-XXXX">

                              <input class="form-control" name="get_details" id="get_details" type="hidden" />
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="input-group">
                     <button type="button" class="btn btn-danger active">Fees ₹ <?= getUsersInfo('service_pricing_driving_licence') ?></button>
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
<!-- -------------------- Get Driving Licence Details End ------------------------ -->

<!-- -------------------- Driving Licence Success Details --------------------- -->
<div class="col-lg-12 d-flex align-items-strech m-auto <?php echo $resultSuccess ? 'd-none' : ''; ?> ">
   <div id="errors" class="card text-bg-primary border-0 w-100">
      <div class="card mx-2 mb-2 mt-2">
         <div class="card-body">
             <div class="figure d-block">
                <blockquote class="blockquote"> 
                <b style="color:black">Your driving licence has been Fetch Successfully</b>
                </blockquote>
              </div>
            <form method="post" action="">
               <div class="row">
                  <label class="col-sm-2 col-form-label" for="basic-default-name">License Number :</label>
                  <div class="col-sm-5">
                     <input type="text" class="form-control" placeholder="DL Number" value="<?php echo $data['dlNumber']; ?>" readonly>
                  </div>
                  <div class="col-sm-2">
                     <img src="<?= $data['photo']?>" height="100%" width="70">
                  </div>
               </div>
               <div class="row mb-3" style="margin-top: -40px">
                  <label class="col-sm-2 col-form-label" for="basic-default-company">Name :</label>
                  <div class="col-sm-5">
                     <input class="form-control " type="text" value="<?php echo strtoupper($data['ownerName']); ?>" readonly>
                  </div>
               </div>
               <div class="mb-3">
                  <div class="row">
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-sm-4 col-form-label" for="basic-default-email">Father Name :</label>
                           <div class="col-md-8">
                              <input class="form-control" type="text" value="<?php echo strtoupper($data['fatherName']); ?>" readonly>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-sm-2 col-form-label" for="basic-default-phone">State :</label>
                           <div class="col-md-8">
                              <input type="hidden" value="<?= $data['id']  ?>" name="dlno" required>
                              <input type="hidden" value="<?= $data['dlNumber']  ?>" name="dl_no" required>
                              <input type="hidden" value="<?= $data['ownerName']  ?>" name="name" required>
                              <input type="hidden" value="<?= $data['dob']  ?>" name="dob" required>
                              <input type="hidden" value="<?= $data['fatherName']  ?>" name="fathername" required>
                              <input type="hidden" value="<?= $data['bloodGroup']  ?>" name="bgroup" required>
                              <input type="hidden" value="<?= $data['email']  ?>" name="email" required>
                              <input type="hidden" value="<?= $data['mobile']  ?>" name="mobile" required>
                              <input type="hidden" value="<?= $data['gender']  ?>" name="gender" required>
                              <input type="hidden" value="<?= $data['address']  ?>" name="address" required>
                              <input type="hidden" value="<?= $data['rto']  ?>" name="rto" required>
                              <input type="hidden" value="<?= $data['typeVehicle']  ?>" name="typeofvehicle" required>
                              <input type="hidden" value="<?= $data['dlType']  ?>" name="dltype" required>
                              <input type="hidden" value="<?= $data['iDate']  ?>" name="idate" required>
                              <input type="hidden" value="<?= $data['expiryDate']  ?>" name="expirydate" required>
                              <input type="hidden" value="<?= $data['photo']  ?>" name="photo" required>
                              <input type="hidden" value="<?= $file_link  ?>" name="sign" required>

                              <select class="select2 form-control" name="state" id="state" required>
                                 <option value="" disabled selected>Select State</option>
                                 <?php foreach ($states as $state): ?>
                                 <option value="<?php echo $state['State']; ?>"><?php echo ucwords($state['State']); ?></option>
                                 <?php endforeach; ?>
                              </select>
                              <span id="errorlanguage" class="error"></span>
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
                              <select id="district" name="dto-dist" class="select2 form-control" required></select>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-sm-2 col-form-label" for="basic-default-phone">Zip :</label>
                           <div class="col-md-8">
                              <input type="text" class="form-control" name="pincode" placeholder="Pin CODE" required>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-sm-3">
                     <button type="submit" class="btn btn-primary active" name="savedata">PDF Download</button>
                  </div>
                  <div class="driving-license-download-success__card mt-2" style="color:red;">
                    <span class="driving-license-download-success__card-text">The Driving License PDF Password will be in 8 characters</span>
                    <span class="driving-license-download-success__card-text">Combination of the first four letters of your name (as in Driving License) in CAPITAL letters and Year of Birth in YYYY format.</span>
                    <div class="driving-license-download-success__line"></div>
                    <span class="driving-license-download-success__card-text">Example: If your name is JOHN SMITH and your Year of Birth is 1985</span>
                    <span class="driving-license-download-success__card-text">Then your Driving License password is JOHN1985</span>
                </div>

               </div>
            </form>
         </div>
      </div>
   </div>
</div>
<!-- -------------------- Driving Licence Success Details End --------------------- -->
</div>
</div>
<?php
require_once('../layouts/mainFooter.php');
?>
<script>
function Print_pay() {
    var dlno = document.getElementById("dlno").value;
    var dob = document.getElementById("dob").value;
    if (dlno === "") {
        toastr.error("Please enter your Licence number.");
    } else if (dob === "") {
        toastr.error("Please enter date of birth.");
    } else {

        document.vform.submit();
    }
}
</script>
<script>
    $(document).ready(function () {
        $('#state').on('change', function () {
            var state = $(this).val();
            var districtDropdown = $('#district');
            
            // Perform AJAX request
            $.ajax({
                url: "../system/getState-distPin.php",
                type: "GET",
                data: { state: state },
                dataType: "json",
                success: function (response) {
                    updateDropdowns(response);
                },
                error: function (xhr, status, error) {
                    console.error("AJAX request failed:", status, error);
                }
            });
        });
    });

    function updateDropdowns(data) {
    var districtSelect = document.getElementById("district");
    districtSelect.innerHTML = ""; // Clear previous options

    var uniqueDistricts = new Set(); // Use a set to store unique district names

    for (var i = 0; i < data.length; i++) {
        var districtName = data[i].District;

        // Check if the district name is not already in the set
        if (!uniqueDistricts.has(districtName)) {
            var districtOption = document.createElement("option");
            districtOption.value = districtName;
            districtOption.text = districtName;
            districtSelect.appendChild(districtOption);

            // Add the district name to the set to track uniqueness
            uniqueDistricts.add(districtName);
        }
    }
}

</script>
<script>
function formatDate(input) {
    var value = input.value.replace(/\D/g, '');

    if (value.length > 4) {
        value = value.substring(0, 2) + '-' + value.substring(2, 4) + '-' + value.substring(4, 8);
    } else if (value.length > 2) {
        value = value.substring(0, 2) + '-' + value.substring(2, 4);
    }
    
    input.value = value;
}
</script>