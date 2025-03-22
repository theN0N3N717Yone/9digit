<?php 
$pageName = "Create Users"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');

if (isset($_POST['createUser'])) {
            // Retrieve form data
            $ownerName = $_POST["name"];
            $userName = $_POST["userName"];
            $email = $_POST["email"];
            $mobile = $_POST["phone_number"];
            $status = $_POST["status"];
            $address = $_POST["address"];
            $state = $_POST["state"];
            $pinCode = $_POST["pin_code"];
            $panNumber = $_POST["pan_no"];
            $aadhaarNumber = $_POST["uid_no"];
            $walletLoad = $_POST["pay_load_min"];
            $password = $_POST["password"];

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Check if the email or mobile already exists
            $checkUserQuery = "SELECT COUNT(*) FROM users WHERE email_id = :email_id OR mobile_no = :mobile_no";
            $checkUserStatement = $conn->prepare($checkUserQuery);
            $checkUserStatement->bindParam(":email_id", $email);
            $checkUserStatement->bindParam(":mobile_no", $mobile);
            $checkUserStatement->execute();
            $userExists = $checkUserStatement->fetchColumn();

            if ($userExists) {
                echo '<script>toastr.error("User with the same email or mobile already exists. Please use a different email or mobile.");</script>';
                redirect(3000, 'userList');
            } else {

                $insertQuery = "INSERT INTO users (owner_name, username, email_id, mobile_no, status, address, state, pin_code, pan_no, uid_no, password, date_time) VALUES (:owner_name, :username, :email_id, :mobile_no, :status, :address, :state, :pin_code, :pan_no, :uid_no, :password, :date_time)";
                $userCreateStatement = $conn->prepare($insertQuery);

                $userCreateStatement->bindParam(":owner_name", filter_var($ownerName, FILTER_SANITIZE_STRING));
                $userCreateStatement->bindParam(":username", $userName);
                $userCreateStatement->bindParam(":email_id", filter_var($email, FILTER_SANITIZE_STRING));
                $userCreateStatement->bindParam(":mobile_no", $mobile);
                $userCreateStatement->bindParam(":status", filter_var($status, FILTER_SANITIZE_STRING));
                $userCreateStatement->bindParam(":address", filter_var($address, FILTER_SANITIZE_STRING));
                $userCreateStatement->bindParam(":state", filter_var($state, FILTER_SANITIZE_STRING));
                $userCreateStatement->bindParam(":pin_code", filter_var($pinCode, FILTER_SANITIZE_STRING));
                $userCreateStatement->bindParam(":pan_no", filter_var($panNumber, FILTER_SANITIZE_STRING));
                $userCreateStatement->bindParam(":uid_no", filter_var($aadhaarNumber, FILTER_SANITIZE_STRING));
                $userCreateStatement->bindParam(":password", $hashedPassword);
                $userCreateStatement->bindParam(":date_time", $timestamp);

                if ($userCreateStatement->execute()) {
                    $msguser = '<body style="font-family: Nunito, sans-serif; font-size: 15px; font-weight: 400;">
                        <div style="margin-top: 50px;">
                            <table cellpadding="0" cellspacing="0"
                                style="font-family: Nunito, sans-serif; font-size: 15px; font-weight: 400; max-width: 600px; border: none; margin: 0 auto; border-radius: 6px; overflow: hidden; background-color: #fff; box-shadow: 0 0 3px rgba(60, 72, 88, 0.15);">
                                <thead>
                                    <tr
                                        style="background-color: red; padding: 3px 0; line-height: 68px; text-align: center; color: #fff; font-size: 24px; font-weight: 700px; letter-spacing: 1px;">
                                        <th scope="col" style="font-size: 30px;">' . getPortalInfo('webName') . '</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="padding: 20px; background-color: #ffffff; border-radius: 5px;">
                                            <p style="font-size: 16px; line-height: 1.6;">Dear ' . $ownerName . ',</p>
                                            <p style="font-size: 16px; line-height: 1.6;">Your account for ' . getPortalInfo('webName') . ' has been created:</p>
                                            <p style="font-size: 16px; line-height: 1.6;"><strong>Username:</strong> ' . $userName . '</p>
                                            <p style="font-size: 16px; line-height: 1.6;"><strong>Password:</strong> ' . $password . '</p>
                                            <p style="font-size: 16px; line-height: 1.6;">Login: ' . $loginUrl . '</p>
                                            <p style="font-size: 16px; line-height: 1.6;">Please log in using the provided username and password. Consider changing your password after logging in.</p>
                                            <p style="font-size: 16px; line-height: 1.6;">If you have any questions or issues, feel free to contact our support team.</p>
                                            <p style="font-size: 16px; line-height: 1.6; color: red;"><strong>Important: </strong>This email contains sensitive information. Keep your username and password confidential and do not share them with anyone.</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 5px 24px 15px; color: #34495e;">
                                            Best Regards, <br> ' . getPortalInfo('webName') . ' Team
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
                    
                    $server_url = 'https://' . $_SERVER['SERVER_NAME'];
                    $wMessage = "*Important Message from " . getPortalInfo('webName') . "*\n"
                        . "Dear " . $ownerName . ",\n\n"
                        . "Your account for " . getPortalInfo('webName') . " has been created:\n"
                        . "- Username: " . $userName . "\n"
                        . "- Password: " . $password . "\n"
                        . "- Login: " . $server_url . "\n\n"
                        . "Please log in using the provided username and password. Consider changing your password after logging in.\n"
                        . "If you have any questions or issues, feel free to contact our support team.\n"
                        . "*Important:*\n"
                        . "This message contains sensitive information. Keep your username and password confidential and do not share them with anyone.\n\n"
                        . "Best Regards,\n"
                        . getPortalInfo('webName') . " Team\n\n"
                        . "---\n"
                        . "This is an automated message. Please do not reply.";

                    $whatsappStatus = whatsappMessage($mobile, $wMessage);
                    $emailStatus = registerMail($email, '' . getPortalInfo('webName') . ' Registration Successful', $msguser);
                    if ($emailStatus && $whatsappStatus === 1) {
                        echo '<script>toastr.success("User Create Successfully. Username Password has been sent email.");</script>';
                        redirect(3000, 'userList');
                    } else {
                        echo '<script>toastr.info("User Create Successfully. However, there was an issue sending the email. Please contact support.");</script>';
                        redirect(3000, 'userList');
                    }
                } else {
                    echo '<script>toastr.error("User Create Errors");</script>';
                    redirect(3000, 'userList');
                }
            }
        }
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
<!-- Aadhaar Print biometric capacitor -->
<div class="col-lg-12 d-flex align-items-strech m-auto">
   <div id="errors" class="card text-bg-primary border-0 w-100">
      <div class="card mx-2 mb-2 sprint-box mt-2">
         <div class="card-body">
      <!-- ---------------------  Create Users capacitor  ---------------- -->
