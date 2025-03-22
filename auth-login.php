<?php
require_once('system/connectivity_functions.php');
// Generate and store a CSRF token in the session
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-wide  customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title><?= getPortalInfo('webName') ?> Login</title>

    <meta name="description" content="Explore a wide range of financial services on our platform - PAN card creation, PAN lookup, recharge, bill payments, and more. Your one-stop solution for fintech services." />
    <meta name="keywords" content="PAN card creation, PAN find, recharge, bill payment, fintech services, online financial services, PAN lookup" />

    <!-- Canonical SEO -->
    <link rel="canonical" href="https://themeselection.com/item/sneat-bootstrap-html-admin-template/">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="assets/vendor/fonts/flag-icons.css" />
    <link rel="stylesheet" href="assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="assets/css/demo.css" />
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="assets/vendor/libs/typeahead-js/typeahead.css" />
    <link rel="stylesheet" href="assets/vendor/libs/@form-validation/form-validation.css" />
    <link rel="stylesheet" href="assets/vendor/css/rtl/kavya-all.css" />
    <link rel="stylesheet" href="assets/vendor/css/pages/page-auth.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <link rel="stylesheet" href="assets/vendor/libs/toastr/toastr.css">
    <script src="assets/vendor/js/helpers.js"></script>
    <script src="assets/vendor/js/template-customizer.js"></script>
    <script src="assets/js/config.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</head>

