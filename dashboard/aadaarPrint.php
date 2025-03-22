<?php
$pageName = "Aadhaar Print Advance"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');

// Check if the Aadhaar verification form is submitted
if (isset($_POST['bioenc'])) {
    $getresult = true;
    $bio = $_POST['bioenc'];
    $aadhar = $_POST['aadhar'];

    $amount = getUsersInfo('service_pricing_aadhaar');

        // Check if the amount is greater than the user's balance
        if ($amount > getUsersInfo('balance')) {
            echo '<script>toastr.error("Insufficient Balance. Please load balance.");</script>';
            redirect(3000, '');
            $getresult = false;
        } else {
            
            // Aadhaar verification api functions
            $response = performAadhaarVerification($aadhar, $bio);
            
            $json = json_decode($response, true);
            
            echo $response;
            $img = $json['image']; 
            $uid = $json['aadhar'];
            $name = $json['name'];
            $dob = $json['dobadhar'];
            $gender=$json['gender'];
            $txtadd = $json['address'];
            
            // Check if Aadhaar verification is successful
            if ($json['statusCode'] === 200) {
                $getresult = true;

    
            // Debit the user's balance
            $new_bal = getUsersInfo('balance') - $amount;
            $sqlu = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
            $sqlu->execute([$new_bal, getUsersInfo('id')]);

            // Insert a transaction record
            $txnsql = "INSERT INTO `transactions`(`date_time`, `timestamp`, `userId`, `mode`, `type`, `amount`,`balance`, `reference`, `remark`, `status`)
             VALUES (:date_time,:timestamp,:userId,:mode,:type,:amount,:balance,:reference,:remark,:status)";
            $mode = 'Aadhar Print';
            $type = 'debit';
            $remark = 'Aadhar Print Transaction - Requested by: ' . $name . ' (Aadhar Number: ' . $uid . ')';

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
                // Transaction successful
            }
        } else {
        $getresult = false;
        //$UidaiError = false;
        echo '<script>toastr.info("Biometric capture issue. or Aadhaar server is currently down. Please try again later.");</script>';
        //redirect(3000, '');



    }
    } 
}

