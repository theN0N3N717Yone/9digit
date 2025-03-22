<?php
$pageName = "Users List"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user']) && !empty($_POST['id'])) {
    try {
        // Retrieve data from the form
        $id = $_POST['id'];
        $ownerName = $_POST['owner_name'];
        $shopName = $_POST['shop_name'];
        $username = $_POST['username'];
        $email = $_POST['email_id'];
        $mobile = $_POST['mobile_no'];
        $status = $_POST['status'];
        $address = $_POST['address'];
        $otpStatus = $_POST['otp'];
        $panNumber = $_POST['pan_no'];
        $aadhaarNumber = $_POST['uid_no'];
    
        // Update Charges Management
        $service_pricing_aadhaar = $_POST['service_pricing_aadhaar'];
        $service_pricing_voter = $_POST['service_pricing_voter'];
        $service_pricing_driving_licence = $_POST['service_pricing_driving_licence'];
        $service_pricing_ayushman = $_POST['service_pricing_ayushman'];
        $service_pricing_panFind = $_POST['service_pricing_panFind'];
        $service_pricing_vmLink = $_POST['service_pricing_vmLink'];
        $service_pricing_rashan = $_POST['service_pricing_rashan'];
        $service_pricing_rc = $_POST['service_pricing_rc'];
        $service_pricing_ekycpan = $_POST['service_pricing_ekycpan'];
        $p_nsdl = $_POST['p_nsdl'];

        // Update Mobile Recharge Commissions
        $airtel = $_POST['airtel'];
        $idea = $_POST['idea'];
        $jio = $_POST['jio'];
        $bsnlTopup = $_POST['bsnl_topup'];
        $bsnlSpecial = $_POST['bsnl_special'];
    
        // Update DTH Recharge Commissions
        $airtelDth = $_POST['airtel_dth'];
        $dishDth = $_POST['dish_dth'];
        $tataskyDth = $_POST['tatasky_dth'];
        $videoconDth = $_POST['videocon_dth'];
        $sunDth = $_POST['sun_dth'];

        // Prepare the SQL query using a parameterized statement
        $sql = "UPDATE users SET 
                owner_name=:ownerName, 
                shop_name=:shopName, 
                username=:username, 
                email_id=:email_id, 
                mobile_no=:mobile_no, 
                status=:status, 
                address=:address, 
                otp=:otpStatus, 
                pan_no=:panNumber, 
                uid_no=:aadhaarNumber,
                
                service_pricing_aadhaar=:service_pricing_aadhaar,
                service_pricing_voter=:service_pricing_voter,
                service_pricing_driving_licence=:service_pricing_driving_licence,
                service_pricing_ayushman=:service_pricing_ayushman,
                service_pricing_panFind=:service_pricing_panFind,
                service_pricing_vmLink=:service_pricing_vmLink,
                service_pricing_rashan=:service_pricing_rashan,
                service_pricing_rc=:service_pricing_rc,
                service_pricing_ekycpan=:service_pricing_ekycpan,
                p_nsdl=:p_nsdl,
                e_nsdl=:e_nsdl,
                
                airtel=:airtel,
                idea=:idea,
                jio=:jio,
                bsnl_topup=:bsnlTopup,
                bsnl_special=:bsnlSpecial,
                airtel_dth=:airtelDth,
                dish_dth=:dishDth,
                tatasky_dth=:tataskyDth,
                videocon_dth=:videoconDth,
                sun_dth=:sunDth
                WHERE id=:id";

        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':ownerName', $ownerName);
        $stmt->bindParam(':shopName', $shopName);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email_id', $email);
        $stmt->bindParam(':mobile_no', $mobile);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':otpStatus', $otpStatus);
        $stmt->bindParam(':panNumber', $panNumber);
        $stmt->bindParam(':aadhaarNumber', $aadhaarNumber);
        $stmt->bindParam(':service_pricing_aadhaar', $service_pricing_aadhaar);
        $stmt->bindParam(':service_pricing_voter', $service_pricing_voter);
        $stmt->bindParam(':service_pricing_driving_licence', $service_pricing_driving_licence);
        $stmt->bindParam(':service_pricing_ayushman', $service_pricing_ayushman);
        $stmt->bindParam(':service_pricing_panFind', $service_pricing_panFind);
        $stmt->bindParam(':service_pricing_vmLink', $service_pricing_vmLink);
        $stmt->bindParam(':service_pricing_rashan', $service_pricing_rashan);
        $stmt->bindParam(':service_pricing_rc', $service_pricing_rc);
        $stmt->bindParam(':service_pricing_ekycpan', $service_pricing_ekycpan);
        $stmt->bindParam(':p_nsdl', $p_nsdl);
        $stmt->bindParam(':e_nsdl', $p_nsdl);
        $stmt->bindParam(':airtel', $airtel);
        $stmt->bindParam(':idea', $idea);
        $stmt->bindParam(':jio', $jio);
        $stmt->bindParam(':bsnlTopup', $bsnlTopup);
        $stmt->bindParam(':bsnlSpecial', $bsnlSpecial);
        $stmt->bindParam(':airtelDth', $airtelDth);
        $stmt->bindParam(':dishDth', $dishDth);
        $stmt->bindParam(':tataskyDth', $tataskyDth);
        $stmt->bindParam(':videoconDth', $videoconDth);
        $stmt->bindParam(':sunDth', $sunDth);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();
        echo "<script>toastr.success('Record updated successfully || Form : $ownerName');</script>";
        redirect(1500, 'userList');
    } catch (PDOException $e) {
        echo "<script>toastr.error('Error updating record: " . $e->getMessage() . "');</script>";
        redirect(1500, 'userList');
    }
}
if (isset($_GET['resetpass']) && !empty($_GET['token'])) {
    $id = $_GET['token'];

$stmt = $conn->prepare("select * from users WHERE id = ?");
$stmt->execute([$id]);
$urow = $stmt->fetch();
$newPassword = 'Sprint@2023';

$hash = password_hash($newPassword,PASSWORD_DEFAULT);

$sql = $conn->prepare("UPDATE users SET password=?  WHERE id=?");

if ($sql->execute([$hash, $id])) {
    echo '<script>Ps_alert("Password Reset Successfully","success")</script>';
    redirect(1500, 'users');
    $userName = strtoupper($urow['owner_name']);
    $userId = strtoupper($urow['username']);
    $email = $urow['email_id'];

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
                            <p style="font-size: 16px; line-height: 1.6;">Dear '.$userName.',</p>
                            <p style="font-size: 16px; line-height: 1.6;">Your password for ' . getPortalInfo('webName') . ' has been reset successfully:</p>
                            <p style="font-size: 16px; line-height: 1.6;"><strong>Username:</strong> '.$userId.'</p>
                            <p style="font-size: 16px; line-height: 1.6;"><strong>New Password:</strong> '.$newPassword.'</p>
                            <p style="font-size: 16px; line-height: 1.6;">Login: https://pansprint.in/NEW/auth-login</p>
                            <p style="font-size: 16px; line-height: 1.6;">Please log in using the provided username and new password. Consider changing your password after logging in.</p>
                            <p style="font-size: 16px; line-height: 1.6;">If you have any questions or issues, feel free to contact our support team.</p>
                            <p style="font-size: 16px; line-height: 1.6; color: red;"><strong>Important: </strong>This email contains sensitive information. Keep your username and password confidential and do not share them with anyone.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 24px 15px; color: #34495e;">
                            Best Regards, <br> Onboarding Team
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
    $emailStatus = SendMail($email, '' . getUsersInfo('webName') . ' Password Reset Successful', $msguser);
    echo "<script>toastr.success('Password reset successfully || Form : $userName');</script>";
    redirect(1500, 'userList');
} else {
    echo "<script>toastr.error('Error Reset record:');</script>";
    redirect(1500, 'userList');
}

}
if (isset($_GET['delete']) && !empty($_GET['token'])) {
    $id = $_GET['token'];

    // Prepare and execute the DELETE query
    $usql = $conn->prepare("DELETE FROM `users` WHERE id = ?");
    $success = $usql->execute([$id]);

    // Check if the deletion was successful
    if ($success) {
        echo "<script>toastr.success('Record deleted successfully');</script>";
        redirect(3000,'userList');
    } else {
        echo "<script>toastr.error('Error deleting record');</script>";
        redirect(1500,'userList');
    }
}

