<?php
$pageName = "Aadhaar Print Advance"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');

// Check if the data saving form is submitted
if (isset($_POST['savedata'])) {
    
    $amount = getUsersInfo('service_pricing_aadhaar');
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
            $mode = 'Aadhar Manual Print';
            $type = 'debit';
            $remark = 'Aadhar Manual Print Transaction - Requested by: ' . $name . ' (Aadhar Number: ' . $aadharno . ')';

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
}}}
?>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 m-auto p-t-10">
<div class="card">
   <h5 class="card-header"><div class="figure d-block">
            <blockquote class="blockquote"> 
            <b style="color:black;">Aadhaar Manual Generate</b>
            </blockquote>
          </div></h5>
   <!-- Account -->
   <div class="card-body">
      <div class="d-flex align-items-start align-items-sm-center gap-4">
         <img src="../../assets/img/avatars/1.png" alt="user-avatar" class="d-block rounded" height="100" width="100" id="uploadedImage">
         <div class="button-wrapper">
            <label class="btn btn-primary me-2 mb-4" id="imageContainer">
               <div class="d-none d-sm-block">Upload photo</div>
               
            </label>
            <p class="mb-0 text-danger">Allowed JPG, JPEG or PNG. Max size of 100K</p>
         </div>
      </div>
   </div>
   <hr class="my-0">
   <div class="card-body">
      <form method="POST" class="fv-plugins-bootstrap5 fv-plugins-framework" action="" id="myForm">
         <div class="row">
            <div class="mb-3 col-md-4 fv-plugins-icon-container">
               <label for="firstName" class="form-label">Aadhaar Full Name</label>
               <input type="text" class="form-control" name="name" id="name" placeholder="Enter Aadhaar Name" oninput="this.value = this.value.replace(/\b\w/g, function(char) { return char.toUpperCase(); })" required />
                <input type="file" id="fileInput" style="display:none;">
            </div>
            <div class="mb-3 col-md-4">
               <label for="email" class="form-label">Relation Name</label>
               <input class="form-control" type="text" id="relationName" name="relationName" placeholder="Enter Relation Name" oninput="this.value = this.value.replace(/\b\w/g, function(char) { return char.toUpperCase(); })" required />
            </div>
            <div class="mb-3 col-md-4 fv-plugins-icon-container">
               <label for="relationType" class="form-label">Relation Type</label>
               <select id="relationType" name="relationType" class="select2 form-select select2-hidden-accessible" onchange="populateRelation()">
                     <option value="" data-select2-id="4">Select Relation</option>
                     <option value="W/O">Husband</option>
                     <option value="S/O">Son Off</option>
                     <option value="D/O">Daughter Off</option>
                     <option value="C/O">Care Off</option>
                  </select>
            </div>
            <div class="mb-3 col-md-3">
               <label for="gender" class="form-label">Gender</label>
                 <select class="form-select" name="gender" id="gender">
                     <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Transgender">Transgender</option>
                </select>

            </div>
            <div class="mb-3 col-md-3">
               <label class="form-label" for="aadharno">Aadhaar Number</label>
               <input class="form-control" maxlength="12" id="aadharno" name="aadharno" type="text" placeholder="Full Aadhaar Number" required>
               <input type="hidden" id="decodedData" placeholder="Decoded Data will appear here" name="imgdata">
            </div>
            <div class="mb-3 col-md-3">
               <label for="address" class="form-label">Date of Birth</label>
                 <input class="form-control  " name="username" type="hidden" value="<?php  echo time(); // $userdata['username']; ?>" required>
                  <input class="form-control  " name="dobadhar" type="date"  required>
                  <input class="form-control  " name="houseno" type="hidden" value="<?php echo $houseno; ?>">
                  <input class="form-control " name="street" type="hidden" value="<?php echo $street ?>">
                  <input class="form-control " name="pincode" type="hidden" value="<?php echo $pincode; ?>">
                  <input class="form-control " name="vtcandpost" type="hidden" value="<?php echo $vtc; ?>">
                  <input class="form-control " name="dist" type="hidden" value="<?php echo $dist; ?>">
                  <input class="form-control " name="statename" type="hidden" value="<?php echo $state; ?>">

            </div>
            <div class="mb-3 col-md-12">
               <label for="state" class="form-label">Full Address</label>
               <input type="text" class="form-control" id="txtSource" name="address" placeholder="Full Address As per Aadhaar" oninput="this.value = this.value.replace(/\b\w/g, function(char) { return char.toUpperCase(); })" required />
            </div>
            <div class="mb-3 col-md-3">
               <label for="Language" class="form-label">Language</label>
                 <select autofocus class="form-select" name="language" id="language" required>
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
            <div class="col-md-3 mt-4">
               <?php $timestamp = date( "Y-m-d" ); ?>
                  <input type="hidden" class="form-control" name="ddate" id="exampleFormControlInput1" placeholder="12/12/2019" value="<?php echo $timestamp;?>" required>
                  <input type="hidden" class="form-control" id="name_regional" name="namelocal" placeholder="Enter Name" required>
                  <input class=" mng_cp form-control " id="birthtithilocal" name="birthtithilocal" type="hidden" value="">
                  <input class="form-control " id="birthtithi" name="birthtithi" readonly="readonly" type="hidden" value="Birth Tithi">
                  <input class="form-control " id="pata" name="pata" readonly="readonly" type="hidden" value="address">
                  <input class="form-control " id="patalocal" name="patalocal" readonly="readonly" type="hidden" value="">
                  <input type="hidden" class="form-control" name="genderlocal" id="genderlocal">
                  <input type="hidden" id="txtTarget" name="addresslocal">
                  
                  <input class="btn btn-primary" type="submit" name="savedata" value="PDF Download">
                </div>
            </div>
         </div>
      </form>
   </div>
</div>
</div>
</div>
</div>

<script>
    function populateRelation() {
        var relationType = document.getElementsByName("relationType")[0].value;
        var relationName = document.getElementsByName("relationName")[0].value;
        var address = document.getElementsByName("address")[0];

        // Concatenate relationType and relationName
        var fullRelation = relationType + ": " + relationName;

        // Append fullRelation to address only if it doesn't already contain it
        if (fullRelation && !address.value.includes(fullRelation)) {
            address.value += "" + fullRelation;
        }
    }
</script>
<script>
        document.getElementById('imageContainer').addEventListener('click', function() {
            document.getElementById('fileInput').click();
        });

        document.getElementById('fileInput').addEventListener('change', function() {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var img = document.getElementById('uploadedImage');
                    img.src = e.target.result;
                    // Here you can perform decoding of the image data
                    // For example, if you want to display base64 data, you can do:
                    document.getElementById('decodedData').value = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
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
document.addEventListener("DOMContentLoaded", function() {
    var isCapsLockActive = false;

    document.addEventListener("keydown", function(event) {
        if (event.getModifierState("CapsLock")) {
            isCapsLockActive = false;
            alert("Caps Lock is ON. Please turn it OFF.");
        } else {
            isCapsLockActive = false;
        }
    });

    document.addEventListener("keypress", function(event) {
        if (isCapsLockActive) {
            event.preventDefault();
        }
    });
});
</script>
