<?php
$pageName = "Voter Print Advance"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');
?>
<script type="text/javascript">
  function readURL(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function (e) {
        $('#blah')
          .attr('src', e.target.result);
      };

      reader.readAsDataURL(input.files[0]);
    }
  }
</script>
<?php

$getResult = false;
$resultSuccess = true;
if (isset($_POST['get_details']) && isset($_FILES['photo']) && !empty($_FILES['photo']['tmp_name'])) {

    if (getimagesize($_FILES['photo']['tmp_name'])) {
        $imageData = file_get_contents($_FILES['photo']['tmp_name']);
        $base64Image = base64_encode($imageData);
    } else {
        echo '<script>toastr.error("The uploaded file is not a valid image.");</script>';
        redirect(3000, '');
    }


    $getResult = true;
    $epic_no = $_POST['epicno'];
    $captcha = $_POST['captcha'];
    $captchaID = $_POST['captchaID'];

    $amount = getUsersInfo('service_pricing_voter');

    // Check if the amount is greater than the user's balance
    if ($amount > getUsersInfo('balance')) {
        echo '<script>toastr.error("Insufficient Balance. Please load balance.");</script>';
        $getResult = false;
        redirect(3000, '');
    } else {
        
        $response = performVoterVerification($epic_no, $captchaID, $captcha);

        $data = json_decode($response, true);
        //echo $response;
        if ($data['StatusCode'] === 200) {
            
        $getResult = false;
        $resultSuccess = true;
        echo '<script>toastr.info("Voter data not found / ECI server is currently down. Please try again later.");</script>';
        redirect(3000, '');
            
        } else {
        $resultSuccess = false;

        // Debit the user's balance
        $new_bal = getUsersInfo('balance') - $amount;
        $sqlu = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
        $sqlu->execute([$new_bal, getUsersInfo('id')]);

        // Insert a transaction record
        $txnsql = "INSERT INTO `transactions`(`date_time`, `timestamp`, `userId`, `mode`, `type`, `amount`,`balance`, `reference`, `remark`, `status`)
         VALUES (:date_time,:timestamp,:userId,:mode,:type,:amount,:balance,:reference,:remark,:status)";
        $mode = 'Voter Print';
        $type = 'debit';
        $remark = 'Voter Print Transaction - Requested by: ' . $data['fullName'] . ' (e-EPIC Number: ' . $data['epicNumber'] . ')';
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

    $firstItem = $data;
    $epicNumber = $firstItem['epicNumber'];
    $fullName = $firstItem['fullName'];
    $fullNameHindi = $firstItem['fullNameHindi'];
    $relationName = $firstItem['relationName'];
    $relationNameHindi = $firstItem['relationNameHindi'];
    $tahsil = $firstItem['tahsil'];
    $tahsilHindi = $firstItem['tahsilHindi'];
    $dist = $firstItem['district'];
    $distHindi = $firstItem['districtHindi'];
    $state = $firstItem['state'];
    $stateHindi = $firstItem['stateHindi'];
    $portNo = $firstItem['partNumber'];
    $portName = $firstItem['partName'];
    $portNameHindi = $firstItem['partNameHindi'];
    $asmblyName = $firstItem['assemblyName'];
    $asmblyNameHindi = $firstItem['assemblyNameHindi'];
    $acNumber = $firstItem['acNumber'];
    $age = $firstItem['age'];
    $reln_type = $firstItem['relationType'];
    $gender = $firstItem['gender'];
        

        
        
    }

}}}

