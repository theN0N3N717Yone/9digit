<?php
//error_reporting(E_ALL); ini_set('display_errors', 1);
$pageName = "Balance Transfer"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');

if (getUsersInfo('usertype') === "mainadmin") {

    // Handle the form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['transfer'])) {
        // Retrieve form data
        $username = $_POST['username'];
        $type = $_POST['type'];
        $remark = $_POST['remark'];
        $amount = floatval($_POST['amount']); // Convert amount to float (adjust as needed)

        // Fetch user's current balance from the database
        $query = "SELECT id, owner_name, username, balance, email_id FROM users WHERE username = :username";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the user exists
        if ($user) {
            // Update user's balance based on transaction type
            if ($type == 'credit') {
                $newBalance = $user['balance'] + $amount;
            } elseif ($type == 'debit' && $amount <= $user['balance']) {
                $newBalance = $user['balance'] - $amount;
            } else {
                // Handle invalid transaction type or insufficient balance
                echo '<script>toastr.error("Invalid transaction type or insufficient balance.");</script>';
                redirect(1500, 'transferBalance');
            }

            // Generate a unique order ID and timestamp
            $blmt = 'By Admin';
            $status = 'success';
            // Update user's balance in the database
            $updateQuery = "UPDATE users SET balance = :balance WHERE id = :userId";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bindParam(':balance', $newBalance);
            $updateStmt->bindParam(':userId', $user['id']);
            $updateStmt->execute();
            
            
            // Insert a transaction record
            $insertQuery = "INSERT INTO `transactions`(`date_time`, `timestamp`, `userId`, `mode`, `type`, `amount`,`balance`, `reference`, `remark`, `status`)
             VALUES (:date_time,:timestamp,:userId,:mode,:type,:amount,:balance,:reference,:remark,:status)";

            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bindParam(":date_time", $date);
            $insertStmt->bindParam(":timestamp", $timestamp);
            $insertStmt->bindParam(":userId", $user['id']);
            $insertStmt->bindParam(":mode", $blmt);
            $insertStmt->bindParam(":type", $type);
            $insertStmt->bindParam(":amount", $amount);
            $insertStmt->bindParam(":balance", $newBalance);
            $insertStmt->bindParam(":reference", $reference);
            $insertStmt->bindParam(":remark", $remark);
            $insertStmt->bindParam(":status", $status);
            $insertStmt->execute();
            
            $email_message = '<body style="font-family: Nunito, sans-serif; font-size: 15px; font-weight: 400;">
                <div style="margin-top: 50px;">
                    <table cellpadding="0" cellspacing="0"
                        style="font-family: Nunito, sans-serif; font-size: 15px; font-weight: 400; max-width: 600px; border: none; margin: 0 auto; border-radius: 6px; overflow: hidden; background-color: #fff; box-shadow: 0 0 3px rgba(60, 72, 88, 0.15);">
                        <thead>
                            <tr
                                style="background-color: #3498db; padding: 3px 0; line-height: 68px; text-align: center; color: #fff; font-size: 24px; font-weight: 700px; letter-spacing: 1px;">
                                <th scope="col" style="font-size: 30px;">'.getPortalInfo('webName').'</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding: 20px; background-color: #ffffff; border-radius: 5px;">
                                    <p style="font-size: 16px; line-height: 1.6;">Dear '.$user['owner_name'].',</p>
                                    <p style="font-size: 16px; line-height: 1.6;">We wanted to inform you that a recent '.ucfirst($type).' transaction has been processed in your account:</p>
                                    <p style="font-size: 24px; line-height: 1.6; color: #2ecc71;"><strong>Amount: '.$amount.'</strong></p>
                                    <p style="font-size: 16px; line-height: 1.6;">Transaction Type: '.ucfirst($type).'</p>
                                    <p style="font-size: 16px; line-height: 1.6;">Remark: '.$remark.'</p>
                                    <p style="font-size: 16px; line-height: 1.6;">Current Balance: '.$newBalance.'</p>
                                    <p style="font-size: 16px; line-height: 1.6;">Thank you for choosing '.getPortalInfo('webName').'. If you have any questions or concerns, please feel free to contact us.</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 5px 24px 15px; color: #34495e;">
                                    Best Regards, <br> '.getPortalInfo('webName').' Team
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 16px 8px; color: #34495e; background-color: #3498db; text-align: center;">
                                    <p style="font-size: 12px; color: #ffffff; margin-top: 0;">This is an automated email. Please do not reply to this message.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </body>';
            $emailStatus = SendMail($user['email_id'], getPortalInfo('webName') . ' Transaction Notification', $email_message);
        if ($emailStatus === 1) {
            echo '<script>toastr.success("Transaction successful. Updated balance: ' . $newBalance . '");</script>';
            redirect(3000, 'transferBalance');
        } else {
            echo '<script>toastr.info("Transaction successful. Updated balance: ' . $newBalance . ' || However, there was an issue sending the email. Please contact support.");</script>';
            redirect(3000, 'transferBalance');
        }
        } else {
            echo '<script>toastr.error("User not found.");</script>';
            redirect(1500, 'transferBalance');
        }
    }
