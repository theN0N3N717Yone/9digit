<!DOCTYPE html>
<html lang="en" class="light-style layout-wide  customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>New Registration</title>

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
    <link rel="stylesheet" href="assets/vendor/css/rtl/kavya-all.css" />
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="assets/vendor/libs/typeahead-js/typeahead.css" />
    <link rel="stylesheet" href="assets/vendor/libs/@form-validation/form-validation.css" />
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
//error_reporting(E_ALL); ini_set('display_errors', 1);
require_once('system/connectivity_functions.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['createUser'])) {
    // Securely retrieve user inputs
    $name = $_POST['name'];
    $fname = $_POST['fname'];
    $userName = $_POST['userName'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $company = $_POST['company'];
    $address = $_POST['address'];
    $state = $_POST['state'];
    $pin_code = $_POST['pin_code'];
    $pan_no = $_POST['pan_no'];
    $uid_no = $_POST['uid_no'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if the user already exists using prepared statement
    $check_query = "SELECT * FROM users WHERE mobile_no = :mobile_no OR username = :userName OR email_id = :email_id";
    $check_statement = $conn->prepare($check_query);
    $check_statement->bindParam(':mobile_no', $phone_number, PDO::PARAM_STR);
    $check_statement->bindParam(':userName', $userName, PDO::PARAM_STR);
    $check_statement->bindParam(':email_id', $email, PDO::PARAM_STR);
    $check_statement->execute();

    if ($check_statement->rowCount() > 0) {
        // User already exists, handle accordingly (e.g., show an error message)
        echo '<script>toastr.error("User with the given username, mobile number, or email already exists.");</script>';
        redirect(3000, '');
    } else {
        // Your SQL query for insertion using prepared statement
        $insert_query = "INSERT INTO users (owner_name, father_name, username, email_id, mobile_no, shop_name, address, state, pin_code, pan_no, uid_no, password, date_time) 
                        VALUES (:owner_name, :father_name, :username, :email_id, :mobile_no, :shop_name, :address, :state, :pin_code, :pan_no, :uid_no, :password, :date_time)";
        $insert_statement = $conn->prepare($insert_query);
        $insert_statement->bindParam(':owner_name', $name, PDO::PARAM_STR);
        $insert_statement->bindParam(':father_name', $fname, PDO::PARAM_STR);
        $insert_statement->bindParam(':username', $userName, PDO::PARAM_STR);
        $insert_statement->bindParam(':email_id', $email, PDO::PARAM_STR);
        $insert_statement->bindParam(':mobile_no', $phone_number, PDO::PARAM_STR);
        $insert_statement->bindParam(':shop_name', $company, PDO::PARAM_STR);
        $insert_statement->bindParam(':address', $address, PDO::PARAM_STR);
        $insert_statement->bindParam(':state', $state, PDO::PARAM_STR);
        $insert_statement->bindParam(':pin_code', $pin_code, PDO::PARAM_STR);
        $insert_statement->bindParam(':pan_no', $pan_no, PDO::PARAM_STR);
        $insert_statement->bindParam(':uid_no', $uid_no, PDO::PARAM_STR);
        $insert_statement->bindParam(':password', $password, PDO::PARAM_STR);
        $insert_statement->bindParam(':date_time', $timestamp);
        if($insert_statement->execute()){
         $msguser = '<body style="font-family: Nunito, sans-serif; font-size: 15px; font-weight: 400;">
            <div style="margin-top: 50px;">
                <table cellpadding="0" cellspacing="0"
                    style="font-family: Nunito, sans-serif; font-size: 15px; font-weight: 400; max-width: 600px; border: none; margin: 0 auto; border-radius: 6px; overflow: hidden; background-color: #fff; box-shadow: 0 0 3px rgba(60, 72, 88, 0.15);">
                    <thead>
                        <tr
                            style="background-color: red; padding: 3px 0; line-height: 68px; text-align: center; color: #fff; font-size: 24px; font-weight: 700px; letter-spacing: 1px;">
                            <th scope="col" style="font-size: 30px;">SprintPan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 20px; background-color: #ffffff; border-radius: 5px;">
                                <p style="font-size: 16px; line-height: 1.6;">Dear '.$name.',</p>
                                <p style="font-size: 16px; line-height: 1.6;">Your account for SprintPan has been created:</p>
                                <p style="font-size: 16px; line-height: 1.6;"><strong>Username:</strong> '.$userName.'</p>
                                <p style="font-size: 16px; line-height: 1.6;"><strong>Password:</strong> '.$_POST['password'].'</p>
                                <p style="font-size: 16px; line-height: 1.6;">Login: https://pansprint.in/NEW/auth-login</p>
                                <p style="font-size: 16px; line-height: 1.6;">Please log in using the provided username and password. Consider changing your password after logging in.</p>
                                <p style="font-size: 16px; line-height: 1.6;">If you have any questions or issues, feel free to contact our support team.</p>
                                <p style="font-size: 16px; line-height: 1.6; color: red;"><strong>Important: </strong>This email contains sensitive information. Keep your username and password confidential and do not share them with anyone.</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 5px 24px 15px; color: #34495e;">
                                Best Regards, <br> SprintPan Team
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 16px 8px; color: #34495e; background-color: red; text-align: center;">
                                <p style="font-size: 12px; color: #ffffff; margin-top: 0;">This is an automated email. Please do not reply to this message.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </body>';
         $emailStatus = registerMail($email, 'PansprintPan Registration Successful', $msguser); 
        if ($emailStatus === 1) {
            echo '<script>toastr.success("User registered successfully! An email has been sent to your registered email address");</script>';
            redirect(3000, 'auth-login.php');
        } else {
            echo '<script>toastr.info("User registered successfully. However, there was an issue sending the email. Please contact support.");</script>';
            redirect(3000, 'auth-login.php');
        }
        
      } else {
        echo '<script>toastr.error("Server Down!");</script>';
        redirect(3000, '');
      }
    }
}
?>
    <div class="authentication-wrapper authentication-cover">
        <div class="authentication-inner row m-0">
            <div class="row h-100 align-items-center justify-content-center" style="min-height: 100vh;">
                <div class="card col-12 col-sm-8 col-md-6 col-lg-5 col-xl-8">
                   <div class=" card-body">
                        <div class="text-center">
                          <h3>New Registration</h3>
                        </div>
                        <hr class="border border-primary">
                        <form class="was-validated row g-3" action="" method="POST">
                            
                          <!-- User Information -->
                          
                          <div class="col-12 col-md-4">
                            <label class="form-label" for="name">Name</label>
                            <input type="text" id="name" name="name" class="form-control border border-primary" placeholder="Enter your full name" required/>
                          </div>
                          <div class="col-12 col-md-4">
                            <label class="form-label" for="fname">Father Name</label>
                            <input type="text" id="fname" name="fname" class="form-control border border-primary" placeholder="Enter your father full name" required/>
                          </div>
                          <div class="col-12 col-md-4">
                            <label class="form-label" for="userName">Username</label>
                            <input type="text" id="userName" name="userName" class="form-control border border-primary" placeholder="Username auto generate" readonly required/>
                          </div>
                          <div class="col-12 col-md-6">
                            <label class="form-label" for="email">Email</label>
                            <input type="text" id="email" name="email" class="form-control border border-primary" placeholder="example@domain.com" required/>
                          </div>
                          <div class="col-12 col-md-3">
                            <label class="form-label" for="phone_number">Mobile</label>
                            <input type="text" id="phone_number" name="phone_number" class="form-control border border-primary" oninput="transformMobileNumber()" placeholder="999999999" required/>
                          </div>
                          <div class="col-12 col-md-3">
                            <label class="form-label" for="company">Organization Name</label>
                            <input type="text" id="company" name="company" class="form-control border border-primary" placeholder="SprintAPI LTD" required/>
                          </div>
                          <div class="col-12 col-md-12">
                            <label class="form-label" for="address">Address</label>
                            <textarea id="address" name="address" class="form-control border border-primary" placeholder="Enter Your Full Address" required></textarea>
                          </div>
                          <div class="col-12 col-md-3">
                            <label class="form-label" for="state">State</label>
                            <input type="text" id="state" name="state" class="form-control border border-primary" placeholder="Exmp: Rajasthan" required/>
                          </div>
                          <div class="col-12 col-md-3">
                            <label class="form-label" for="pin_code">Pin Code</label>
                            <input type="text" id="pin_code" name="pin_code" class="form-control border border-primary" placeholder="123456" required/>
                          </div>
                          <div class="col-12 col-md-3">
                            <label class="form-label" for="pan_no">Pan Number</label>
                            <input type="text" id="pan_no" name="pan_no" class="form-control border border-primary" placeholder="1234ABCD6" required/>
                          </div>
                          <div class="col-12 col-md-3">
                            <label class="form-label" for="uid_no">Aadhaar Number</label>
                            <input type="text" id="uid_no" name="uid_no" class="form-control border border-primary" placeholder="21203XXXXXXX" required/>
                          </div>
                          
                          <div class="col-3">
                            <label class="form-label" for="password">Password</label>
                            <input type="text" id="password" name="password" class="form-control border border-primary" pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$" title="Password must contain at least 1 letter, 1 number, and 1 special character. Minimum length is 8 characters." placeholder="********" required/>
                          </div>
                          <div class="col-2 text-center mt-4">
                                <button type="submit" name="createUser" class="btn btn-primary d-block mt-4">Submit</button>
                            </div>
                            <div class="col-7 text-end mt-4">
                                <p class="mt-4">Already have an Account? <a href="auth-login.php">Sign In</a></p>
                            </div>
                            <div class="col-12">
                                <?php echo $message ?>
                            </div>
                        </form>
                      </div>  
                </div>
            </div>
        </div>
        <!-- Sign Up End -->
    </div>
    <script>
    function transformMobileNumber() {
        var mobileInput = document.getElementById("phone_number");
        var usernameInput = document.getElementById("userName");
        var mobileValue = mobileInput.value;
        var numericMobileValue = mobileValue.replace(/[^0-9]/g, '');
    
        // Check if the numeric value is not empty
        if (numericMobileValue !== '') {
            // Create a new value by combining 'PS' with the last 6 digits of the mobile number
            var newValue = 'PS' + numericMobileValue.slice(-6);
    
            // Set the new value to the username input field
            usernameInput.value = newValue;
            
            let numericValue = inputValue.replace(/[^0-9]/g, '');

            // Update the input value
            event.target.value = numericValue;
        }
    }
    </script>
    <!-- JavaScript Libraries -->
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
    <script src="assets/js/sprintauth-login.js"></script>
</body>

</html>