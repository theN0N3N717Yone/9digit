<?php 
$pageName = "User Panding"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');
if(getUsersInfo('usertype') === "mainadmin"){

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve values from the form
    $status = $_POST["status"];
    $id = $_POST["id"];

    // Perform SQL update based on the status and ID
    $updateQuery = "UPDATE users SET status = :status WHERE id = :id";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bindParam(':status', $status);
    $updateStmt->bindParam(':id', $id);

    // Fetch additional user information from the database
    $userQuery = "SELECT owner_name, username, email_id FROM users WHERE id = :id";
    $userStmt = $conn->prepare($userQuery);
    $userStmt->bindParam(':id', $id);
    $userStmt->execute();
    $userData = $userStmt->fetch(PDO::FETCH_ASSOC);

    if ($updateStmt->execute()) {
        // Email template with fetched user information
        $msgUserApproved = '<body style="font-family: Nunito, sans-serif; font-size: 15px; font-weight: 400;">
            <div style="margin-top: 50px;">
                <table cellpadding="0" cellspacing="0"
                    style="font-family: Nunito, sans-serif; font-size: 15px; font-weight: 400; max-width: 600px; border: none; margin: 0 auto; border-radius: 6px; overflow: hidden; background-color: #fff; box-shadow: 0 0 3px rgba(60, 72, 88, 0.15);">
                    <thead>
                        <tr
                            style="background-color: green; padding: 3px 0; line-height: 68px; text-align: center; color: #fff; font-size: 24px; font-weight: 700px; letter-spacing: 1px;">
                            <th scope="col" style="font-size: 30px;">' . getPortalInfo('webName') . '</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 20px; background-color: #ffffff; border-radius: 5px;">
                                <p style="font-size: 16px; line-height: 1.6;">Dear ' . $userData['owner_name'] . ',</p>
                                <p style="font-size: 16px; line-height: 1.6;">Your account for ' . getPortalInfo('webName') . ' has been approved:</p>
                                <p style="font-size: 16px; line-height: 1.6;"><strong>Username:</strong> ' . $userData['username'] . '</p>
                                <p style="font-size: 16px; line-height: 1.6;">You can now log in and access your account.</p>
                                <p style="font-size: 16px; line-height: 1.6;">Login: https://pansprint.in/NEW/auth-login</p>
                                <p style="font-size: 16px; line-height: 1.6;">If you have any questions or issues, feel free to contact our support team.</p>
                                <p style="font-size: 16px; line-height: 1.6; color: green;"><strong>Congratulations!</strong> Your account is now active and ready for use.</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 5px 24px 15px; color: #34495e;">
                                Best Regards, <br> Onboarding Team
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 16px 8px; color: #34495e; background-color: green; text-align: center;">
                                <p style="font-size: 12px; color: #ffffff; margin-top: 0;">This is an automated email. Please do not reply to this message.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </body>';

        $emailStatusApproved = SendMail($userData['email_id'], '' . getPortalInfo('webName') . ' Account Approved', $msgUserApproved);
        echo "<script>toastr.success('User " . ucfirst($status) . " successfully');</script>";
        redirect(1500, 'userList');
    } else {
        echo "<script>toastr.error('Failed to update status. Please try again.');</script>";
        redirect(1500, 'userPanding');
    }
}
?>

<div class="container-fluid pt-4 px-4">
    <div class="col-12">
        <div class="card rounded h-100 p-4 border border-primary">
            <div class="table-responsive rounded mt-4">
                <table id="" class="table table-bordered table-hover">
                <thead style="background: #000cad;">
                    <tr>
                        <th style='display:none'>#</th>
                        <th style="color: #fff;">USER DETAILS</th>
                        <th style="color: #fff;">CONTACT</th>
                        <th style="color: #fff;">DOCS</th>
                        <th style="color: #fff;"><i class="bx bx-map"></i></th>
                        <th style="color: #fff;">BALANCE</th>
                        <th style="color: #fff;">CREATE_ON</th>
                        <th style="color: #fff;">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    $stmt = $conn->prepare("SELECT * FROM users WHERE status='unapproved' ORDER BY id DESC");
                    $stmt->execute();

                    
                    $sl = 1;

                    $inactiveUserFound = false; // Flag to check if unapproved user is found
                    
                    while ($row = $stmt->fetch()) {
                        if ($row['status'] === "approved") {
                            $status = '<span class="badge bg-success">' . strtoupper($row['status']) . '</span>';
                        } else if ($row['status'] === "unapproved") {
                            $status = '<span class="badge bg-primary">' . strtoupper($row['status']) . '</span>';
                            $inactiveUserFound = true; // Set the flag to true
                        } else if ($row['status'] === "suspended") {
                            $status = '<span class="badge bg-info">' . strtoupper($row['status']) . '</span>';
                        }
                    
                        // Display the table row
                        echo "<tr>
                                <td style='display:none'>" . $sl . "</td>
                                <td class=''>" . $row['username'] . "<br>" . $row['owner_name'] . "</td>
                                <td class=''>" . $row['mobile_no'] . "<br>" . $row['email_id'] . "</td>
                                <td class=''>" . $row["pan_no"] . "<br>" . $row["uid_no"] . "</td>
                                <td class=''>" . $row["address"] . "</td>
                                <td class=''><b>₹" . $row["balance"] . "</b></td>
                                <td class=''>" . date("d M Y", strtotime($row['date_time'])) . "</td>
                                <td class=''>
                                    <form action='' method='post'>
                                        <select class='form-select border border-warning form-select-sm' aria-label='.form-select-sm example' name='status'>
                                            <option selected=''>Status</option>
                                            <option value='approved'>Approved</option>
                                            <option value='suspended'>Suspended</option>
                                        </select>
                                        <input type='hidden' class='btn btn-sm mt-1 btn-primary' name='id' value='" . $row['id'] . "'>
                                        <input type='submit' class='btn btn-sm mt-1 btn-primary' value='Submit'>
                                    </form>
                                </td>
                            </tr>";
                        $sl++;
                    }
                    
                    // Display message if no inactive users are found
                    if (!$inactiveUserFound) {
                        echo "<tr class='text-center'><td colspan='8'><img src='../assets/img/icons/sprinticon/notfound.svg' height='100' width='100'></td></tr>";
                    }
                    ?>

                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

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