<form class="was-validated row g-1" action="" method="POST">

                        <!-- User Information -->

                        <div class="col-12 col-md-6">
                            <label class="form-label" for="name">Name</label>
                            <input type="text" id="name" name="name" class="form-control border border-primary" placeholder="" required />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="registration_date">Registration Date</label>
                            <input type="datetime-local" id="registration_date" name="registration_date" class="form-control border border-primary" placeholder="" required />
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="userName">Username</label>
                            <input type="text" id="userName" name="userName" class="form-control border border-primary" placeholder="" readonly />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="email">Email</label>
                            <input type="text" id="email" name="email" class="form-control border border-primary" placeholder="example@domain.com" required />
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label" for="phone_number">Mobile</label>
                            <input type="text" id="phone_number" name="phone_number" class="form-control border border-primary" oninput="transformMobileNumber()" placeholder="999999999" required />
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label" for="status">Status</label>
                            <select name="status" class="form-select border border-primary" aria-label="Default select example" required>
                                <option id="status"></option>
                                <option value="approved">Active</option>
                                <option value="inapproved">Inactive</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-12">
                            <label class="form-label" for="address">Address</label>
                            <textarea id="address" name="address" class="form-control border border-primary" placeholder="Enter Your Full Address" required></textarea>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label" for="state">State</label>
                            <input type="text" id="state" name="state" class="form-control border border-primary" placeholder="" required />
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label" for="pin_code">Pin Code</label>
                            <input type="text" id="pin_code" name="pin_code" class="form-control border border-primary" placeholder="" required />
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label" for="pan_no">Pan Number</label>
                            <input type="text" id="pan_no" name="pan_no" class="form-control border border-primary" placeholder="" required />
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label" for="uid_no">Aadhaar Number</label>
                            <input type="text" id="uid_no" name="uid_no" class="form-control border border-primary" placeholder="" required />
                        </div>

                        <div class="col-4">
                            <label class="form-label" for="pay_load_min">Minimum Wallet Load</label>
                            <input type="text" id="pay_load_min" name="pay_load_min" class="form-control border border-primary" placeholder="" required />
                        </div>
                        <div class="col-3">
                            <label class="form-label" for="password">Password</label>
                            <input type="text" id="password" name="password" class="form-control border border-primary" placeholder="" required />
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" name="createUser" class="btn btn-primary me-sm-3 me-1">Create</button>
                            <button type="reset" class="btn btn-info" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                        </div>
                    </form>
      <!-- ---------------------  End Create Users capacitor  ---------------- -->
   </div>
   </div>
</div>
</div>
</div>
</div>
<script>
        function transformMobileNumber() {
            var mobileInput = document.getElementById("phone_number");
            var usernameInput = document.getElementById("userName");
            var mobileValue = mobileInput.value;
            var numericMobileValue = mobileValue.replace(/\D/g, '');

            // Check if the numeric value is not empty
            if (numericMobileValue !== '') {
                // Create a new value by combining 'PS' with the last 6 digits of the mobile number
                var newValue = 'PS' + numericMobileValue.slice(-6);

                // Set the new value to the username input field
                usernameInput.value = newValue;
            }
        }
    </script>
<?php 
require_once('../layouts/mainFooter.php');
?>