?>

    <div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
<!-- Aadhaar Print biometric capacitor -->
<div class="col-lg-4 d-flex align-items-strech m-auto <?php echo $getresult ? 'd-none' : ''; ?> ">
   <div id="errors" class="card text-bg-primary border-0 w-100">
      <div class="card mx-2 mb-2 sprint-box mt-2">
                                    <div class="card-body">
                                        
                                        <form class="was-validated" action="" method="post">
                                            <div class="mb">
                                                <label for="username" class="form-label">Name</label>
                                                <select id="username" name="username" class="select2 form-select mb-2 border border-primary" required>
                                                    <option value="">Select User</option>
                                                    <?php $query = "SELECT id, owner_name, username FROM users";
                                                    $stmt = $conn->prepare($query);
                                                    $stmt->execute();
                                                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                    if (count($result) > 0) {
                                                        foreach ($result as $row) {
                                                            echo "<option value='" . $row["username"] . "'>" . $row["username"] . " || " . $row["owner_name"] . "</option>";
                                                        }
                                                    } ?>
                                                </select>
                                            </div>
                                            <div class="mt-2">
                                                <label for="nameSmall" class="form-label">Type</label>
                                                <select id="type" name="type" class="form-select mb-2 border border-primary" required>
                                                    <option value="">Select Type</option>
                                                    <option value="credit">Credit</option>
                                                    <option value="debit">Debit</option>
                                                </select>
                                            </div>
                                            <div class="mb">
                                                <label class="form-label" for="emailSmall">Remark</label>
                                                <input type="text" class="form-control mb-2 border border-primary" id="remark" name="remark" placeholder="Remark" required>
                                            </div>
                                            <div class="mb">
                                                <label for="dobSmall" class="form-label">Amount</label>
                                                <input type="text" class="form-control mb-2 border border-primary" id="amount" name="amount" placeholder="Enter Amount" required>
                                            </div>
                                            <button type="submit" name='transfer' class="btn btn-primary btn-buy-now me-3">Submit</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
<?php } else { ?>
    <div class="misc-wrapper text-center" style="margin-top: 100px">
        <span class="text-danger"><i class="bi bi-exclamation-triangle display-1 text-primary"></i></span>
        <h2 class="mb-2 mx-2">You are not authorized!</h2>
        <p class="mb-4 mx-2 text-danger">You do not have permission to view this page using the credentials that you have provided while login. <br> Please contact your site administrator.</p>
        <a href="dashboard" class="btn btn-primary">Back to home</a>
    </div>
<?php } ?>
<?php
require_once('../layouts/mainFooter.php');
?>