// Check if the data saving form is submitted
if (isset($_POST['savedata'])) {
    // Retrieve form data
    $name = $_POST['name'];
    $address = $_POST['address'];
    $aadharno = $_POST['aadharno'];
    $username = $_POST['username'];
    $dobadhar = $_POST['dobadhar'];
    $houseno = $_POST['houseno'];
    $street = $_POST['street'];
    $pincode = $_POST['pincode'];
    $vtc = $_POST['vtcandpost'];
    $dist = $_POST['dist'];
    $state = $_POST['statename'];
    $language = $_POST['language'];
    $imgdata = $_POST['imgdata'];
    $ddate = $_POST['ddate'];
    $namelocal = $_POST['namelocal'];
    $birthtithilocal = $_POST['birthtithilocal'];
    $gender = $_POST['gender'];
    $birthtithi = $_POST['birthtithi'];
    $pata = $_POST['pata'];
    $patalocal = $_POST['patalocal'];
    $genderlocal = $_POST['genderlocal'];
    $addresslocal = $_POST['addresslocal'];

    // Define print type
    $print_type = "Aadhaar Card";

    // Create an array with print data
    $printData = array(
        'name' => $name,
        'address' => $address,
        'aadharno' => $aadharno,
        'username' => $username,
        'dobadhar' => $dobadhar,
        'houseno' => $houseno,
        'street' => $street,
        'pincode' => $pincode,
        'vtcandpost' => $vtc,
        'dist' => $dist,
        'statename' => $state,
        'language' => $language,
        'imgdata' => $imgdata,
        'ddate' => $ddate,
        'namelocal' => $namelocal,
        'birthtithilocal' => $birthtithilocal,
        'gender' => $gender,
        'birthtithi' => $birthtithi,
        'pata' => $pata,
        'patalocal' => $patalocal,
        'genderlocal' => $genderlocal,
        'addresslocal' => $addresslocal
    );

    // Set a remark for the transaction
    $Mremark = 'Name: ' . $name . " - Aadhar Number: " . $aadharno;

    // Insert print record into the database
    $adhar_insert = "INSERT INTO `printRecords` (`name`, `idNumber`, `reqId`, `userId`, `print_type`, `photo`, `date`, `time` , `printData`) 
                     VALUES (:name, :idNumber, :reqId, :userId, :print_type, :photo, :date, :time, :printData)";

    $adhar = $conn->prepare($adhar_insert);
    $userIdd = getUsersInfo('id');
    // Bind parameters
    $adhar->bindParam(":name", $name);
    $adhar->bindParam(":idNumber", $aadharno);
    $adhar->bindParam(":reqId", $reference);
    $adhar->bindParam(":userId", $userIdd);
    $adhar->bindParam(":date", $date);
    $adhar->bindParam(":time", $timestamp);
    $adhar->bindParam(":print_type", $print_type);
    $adhar->bindParam(":photo", $imgdata);
    $adhar->bindParam(":printData", json_encode($printData, JSON_UNESCAPED_UNICODE));

    // Execute the query
    if ($adhar->execute()) {
        // Display success toastr
        echo '<script>toastr.success("Aadhaar Download successful from '.$Mremark.'.");</script>';
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
<!-- Aadhaar Print biometric capacitor -->
<div class="col-lg-4 d-flex align-items-strech m-auto <?php echo $getresult ? 'd-none' : ''; ?> ">
   <div id="errors" class="card text-bg-primary border-0 w-100">
      <div class="card mx-2 mb-2 sprint-box mt-2">
         <div class="card-body">
            <div class="mb-3">
               <div class="d-flex align-items-center">
                  <h5 class="mb-0 mt-0 uidai">Enter your aadhaar number <span class="text-danger">*</span></h5>
               </div>
               <div class="mt-1">
                  <div class="input-group">
                     <button class="btn bg-danger rounded-start" type="button" style="color: #fff">
                        UID
                     </button>
                     <input name="ctl00$ContentPlaceHolder1$txtUID" aria-label="ctl00$ContentPlaceHolder1$txtUID" aria-describedby="basic-addon-search31" type="text" autofocus maxlength="12" oninput="verify()" id="txtUID" class="form-control vd_Required A_AadharNo" aria-describedby="Help" autocomplete="off" autofocus placeholder="9999 XXXX XXXX" />

                  </div>
                  <span style="font-size:12px; color:red;" id="basic-addon-search32"></span>
               </div>
               <div class="form-check form-check-inline mt-2">
                  <input name="collapsible-address-type" class="form-check-input" type="radio" value="" id="collapsible-address-type-office" required>
                  <label class="form-check-label text-primary" for="collapsible-address-type-office"> Terms and Conditions: </label>
                </div>
            </div>
            <div class="">
               <div class="">
                  <div id="ready">
                     <a href="javascript:void(0);" class="xxctyOkaM8bvp4lm2amz"><img class="Xj6seOK2KDoCGIvsPERK" id="capture" src="../assets/img/icons/ready.png">
                        <p class="LFvZ84K5eG9rZIj8aH7y" style="color: green;"> <b>Device connected. please capture your right finger</b></p>
                     </a>
                  </div>
                  <div id="successcap" class="d-none">
                     <a href="javascript:void(0)" class="xxctyOkaM8bvp4lm2amz"><img class="Xj6seOK2KDoCGIvsPERK" src="../assets/img/icons/yes.png">
                        <p class="LFvZ84K5eG9rZIj8aH7y" style="color: green;"> <b>Finger Captured successfully</b></p>
                     </a>
                  </div>
                  <div id="scaning" class="d-none">
                     <a href="javascript:void(0)" class="xxctyOkaM8bvp4lm2amz"><img class="Xj6seOK2KDoCGIvsPERK" src="../assets/img/icons/fingerprint-scan.gif">
                        <p class="LFvZ84K5eG9rZIj8aH7y" style="color: green;"> <b>Finger scanned please wait!</b></p>
                     </a>

                  </div>
                  <div id="timeout" class="d-none">
                     <a href="javascript:void(0)" class="xxctyOkaM8bvp4lm2amz"><img class="Xj6seOK2KDoCGIvsPERK" src="../assets/img/icons/not.png">
                        <p class="LFvZ84K5eG9rZIj8aH7y" style="color: green;"> <b>Fingerprint capture timed out. Please try again</b></p>
                     </a>

                  </div>
                  <div id="notready" class="">
                     <a href="javascript:void(0)" class="xxctyOkaM8bvp4lm2amz"><img class="Xj6seOK2KDoCGIvsPERK" src="../assets/img/icons/not.png">
                        <p class="LFvZ84K5eG9rZIj8aH7y" style="color: red;"> <b>Device not connected. Please try again.</b></p>
                     </a>
                  </div>
                  <div id="notplgin" class="">
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- End Aadhaar Print biometric capacitor -->

<!-- Hidden Form for Data Submission -->
<form  action="" method="post" name='f1' style="display:none;">
  <input type="hidden" name="aadhar" id="aadhar"/>
  <textarea name="bioenc" id="biodata"></textarea>
</form>

<!-- Aadhaar Details Fetch Success -->
<div class="col-lg-12 m-auto p-t-10 <?= $getresult ? '' : 'd-none' ?>">
   <div class="card border">
      <div class="card-body">
          <div class="figure d-block">
            <blockquote class="blockquote"> 
            <b style="color:black;">Your Aadhaar Card has been Fetch Successfully</b>
            <p style="color:red; font-size: 14px;">
                The Aadhaar letter PDF Password will be in 8 characters Combination of the first four letter of your name (as in Aadhaar) in CAPITAL letters and Year of Birth in YYYY format. Example : Your name is ANISH Y KUMAR Your Year of Birth is 1989 Then your e-Aadhaar password is ANIS1989
            </p>
            </blockquote>
          </div>
         <form method="post" action="" id="myForm">
            <div class="row mb-3">
               <label class="col-sm-2 col-form-label" for="basic-default-name">Aadhaar Name :</label>
               <div class="col-sm-5">
                  <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" value="<?php  echo $name; ?>" required readonly>
               </div>
               <div class="col-sm-2">
                  <img src="<?= $img ?>" height="100%" width="70">
               </div>
            </div>
            <div class="row mb-3" style="margin-top: -40px">
               <label class="col-sm-2 col-form-label" for="basic-default-name">Full Address</label>
               <div class="col-sm-5">
                  <input type="text" class="form-control" id="txtSource" name="address" value="<?php echo $txtadd; ?>" required readonly>
               </div>
            </div>
            <div class="row mb-3">
               <label class="col-sm-2 col-form-label" for="basic-default-company">Aadhaar Number</label>
               <div class="col-sm-5">
                  <input class="  form-control " maxlength="12" id="aadharno" name="aadharno" type="text" value="<?php echo $uid;?>" required readonly>
               </div>
            </div>
            <div class="row mb-3">
               <label class="col-sm-2 col-form-label" for="basic-default-email">Date of Birth</label>
               <div class="col-sm-5">
                  <input class="form-control  " name="username" type="hidden" value="<?php  echo time(); // $userdata['username']; ?>" required readonly>
                  <input class="form-control  " name="dobadhar" type="text" value="<?php  echo $dob ;?>" required readonly>
                  <input class="form-control  " name="houseno" type="hidden" value="<?php echo $houseno; ?>">
                  <input class="form-control " name="street" type="hidden" value="<?php echo $street ?>">
                  <input class="form-control " name="pincode" type="hidden" value="<?php echo $pincode; ?>">
                  <input class="form-control " name="vtcandpost" type="hidden" value="<?php echo $vtc; ?>">
                  <input class="form-control " name="dist" type="hidden" value="<?php echo $dist; ?>">
                  <input class="form-control " name="statename" type="hidden" value="<?php echo $state; ?>">
               </div>
            </div>
            <div class="row">
               <label class="col-sm-2 col-form-label" for="basic-default-phone">Language</label>
               <div class="col-sm-5">
                  <select autofocus class="form-control" name="language" id="language" required>
                     <option value="">Select Language</option>
                     <option value="HI">Hindi</option>
                     <option value="PA">Punjabi</option>
                     <option value="GU">Gujarati</option>
                     <option value="MR">Marathi</option>
                     <option value="TA">Tamil</option>
                     <option value="KN">Kannada</option>
                     <option value="BN">Bengali</option>
                     <option value="TE">Telugu</option>
                     <option value="OR">Oriya</option>
                     <option value="SD">Sindhi</option>
                  </select>
               </div>
               <div class="col-sm-3">
                  <input type="hidden" value="<?php echo $img; ?>" name="imgdata">
                  <?php

                $timestamp = date( "Y-m-d" );

                ?>
                  <input type="hidden" class="form-control" name="ddate" id="exampleFormControlInput1" placeholder="12/12/2019" value="<?php echo $timestamp;?>" required>
                  <input type="hidden" class="form-control" id="name_regional" name="namelocal" placeholder="Enter Name" required>
                  <input class=" mng_cp form-control " id="birthtithilocal" name="birthtithilocal" type="hidden" value="">
                  <input type="hidden" class="form-control" name="gender" id="gender" value="<?php  echo $gender; ?> " placeholder="Male">
                  <input class="form-control " id="birthtithi" name="birthtithi" readonly="readonly" type="hidden" value="Birth Tithi">
                  <input class="form-control " id="pata" name="pata" readonly="readonly" type="hidden" value="address">
                  <input class="form-control " id="patalocal" name="patalocal" readonly="readonly" type="hidden" value="">
                  <input type="hidden" class="form-control" name="genderlocal" id="genderlocal">
                  <input type="hidden" id="txtTarget" name="addresslocal">
                  <button class="btn btn-primary" type="submit" name="savedata">PDF Download</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>
<!-- End Aadhaar Details Fetch Success -->
</div>
</div>
<!-- Modal for Aadhar Download Guidelines -->
<div class="modal-onboarding modal fade animate__animated show" id="aadharDownloadModal" tabindex="-1" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content text-center">
      <div class="modal-header border-0">
        <a class="text-muted close-label" href="javascript:void(0);" data-bs-dismiss="modal">Skip Intro</a>
      </div>
      <div id="modalHorizontalCarouselControls" class="carousel slide pb-4 mb-2" data-bs-interval="false">
        <div class="carousel-indicators">
          <button type="button" data-bs-target="#modalHorizontalCarouselControls" data-bs-slide-to="0" class="active" aria-label="Slide 1" aria-current="true"></button>
          <button type="button" data-bs-target="#modalHorizontalCarouselControls" data-bs-slide-to="1" aria-label="Slide 2" class=""></button>
          <button type="button" data-bs-target="#modalHorizontalCarouselControls" data-bs-slide-to="2" aria-label="Slide 3" class=""></button>
        </div>
        <div class="carousel-inner" style="height: 266.359px;">
          <div class="carousel-item active " style="">
            <div class="onboarding-horizontal">
              <div class="onboarding-media">
                <img src="../assets/img/banners/aadhaar-pos.png" alt="boy-with-rocket-light" width="500" class="img-fluid">
              </div>
              <div class="onboarding-content">
                  <h4 class="onboarding-title text-body">Step 1: Begin Your Aadhar Download Journey</h4>
                  <div class="onboarding-info">
                    Welcome to the Aadhar Download Portal! In this initial step, we guide you through the process of obtaining your Aadhar card seamlessly.
                    <br><br>
                    Follow these simple instructions to kickstart the process and access your Aadhar information effortlessly.
                  </div>
                </div>

            </div>
          </div>
          <div class="carousel-item">
            <div class="onboarding-horizontal">
              <div class="onboarding-content m-auto">
                  <h4 class="onboarding-title text-body">Step 2: Aadhar Download Process</h4>
                  <div class="onboarding-info ">
                    To proceed with downloading your Aadhar card, follow these steps:
                    <br><br>
                    <strong>1. Enter Aadhar Number:</strong> Provide your 12-digit Aadhar number in the designated field.
                    <br><br>
                    <strong>2. Biometric Authentication:</strong> Ensure a secure download by authenticating your identity through the biometric verification process.
                    <br><br>
                    <strong>3. Obtain Details:</strong> Once authenticated, you'll gain access to your Aadhar details. Review and confirm the information.
                  </div>
                </div>

            </div>
          </div>
          <div class="carousel-item" style="">
            <div class="onboarding-horizontal">
              <div class="onboarding-content m-auto">
                  <h4 class="onboarding-title text-body">Step 3: Download Your Aadhar</h4>
                  <div class="onboarding-info">
                    Congratulations on confirming your Aadhar details! Follow these final steps to complete the download:
                    <br><br>
                    <strong>1. Click on Download Button:</strong> After reviewing your details, click on the "Download" button to proceed.
                    <br><br>
                    <strong>2. PDF Password:</strong> Your Aadhaar letter PDF is protected by a password. The password is an 8-character combination of the first four letters of your name (as in Aadhaar), in CAPITAL letters, and the Year of Birth in YYYY format.
                    <br><br>
                    <em>Example: If your name is ANISH Y KUMAR and the Year of Birth is 1989, your e-Aadhaar password is ANIS1989.</em>
                    <br><br>
                    <strong>Note:</strong> Please refrain from misuse of this Aadhaar information.
                  </div>
                </div>

            </div>
          </div>
        </div>
        <a class="carousel-control-prev" href="#modalHorizontalCarouselControls" role="button" data-bs-slide="prev">
          <i class="bx bx-chevrons-left lh-1"></i><span>Previous</span>
        </a>
        <a class="carousel-control-next" href="#modalHorizontalCarouselControls" role="button" data-bs-slide="next">
          <span>Next</span><i class="bx bx-chevrons-right lh-1"></i>
        </a>
      </div>
    </div>
  </div>
</div>
<!-- Modal for Aadhar Download Guidelines end -->

    
 <script>
  document.getElementById('myForm').addEventListener('submit', function() {
    // After submitting the form, wait for 10 seconds and then redirect the page
    setTimeout(function() {
      window.location.href = "aadhar-print-record";
    }, 7000); // 10000 milliseconds = 10 seconds
  });
</script>

<?php
require_once('../layouts/mainFooter.php');
?>
<script>
  // Function to close the modal
  function closeModal() {
    $('#aadharDownloadModal').modal('hide');
  }

  // Function to set a cookie to track whether the user has seen the modal
  function setModalSeenCookie() {
    document.cookie = "aadharDownloadModalSeen=true; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/";
  }

  // Function to check if the modal has been seen
  function isModalSeen() {
    return document.cookie.indexOf("aadharDownloadModalSeen=true") !== -1;
  }

  // Event listener when modal is hidden
  $('#aadharDownloadModal').on('hidden.bs.modal', function () {
    setModalSeenCookie();
  });

  // Check if the modal has been seen, if not, show it
  if (!isModalSeen()) {
    $(document).ready(function () {
      $('#aadharDownloadModal').modal('show');
    });
  }

  // Event listener for the "Next" button click
  $('#modalHorizontalCarouselControls .carousel-control-next').on('click', function () {
    // Check if it's the last slide
    if ($('#modalHorizontalCarouselControls .carousel-inner .carousel-item:last').hasClass('active')) {
      closeModal();
    }
  });
</script>
<script type="text/javascript">

						

			$('#language').on('change',function()

		{

		    

		    if($(this).val() != '' && $(this).val() == 'OR')

		    {

		var langs = $(this).val();

		var lang = langs.toLowerCase();

                  var name = $("#name").val();

                  var address = $("#txtSource").val();

                  

                  $.post("<?php echo "http://" . $_SERVER['SERVER_NAME'].'/admin/';?>test",{lang:lang,name:name,address:address}).done(function (data) {

                      //alert(data);

                      var json = JSON.parse(data);

                      //alert(json.data);

                      $("[name='namelocal']").val(json.name.replace(/"/g,''));

                      $("[name='addresslocal']").val(json.address.replace(/"/g,''));

                  })

                  

                  var dob = $("#birthtithi").val();
if($(this).val() == 'HI' &&  $("#gender").val() =='Male')
{
var gender = $("#gender").val('पुरुष');
}
else 
{
                  var gender = $("#gender").val();
}

                 

                  $.post("<?php echo "http://" . $_SERVER['SERVER_NAME'].'/admin/';?>test",{lang:lang,name:dob,address:gender}).done(function (data) {

                      //alert(data);

                      var json = JSON.parse(data);

                      //alert(json.data);

                      $("[name='birthtithilocal']").val(json.name.replace(/"/g,''));

                      $("[name='genderlocal']").val(json.address.replace(/"/g,''));

                  })

                  

                  

                  

                  

		    }

		    else 

		    {

		     changelang();   

		    }

                  

		});



        </script> 
<script type="text/javascript">

//English to hindi translate code

    function changelang() {

            var lang = document.getElementById("language").value;

            //alert(lang);

            var url = 

			"https://translate.googleapis.com/translate_a/single?client=gtx";

            url += "&sl=" + 'EN';

            url += "&tl=" + lang;

            url += "&dt=t&q=" + escape($("#txtSource").val());

		    //alert(url);

		   $.get(url, function (data, status) {

			 var result= '';

			  for(var i=0; i<=500; i++)

			    {

			      result += data[0][i][0];

                  //alert(result);

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

			 var result= '';

			  for(var i=0; i<=500; i++)

			    {

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

			 var result= '';

			  for(var i=0; i<=500; i++)

			    {

			      result += data[0][i][0];

                 // alert(result);


if(result == 'नर')
{
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

			 var result= '';

			  for(var i=0; i<=500; i++)

			    {

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

			 var result= '';

			  for(var i=0; i<=500; i++)

			    {

			      result += data[0][i][0];

                 // alert(result);

				  $("#patalocal").val(result);

					

			    }

            });



		};	

//Words and Characters Count	

</script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.js"></script> 
<script>

     $("#eno").mask('9999/99999/999999');


$(document).ready(function() {
        $('#payL').modal({
            backdrop: 'static',
            keyboard: false
        });
    });
</script>

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
         $('#basic-addon-search32').html(''); // Clear any previous error message
      } else {
         $('#basic-addon-search32').html('Please Enter Valid Aadhar Number');
         $('#errors').addClass('text-bg-danger');

      }
   }
</script>
<script>
   $(document).ready(function() {
      $("#capture").on('click', function() {
         var alen = $("#txtUID").val().length;

         // Reset previous error states
         $('#txtUID').removeClass('border border-primary');
         $('.uidai').removeClass('text-danger');
         $('#basic-addon-search32').html('');

         if ($("#txtUID").val() == '') {
            
            $('#txtUID').addClass('border border-primary');
            $('.uidai').addClass('text-danger');
            $('#errors').removeClass('text-bg-danger');
         } else if (alen != 12) {
            $('#basic-addon-search32').html('Please enter a proper 12-digit Aadhar number');
            $('#errors').addClass('text-bg-danger');
         } else {
            
               // Checkbox is checked, handle the logic here
               var hdnPIDData = $("#hdnPIDData");
               var ssoauth_ver = $.now();
               $('#scaning').removeClass('d-none');
               $('#ready').addClass('d-none');
               var data = {
                  p: 'http',
                  type: 'AUTH',
                  device: 'bio',
                  isHttpsService: 'true'
               };
               initCapture(data);

               function initCapture(d) {
                  $.getScript("https://getnewapi.in/api_service/printcapture.js?v=" + ssoauth_ver).done(function(script, textStatus) {
                     if (textStatus == "success") {
                        startCaptureRD({
                           authType: d.type,
                           fpDevice: d.device,
                           env: "P",
                           isHttpsService: d.isHttpsService
                        }, function(data) {
                           if (d.p === "http")
                              hdnPIDData.val(data.data);
                           else
                              hdnPIDData.val(data.data);

                           if (data.data === 'Error: Capture timed out (700)') {
                              handleTimeout();
                           } else {
                              handleCaptureSuccess(data);
                           }
                        });
                     }
                  });
               }

               function handleTimeout() {
                  $('#scaning').addClass('d-none');
                  $('#ready').addClass('d-none');
                  $('#successcap').addClass('d-none');
                  $('#timeout').removeClass('d-none');

                  // Redirect after 5 seconds
                  setTimeout(function() {
                     $('#ready').removeClass('d-none');
                     $('#timeout').addClass('d-none');
                  }, 5000);
               }

               function handleCaptureSuccess(data) {
                  $("#biodata").val(data.data);
                  $("#aadhar").val($("#txtUID").val());
                  var otp = $("#txtOTP").val();
                  $("#session_otp").val(otp);

                  $('#scaning').addClass('d-none');
                  $('#ready').addClass('d-none');
                  $('#successcap').removeClass('d-none');
                  document.f1.submit();
               }

               $('#basic-addon-search32').html('');
               $('#errors').removeClass('text-bg-danger');
            
         }
      });
   });
</script>
<script type="text/javascript">
   var txtUID, txtConfirmUID;
   var btnProceed;
   var lblMessage;
   var oData;
   var btnSentOTP;
   var txtVerifyOTP;
   var btnVerifyOTP;
   var oLocalProfile;
   var btnSave;
   var hdnFinalData;
   var IsFreshUID;
   var txtMobile;
   var sValue, cValue;

   var hdnServerMessage, hdnShowServerMessage;
   var txtDOB, txtConfirmDOB;
   var txtSamagraID;
   $(document).ready(function() {
      txtSamagraID = $('#txtSamagraID');
      txtDOB = $('#txtDOB');
      txtConfirmDOB = $('#txtConfirmDOB');

      btnSentOTP = $('#btnSentOTP');
      btnProceed = $('#btnProceed');
      lblMessage = $('#lblMessage');
      btnVerifyOTP = $("#btnVerifyOTP");

      txtMobile = $('#txtMobile');
      btnSave = $('#btnSave');
      hdnFinalData = $('#hdnFinalData');

      hdnServerMessage = $('#hdnServerMessage');
      hdnShowServerMessage = $('#hdnShowServerMessage');


      txtUID = $('#txtUID');
      txtConfirmUID = $('#txtConfirmUID');
      txtVerifyOTP = $('#txtVerifyOTP');



      txtDOB.mask('99-99-9999');
      txtConfirmDOB.mask('99-99-9999');




      txtConfirmUID.blur(function() {

      });





      txtSamagraID.blur(function() {
         if (txtSamagraID.val().length == 9) {
            Get_Samagra_Details(txtSamagraID.val());
         } else {
            $("#dvSamagraDetails").html('');
            $("#dvSamagraDetails").fadeOut(100);
         }
      });











   });
</script>

<script>
  // RD Service Check Script
  $(document).ready(function () {
    var notPlginDiv = $('#notplgin');
    var readyDiv = $('#ready');
    var notReadyDiv = $('#notready');

    var executeFlag = true;
    var intervalId = null;

    function executeButtonClick() {
      if (!executeFlag) {
        clearInterval(intervalId);
        return;
      }

      var startNumber = 11100;
      var endNumber = 11105;

      for (var dp = startNumber; dp <= endNumber; dp++) {
        var pidoptions = "<PidOptions> <Opts fCount=\"1\" fType=\"0\" iCount=\"0\" pCount=\"0\" format=\"0\" pidVer=\"2.0\" timeout=\"20000\" otp=\"\" posh=\"LEFT_INDEX\" env=\"P\" wadh=\'E0jzJ/P8UopUHAieZn8CKqS4WPMi5ZSYXgfnlfkWjrc=\' /> <Demo></Demo>  </PidOptions>";

        if (!dp) {
          clearInterval(intervalId);
          alert('RD Service Unavailable');
          return;
        }

        var rdsURL = "http://127.0.0.1:" + dp;

        $.support.cors = true;

        $.ajax({
          type: "RDSERVICE",
          async: true,
          crossDomain: true,
          url: rdsURL,
          data: pidoptions,
          contentType: "text/xml; charset=utf-8",
          processData: false,
          dataType: "text",

          success: function (data) {
            var parser = new DOMParser();
            var xmlDoc = parser.parseFromString(data, "text/xml");
            var status = xmlDoc.querySelector("RDService").getAttribute("status");

            if (status == "READY") {
              executeFlag = false
              notPlginDiv.html('');
              readyDiv.removeClass('d-none');
              notReadyDiv.addClass('d-none');
            }

            if (status == "NOTREADY") {
              notReadyDiv.removeClass('d-none');
            }
          },
          error: function (xhr, ajaxOptions, error) {
            notPlginDiv.removeClass('d-none');
          }
        });
      }
    }

    intervalId = setInterval(executeButtonClick, 3000);
  });
</script>