?>
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">



   <div class="row g-4 mb-4">
      <div class="col-sm-6 col-xl-3">
         <div class="card">
            <div class="card-body">
               <div class="d-flex align-items-start justify-content-between">
                  <div class="content-left">
                     <span>Approved Users</span>
                     <div class="d-flex align-items-end mt-2">
                        <h4 class="mb-0 me-2"><?php $query = "SELECT * FROM users WHERE status = 'approved'"; $stmt = $conn->query($query); $activeUsers = $stmt->rowCount(); echo $activeUsers; ?></h4>
                     </div>
                     <p class="mb-0">Total Users Approved</p>
                  </div>
                  <div class="avatar">
                     <span class="avatar-initial rounded bg-label-danger">
                        <i class="bx bx-user-check bx-sm"></i>
                     </span>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="col-sm-6 col-xl-3">
         <div class="card">
            <div class="card-body">
               <div class="d-flex align-items-start justify-content-between">
                  <div class="content-left">
                     <span>Pending Users</span>
                     <div class="d-flex align-items-end mt-2">
                        <h4 class="mb-0 me-2"><?php $query = "SELECT * FROM users WHERE status = 'unapproved'"; $stmt = $conn->query($query); $activeUsers = $stmt->rowCount(); echo $activeUsers; ?></h4>
                     </div>
                     <p class="mb-0">Total Users Pending</p>
                  </div>
                  <div class="avatar">
                     <span class="avatar-initial rounded bg-label-success">
                        <i class="bx bx-time-five bx-sm"></i>
                     </span>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="col-sm-6 col-xl-3">
         <div class="card">
            <div class="card-body">
               <div class="d-flex align-items-start justify-content-between">
                  <div class="content-left">
                     <span>Unapproved Users</span>
                     <div class="d-flex align-items-end mt-2">
                        <h4 class="mb-0 me-2"><?php $query = "SELECT * FROM users WHERE status = 'unapproved'"; $stmt = $conn->query($query); $activeUsers = $stmt->rowCount(); echo $activeUsers; ?></h4>
                        <small class="text-success">(+29%)</small>
                     </div>
                     <p class="mb-0">Total Users</p>
                  </div>
                  <div class="avatar">
                     <span class="avatar-initial rounded bg-label-primary">
                        <i class="bx bx-user-minus bx-sm"></i>
                     </span>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <div class="col-sm-6 col-xl-3">
         <div class="card">
            <div class="card-body">
               <div class="d-flex align-items-start justify-content-between">
                  <div class="content-left">
                     <span>Suspended Users</span>
                     <div class="d-flex align-items-end mt-2">
                        <h4 class="mb-0 me-2"><?php $query = "SELECT * FROM users WHERE status = 'suspended'"; $stmt = $conn->query($query); $activeUsers = $stmt->rowCount(); echo $activeUsers; ?></h4>
                     </div>
                     <p class="mb-0">Users Suspended</p>
                  </div>
                  <div class="avatar">
                     <span class="avatar-initial rounded bg-label-warning">
                        <i class="bx bx-user-x bx-sm"></i>
                     </span>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!-- Users List Table -->
   <div class="card shadow-lg sprint-box">
      <div class="card-header flex-column flex-md-row">
         <form method="post" action="">
            <div class="row">
               <div class="col-3">
                  <div class="form-group">
                     <label class="form-label" style="color: black;">Filter</label>
                     <input type="text" class="form-control flatpickr-input" name="daterange" placeholder="<?php echo $date; ?> to YYYY-MM-DD" id="flatpickr-range" readonly="readonly">
                  </div>
               </div>
               <div class="col-2">
                  <div class="form-group">
                     <label class="form-label" style="color: black;">By Status</label>
                     <select name="status" class="form-select">
                        <option value="">Select Status</option>
                        <option value="approved">Approved</option>
                        <option value="unapproved">Unapproved</option>
                        <option value="suspended">Suspended</option>
                     </select>
                  </div>
               </div>
               <div class="col-7">
                  <div class="form-group">
                     <label class="form-label" style="color: black;">Search</label>
                     <input type="search" autocomplete="off" name="search" class="form-control border-danger" placeholder="Search By:- Reference Number / Mode / Remark" autofocus />
                  </div>
               </div>
               <div class="col-5 mt-4">
                  <div class="dt-action-buttons text-start pt-3 pt-md-0">
                     <div class="dt-buttons">
                        <button class="dt-button create-new btn btn-primary" type="submit">Filter</button>
                     </div>
                  </div>
               </div>
            </div>
         </form>
      </div>
      <div class="card-body">
         <div id="demo_info" class="p">
            <table id="example" class="datatables-basic table border-top" style="width:100%">
               <thead style="background: #000cad;">
                  <tr>
                     <th style="display: none;">#</th>
                     <th style="color: #fff;">USER</th>
                     <th style="color: #fff;">CONTACT</th>
                     <th style="color: #fff;">BALANCE</th>
                     <th style="color: #fff;">STATUS</th>
                     <th style="color: #fff;">ACTION</th>
                  </tr>
               </thead>
               <tbody>
                  <?php
                            $conn = connectDB();
                            
                            $stmt = $conn->prepare("select * from users ORDER BY `id` DESC");
                            $stmt->execute();
                            
                            
                            if (isset($_POST['daterange'])) {
                                $dateRange = $_POST['daterange'];
                                list($fromdate, $todate) = explode(' to ', $dateRange);
                            
                                $stmt = $conn->prepare("SELECT * FROM users WHERE date_time BETWEEN :fromdate AND :todate ORDER BY `id` DESC");
                                $stmt->execute([
                                    'fromdate' => $fromdate,
                                    'todate' => $todate
                                ]);
                            } else {
                                // Default values if the form is not submitted
                                $fromdate = $date;
                                $todate = $date;
                            
                                // Fetch data without filtering
                                $stmt = $conn->prepare("SELECT * FROM users ORDER BY `id` DESC");
                                $stmt->execute();
                            }
                            
                            if(!empty($_POST['search'])){
            
                            $search = $_POST['search']; 
                            $stmt = $conn->prepare("select * from users WHERE username LIKE '{$search}%' OR mobile_no LIKE '{$search}%' OR email_id LIKE '{$search}%' 
                            OR owner_name LIKE '{$search}%'
                            OR address LIKE '{$search}%'
                            OR status LIKE '{$search}%'
                            OR uid_no LIKE '{$search}%'
                            OR pan_no LIKE '{$search}%'
                             ORDER BY `id` DESC");
                            $stmt->execute();
                            }
                            $sl=1;
                            while($row=$stmt->fetch()) {
                            if($row['status'] === 'approved'){
                                $status = '<span class="badge bg-label-success">' . $row['status'] . '</span>';
                            } else if($row['status'] === 'unapproved'){
                                $status = '<span class="badge bg-label-secondary">' . $row['status'] . '</span>';
                            } else if($row['status'] === 'suspended'){
                                $status = '<span class="badge bg-label-danger">' . $row['status'] . '</span>';
                            }
        		            // Display the table row
                                echo "<tr>
                                    <td style='display:none'>{$sl}</td>
                                    <td>{$row['username']}<br>{$row['owner_name']}</td>
                                    <td>{$row['mobile_no']}<br>{$row['email_id']}</td>
                                    <td>₹  " . number_format($row['balance'], 2) . "</td>
                                    <td>{$status}<br>Reg: " . date('d M Y', strtotime($row['date_time'])) . "</td>
                                    <td class='text-end'>
                                            <button type='button' data-id=" . base64_encode($row['id']) . " class='btn btn-sm btn-icon btn-outline-primary' data-bs-toggle='modal' data-bs-target='#editUser'> <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' style='fill: ;transform: ;msFilter:;'><path d='m18.988 2.012 3 3L19.701 7.3l-3-3zM8 16h3l7.287-7.287-3-3L8 13z'></path><path d='M19 19H8.158c-.026 0-.053.01-.079.01-.033 0-.066-.009-.1-.01H5V5h6.847l2-2H5c-1.103 0-2 .896-2 2v14c0 1.104.897 2 2 2h14a2 2 0 0 0 2-2v-8.668l-2 2V19z'></path></svg> </button>

                                            <a href='javascript:void(0);' onclick='resetPass(".$row['id'].");' class='btn btn-sm btn-icon btn-outline-primary'><svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' style='fill: ;transform: ;msFilter:;'><path d='M18 10H9V7c0-1.654 1.346-3 3-3s3 1.346 3 3h2c0-2.757-2.243-5-5-5S7 4.243 7 7v3H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8a2 2 0 0 0-2-2zm-7.939 5.499A2.002 2.002 0 0 1 14 16a1.99 1.99 0 0 1-1 1.723V20h-2v-2.277a1.992 1.992 0 0 1-.939-2.224z'></path></svg></a>
                                            
                                            <a href='javascript:void(0);' onclick='deleteUser(".$row['id'].");' class='btn btn-sm btn-icon btn-outline-primary'><svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' style='fill: ;transform: ;msFilter:;'><path d='M16 2H8C4.691 2 2 4.691 2 8v13a1 1 0 0 0 1 1h13c3.309 0 6-2.691 6-6V8c0-3.309-2.691-6-6-6zm4 14c0 2.206-1.794 4-4 4H4V8c0-2.206 1.794-4 4-4h8c2.206 0 4 1.794 4 4v8z'></path><path d='M8 11h8v2H8z'></path></svg></a>
                                            <button type='button' data-id=" . base64_encode($row['id']) . " class='btn btn-sm btn-icon btn-outline-primary' data-bs-toggle='modal' data-bs-target='#deleteUser'> <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' style='fill: rgba(1, 11, 129, 1);transform: ;msFilter:;'><path d='M20.995 6.9a.998.998 0 0 0-.548-.795l-8-4a1 1 0 0 0-.895 0l-8 4a1.002 1.002 0 0 0-.547.795c-.011.107-.961 10.767 8.589 15.014a.987.987 0 0 0 .812 0c9.55-4.247 8.6-14.906 8.589-15.014zM12 19.897C5.231 16.625 4.911 9.642 4.966 7.635L12 4.118l7.029 3.515c.037 1.989-.328 9.018-7.029 12.264z'></path><path d='m11 12.586-2.293-2.293-1.414 1.414L11 15.414l5.707-5.707-1.414-1.414z'></path></svg> </button>
                                            
                                    </td>

                                </tr>";
                                $sl++;
                            }
                            
                            
                            ?>
               </tbody>
            </table>
         </div>
      </div>
   </div>


</div>
<!-- / Content -->
<!-- Edit User Modal -->
<div class="modal fade" id="editUser" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple modal-edit-user">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3>Edit User Information User: <span id="m_status"></span></h3>
          <p>Updating user details will receive a privacy audit.</p>
        </div>
        <form id="" class="row g-3" action="" method="POST">
            
          <!-- User Information -->
          
          <div class="col-12 col-md-6">
            <label class="form-label" for="name">Name</label>
            <input type="hidden" id="id" name="id" class="form-control" placeholder="" />
            <input type="text" id="name" name="owner_name" class="form-control" placeholder="" />
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label" for="shop_name">Shop Name</label>
            <input type="text" id="shop_name" name="shop_name" class="form-control" placeholder="" />
          </div>
          <div class="col-12">
            <label class="form-label" for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" placeholder="" />
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label" for="email_id">Email</label>
            <input type="text" id="email_id" name="email_id" class="form-control" placeholder="example@domain.com" />
          </div>
          <div class="col-12 col-md-3">
            <label class="form-label" for="mobile_no">Mobile</label>
            <input type="text" id="mobile_no" name="mobile_no" class="form-control" placeholder="999999999" />
          </div>
          <div class="col-12 col-md-3">
            <label class="form-label" for="status">Status</label>
            <select name="status" class="form-select" aria-label="Default select example">
                <option id="status"></option>
                <option value="approved">Approved</option>
                <option value="unapproved">Unapproved</option>
                <option value="suspended">Suspended</option>
            </select>
          </div>
          <div class="col-12 col-md-12">
            <label class="form-label" for="address">Address</label>
            <textarea id="address" name="address" class="form-control" placeholder="Enter Your Full Address"></textarea>
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label" for="otp">OTP Status</label>
            <select name="otp" class="form-select">
              <option id="otp">Select</option>
              <option value="yes">Enable</option>
              <option value="no">Disable</option>
            </select>
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label" for="pan_no">Pan Number</label>
            <input type="text" id="pan_no" name="pan_no" class="form-control" placeholder="" />
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label" for="uid_no">Aadhaar Number</label>
            <input type="text" id="uid_no" name="uid_no" class="form-control" placeholder="" />
          </div>
          
          <!-- Charges -->
          
          <li class="small text-uppercase" style="color:black;">
				<span class="menu-header-text"><b>PRINT Charges Managenent</b></span>
		  </li>
          <div class="col-12 col-md-2">
            <label class="form-label" for="service_pricing_aadhaar">Aadhaar</label>
            <input type="text" id="service_pricing_aadhaar" name="service_pricing_aadhaar" class="form-control" placeholder="" />
          </div>
          <div class="col-12 col-md-2">
            <label class="form-label" for="service_pricing_voter">Voter</label>
            <input type="text" id="service_pricing_voter" name="service_pricing_voter" class="form-control" placeholder="" />
          </div>
          <div class="col-12 col-md-2">
            <label class="form-label" for="service_pricing_driving_licence">Licence</label>
            <input type="text" id="service_pricing_driving_licence" name="service_pricing_driving_licence" class="form-control" placeholder="" />
          </div>
          <div class="col-12 col-md-2">
            <label class="form-label" for="service_pricing_ayushman">Ayoushman</label>
            <input type="text" id="service_pricing_ayushman" name="service_pricing_ayushman" class="form-control" placeholder="" />
          </div>
          <div class="col-12 col-md-2">
            <label class="form-label" for="service_pricing_panFind">Pan Find</label>
            <input type="text" id="service_pricing_panFind" name="service_pricing_panFind" class="form-control" placeholder="" />
          </div>
          <div class="col-12 col-md-2">
            <label class="form-label" for="service_pricing_vmLink">V Mobile Link</label>
            <input type="text" id="service_pricing_vmLink" name="service_pricing_vmLink" class="form-control" placeholder="" />
          </div>
          <div class="col-12 col-md-2">
            <label class="form-label" for="service_pricing_rashan">Rashan</label>
            <input type="text" id="service_pricing_rashan" name="service_pricing_rashan" class="form-control" placeholder="" />
          </div>
          <div class="col-12 col-md-3">
            <label class="form-label" for="e_nsdl">Vehicle RC</label>
            <input type="text" id="service_pricing_rc" name="service_pricing_rc" class="form-control" placeholder="" />
          </div>
          <div class="col-12 col-md-3">
            <label class="form-label" for="p_nsdl">Physical Pan</label>
            <input type="text" id="p_nsdl" name="p_nsdl" class="form-control" placeholder="" />
          </div>
          <div class="col-12 col-md-3">
            <label class="form-label" for="p_nsdl">Instant Pan</label>
            <input type="text" id="service_pricing_ekycpan" name="service_pricing_ekycpan" class="form-control" placeholder="" />
          </div>
          
          <!-- Mobile Recharge Commissions -->
          
          <li class="small text-uppercase" style="color:black;">
				<span class="menu-header-text"><b>Mobile Recharge Commissions</b></span>
		  </li>
		  <div class="col-12 col-md-2">
            <label class="form-label" for="airtel">Airtel</label>
            <input type="text" id="airtel" name="airtel" class="form-control" placeholder="" />
          </div>
          <div class="col-12 col-md-2">
            <label class="form-label" for="idea">Idea/Voda</label>
            <input type="text" id="idea" name="idea" class="form-control" placeholder="" />
          </div>
          <div class="col-12 col-md-2">
            <label class="form-label" for="jio">Jio</label>
            <input type="text" id="jio" name="jio" class="form-control" placeholder="" />
          </div>
          <div class="col-12 col-md-2">
            <label class="form-label" for="bsnl_topup">BSNL_T</label>
            <input type="text" id="bsnl_topup" name="bsnl_topup" class="form-control" placeholder="" />
          </div>
          <div class="col-12 col-md-2">
            <label class="form-label" for="bsnl_special">BSNL_S</label>
            <input type="text" id="bsnl_special" name="bsnl_special" class="form-control" placeholder="" />
          </div>
          
          <!-- DTH Recharge Commissions -->
          
          <li class="small text-uppercase" style="color:black;">
				<span class="menu-header-text"><b>DTH Recharge Commissions</b></span>
		  </li>
		  <div class="col-12 col-md-2">
            <label class="form-label" for="airtel_dth">Airtel</label>
            <input type="text" id="airtel_dth" name="airtel_dth" class="form-control" placeholder="" />
          </div>
          <div class="col-12 col-md-2">
            <label class="form-label" for="dish_dth">Dish TV</label>
            <input type="text" id="dish_dth" name="dish_dth" class="form-control" placeholder="" />
          </div>
          <div class="col-12 col-md-2">
            <label class="form-label" for="tatasky_dth">Tatasky</label>
            <input type="text" id="tatasky_dth" name="tatasky_dth" class="form-control" placeholder="" />
          </div>
          <div class="col-12 col-md-2">
            <label class="form-label" for="videocon_dth">Videocon</label>
            <input type="text" id="videocon_dth" name="videocon_dth" class="form-control" placeholder="" />
          </div>
          <div class="col-12 col-md-2">
            <label class="form-label" for="sun_dth">Sun TV</label>
            <input type="text" id="sun_dth" name="sun_dth" class="form-control" placeholder="" />
          </div>
          <div class="col-6">
            <label class="form-label" for="pay_load_min">Minimum Wallet Load</label>
            <input type="text" id="pay_load_min" name="pay_load_min" class="form-control" placeholder="" />
          </div>
          <div class="col-12 text-center">
            <button type="submit" name="update_user" class="btn btn-primary me-sm-3 me-1">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--/ Edit User Modal -->

<?php
require_once('../layouts/mainFooter.php');
?>
<script>
    $(document).ready(function() {
        $('button[data-bs-target="#editUser"]').on('click', function() {
            var access_token = $(this).data('id');

            // AJAX request to fetch user data
            $.ajax({
                url: '../system/getData.php',
                method: 'GET',
                data: { access_token: access_token },
                success: function(response) {
                    // Parse the JSON response
                    var userData = JSON.parse(response);

                    // Now you can use these variables to populate the modal's content
                    $('#id').val(userData.id);
                    $('#name').val(userData.owner_name);
                    $('#shop_name').val(userData.shop_name);
                    $('#mobile_no').val(userData.mobile_no);
                    $('#email_id').val(userData.email_id);
                    $('#pan_no').val(userData.pan_no);
                    $('#uid_no').val(userData.uid_no);
                    $('#address').val(userData.address);
                    $('#pay_load_min').val(userData.pay_load_min);
                    $('#username').val(userData.username);
                    
                    // Print service pricing
                    
                    $('#service_pricing_aadhaar').val(userData.service_pricing_aadhaar);
                    $('#service_pricing_panFind').val(userData.service_pricing_panFind);
                    $('#service_pricing_voter').val(userData.service_pricing_voter);
                    $('#service_pricing_rashan').val(userData.service_pricing_rashan);
                    $('#service_pricing_vmLink').val(userData.service_pricing_vmLink);
                    $('#service_pricing_driving_licence').val(userData.service_pricing_driving_licence);
                    $('#service_pricing_ayushman').val(userData.service_pricing_ayushman);
                    $('#service_pricing_rc').val(userData.service_pricing_rc);
                    $('#service_pricing_ekycpan').val(userData.service_pricing_ekycpan);
                    $('#e_nsdl').val(userData.e_nsdl);
                    $('#p_nsdl').val(userData.p_nsdl);
                    
                    
                    $('#airtel').val(userData.airtel);
                    $('#idea').val(userData.idea);
                    $('#jio').val(userData.jio);
                    $('#bsnl_topup').val(userData.bsnl_topup);
                    $('#bsnl_special').val(userData.bsnl_special);
                    
                    $('#airtel_dth').val(userData.airtel_dth);
                    $('#dish_dth').val(userData.dish_dth);
                    $('#tatasky_dth').val(userData.tatasky_dth);
                    $('#videocon_dth').val(userData.videocon_dth);
                    $('#sun_dth').val(userData.sun_dth);
                    $('#google_play').val(userData.google_play);
                    $('#pay_load_min').val(userData.pay_load_min);
                    
                    
                    var status = userData.status;
                    var capitalizedStatus = status.charAt(0).toUpperCase() + status.slice(1);
                    $('#status').html(capitalizedStatus);
                    $('#m_status').html(capitalizedStatus);
                    var otp = userData.otp;
                    if(otp === 'yes'){
                        capiOTP = 'Enabled';
                    }else{
                        capiOTP = 'Disabled';
                    }
                    $('#otp').html(capiOTP);

                    // Populate other fields as needed
                },
                error: function(error) {
                    console.error('Error fetching user data: ', error);
                }
            });
        });
    });
</script>
<script>
function deleteUser(id) {
    // Show SweetAlert deleteUser
    swal.fire({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this record!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Delete",
        cancelButtonText: "Cancel",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete.isConfirmed) {
            // If user clicks 'Delete', redirect to the delete URL
            window.location.href = 'userList?delete=&token=' + id;
        } else {
            // If the user clicks 'Cancel', show a confirmation message
            swal.fire({
                title: "User Delete Canceled",
                text: "The user has not been deleted.",
                icon: "info"
            });
        }
    });
}
function resetPass(id) {
    // Show SweetAlert resetPass
    swal.fire({
        title: "Reset Password Confirmation",
        text: "Are you sure you want to reset the password for this user? This action cannot be undone.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Reset Password",
        cancelButtonText: "Cancel"
    })
    .then((result) => {
        if (result.isConfirmed) {
            // If the user clicks 'Reset Password', redirect to the reset password URL
            window.location.href = 'userList?resetpass=&token=' + id;
        } else {
            // If the user clicks 'Cancel', show a confirmation message
            swal.fire("Password Reset Canceled", "The password has not been reset.", "info");
        }
    });
}
</script>