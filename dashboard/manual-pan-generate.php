<?php
$pageName = "Manual PAN Generate"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');

// Form submission
if(isset($_POST['save'])) {
    
    $panNo = $_POST['panNo'];
    $name = $_POST['name'];
    $fname = $_POST['fname'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $imgUrl = $_POST['imgUrl'];
    $signUrl = $_POST['signUrl'];
    $amount = '50';

    // Check if the amount is greater than the user's balance
    if ($amount > getUsersInfo('balance')) {
        echo '<script>toastr.error("Insufficient Balance. Please load balance.");</script>';
        redirect(3000, '');
        $getresult = false;
    } else {
    
    // Debit the user's balance
    $new_bal = getUsersInfo('balance') - $amount;
    $sqlu = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
    $sqlu->execute([$new_bal, getUsersInfo('id')]);

    // Insert a transaction record
    $txnsql = "INSERT INTO `transactions`(`date_time`, `timestamp`, `userId`, `mode`, `type`, `amount`,`balance`, `reference`, `remark`, `status`)
     VALUES (:date_time,:timestamp,:userId,:mode,:type,:amount,:balance,:reference,:remark,:status)";
    $mode = 'Manual Pan Generate';
    $type = 'debit';
    $remark = 'Manual Pan Generate Transaction - Requested by: ' . $name . ' (PAN Number: ' . $panNo . ')';

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

    // Create an array with print data
    $printData = array(
        'name' => $name,
        'panNo' => $panNo,
        'fatherName' => $fname,
        'dob' => $dob,
        'gender' => $gender,
        'photo' => $imgUrl,
        'sign' => $signUrl,
    );
    $print_type = 'Manual Pan Generate';
    // Insert print record into the database
    $pan_insert = "INSERT INTO `printRecords` (`name`, `idNumber`, `reqId`, `userId`, `print_type`, `photo`, `date`, `time` , `printData`) 
                     VALUES (:name, :idNumber, :reqId, :userId, :print_type, :photo, :date, :time, :printData)";

    $pan = $conn->prepare($pan_insert);
    $userIdd = getUsersInfo('id');
    // Bind parameters
    $pan->bindParam(":name", $name);
    $pan->bindParam(":idNumber", $panNo);
    $pan->bindParam(":reqId", $reference);
    $pan->bindParam(":userId", $userIdd);
    $pan->bindParam(":date", $date);
    $pan->bindParam(":time", $timestamp);
    $pan->bindParam(":print_type", $print_type);
    $pan->bindParam(":photo", $imgUrl);
    $pan->bindParam(":printData", json_encode($printData, JSON_UNESCAPED_UNICODE));
    
    
    if ($pan->execute()) {
        echo '<script>toastr.success("PAN Download successful from.");</script>';
        //redirect(3000, 'printRecord');
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
}}
?>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 m-auto">
<div class="card">
   <h5 class="card-header">
       <div class="figure d-block" style="margin-bottom: -20px">
            <blockquote class="blockquote"> 
            <b style="color:black;">Manual PAN Card PDF Generate</b>
            </blockquote>
          </div></h5>
   <!-- Account -->
   <hr class="my-0">
   
   <!-- GET JOB CARD DATA -->
   <div class="card-body">
      <form method="POST" class="fv-plugins-bootstrap5 fv-plugins-framework" action="" id="myForm">
         <div class="row">
            <div class="mb-3 col-md-3 fv-plugins-icon-container">
               <label for="panNo" class="form-label">Pan Number</label>
               <input type="text" class="form-control" name="panNo" id="panNo" placeholder="Enter Pan Number" required oninput="getPanDetails()" />
            </div>
            <div class="mb-3 col-md-3 fv-plugins-icon-container">
               <label for="name" class="form-label">Applicant Name</label>
               <input type="text" class="form-control" name="name" id="name" placeholder="Enter Applicant Name" required />
            </div>
            <div class="mb-3 col-md-3 fv-plugins-icon-container">
               <label for="fname" class="form-label">Father Name</label>
               <input type="text" class="form-control" name="fname" id="fname" placeholder="Enter Father Name" required />
            </div>
            <div class="mb-3 col-md-3 fv-plugins-icon-container">
               <label for="dob" class="form-label">Date of birth</label>
               <input type="text" class="form-control" name="dob" id="dob" placeholder="Enter Date of birth" required />
            </div>
            <div class="mb-3 col-md-3 fv-plugins-icon-container">
               <label for="gender" class="form-label">Gender</label>
               <input type="text" class="form-control" name="gender" id="gender" placeholder="Enter Gender" required />
            </div>
            <div class="mb-3 col-md-3 fv-plugins-icon-container">
               <label for="photo" class="form-label">Photo Upload</label>
               <input type="file" class="form-control" name="photo" id="photo" required />
               <img id="photoPreview" src="#" alt="Photo Preview" style="max-width: 100px; display: none;">
            </div>
            <div class="mb-3 col-md-3 fv-plugins-icon-container">
               <label for="sign" class="form-label">signature Upload</label>
               <input type="file" class="form-control" name="sign" id="sign" required />
               <img id="signPreview" src="#" alt="Signature Preview" style="max-width: 100px; display: none;">
            </div>
            <div class="col-md-12">
                  <input class="btn btn-danger" type="button" id="viewButton" value="View">
                  <input type="hidden" id="imgUrl" name="imgUrl">
                  <input type="hidden" id="signUrl" name="signUrl">
                  <input class="btn btn-primary" type="submit" name="save" value="Submit">
                </div>
            </div>
          </form>
     </div>
     <!-- GET JOB CARD DATA -->
   </div>
</div>
</div>
</div>
</div>
<style>
    .panNo {
       position: absolute;
        top: 85px;
        left: 115px;
        font-size: 13px; 
        color: black;
        font-weight: bold;
    }
    .panName {
       position: absolute;
        top: 133px;
        left: 17px;
        font-size: 12px; 
        color: black;
        font-weight: bold;
    } 
    .panFname {
       position: absolute;
        top: 162px;
        left: 17px;
        font-size: 12px; 
        color: black;
        font-weight: bold;
    } 
    .panBirth {
       position: absolute;
        top: 195px;
        left: 17px;
        font-size: 12px; 
        color: black;
        font-weight: bold;
    } 
    .panPhoto {
       position: absolute;
        top: 53px;
        left: 17px;
        font-size: 12px; 
        color: black;
        font-weight: bold;
    } 
    .panSign {
       position: absolute;
        top: 180px;
        left: 120px;
        font-size: 12px; 
        color: black;
        font-weight: bold;
    }
</style>
<!-- Modal -->
<div class="modal fade" id="showPanCard" tabindex="-1" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-lg modal-simple modal-edit-user">
    <div class="modal-content">
      <div class="modal-body p-0 pt-0 pb-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center p-0">
          <p>
              <span class="panNo" id="panCardNumber"></span>
              <span class="panName" id="appName"></span>
              <span class="panFname" id="faName"></span>
              <span class="panBirth" id="dobBirth"></span>
              <span class="panPhoto" id="img"></span>
              <span class="panSign" id="sigNature"></span>
              <img src="../assets/img/backgrounds/panbackggg.png" height="100%" width="700">
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
    $(document).ready(function () {
        // Function to convert file to Base64 string
        function encodeImageFileAsURL(file, callback) {
            var reader = new FileReader();
            reader.onloadend = function () {
                callback(reader.result);
            }
            reader.readAsDataURL(file);
        }

        // Retrieve file input values and generate image URLs
        function generateImageUrls() {
            var photo = $("#photo")[0].files[0];
            var sign = $("#sign")[0].files[0];

            // Check if photo is uploaded
            if (photo) {
                encodeImageFileAsURL(photo, function (photoUrl) {
                    $('#img').html("<img src='" + photoUrl + "' height='100%' width='55'>");
                    $('#imgUrl').val(photoUrl);
                });
            }

            // Check if signature is uploaded
            if (sign) {
                encodeImageFileAsURL(sign, function (signUrl) {
                    $('#sigNature').html("<img src='" + signUrl + "' height='20' width='100'>");
                    $('#signUrl').val(signUrl);
                });
            }
        }

        // Call the function to generate image URLs initially
        generateImageUrls();

        // Attach a change event listener to the file inputs to regenerate image URLs when files are selected
        $("#photo, #sign").change(function () {
            generateImageUrls();
        });

        // Attach a click event listener to the viewButton
        $("#viewButton").click(function () {
            // Retrieve text input values
            var panCardNo = $("#panNo").val();
            var name = $("#name").val();
            var fname = $("#fname").val();
            var dob = $("#dob").val();
            var gender = $("#gender").val();

            $('#panCardNumber').html(panCardNo);
            $('#appName').html(name);
            $('#faName').html(fname);
            $('#dobBirth').html(dob);
            $('#Gender').html(gender);

            // Show the modal
            $("#showPanCard").modal('show');
        });
    });
</script>
<script>

function getPanDetails() {
    var pan = document.getElementById('panNo').value;

    // Check if PAN number length is 10
    if (pan.length === 10) {
        // Making the API call through your own proxy
        fetch("../system/VerifyYourPanDeatils.php?pan=" + pan)
            .then(response => response.json())
            .then(data => {
                // Update form fields with response data
                document.getElementById('name').value = data.name || '';
                document.getElementById('fname').value = data.father_name || '';
                document.getElementById('dob').value = data.dob || '';
                document.getElementById('gender').value = data.gender || '';
                // Update other fields similarly
            })
            .catch(error => {
                console.error('Error:', error);
                // Handle error
            });
    }
}


</script>
<?php
require_once('../layouts/mainFooter.php');
?>