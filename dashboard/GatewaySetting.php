<?php 
//error_reporting(E_ALL); ini_set('display_errors', 1);
$pageName = "Gateway Setting"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');
if(getUsersInfo('usertype') === "mainadmin"){
?>
<?php


if (isset($_POST['upiupdate']) && !empty($_POST['upi_data']) && !empty($_POST['upi_id']) && !empty($_POST['upi_name']) && !empty($_POST['mid']) ) {
    $upi_data = strip_tags($_POST['upi_data']);
    $upi_id = strip_tags($_POST['upi_id']);
    $upi_name = safe_str($_POST['upi_name']);
    $mid = strip_tags($_POST['mid']);
    $allow = array("paytm");

    if (isUPI($upi_id, $allow)) {
        $paytm_business = json_encode(array(
            "upi_data" => json_decode($upi_data, true),
            "upi_id" => $upi_id,
            "upi_name" => $upi_name,
            "mid" => $mid
        ));
        $userID = '1';
        // Update Paytm Business details in the 'users' table
        $updateQuery = "UPDATE `users` SET paytm_business = :paytm_business WHERE id = :user_id";
        $updateStatement = $conn->prepare($updateQuery);
        $updateStatement->bindParam(':paytm_business', $paytm_business, PDO::PARAM_STR);
        $updateStatement->bindParam(':user_id', $userID, PDO::PARAM_INT);

        if ($updateStatement->execute()) {
            echo '<script>toastr.success("Paytm Business Details Updated!");</script>';
            redirect(1500, '');
        } else {
            echo '<script>toastr.error("Service is Down!");</script>';
            redirect(1500, '');
        }
    } else {
        echo '<script>toastr.error("UPI Address is not valid, currently support ' . implode(", ", $allow) . '");</script>';
        redirect(1500, '');
    }
}
?>
<?php 
$upi = getUpiDetails('1', 'upi_id');
if($upi === null){
    echo '<script>toastr.error("Paytm business not connected");</script>';
}else{
    echo '<script>toastr.success("Paytm business has already been connected.");</script>';
}
?>
<? if(getUsersInfo('usertype') !== "demo"){ 
$class = "disabled";
}
?>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-sm-12 col-xl-12">
            <div class="rounded h-100 p-4 border border-primary">
                <div class="row g-0 rounded shadow-lg border border-warning">
                    <div class="col-md-6 mb-md-0 p-md-4">
                        <form class="was-validated" action="" method="post">
                            <div class="mb-3">
                                <div class="text-center" id="customFileInput" onclick="openFileInput()">
                                  <img src="../assets/img/icons/QR_code.svg" height="29" width="29"> Upload Paytm Business QR Code <img src="../assets/img/icons/QR_code.svg" height="29" width="29">
                                </div>
                                <input type="file" id="psfileInput" onchange="QRCodeScan(this, 'upi_data', 'base64_img')" class="form-control" accept="image/*">
                                <input type="hidden" name="qr_code" id="base64_img">
                                <input type="hidden" name="upi_data" id="upi_data">
                            </div>
                            <div class="mb-3">
                                <input type="text" name="upi_id"  id ="upi_id" value="<?php echo getUpiDetails('1', 'upi_id')?>" onchange="UPIValid(this.value);"  class="form-control" placeholder="Enter Your UPI Address" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" name="upi_name" id="upi_name" value="<?php echo getUpiDetails('1', 'upi_name')?>" class="form-control" placeholder="Enter Display Name" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" name="mid" value="<?php echo getUpiDetails('1', 'mid')?>" class="form-control" placeholder="Enter Merchant ID" required>
                            </div>
                            <div class="mb-3">
                                <button type="submit" name="upiupdate" class="btn btn-primary">Submit</button>
                            </div>
                            <script type="text/javascript" src="../assets/js/qrcode.js"></script>
                            <script type="text/javascript">
                
                                function encodeImageFileAsURL(element,elm) {
                                var file = element.files[0];
                                var reader = new FileReader();
                                reader.onloadend = function() {
                                document.getElementById(elm).value = reader.result; 
                                }
                                reader.readAsDataURL(file);
                                }
                                
                                
                                function QRCodeScan(element,elm,base64_img){
                                toastr.info("Please wait QR Code is Verifying!");
                                encodeImageFileAsURL(element,base64_img);
                                
                                document.getElementById("upi_id").value = '';
                                document.getElementById("upi_name").value = '';
                                document.getElementById(elm).innerHTML = ''; 
                                
                                setTimeout(function(){
                                
                                const qrcode = new QRCode.Decoder();
                                
                                qrcode
                                .scan(document.getElementById(base64_img).value)
                                .then(result => {
                                
                                var url = JSON.parse(JSON.stringify(result.data));
                                
                                let params = (new URL(url)).searchParams;
                                var pa = params.get('pa');
                                var pn =  params.get('pn'); 
                                var obj = { pa: pa, pn: pn};
                                var myJSON = JSON.stringify(obj);
                                //console.log(pa+" . "+pn);
                                if(pa!=null && pn!=null){
                                toastr.success("QR Code Verified!");    
                                document.getElementById("upi_id").value = pa;
                                document.getElementById("upi_name").value = pn;
                                document.getElementById(elm).value = myJSON; 
                                }else{
                                toastr.error("QR Code is Invalid");    
                                }
                                
                                })
                                .catch(error => {
                                toastr.error("Something went wrong!");    
                                //console.error(result.data);
                                });
                                
                                }, 1000);
                                
                                }  
                                
                                function UPIValid(upi_id) { 
                                if(isUPI(upi_id)!=true){
                                toastr.error("UPI Adress is not valid!");    
                                document.getElementById("upi_id").value = ""; 
                                }
                                }
                                
                                
                                function isUPI(upi) {
                                return upi.endsWith('@paytm') || upi.endsWith('@sbi');
                                }
                                </script> 
                        </form>
                    </div>
                    <div class="col-md-6 ps-md-0"> <!-- Increase the padding -->
                        <h5 class="mt-4">Learn how to connect it through video.</h5>
                        <div class="embed-responsive embed-responsive-21by9"> <!-- Change to 16:9 aspect ratio for typical videos -->
                            <iframe width="100%" height="300" src="https://www.youtube.com/embed/Hiz78PSRyNI" title="Pansprint Infotech Trening" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<script>
  function openFileInput() {
    document.getElementById('psfileInput').click();
  }
</script>
<!-- Not authorized! -->
<?php } else { ?>
    <div class="misc-wrapper text-center" style="margin-top: 100px">
        <span class="text-danger"><i class="bi bi-exclamation-triangle display-1 text-primary"></i></span>
        <h2 class="mb-2 mx-2">You are not authorized!</h2>
        <p class="mb-4 mx-2 text-danger">You do not have permission to view this page using the credentials that you have provided while login. <br> Please contact your site administrator.</p>
        <a href="dashboard" class="btn btn-primary">Back to home</a>
    </div>
<?php } ?>
<!-- \ Not authorized! -->
<?php 
require_once('../layouts/mainFooter.php');
?>