<body>

    <?php

    // Check if the user is already logged in (session is active)
    if (isset($_SESSION['userAuth'])) {
        // Display the alert using JavaScript
        echo '<script>toastr.info("Dear : ' . ucfirst(base64_decode($_SESSION['sprint_session'])) . ' You are already logged in!");</script>';
        redirect(1500, 'dashboard/index.php');
        exit();
    }

    if (isset($_GET['expired']) && $_GET['expired'] === '1') {
        echo '<script>toastr.error("Session Expired! Your session has expired. Please log in again.");</script>';
        redirect(3000, 'auth-login');
    }

    $otpvalidate = true;
    if (isset($_POST['login']) && !empty($_POST['username']) && !empty($_POST['login_pwd'])) {
        if (!empty($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
            session_start();
            $conn = connectDB();
            $username = safe_str($_POST['username']);
            $login_pwd = safe_str($_POST['login_pwd']);

            // Check if OTP variables are set
            $otp = (
                isset($_POST['otp1']) && isset($_POST['otp2']) &&
                isset($_POST['otp3']) && isset($_POST['otp4']) &&
                isset($_POST['otp5']) && isset($_POST['otp6'])
            ) ? safe_str($_POST['otp1']) . safe_str($_POST['otp2']) . safe_str($_POST['otp3']) . safe_str($_POST['otp4']) . safe_str($_POST['otp5']) . safe_str($_POST['otp6']) : '';

            // Check OTP length
            if (strlen($otp) === 6) {
                // Use prepared statements to prevent SQL injection
                $stmt = $conn->prepare("SELECT * FROM `users` WHERE username=:username AND status='approved'");
                $stmt->bindParam(':username', $username);
                $stmt->execute();

                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result) {
                    // Verify the entered OTP against the stored OTP
                    if ($otp === $result['login_otp']) {
                        $rand = rand(5);

                        // Set user data in the session
                        $_SESSION['userAuth'] = base64_encode($result['username']);
                        $_SESSION['login_time'] = time();

                        // Set cookies
                        $cookie_expire = time() + (86400 * 1);
                        setcookie('userAuth', urlencode(base64_encode($result['username'])), $cookie_expire, '/', '', true, true);
                        setcookie('login_time', time(), $cookie_expire, '/', '', true, true);

                        // Update login_otp and csrf_token in the database
                        $stmt = $conn->prepare("UPDATE `users` SET login_otp=:rand WHERE id=:id");
                        $stmt->bindParam(':rand', $rand);
                        $stmt->bindParam(':id', $result['id']);
                        $stmt->execute();
                        echo '<script>toastr.success("Successfully Login your Account!");</script>';
                        redirect(1500, 'dashboard/index.php');
                    } else {
                        $otpvalidate = false;
                        $otperror = "Invalid OTP. Please enter the correct OTP";
                    }
                } else {
                    echo '<script>toastr.error("User not found or not approved!");</script>';
                }
            } else {
                // Handle incorrect OTP length
                $otpvalidate = false;
                $otperror = "Invalid OTP. Please enter a 6-digit OTP.";
            }
        } else {
            echo '<script>toastr.error("Invalid CSRF token. Please try again.");</script>';
            redirect(1500, '');
        }
    }
    ?>

    <!-- Content -->

    <div class="authentication-wrapper authentication-cover">
        <div class="authentication-inner row m-0">
            <!-- /Left Text -->
            <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center p-5">
                <div class="w-100 d-flex justify-content-center">
                    <img src="assets/img/banners/login-banner.png" class="img-fluid" alt="Login image" width="700" data-app-dark-img="illustrations/boy-with-rocket-dark.png" data-app-light-img="illustrations/boy-with-rocket-light.png">
                </div>
            </div>
            <!-- /Left Text -->

            <!-- Login -->
            <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-5 p-4 <?= $otpvalidate ? '' : 'd-none'; ?>" id="login-div">
                <div class="w-px-400 mx-auto">
                    <h4 class="mb-2">Welcome to <?= getPortalInfo('webName') ?>! 👋</h4>
                    <p class="mb-4">Log in to your account and unlock a world of convenience.</p>

                    <form id="Login" class="mb-3" action="auth-login.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="hidden" class="csrf_token" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="text" class="form-control u-id" id="username" name="username" placeholder="Enter your username" autofocus>
                        </div>
                        <div class="mb-3 form-password-toggle">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="password">Password</label>
                                <a href="auth-forgot-password.php">
                                    <small>Forgot Password?</small>
                                </a>
                            </div>
                            <div class="input-group input-group-merge">
                                <input type="password" id="login_pwd" class="form-control u-pass" name="login_pwd" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember-me">
                                <label class="form-check-label" for="remember-me">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                        <div class="pansprint-executing" id="load">
                            <button type="button" class="btn btn-primary d-grid w-100" onclick="SendOtpCode();"> Sign in </button>
                        </div>
                        <!--</form>-->

                        <p class="text-center mt-3">
                            <span>New on our platform?</span>
                            <a href="auth-register.php">
                                <span>Create an account</span>
                            </a>
                        </p>

                    </div>
                </div>
                <!-- /Login -->
                <!-- Two Steps Verification -->
                <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-4 p-sm-5 <?= $otpvalidate ? 'd-none' : ''; ?>" id="otp-div">
                    <div class="w-px-400 mx-auto">
                        <h4 class="mb-2">Two Step Verification 💬</h4>
                        <p class="text-start mb-4">
                            We sent a verification code to your mobile. Enter the code from the mobile in the field below.
                            <span class="fw-medium d-block mt-2" id="mobile"></span>
                        </p>
                        <p class="mb-0 fw-medium">Type your 6 digit security code</p>
                        <!--<form id="twoStepsForm" action="" method="POST">-->
                        <div class="mb-3">
                            <div class="auth-input-wrapper d-flex align-items-center justify-content-sm-between numeral-mask-wrapper">
                                <input type="tel" name="otp1" class="form-control auth-input h-px-50 text-center numeral-mask mx-1 my-2" maxlength="1" autofocus>
                                <input type="tel" name="otp2" class="form-control auth-input h-px-50 text-center numeral-mask mx-1 my-2" maxlength="1">
                                <input type="tel" name="otp3" class="form-control auth-input h-px-50 text-center numeral-mask mx-1 my-2" maxlength="1">
                                <input type="tel" name="otp4" class="form-control auth-input h-px-50 text-center numeral-mask mx-1 my-2" maxlength="1">
                                <input type="tel" name="otp5" class="form-control auth-input h-px-50 text-center numeral-mask mx-1 my-2" maxlength="1">
                                <input type="tel" name="otp6" class="form-control auth-input h-px-50 text-center numeral-mask mx-1 my-2" maxlength="1">
                            </div>
                            <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                <div data-field="otp" data-validator="notEmpty"><?= $otperror ?></div>
                            </div>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary d-grid w-100 mb-3"> Verify my account </button>
                        <span id="countdown" style="display: none; color:blue;">60</span>
                        <div class="text-center" id="myDiv" style="display: none;">Didn't get the code?
                            <a href="javascript:void(0);" onclick="SendOtpCode();">
                                Resend
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /Two Steps Verification -->
        </div>
    </div>
<script>
    document.addEventListener("keydown", function (event) {
        // Disable Enter key (key code 13)
        if (event.key === "Enter") {
            event.preventDefault();
            toastr.error("Enter key is disabled");
        }
    });
</script>

    <!-- / Content -->
    <script src="assets/vendor/libs/jquery/jquery.js"></script>
    <script src="assets/vendor/libs/popper/popper.js"></script>
    <script src="assets/vendor/js/bootstrap.js"></script>
    <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="assets/vendor/libs/hammer/hammer.js"></script>
    <script src="assets/vendor/libs/i18n/i18n.js"></script>
    <script src="assets/vendor/libs/typeahead-js/typeahead.js"></script>
    <script src="assets/vendor/js/menu.js"></script>
    <script src="assets/vendor/libs/@form-validation/popular.js"></script>
    <script src="assets/vendor/libs/@form-validation/bootstrap5.js"></script>
    <script src="assets/vendor/libs/@form-validation/auto-focus.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/pages-auth-two-steps.js"></script>
    <script src="assets/js/pages-auth.js"></script>
    <script src="assets/vendor/libs/toastr/toastr.js"></script>
    <script src="assets/js/ui-toasts.js"></script>
    <script src="assets/js/sprintAuth-login.js"></script>
    <script src="assets/js/kavya-main.js"></script>

</body>

</html>
