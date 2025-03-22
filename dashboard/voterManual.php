<?php
$pageName = "Voter Card Download"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');

if (isset($_POST['verifing'])) {
    $epicNumber = $_POST['epicNumber'];
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://getnewapi.in/api_service/voter_download.php?domain=pansprint.in&token=b723ce51188aa6aa&type=detals&epicno=' . $epicNumber,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_POSTFIELDS => '=',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/x-www-form-urlencoded'
      ),
    ));
    
    $response = curl_exec($curl);
    curl_close($curl);
    echo $response;
    //$response = '{"statusCode":200,"statusMessage":"Success","name":"TEKCHAND","stateCd":"S20","epicNumber":"RHJ1401504","message":"Please wait for 116 seconds before resending new otp!"}';
    $getResult = json_decode($response, true);
    if($getResult['statusCode'] === 200){
        $verifyNone = true;
        $getEpic = $getResult['epicNumber'];
        $getName = $getResult['name'];
        $stateCd = $getResult['stateCd'];
        $getMessage = $getResult['message'];
        echo '<script>toastr.success("'.$getMessage.'");</script>';
    }else{
        $verifyNone = false;
        $getMessage = $getResult['statusMessage'];
        echo '<script>toastr.error("'.$getMessage.'");</script>';
    }
}    
if (isset($_POST['download'])) {   
  $postEpic = $_POST['getEpic'];
  $stateId = $_POST['stateId'];
  $otpHare = $_POST['otp'];

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://getnewapi.in/api_service/voter_download.php?domain=pansprint.in&token=b723ce51188aa6aa&type=downlode&epicno=$postEpic&stateCd=$stateId&otp=$otpHare",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_POSTFIELDS => '=',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/x-www-form-urlencoded'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
if (stripos($response, "%PDF") === 0) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="downloaded_file.pdf"'); // Change filename if needed
    echo $response;
} else {
    echo $response;
}
}
?>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 m-auto">
<div class="card">
   <h5 class="card-header">
       <div class="figure d-block">
            <blockquote class="blockquote"> 
            <b style="color:black;">Voter Orginal PDF</b>
            </blockquote>
          </div></h5>
   <!-- Account -->
   <hr class="my-0">
   
   <!-- GET JOB CARD DATA -->
   <?php if($verifyNone !== true){ ?>
   <div class="card-body">
      <form method="POST" class="fv-plugins-bootstrap5 fv-plugins-framework" action="" id="myForm">
         <div class="row">
            <div class="mb-3 col-md-4 fv-plugins-icon-container">
               <label for="epicNumber" class="form-label">Epic Number</label>
               <input type="text" class="form-control border-primary" name="epicNumber" id="epicNumber" placeholder="Enter voter id Number" required />
            </div>
            <div class="col-md-12">
                  <input class="btn btn-primary" type="submit" name="verifing" value="Verify">
                </div>
            </div>
          </form>
     </div>
     <?php } if($verifyNone === true) { ?>
     <div class="card-body">
      <form method="POST" class="fv-plugins-bootstrap5 fv-plugins-framework" action="">
         <div class="row">
            <div class="mb-3 col-md-4 fv-plugins-icon-container">
               <label for="getEpic" class="form-label">Epic Number</label>
               <input type="text" class="form-control" name="getEpic" id="getEpic" value="<?= $getEpic ?>" required readonly/>
            </div>
            <div class="mb-3 col-md-4 fv-plugins-icon-container">
               <label for="stateId" class="form-label">Name</label>
               <input type="text" class="form-control" name="name" id="name" value="<?= $getName ?>" readonly/>
               <input type="hidden" class="form-control" name="stateId" id="stateId" value="<?= $stateCd ?>" />
            </div>
            <div class="mb-3 col-md-3 fv-plugins-icon-container">
               <label for="otp" class="form-label">OTP Number</label>
               <input type="text" class="form-control" name="otp" id="otp" placeholder="Enter otp number" required />
            </div>
            <div class="col-md-12">
                  <input class="btn btn-primary" type="submit" name="download" value="Download PDF">
                </div>
            </div>
          </form>
     </div>
     <?php } ?>
     <!-- GET JOB CARD DATA -->
   </div>
</div>
</div>
</div>
</div>


<?php
require_once('../layouts/mainFooter.php');
?>