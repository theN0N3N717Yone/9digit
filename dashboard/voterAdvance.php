<?php
$pageName = "Voter Print Advance"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');
$url = getPortalInfo('apiUrl') . '/serviceApi/V1/voterCaptcha';

// Data to be sent in the POST request
$data = array(
    'apiKey' => getPortalInfo('accessToken'),
);

// HTTP options for the POST request
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    ),
);

// Create a stream context with the specified options
$context  = stream_context_create($options);

// Send the POST request and get the response
$response = file_get_contents($url, false, $context);

// Output the response
$result = json_decode($response, true);
    $captchaBase64 = $result['captcha'];
    $captchaID = $result['captchaId'];


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

    $amount = getUsersInfo('service_pricing_voter');

    // Check if the amount is greater than the user's balance
    if ($amount > getUsersInfo('balance')) {
        echo '<script>toastr.error("Insufficient Balance. Please load balance.");</script>';
        $getResult = false;
        redirect(3000, '');
    } else {
        
        $getResult = true;
        $epic_no = $_POST['epicno'];
        $captcha = $_POST['captcha'];
        $captchaID = $_POST['captchaID'];

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

    $adhar_insert = "INSERT INTO `printRecords` (`name`, `idNumber`, `reqId`, `userId`, `print_type`, `photo`, `date`, `time` , `printData`) VALUES (:name, :idNumber, :reqId, :userId, :print_type, :photo, :date, :time, :printData)";

        $eEpic = $conn->prepare($adhar_insert);
        $userIdd = getUsersInfo('id');
        $eEpic->bindParam(":name", $name);
        $eEpic->bindParam(":idNumber", $epicno);
        $eEpic->bindParam(":reqId", $reference);
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
                                    <input type="file" class="form-control" name="photo" id="photo" accept=".jpg, .jpeg"/>
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
                              <div class="col-md-2">
                                 <div class="mb-3">
                                    <label class="form-label" style="margin-top: 35px; margin-left: -20px"> </label>
                                    <img class="me-2" src="data:image/png;base64,<?=$captchaBase64?>" alt="Captcha Image" id="captchaImage" height="37">
                                 </div>
                              </div>
                              <div class="col-md-2">
                                 <div class="mb-3">
                                    <label class="form-label" style="margin-top: 35px; margin-left: -20px"> </label>
                                    <span class="input-group-icon border-none" id="refreshCaptcha"><img src="../assets/img/icons/sprinticon/refresh.svg" height="37" id=""></span>
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
<script>
  function setHouseNo() {
    var houseNo = document.getElementById("houseNo");
    var lang1 = document.getElementById("lang1");
    setHouse();
    if (houseNo.value) {
      lang1.disabled = false;
    } else {
      lang1.disabled = true;
      setHouse();
    }
  }
</script>
<script type="text/javascript">
      function readURL(input) {
        if (input.files && input.files[0]) {
            
          var reader = new FileReader();

          reader.onload = function (e) {
            document.getElementById('imgerror').innerHTML = '';
            $('#blah').attr('src', e.target.result);
          };

          reader.readAsDataURL(input.files[0]);
        }
      }
      
      function setHouse() {
        var houseno = document.getElementById('houseNo').value;
        if (houseno.trim() != "") {
          galimn = houseno;
        }
        var partname = document.getElementById('partname').value;
        var tahsil = document.getElementById('tehsil').value;
        var dist = document.getElementById('district').value;
        var state = document.getElementById('statename').value;
        var pinc = document.getElementById('pincode').value;
        document.getElementById('txtSource').innerHTML = "House No" + galimn + ", " + partname + ", " + tahsil + ", " + dist + ", " + state + " - " + pinc;
        document.getElementById('txtSource').readOnly = true;

      }
      function setaddress() {
        var galimn = "";

        var houseno = document.getElementById('houseno').value;
        if (houseno.trim() != "") {
          galimn = houseno;
        }
        var gali = document.getElementById('gali').value;
        if (gali.trim() != "") {
          galimn = galimn + gali;
        }

        var locality = document.getElementById('locality').value;
        var vtcandpost = document.getElementById('vtcandpost').value;
        var dist = document.getElementById('dists').value;
        var state = document.getElementById('statename').value;
        var pincode = document.getElementById('pincodes').value;
        var policestation = document.getElementById('policestation').value;
        var tahshil = document.getElementById('tahshil').value;

        document.getElementById('txtSource').innerHTML = galimn + locality + " Police Station-" + policestation + ", Tahshil-" + tahshil + ", District-" + dist + ", Pin Code-" + pincode;
      }
    </script>
<script type="text/javascript">
  function validation() {

    var aadharno = document.getElementById('aadharno').value;
    if (aadharno.length < 12) {
      document.getElementById('erroraadharno').innerHTML = " **Please Enter 12 Digit Aadhaar Card Number !!!";
      document.getElementById('aadharno').style.border = "1px solid red";
      document.getElementById('aadharno').focus();
      return false;
    }

    var txtSource = document.getElementById('txtSource').value;
    if (txtSource.trim() == "") {
      document.getElementById('errortxtSource').innerHTML = " **Please Enter Address !!!";
      document.getElementById('txtSource').style.border = "1px solid red";
      document.getElementById('txtSource').focus();
      return false;
    }

    var name_regional = document.getElementById('name_regional').value;
    if (name_regional.trim() == "") {
      document.getElementById('errorname_regional').innerHTML = " **Please Enter Name in Local Language !!!";
      document.getElementById('name_regional').style.border = "1px solid red";
      document.getElementById('name_regional').focus();
      return false;
    }

    var txtTarget = document.getElementById('txtTarget').value;
    if (txtTarget.trim() == "") {
      document.getElementById('errortxtTarget').innerHTML = " **Please Enter Local Language Address !!!";
      document.getElementById('txtTarget').style.border = "1px solid red";
      document.getElementById('txtTarget').focus();
      return false;
    }

  }
</script>

<script type="text/javascript">
  //English to hindi translate code
  function changelang() {
      
      
    //alert("123456789");
    var lang = document.getElementById("lang1").value;
    //alert(lang);
    var url =
      "https://translate.googleapis.com/translate_a/single?client=gtx";
    url += "&sl=" + 'EN';
    url += "&tl=" + lang;
    url += "&dt=t&q=" + escape($("#txtSource").val());
    //alert(url);
    $.get(url, function (data, status) {
      //	alert(data);
      //	alert(status);
      var result = '';
      for (var i = 0; i <= 500; i++) {
        result += data[0][i][0];
        //        alert(result);
        $("#txtTarget").val(result);

      }
    });

    url =
      "https://translate.googleapis.com/translate_a/single?client=gtx";
    url += "&sl=" + 'EN';
    url += "&tl=" + lang;
    url += "&dt=t&q=" + escape($("#name").val());
    //alert(url);
    $.get(url, function (data, status) {
      var result = '';
      for (var i = 0; i <= 500; i++) {
        result += data[0][i][0];
        // alert(result);
        $("#name_regional").val(result);

      }
    });


    var gen = $("#gender").val();
    url =
      "https://translate.googleapis.com/translate_a/single?client=gtx";
    url += "&sl=" + 'EN';
    url += "&tl=" + lang;
    url += "&dt=t&q=" + escape(gen.toLowerCase());
    //alert(url);
    $.get(url, function (data, status) {
      var result = '';
      for (var i = 0; i <= 500; i++) {
        result += data[0][i][0];
        // alert(result);

        if (result == 'नर') {
          result = 'पुरुष';
        }
        $("#genderlocal").val(result);

      }
    });

    url =
      "https://translate.googleapis.com/translate_a/single?client=gtx";
    url += "&sl=" + 'EN';
    url += "&tl=" + lang;
    url += "&dt=t&q=" + escape($("#birthtithi").val());
    //alert(url);
    $.get(url, function (data, status) {
      var result = '';
      for (var i = 0; i <= 500; i++) {
        result += data[0][i][0];
        //alert(result);
        $("#birthtithilocal").val(result);

      }
    });


    url =
      "https://translate.googleapis.com/translate_a/single?client=gtx";
    url += "&sl=" + 'EN';
    url += "&tl=" + lang;
    url += "&dt=t&q=" + escape($("#pata").val());
    //alert(url);
    $.get(url, function (data, status) {
      var result = '';
      for (var i = 0; i <= 500; i++) {
        result += data[0][i][0];
        // alert(result);
        $("#patalocal").val(result);

      }
    });

    url = "";
    url = "https://translate.googleapis.com/translate_a/single?client=gtx";
    url += "&sl=" + 'EN';
    url += "&tl=" + lang;
    url += "&dt=t&q=" + escape($("#assconnameno").val());
    //alert(url);
    $.get(url, function (data, status) {
      var result = '';
      for (var i = 0; i <= 500; i++) {
        result += data[0][i][0];
        // alert(result);
        $("#assconnamenolocal").val(result);

      }
    });

    url = "";
    url = "https://translate.googleapis.com/translate_a/single?client=gtx";
    url += "&sl=" + 'EN';
    url += "&tl=" + lang;
    url += "&dt=t&q=" + escape($("#partnoandname").val());
    //alert(url);
    $.get(url, function (data, status) {
      var result = '';
      for (var i = 0; i <= 500; i++) {
        result += data[0][i][0];
        // alert(result);
        $("#partnoandnamelocal").val(result);

      }
    });

    url = "";
    url = "https://translate.googleapis.com/translate_a/single?client=gtx";
    url += "&sl=" + 'EN';
    url += "&tl=" + lang;
    url += "&dt=t&q=" + escape($("#sign").val());
    //alert(url);
    $.get(url, function (data, status) {
      var result = '';
      for (var i = 0; i <= 500; i++) {
        result += data[0][i][0];
        // alert(result);
        $("#signlocal").val(result);

      }
    });

    url = "";
    url = "https://translate.googleapis.com/translate_a/single?client=gtx";
    url += "&sl=" + 'EN';
    url += "&tl=" + lang;
    url += "&dt=t&q=" + escape($("#sex").val());
    //alert(url);
    $.get(url, function (data, status) {
      var result = '';
      for (var i = 0; i <= 500; i++) {
        result += data[0][i][0];
        // alert(result);
        $("#sexlocal").val(result);

      }
    });

    url = "";
    url = "https://translate.googleapis.com/translate_a/single?client=gtx";
    url += "&sl=" + 'EN';
    url += "&tl=" + lang;
    url += "&dt=t&q=" + escape($("#spouse").val());
    //alert(url);
    $.get(url, function (data, status) {
      //	alert(data);
      //	alert(status);
      var result = '';
      for (var i = 0; i <= 500; i++) {
        result += data[0][i][0];
        // alert(result);
        $("#spousenamelocal").val(result);

      }
    });

    url = "";
    url = "https://translate.googleapis.com/translate_a/single?client=gtx";
    url += "&sl=" + 'EN';
    url += "&tl=" + lang;
    url += "&dt=t&q=" + escape($("#kaname").val());
    //alert(url);
    $.get(url, function (data, status) {
      var result = '';
      for (var i = 0; i <= 500; i++) {
        result += data[0][i][0];
        // alert(result);
        $("#kanamelocal").val(result);

      }
    });
    setTimeout(function() {
    $('#pdfDownloadBtn').removeClass('disabled');
  }, 1000);
    
  };
//Words and Characters Count    
</script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
<script>

  $('#language').on('change', function () {

    if ($(this).val() != '' && $(this).val() == 'OR') {
      var langs = $(this).val();
      var lang = langs.toLowerCase();
      var name = $("#name").val();
      var address = $("#txtSource").val();

      $.post("https://xknfdjdjfktit3kktied3rifcdddsrtwq89764dspt4krktgoe48kjdjbds.com/admin/test.php", { lang: lang, name: name, address: address }).done(function (data) {
        //alert(data);
        var json = JSON.parse(data);
        //alert(json.data);
        $("[name='namelocal']").val(json.name.replace(/"/g, ''));
        $("[name='addresslocal']").val(json.address.replace(/"/g, ''));
      })

      var dob = $("#fathername").val();
      var gender = $("#partname").val();

      $.post("https://xknfdjdjfktit3kktied3rifcdddsrtwq89764dspt4krktgoe48kjdjbds.com/admin/test.php", { lang: lang, name: dob, address: gender }).done(function (data) {
        //alert(data);
        var json = JSON.parse(data);
        //alert(json.data);
        $("[name='fathernamelocal']").val(json.name.replace(/"/g, ''));
        $("[name='partnamelocal']").val(json.address.replace(/"/g, ''));
      })

      var dob = $("#assemblyconnameno").val();
      var gender = $("#kaname").val();

      $.post("https://xknfdjdjfktit3kktied3rifcdddsrtwq89764dspt4krktgoe48kjdjbds.com/admin/test.php", { lang: lang, name: dob, address: gender }).done(function (data) {
        //alert(data);
        var json = JSON.parse(data);
        //alert(json.data);
        $("[name='assemblyconnamenolocal']").val(json.name.replace(/"/g, ''));
        $("[name='kanamelocal']").val(json.address.replace(/"/g, ''));
      })

      var dob = $("#sex").val();
      var gender = $("#sign").val();

      $.post("https://xknfdjdjfktit3kktied3rifcdddsrtwq89764dspt4krktgoe48kjdjbds.com/admin/test.php", { lang: lang, name: dob, address: gender }).done(function (data) {
        //alert(data);
        var json = JSON.parse(data);
        //alert(json.data);
        $("[name='sexlocal']").val(json.name.replace(/"/g, ''));
        $("[name='signlocal']").val(json.address.replace(/"/g, ''));
      })

      var dob = $("#assconnameno").val();
      var gender = $("#partnoandname").val();

      $.post("https://xknfdjdjfktit3kktied3rifcdddsrtwq89764dspt4krktgoe48kjdjbds.com/admin/test.php", { lang: lang, name: dob, address: gender }).done(function (data) {
        //alert(data);
        var json = JSON.parse(data);
        //alert(json.data);
        $("[name='assconnamenolocal']").val(json.name.replace(/"/g, ''));
        $("[name='partnoandnamelocal']").val(json.address.replace(/"/g, ''));
      })

      var dob = $("#birthtithi").val();
      var gender = $("#pata").val();

      $.post("https://xknfdjdjfktit3kktied3rifcdddsrtwq89764dspt4krktgoe48kjdjbds.com/admin/test.php", { lang: lang, name: dob, address: gender }).done(function (data) {
        //alert(data);
        var json = JSON.parse(data);
        //alert(json.data);
        $("[name='birthtithilocal']").val(json.name.replace(/"/g, ''));
        $("[name='patalocal']").val(json.address.replace(/"/g, ''));
      })

      var dob = $("#spouse").val();


      $.post("https://xknfdjdjfktit3kktied3rifcdddsrtwq89764dspt4krktgoe48kjdjbds.com/admin/test.php", { lang: lang, name: dob, address: gender }).done(function (data) {
        //alert(data);
        var json = JSON.parse(data);
        //alert(json.data);
        $("[name='spousenamelocal']").val(json.name.replace(/"/g, ''));

      })


    }
    else {
      changelang();
    }

  });

  function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
      return false;
    }
    return true;
  }

  jQuery('#myModald').modal('show');
  $("#step2").hide();
  $("#finish_data").on("click", function () {

    var sname = $("#spouse").val();
    var police = $("#policestation").val();
    var teh = $("#tahshil").val();
    var pin = $("#pincode").val();
    var dist = $("#dists").val();
    var dobs = $("#dobs").val();
    var langu = $("#language").val();
    if (teh == '' || police == '' || sname == 0 || pin == '' || dist == '' || dobs == '' || langu == 0) {
      alert('Please Fill All Details!!');
      return false;
    }
    else {
      $("#spousename").val(sname);
      $("#tahshil1").val(teh);
      $("#police").val(police);
      $("#language").val(langu);
      $("#dob").val(dobs);
      $('#myModald').modal('hide');
      return false;
    }
  });
</script>

<script>
  //document.getElementById('button').addEventListener('click', function() {
  document.getElementById('img_v1').change(function () {
    var files = document.getElementById('file').files;
    if (files.length > 0) {
      getBase64(files[0]);
    }
  });

  function getBase64(file) {
    var reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = function () {

      document.getElementById("myImg").src = reader.result;
      document.getElementById("img_vl").value = reader.result;
      $("#step1").hide();
      $("#step2").show();
    };
    reader.onerror = function (error) {
      console.log('Error: ', error);
    };
  }
</script>
<script>
  // Get the file input element
  const photoInput = document.getElementById('photo');
  // Get the preview image element
  const previewImg = document.getElementById('preview');

  // Listen for changes in the file input
  photoInput.addEventListener('change', function (event) {
    // Get the selected file
    const file = event.target.files[0];

    // Check if a file is selected
    if (file) {
      // Create a FileReader object
      const reader = new FileReader();

      // Set the image source when it's loaded
      reader.onload = function (e) {
        previewImg.src = e.target.result;
        previewImg.style.display = 'block'; // Show the preview image
      };

      // Read the file as a data URL
      reader.readAsDataURL(file);
    }
  });
</script>

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
<script>
    $(document).ready(function () {
        // Function to refresh captcha
        function refreshCaptcha() {
            // Show the loading spinner
            $('#refreshCaptcha img').addClass('animate__animated animate__rotateOut');
            $.ajax({
                type: 'POST',
                url: '../system/voterCaptcha.php', // Update with the correct path to your PHP script
                data: {},
                dataType: 'json', // Expect JSON response
                success: function (data) {
                    if (data.status === true && data.statusCode === "100") {
                        // Update captcha image and ID
                        $('#captchaImage').attr('src', 'data:image/png;base64,' + data.captcha);
                        $('#captchaID').val(data.captchaId);
                    } else {
                        alert('Failed to retrieve CAPTCHA: ' + data.message);
                    }
                },
                error: function () {
                    alert('Error refreshing captcha.');
                },
                complete: function () {
                    // Hide the loading spinner after the request is complete
                    $('#refreshCaptcha img').removeClass('animate__animated animate__rotateOut');
                }
            });
        }

        // Bind refresh click event
        $('#refreshCaptcha').click(function () {
            refreshCaptcha();
        });
    });
</script>