// Check if the form is submitted
if (isset($_POST['saveVoterData'])) {
    
    // Retrieve form data
    $name = $_POST['name'];
    $epicno = $_POST['epicno'];
    $image_data = $_POST['image_data'];
    $print_type = "Voter Card";
    $printData = $_POST;

    
    $Mremark = 'Name: '. $name." - e-Epic Number: ".$epicno;

    $adhar_insert = "INSERT INTO `printRecords` (`name`, `idNumber`, `userId`, `print_type`, `photo`, `date`, `time` , `printData`) VALUES (:name, :idNumber, :userId, :print_type, :photo, :date, :time, :printData)";

        $eEpic = $conn->prepare($adhar_insert);
        $userIdd = getUsersInfo('id');
        $eEpic->bindParam(":name", $name);
        $eEpic->bindParam(":idNumber", $epicno);
        $eEpic->bindParam(":userId", $userIdd);
        $eEpic->bindParam(":date", $date);
        $eEpic->bindParam(":time", $timestamp);
        $eEpic->bindParam(":print_type", $print_type);
        $eEpic->bindParam(":photo", $image_data);
        $eEpic->bindParam(":printData", json_encode($printData, JSON_UNESCAPED_UNICODE));
                

                // Execute the query
    if ($eEpic->execute()) {
        // Display success toastr
        echo '<script>toastr.success("Voter Download successful from '.$Mremark.'.");</script>';
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
      <!-- ---------------------  Voter Print Start  ---------------- -->
      <div class="col-lg-10 d-flex align-items-strech m-auto <?php echo $getResult ? 'd-none' : ''; ?> ">
         <div id="errors" class="card text-bg-primary border-0 w-100">
            <div class="card balance-box mx-2 mb-2 mt-2">
               <div class="">
                  <div class="card-body m-auto mx-auto">
                     <form enctype="multipart/form-data" action="" name="vform" method="POST">
                        <div class="form-body">
                           <div class="row">
                              <div class="col-md-6">
                                 <div class="mb-3">
                                    <label class="form-label">PHOTO</label>
                                    <input type="text" class="form-control" name="photo" id="photo" accept=".jpg, .jpeg"/>
                                    <span style="color:red; font-size: 14px" id="photo-error"></span>
                                 </div>
                              </div>
                              <div class="col-md-6">
                                 <!-- Added text-center class to center form elements -->
                                 <div class="mb-3">
                                    <label class="form-label">E-EPIC NUMBER</label>
                                    <input type="text" class="form-control" placeholder="Enter voter e-epic number" name="epicno" id="epicno" regex=“^[A-Z]{3}[0-9]{7}$” autofocus>
                                    <input class="form-control" name="get_details" id="get_details" type="hidden"/>
                                    <span style="color:red; font-size: 14px" id="epic-error"></span>
                                 </div>
                              </div>
                           </div>
                           <div class="row">
                              <!-- Added text-center class to center form elements -->
                              <div class="col-md-4">
                                 <div class="mb-3">
                                    <label class="form-label">CAPTCHA</label>
                                    <input type="text" class="form-control" placeholder="Enter captcha code" name="captcha" id="captcha"/>
                                    <input class="form-control border-primary me-2" name="captchaID" id="captchaID" value="<?=$captchaID?>" type="hidden">
                                    <span style="color:red; font-size: 14px" id="captcha-error"></span>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="input-group">
                           <button type="button" class="btn btn-danger active">Processing Fees ₹ <?= getUsersInfo('service_pricing_voter') ?></button>
                           <button type="button" class="btn btn-primary font-medium" onclick="Print_pay()"> Get Details </button>
                        </div>
                     </form>
                     <p class="mt-4 text-danger"><b>Keep in mind that the format and specific details on a voter card can vary between countries and regions. If you are looking for information specific to a particular location, it's recommended to refer to the guidelines provided by the relevant election commission or electoral authority in that area.</b></p>
                  </div>
               </div>
            </div>
         </div>
      </div>
<!-- ------------------------- Aadhaar Details Fetch Success ---------------------------- -->
<div class="col-lg-12 m-auto p-t-10 <?php echo $resultSuccess ? 'd-none' : ''; ?>">
   <div class="card border">
      <div class="card-body">
          <div class="figure d-block">
            <blockquote class="blockquote"> 
            <b style="color:black">Your e-EPIC Card has been Fetch Successfully</b>
            </blockquote>
          </div>
         <form method="post" enctype="multipart/form-data" action="">
            <div class="row mb-3">
               <label class="col-sm-2 col-form-label" for="basic-default-name">Name</label>
               <div class="col-sm-5">
                  <input class="form-control " type="text" value="<?php echo strtoupper($fullName); ?>" readonly>
               </div>
               <div class="col-sm-2">
                  <img src="data:image/jpeg;base64,<?= $base64Image ?>" height="90" width="70">
               </div>
            </div>
            <div class="row mb-3" style="margin-top: -50px">
               <label class="col-sm-2 col-form-label" for="basic-default-name"><?php echo $reln_type; ?> Name</label>
               <div class="col-sm-5">
                  <input class="form-control" type="text" value="<?php echo strtoupper($relationName); ?>" readonly>
                <input class="form-control" name="image_data" type="hidden" value="data:image/jpeg;base64,<?= $base64Image ?>" require>
                <input class="form-control" name="username" readonly="readonly" type="hidden" value="<?php echo $userdata['username']; ?>" require>
                <input class="form-control" name="epicno" readonly="readonly" type="hidden" value="<?php echo $epicNumber; ?>" require>
                <input class="form-control" name="ps_name" readonly="readonly" type="hidden" value="<?php echo $fullName; ?>" require>
                <input class="form-control" name="ps_name_v1" readonly="readonly" type="hidden" value="<?php echo $fullNameHindi; ?>" require>
                <input class="form-control" name="slno_inpart" readonly="readonly" type="hidden" value="<?php echo $slno_inpart; ?>" require>
                <input class="form-control" name="name" id="name" readonly onfocus="this.removeAttribute('readonly');" type="hidden" value="<?php echo strtoupper($fullName); ?>">
                <input class="form-control" name="namelocal" id="namelocal" readonly onfocus="this.removeAttribute('readonly');" type="hidden" value="<?php echo $fullNameHindi; ?>">
                <input class="form-control" name="dobadhar" readonly onfocus="this.removeAttribute('readonly');" id="dob" type="hidden" value="<?php echo $age; ?>" required>
                <input class="form-control " id="gender" readonly onfocus="this.removeAttribute('readonly');" name="gender" type="hidden" value="<?php echo $gender; ?>" required>
                <input class="form-control" id="spouse" name="father/husband" readonly onfocus="this.removeAttribute('readonly');" id="dob" type="hidden" value="<?php echo $reln_type; ?>" required>
                <input class="form-control" name="fathername" id="fathername" readonly onfocus="this.removeAttribute('readonly');" type="hidden" value="<?php echo $relationName; ?>" required placeholder="Father/Husband Name">
                <input class="form-control" name="fathernamelocal" id="fathernamelocal" readonly onfocus="this.removeAttribute('readonly');" type="hidden" value="<?php echo $relationNameHindi; ?>" required placeholder="Father/Husband Name(Local Language)">
                <input class="form-control" readonly onfocus="this.removeAttribute('readonly');" name="tahshil" id="tehsil" type="hidden" value="<?php echo ucfirst(strtolower($tahsil)); ?>" required placeholder="Tahshil">
                <input class="form-control" readonly onfocus="this.removeAttribute('readonly');" name="district" id="district" type="hidden" value="<?php echo ucfirst(strtolower($dist)); ?>" required placeholder="District">
                <input class="form-control" id="assemblyconnameno" readonly onfocus="this.removeAttribute('readonly');" name="assemblyconnameno" type="hidden" value="<?php echo $acNumber.' - '.$asmblyName; ?>" required placeholder="Assembly Constituency Number and Name">
                <input class="form-control" id="assemblyconnamenolocal" readonly onfocus="this.removeAttribute('readonly');" name="assemblyconnamenolocal" type="hidden" value="<?php echo $acNumber.' - '.$asmblyNameHindi; ?>" required placeholder="Assembly Constituency Number and Name Local Language">
                <input class="form-control" required name="partno" readonly onfocus="this.removeAttribute('readonly');" type="hidden" value="<?php echo $portNo; ?>" required placeholder="Part Number">
                <input class="form-control" readonly onfocus="this.removeAttribute('readonly');" required id="partname" name="partname" type="hidden" value="<?php echo ucfirst(strtolower($portName)); ?>" required placeholder="Part Name">
                <input class="form-control" id="partnamelocal" readonly onfocus="this.removeAttribute('readonly');" name="partnamelocal" type="hidden" value="<?php echo $portNameHindi; ?>" required placeholder="Part Name(Local Language)">
                <input class="form-control" id="houseno" name="houseno" type="hidden" value="<?php echo $txtbuld; ?>">
                <input class="form-control" id="gali" name="gali" type="hidden" value="<?php echo $txtgali; ?>">
                <input class="form-control" id="locality" name="locality" type="hidden" value="<?php echo $txtlocality; ?>">
                <input class="form-control" id="vtcandpost" name="vtcandpost" type="hidden" value="<?php echo $txtpost; ?>">
                <input class="form-control" id="dist" name="dist" type="hidden" value="<?php echo $dist; ?>">
                <input class="form-control" id="statename" name="statename" oninput="setHouseNo()" type="hidden" value="<?php echo $state; ?>">
                <input class="form-control" id="aadharname" name="aadharname" type="hidden" value="<?php echo $aadharname; ?>">
                <input class="form-control" id="aadharfathername" name="aadharfathername" type="hidden" value="<?php echo $aadharfname; ?>">
                <input class="form-control" id="genderlocal" name="genderlocal" type="hidden" value="">
                <input class="form-control" id="birthtithi" name="birthtithi" type="hidden" value="BirthTithi / Age ">
                <input class="form-control" id="birthtithilocal" name="birthtithilocal" type="hidden" value="">
                <input class="form-control" id="pata" name="pata" type="hidden" value="address">
                <input class="form-control" id="patalocal" name="patalocal" type="hidden" value="">
                <input class="form-control" id="spousenamelocal" name="spousenamelocal" type="hidden" value="">
                <input class="form-control" id="kaname" name="kaname" type="hidden" value="Ka Name">
                <input class="form-control" id="kanamelocal" name="kanamelocal" type="hidden" value="">
                <input class="form-control" id="sex" name="sex" readonly type="hidden" value="Sex">
                <input class="form-control" id="sexlocal" name="sexlocal" type="hidden" value="">
                <input class="form-control" id="sign" name="sign" type="hidden" value="Electoral Registration Officer">
                <input class="form-control" id="signlocal" name="signlocal" type="hidden" value="">
                <input class="form-control" id="assconnameno" name="assconnameno" type="hidden" value="Assembly Constituency Number and Name">
                <input class="form-control" id="assconnamenolocal" name="assconnamenolocal" type="hidden" value="">
                <input class="form-control" id="partnoandname" name="partnoandname" type="hidden" value="Part Number and Name">
                <input class="form-control" id="partnoandnamelocal" name="partnoandnamelocal" type="hidden" value="">
                <textarea class="form-control" id="txtTarget" style="height:55px" name="addresslocal" rows="10" type="text" hidden></textarea>
               </div>
            </div>
            <div class="row mb-3">
               <label class="col-sm-2 col-form-label" for="basic-default-company">e-EPIC Number</label>
               <div class="col-sm-5">
                  <input type="text" class="form-control" placeholder="e-EPIC Number" value="<?php echo $epicNumber; ?>" readonly>
               </div>
            </div>
            <div class="row mb-3">
               <label class="col-sm-2 col-form-label" for="basic-default-email">Full Address</label>
               <div class="col-sm-5">
                  <textarea class="form-control" style="height:55px" id="txtSource" name="address" rows="5" type="text" required></textarea>
               </div>
            </div>
            <div class="row">
               <label class="col-sm-2 col-form-label" for="basic-default-phone">Language</label>
               <div class="col-sm-2">
                  <input class="form-control border-danger" id="pincode" name="pincode" placeholder="Pin Code" type="tel" min="6" required>
               </div>
               <div class="col-sm-2">
              <input class="form-control border-danger" id="houseNo" placeholder="House No" name="houseNo" oninput="setHouseNo()" type="text" title="Please fill houseNo Number" required>
              </div>
               <div class="col-sm-3">
                  <select class="form-select border-danger" oninput="changelang()" name="language" id="lang1" required disabled>
                  <option value="">Select Language</option>
                  <option value="HI">Hindi</option>
                  <option value="PA">Punjabi</option>
                  <option value="GU">Gujrati</option>
                  <option value="MR">Marathi</option>
                  <option value="TA">Tamil</option>
                  <option value="KN">Kannada</option>
                  <option value="BN">Bengali</option>
                  <option value="TE">Telugu</option>
                  <option value="SD">Sindhi</option>
                  <option value="OR">Oriya</option>
                </select>
               </div>
               <div class="col-sm-3">
                  <button type="submit" class="btn btn-primary disabled" name="saveVoterData" id="pdfDownloadBtn">PDF Download</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>
<!-- ------------------------- Aadhaar Details Fetch Success End---------------------------- -->

      
   </div>
</div>

<?php
require_once('../layouts/mainFooter.php');
?>
<script>
function Print_pay() {
    var captchaInput = document.getElementById("captcha").value;
    var epicno = document.getElementById("epicno").value;
    var photo = document.getElementById("photo").value;
    if (photo === "") {
        toastr.error('Please Upload Photo.');
    } else if (epicno === "") {
        toastr.error('Please enter your e-EPIC number');
    } else if (captchaInput === "") {
        toastr.error('Please enter captcha code');
    } else {
        document.vform.submit();
        $('#epic_no').html(epicno);
    }
}
$(document).ready(function() {
    $('#pay').modal({
        backdrop: 'static',
        keyboard: false
    });
});
</script>
