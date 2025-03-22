<?php
$pageName = "Paperless PAN Registration Process"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');

    // Check if user is logged in
    $username = getUsersInfo('id');
    $service_name = 'active'; // Service name for PAN status

    // Check if the service is active for the user
    if (isServiceActive($conn, $username, $service_name)) {
        
        
    } else {
        // Service not active, display alert
        echo '<div class="modal fade" id="recharge_success_modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" role="dialog">
                <div class="modal-dialog modal-sm modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body text-center p-4">
                            <h2 style="color: red;"><b>Instant PAN</b></h2>
                            <p>Your PAN registration service is not active. Please activate it to proceed.</p>
                            <button class="btn btn-danger active" id="proceedActiveBtn">Proceed to Active</button>
                            <a href="index" class="btn btn-secondary active">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                document.getElementById("proceedActiveBtn").addEventListener("click", function(event) {
                    // Activate service and debit balance
                    var confirmation = confirm("Are you sure you want to activate the service?");
                    if (confirmation) {
                        // Call server-side PHP to activate service and debit balance
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", "../system/epan_service.php", true);
                        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                if (xhr.responseText == "success") {
                                    alert("Service activated successfully.");
                                    window.location.reload(); // Reload page to reflect changes
                                } else if(xhr.responseText == "low balance") {
                                    alert("Insufficient Balance. Please load balance..");
                                } else {
                                    alert("Failed to activate service. Please try again later.");
                                }
                            }
                        };
                        xhr.send();
                    }
                });
                
                $(document).ready(function() {
                    $("#recharge_success_modal").modal("show");
                });
            </script>';
    }
 
?>

<!-- Index.html -->

<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Image Banner -->
    <div class="col-lg-12 mb-4 order-0">
        <div class="card">
            <img src="../assets/img/backgrounds/nsdl-kavya-banner.png" class="card-img-top" alt="SprintPAN Banner">
        </div>
    </div>
    <!-- KYC Process Content -->
    <div class="col-lg-12 mb-4 order-0">
        <div class="">
            <h4 class="step-title" style="color:black"><b><span style="color:red"><?= getPortalInfo('webName') ?></span> has collaborated with NSDL for PAN card services thus enabling our agents to provide seamless PAN card services to their customer through our portal.</b></h4>
            <p class="lead">Welcome to the paperless PAN registration process. Follow the steps below:</p>
            <ol class="steps">
                <li>Provide personal information.</li>
                <li>Upload scanned documents for verification.</li>
                <li>Complete biometric verification (if required).</li>
                <li>Receive PAN confirmation.</li>
            </ol>
            <p>If you have any questions or need assistance, please contact our support team.</p>
            <!-- Apply Now Button -->
            <div class="mt-4">
                <a href="javascript:void(0);" id="applyNowBtn" class="btn btn-primary active">Login</a>
            </div>
        </div>
    </div>
    <!-- Guidelines -->
    <div class="col-lg-12 mb-4 order-0">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title text-center">Guidelines for Paperless PAN Registration</h3>
                <ul class="guidelines">
                    <li>Ensure all information provided is accurate and up-to-date.</li>
                    <li>Scanned documents should be clear and legible.</li>
                    <li>Follow instructions carefully during the biometric verification process.</li>
                    <li>Keep your contact information updated for timely communication.</li>
                    <li>Check your email regularly for PAN confirmation updates.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById("applyNowBtn").addEventListener("click", function(event) {
        event.preventDefault(); // Prevent default action of button click
        
        // Display spinner and change button text to "Verifying User"
        var btn = this;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> &nbsp;Please wait proceed to redirect NSDL';
        btn.disabled = true;

        // Confirm with the user before proceeding
        if (confirm("I (Consumer ) hereby state that I have no objection in authenticating myself with Aadhaar based UID/VID authentication system and provide my consent for the same.")) {
            // If confirmed, wait for 3 seconds then redirect user to payment page
            setTimeout(function() {
                window.location.href = "ekyc-panApply?accessToken=<?php echo base64_encode('KAVYAINFOTECH'); ?>";
            }, 5000);
        } else {
            // If canceled, reset button state
            btn.innerHTML = 'Apply Now';
            btn.disabled = false;
        }
    });
    
    
    document.getElementById("recordNowBtn").addEventListener("click", function(event) {
        event.preventDefault(); // Prevent default action of button click
        
        // Display spinner and change button text to "Verifying User"
        var btn = this;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> &nbsp;Please wait a moment';
        btn.disabled = true;

            setTimeout(function() {
                window.location.href = "ekycpan-record?accessToken=<?php echo base64_encode('KAVYAINFOTECH'); ?>";
            }, 5000);
    });
</script>


<?php
require_once('../layouts/mainFooter.php');
?>