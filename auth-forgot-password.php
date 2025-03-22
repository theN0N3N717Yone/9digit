<?php
require_once('system/connectivity_functions.php');

// Generate and store a CSRF token in the session
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Retrieve email from the form
$email = $_POST['email'];


try {
    $conn = connectDB();

    // Check if email exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email_id = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        
        // Fetch user data
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Extract user information
        $name = $userData['owner_name']; // Change 'name' to the actual column name in your database
        $userName = $userData['username'];
        
        // Generate a random password
        $new_password = generateRandomString();

        // Update the password in the database
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_stmt = $conn->prepare("UPDATE users SET password = :password WHERE email_id = :email");
        $update_stmt->bindParam(':password', $hashed_password);
        $update_stmt->bindParam(':email', $email);
        $update_stmt->execute();

        // Send the new password to the user's email (You need to implement this)
        $messages = "Password has been reset. Check your email for the new password.";
        $passMessage = '<body style="font-family: Nunito, sans-serif; font-size: 15px; font-weight: 400;">
            <div style="margin-top: 50px;">
                <table cellpadding="0" cellspacing="0"
                    style="font-family: Nunito, sans-serif; font-size: 15px; font-weight: 400; max-width: 600px; border: none; margin: 0 auto; border-radius: 6px; overflow: hidden; background-color: #fff; box-shadow: 0 0 3px rgba(60, 72, 88, 0.15);">
                    <thead>
                        <tr
                            style="background-color: #ff5733; padding: 3px 0; line-height: 68px; text-align: center; color: #fff; font-size: 24px; font-weight: bold; letter-spacing: 1px;">
                            <th scope="col" style="font-size: 30px;">'.getPortalInfo('webName').'</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 20px; background-color: #ffffff; border-radius: 5px;">
                                <p style="font-size: 16px; line-height: 1.6;">Dear '.$name.',</p>
                                <p style="font-size: 16px; line-height: 1.6;">You have requested to reset your password for '.getPortalInfo('webName').':</p>
                                <p style="font-size: 16px; line-height: 1.6;"><strong>New Password:</strong> '.$new_password.'</p>
                                <p style="font-size: 16px; line-height: 1.6;">Login: https://yourcompany.com/auth-login</p>
                                <p style="font-size: 16px; line-height: 1.6;">Please log in using the provided new password. Consider changing your password after logging in.</p>
                                <p style="font-size: 16px; line-height: 1.6;">If you did not request this password reset or need further assistance, please contact our support team.</p>
                                <p style="font-size: 16px; line-height: 1.6; color: #ff5733;"><strong>Important: </strong>Keep your new password confidential and do not share it with anyone.</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 5px 24px 15px; color: #34495e;">
                                Best Regards, <br> '.getPortalInfo('webName').' Team
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 16px 8px; color: #34495e; background-color: #ff5733; text-align: center;">
                                <p style="font-size: 12px; color: #ffffff; margin-top: 0;">This is an automated email. Please do not reply to this message.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </body>';

       $emailStatus = registerMail($email, 'You have requested to reset your password for '.$userName.'', $passMessage); 
        redirect(3000, 'auth-register');
    } 
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Function to generate a random string
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}
?>

<!DOCTYPE html>

<html
  lang="en"
  class="light-style layout-wide customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Forgot Password | <?= getPortalInfo('webName') ?></title>
    <meta name="description" content="<?= getPortalInfo('webName') ?> Forgot Password" />
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../assets/vendor/css/pages/page-auth.css" />

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../assets/js/config.js"></script>
  </head>

  <body>
    <!-- Content -->

    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
          <!-- Forgot Password -->
          <div class="card">
            <div class="card-body">
              <!-- Logo -->
              <div class="app-brand justify-content-center">
                <a href="auth-forgot-password" class="app-brand-link gap-2">
                  <span class="app-brand-text demo text-body fw-bold"><?= strtoupper(getPortalInfo('webName')) ?></span>
                </a>
              </div>
              <!-- /Logo -->
              <h4 class="mb-2">Forgot Password? 🔒</h4>
              <p class="mb-4">Enter your email and we'll send you instructions to reset your password</p>
              <form method="POST" class="mb-3" action="<?= $_SERVER['PHP_SELF'] ?>">
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input
                    type="text"
                    class="form-control"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    autofocus />
                </div>
                <input class="btn btn-primary d-grid w-100" type="submit" value="Reset Password">
              </form>
              <div class="text-center">
                <a href="auth-login.php" class="d-flex align-items-center justify-content-center">
                  <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
                  Back to login
                </a>
                <span class="text-danger">
                <?= $messages ?></span>
              </div>
            </div>
          </div>
          <!-- /Forgot Password -->
        </div>
      </div>
    </div>

    <!-- / Content -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->

    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>
  </body>
</html>
