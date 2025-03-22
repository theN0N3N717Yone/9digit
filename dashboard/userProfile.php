<?php 
$pageName = "User Profile"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');

// Assuming the form is submitted using POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
if(isset($_POST['profileUpdate'])){
$userID = getUsersInfo('id');    
    $newName = $_POST['name'];
    $newEmail = $_POST['email'];
    $newPhoneNumber = $_POST['phoneNumber'];
    $companyName = $_POST['company'];

    // Perform SQL update query
    $sql = "UPDATE users SET owner_name = :owner_name, email_id = :email_id, mobile_no = :mobile_no, shop_name = :shop_name WHERE id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':owner_name', $newName, PDO::PARAM_STR);
    $stmt->bindParam(':email_id', $newEmail, PDO::PARAM_STR);
    $stmt->bindParam(':mobile_no', $newPhoneNumber, PDO::PARAM_STR);
    $stmt->bindParam(':shop_name', $companyName, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $userID, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt) { 
        echo "<script>toastr.success('Success! Your profile has been updated.');</script>"; 
        redirect(2000, '');
    } else {
        echo "<script>toastr.error('Error! Something went wrong.');</script>"; 
        redirect(2000, '');
    }
}    
if (isset($_POST['passUpdate'])) {
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($newPassword !== $confirmPassword) {
        echo "<script>toastr.error('New Password and Confirm Password do not match. Please try again.');</script>";
        redirect(2000, '');
    } else {
        $storedPassword = getUsersInfo('password');
        if (password_verify($oldPassword, $storedPassword)) {
            
            // Old password is correct, proceed with the update
            $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Perform SQL update query
            $sql = "UPDATE users SET password = :new_password WHERE id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':new_password', $hashedNewPassword, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $userID, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt) {
                echo "<script>toastr.success('Password updated successfully.');</script>";
                redirect(2000, '');
            } else {
                echo "<script>toastr.success('Error updating password. Please try again.');</script>";
                redirect(2000, '');
            }
        } else {
            echo "<script>toastr.success('Old Password is incorrect. Please try again.');</script>";
            redirect(2000, '');
        }
    }
}

if (isset($_POST['toggleOTP'])) {
    // Assuming you have a 'users' table with columns 'id' and 'otp'
    $currentStatus = getUsersInfo('otp');

    // Toggle the OTP status
    $newStatus = $currentStatus == 'yes' ? 'no' : 'yes';

    // Update the database
    $sql = "UPDATE users SET otp = :new_status WHERE id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':new_status', $newStatus, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userID, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt) {
        echo 'OTP status updated successfully.';
    } else {
        echo 'Error updating OTP status.';
    }
} else {
    // Invalid request
    http_response_code(400);
    echo 'Bad Request';
}

}
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">

<div class="col-lg-10 d-flex align-items-strech m-auto">
   <div id="errors" class="card text-bg-primary border-0 w-100">
      <div class="card mx-2 mb-2 sprint-box mt-2">
         <div class="card-body">
                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                        <div class="button-wrapper">
                            <?php
                                $otpStatus = getUsersInfo('otp'); // Assuming getUsersInfo returns 'yes' or 'no'
                                
                                // Use a conditional statement to determine whether to show or hide content
                                if ($otpStatus === 'yes') {
                                    $displayClass = 'enabled-content';
                                } else {
                                    $displayClass = 'disabled-content';
                                }
                                $isChecked = ($otpStatus === 'yes') ? 'checked' : '';
                                $isText = ($otpStatus === 'yes') ? 'OTP is enabled.' : 'OTP is disabled.';
                                $isClass = ($otpStatus === 'yes') ? 'text-primary' : 'text-danger';
                                ?>
                                <label class="switch switch-square">
                                    <input type="checkbox" class="switch-input" <?= $isChecked ?> data-otp-status="<?= $otpStatus ?>">
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"></span>
                                        <span class="switch-off"></span>
                                    </span>
                                    <span class="switch-label <?= $isClass ?>"><?= $isText ?></span>
                                </label>

                            <button class="btn btn-primary account-image-reset me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#profileUpdate" aria-controls="profileUpdate">Update Profile</button>
                            <button class="btn btn-primary account-image-reset" type="button" data-bs-toggle="offcanvas" data-bs-target="#passwordUpdate" aria-controls="passwordUpdate">Change Password</button>
                        </div>
                    </div>
                </div>
                <hr class="my-0 bg-warning">
                <!-- Display User Details in a Table -->
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th scope="row">UserID</th>
                                <td class="text-primary">
                                    <b><?= ucwords(getUsersInfo('username')) ?></b>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Name</th>
                                <td><?= ucwords(getUsersInfo('owner_name')) ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Phone Number</th>
                                <td><?= getUsersInfo('mobile_no') ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Email ID</th>
                                <td><?= getUsersInfo('email_id') ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Organization</th>
                                <td><?= ucwords(getUsersInfo('shop_name')) ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Full Address</th>
                                <td><?= ucwords(getUsersInfo('address')) ?>, <?= ucwords(getUsersInfo('state')) ?> - <?= ucwords(getUsersInfo('pin_code')) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
      </div>
   </div>
