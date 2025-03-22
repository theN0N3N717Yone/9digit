<?php
$pageName = "JOB Card Download"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');

// Check if the data saving form is submitted
if (isset($_POST['verifing'])) {
    $jobNo = $_POST['jobnumber'];
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => "https://getnewapi.in/api_service/jobcard.php?jobno=$jobNo",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    echo $response;
    
    //{"statusCode": 200,"statusMessage": "Success","jobno": "MH-XX-015-133-XXX/XXX","job_Pdf": "data:application/pdf;"}


    
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
            $mode = 'Aadhar Print';
            $type = 'debit';
            $remark = 'Aadhar Print Transaction - Requested by: ' . $name . ' (Aadhar Number: ' . $aadharno . ')';

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
        <div class="col-lg-12 m-auto">
<div class="card">
   <h5 class="card-header">
       <div class="figure d-block" style="margin-bottom: -20px">
            <blockquote class="blockquote"> 
            <b style="color:black;">JOB Card Download</b>
            </blockquote>
          </div></h5>
   <!-- Account -->
   <hr class="my-0">
   
   <!-- GET JOB CARD DATA -->
   <div class="card-body">
      <form method="POST" class="fv-plugins-bootstrap5 fv-plugins-framework" action="" id="myForm">
         <div class="row">
            <div class="mb-3 col-md-4 fv-plugins-icon-container">
               <label for="jobnumber" class="form-label">job card Number</label>
               <input type="text" class="form-control" name="jobnumber" id="jobnumber" placeholder="Enter job card Number" required />
            </div>
            <div class="mb-3 col-md-4 fv-plugins-icon-container">
               <label for="mob" class="form-label">Applicant Mobile Number</label>
               <input type="text" class="form-control" name="mob" id="mob" placeholder="Enter Mobile Numbe" required />
            </div>
            <div class="col-md-12">
                  <input class="btn btn-primary" type="submit" name="verifing" value="Verify">
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


<?php
require_once('../layouts/mainFooter.php');
?>