</div>
</div>
</div>


    <!-- Off-canvas Form for Editing Profile -->
    <div class="offcanvas offcanvas-end rounded h-100 border border-primary" tabindex="-1" id="profileUpdate" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header">
            <h5 id="offcanvasRightLabel">Edit Profile Info</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close" style="background-color:black; color:#fff"></button>
        </div>
        <div class="offcanvas-body">
            
            <!-- Profile Editing Form -->
            
            <form class="was-validated" method="POST" action="">
                <div class="row">
                    <div class="mb-3 col-md-12">
                        <label for="name" class="form-label">Name</label>
                        <input class="form-control" type="text" id="name" name="name" value="<?= ucfirst(getUsersInfo('owner_name')) ?>" required />
                    </div>
                    <div class="mb-3 col-md-12">
                        <label for="email" class="form-label">E-mail</label>
                        <input class="form-control" type="email" id="email" name="email" value="<?= getUsersInfo('email_id') ?>" placeholder="john.doe@example.com" required />
                    </div>
                    <div class="mb-3 col-md-12">
                        <label class="form-label" for="phoneNumber">Phone Number</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text">IN (+91)</span>
                            <input type="tel" id="phoneNumber" name="phoneNumber" class="form-control" value="<?= getUsersInfo('mobile_no') ?>" required maxlength="10" />
                        </div>
                    </div>
                    <div class="mb-3 col-md-12">
                        <label for="company" class="form-label">Organization Name</label>
                        <input class="form-control" type="text" id="company" name="company" value="<?= getUsersInfo('shop_name') ?>" placeholder="Enter company name" required />
                    </div>
                    <div class="mb-3 col-md-12">
                        <button class="btn btn-primary" type="submit" name="profileUpdate">Submit</button>
                    </div>
                </div>
            </form>
            
            <!-- \ Profile Editing Form -->
            
        </div>
    </div>
    
    <!-- Off-canvas Form for Password Update -->
    <div class="offcanvas offcanvas-end rounded h-100 border border-primary" tabindex="-1" id="passwordUpdate" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header">
            <h5 id="offcanvasRightLabel">Change Password</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close" style="background-color:black; color:#fff"></button>
        </div>
        <div class="offcanvas-body">
            
            <!-- Password Update Form -->
            
            <form class="was-validated" method="POST" action="">
                <div class="row">
                    <div class="mb-3 col-md-12">
                        <label for="oldPassword" class="form-label">Old Password</label>
                        <input class="form-control" type="text" id="oldPassword" name="oldPassword" placeholder="OLD Password" required/>
                    </div>
                    <div class="mb-3 col-md-12">
                        <label for="newPassword" class="form-label">New Password</label>
                        <input class="form-control" type="text" id="newPassword" name="newPassword" pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$" title="Password must contain at least 1 letter, 1 number, and 1 special character. Minimum length is 8 characters." placeholder="Enter New Password" required/>
                    </div>
                    <div class="mb-3 col-md-12">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input class="form-control" type="text" id="confirmPassword" name="confirmPassword" pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$" title="Password must contain at least 1 letter, 1 number, and 1 special character. Minimum length is 8 characters." placeholder="Confirm Password" required/>
                    </div>
                    <div class="mb-3 col-md-12">
                        <button class="btn btn-primary" type="submit" name="passUpdate">Submit</button>
                    </div>
                </div>
            </form>
            
            <!-- \ Password Update Form -->
            
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('.switch-input').on('change', function () {
            var isChecked = $(this).prop('checked') ? 'yes' : 'no';
            var userId = <?= getUsersInfo('id') ?>; // You need to replace this with the actual function to get the user ID

            // Make an AJAX request to update the database
            $.ajax({
                type: 'POST',
                url: '../system/update-otp-status.php', // Replace with the actual endpoint
                data: { userId: userId, otpStatus: isChecked },
                success: function (response) {
                    // Handle the response if needed
                    toastr.success(response);
                
                    // Reload the page after 3 seconds
                    setTimeout(function () {
                        location.reload();
                    }, 3000);
                },

                error: function (error) {
                    // Handle the error if needed
                    toastr.error(error);
                }
            });
        });

        function getUserId() {
            // Implement the logic to get the user ID
            // For example, you can add a data attribute to the container element
            return $('.switch-input').data('user-id');
        }
    });
</script>
<?php require_once('../layouts/mainFooter.php'); ?>
