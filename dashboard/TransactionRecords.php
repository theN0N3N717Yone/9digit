<?php
// ini_set('display_errors', 1);  error_reporting(E_ALL);
$pageName = "Transaction Record"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');
?>
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- DataTales Example -->
        <div class="card shadow-lg">
            <div class="card-header flex-column flex-md-row">
                <form method="post" action="">
                    <div class="row">
                        <div class="col-2">
                            <div class="form-group">
                                <label class="form-label" style="color: black;">Filter</label>
                                <input type="text" class="form-control form-control-sm flatpickr-input" name="daterange" placeholder="<?php echo $date; ?> to YYYY-MM-DD" id="flatpickr-range" readonly="readonly">
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                <label class="form-label" style="color: black;">By Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">Select Status</option>
                                    <option value="success">Completed</option>
                                    <option value="rejected">Failed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label" style="color: black;">Search</label>
                                <input type="search" autocomplete="off" name="search" class="form-control form-control-sm border-danger"
                                    placeholder="Search By:- Reference Number / Mode / Remark"
                                    autofocus />
                            </div>
                        </div>
                        <div class="col-1">
                            <div class="form-group">
                                <label class="form-label"><i class="bx bx-filter"></i></label>
                                <input type="submit" class="btn btn-sm btn-primary active" name="Filter" value="Filter">
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
                                <?php if(getUsersInfo('usertype') === "mainadmin"){?>
                                <th style="color: #fff;">Users</th>
                                <?php } ?>
                                <th style="color: #fff;">Order</th>
                                <th style="color: #fff;">Amount</th>
                                <th style="color: #fff;">Balance</th>
                                <th style="color: #fff;">Remark</th>
                                <th style="color: #fff;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if(getUsersInfo('usertype') !== "mainadmin"){
                            $conn = connectDB();
                            
                            $stmt = $conn->prepare("select * from transactions WHERE userId = ? AND mode != 'QR Payment' ORDER BY `id` DESC");
                            $stmt->execute([getUsersInfo('id')]);
                            
                            
                            if (isset($_POST['daterange'])) {
                                $dateRange = $_POST['daterange'];
                                list($fromdate, $todate) = explode(' to ', $dateRange);
                            
                                $stmt = $conn->prepare("SELECT * FROM transactions WHERE date_time BETWEEN :fromdate AND :todate AND userId = :userId AND mode != 'QR Payment' ORDER BY `id` DESC");
                                $stmt->execute([
                                    'fromdate' => $fromdate,
                                    'todate' => $todate,
                                    'userId' => getUsersInfo('id')
                                ]);
                            } else {
                                // Default values if the form is not submitted
                                $fromdate = $date;
                                $todate = $date;
                            
                                // Fetch data without filtering
                                $stmt = $conn->prepare("SELECT * FROM transactions WHERE userId = :userId AND mode != 'QR Payment' ORDER BY `id` DESC");
                                $stmt->execute([
                                    'userId' => getUsersInfo('id')
                                ]);
                            }
                            
                            $stmt = $conn->prepare("select * from transactions WHERE date_time between '".$fromdate."' AND '".$todate."' AND userId=? AND mode != 'QR Payment' ORDER BY `id` DESC");
                            $stmt->execute([getUsersInfo('id')]);
            
                            if(!empty($_POST['search'])){
            
                            $search = $_POST['search']; 
                            $stmt = $conn->prepare("select * from transactions WHERE reference LIKE '{$search}%' OR mode LIKE '{$search}%' OR remark LIKE '{$search}%' 
                             AND userId=? AND mode != 'QR Payment' ORDER BY `id` DESC");
                            $stmt->execute([getUsersInfo('id')]);
                            }
                            $sl=1;
                            while($row=$stmt->fetch()) {
                                
                            
                            
                            $usql = $conn->prepare("select * from users WHERE id = ?");
                            $usql->execute([$row['userId']]);
                            $usr_d=$usql->fetch();
                            
                            if ($row['type'] == 'Correction pan') {
                                $pan_type = 'CSF';
                            } else {
                                $pan_type = "NEW";
                            }
                            if ($row['status'] && preg_match('/success/i', $row['status'])) {
                                $status = '<span class="badge bg-primary active">Success</span>';
                            }
                            if ($row['status'] && preg_match('/rejected/i', $row['status'])) {
                                $status = '<span class="badge bg-danger">Rejected</span>';
                            }
                            if ($row['type'] === "debit" || $row['type'] === "Debit") {
                                // If the 'refunded_sts' column is empty or contains a single space, set $refunded_sts to "N/A"
                                $crdr = "<span style='color:red;'>-" . number_format($row['amount'], 2) . "</span>";
                                $PSTP = '<span>Debit</span>';
                            } else {
                                // If the 'refunded_sts' column has a value, assign that value to $refunded_sts
                                $crdr = "<span style='color:blue;'>+" . number_format($row['amount'], 2) . "</span>";
                                $PSTP = '<span>Credit</span>';
                            }
                            $username = '<span>ID: '.$usr_d["username"].'</span>';
        		            // Display the table row
                                echo "<tr>
                                    <td style='display:none'>{$sl}</td>
                                    <td class='w-25 small'>
                                    <a class='text-primary' type='button'>{$row['reference']}</a>
                                    
                                    <br>" . date('d M Y | H:i:s A', strtotime($row['timestamp'])) . "</td>
                                    <td>{$crdr}</td>
                                    <td>" . number_format($row['balance'], 2) . "</td>
                                    <td class='w-50 small'>{$row["remark"]}</td>
                                    <td>{$row["status"]}</td>

                                </tr>";
                                $sl++;
                            }
                            } else {
                            
                            $conn = connectDB();
                            
                            $stmt = $conn->prepare("select * from transactions WHERE mode != 'QR Payment' ORDER BY `id` DESC");
                            $stmt->execute();
                            
                            
                            if (isset($_POST['daterange'])) {
                                $dateRange = $_POST['daterange'];
                                list($fromdate, $todate) = explode(' to ', $dateRange);
                            
                                $stmt = $conn->prepare("SELECT * FROM transactions WHERE date_time BETWEEN :fromdate AND :todate AND mode != 'QR Payment' ORDER BY `id` DESC");
                                $stmt->execute([
                                    'fromdate' => $fromdate,
                                    'todate' => $todate
                                ]);
                            } else {
                                // Default values if the form is not submitted
                                $fromdate = $date;
                                $todate = $date;
                            
                                // Fetch data without filtering
                                $stmt = $conn->prepare("SELECT * FROM transactions WHERE mode != 'QR Payment' ORDER BY `id` DESC");
                                $stmt->execute();
                            }
                            
                            $stmt = $conn->prepare("select * from transactions WHERE date_time between '".$fromdate."' AND '".$todate."' AND mode != 'QR Payment' ORDER BY `id` DESC");
                            $stmt->execute();
            
                            if(!empty($_POST['search'])){
            
                            $search = $_POST['search']; 
                            $stmt = $conn->prepare("select * from transactions WHERE reference LIKE '{$search}%' OR mode LIKE '{$search}%' OR remark LIKE '{$search}%' 
                             AND mode != 'QR Payment' ORDER BY `id` DESC");
                            $stmt->execute();
                            }
                            $sl=1;
                            while($row=$stmt->fetch()) {
                                

                            $usql = $conn->prepare("select * from users WHERE id = ?");
                            $usql->execute([$row['userId']]);
                            $usr_d=$usql->fetch();
                            
                            if ($row['type'] == 'Correction pan') {
                                $pan_type = 'CSF';
                            } else {
                                $pan_type = "NEW";
                            }
                            if ($row['status'] && preg_match('/success/i', $row['status'])) {
                                $status = '<span class="badge bg-primary active">Success</span>';
                            }
                            if ($row['status'] && preg_match('/rejected/i', $row['status'])) {
                                $status = '<span class="badge bg-danger">Rejected</span>';
                            }
                            if ($row['type'] === "debit" || $row['type'] === "Debit") {
                                // If the 'refunded_sts' column is empty or contains a single space, set $refunded_sts to "N/A"
                                $crdr = "<span style='color:red;'>-" . number_format($row['amount'], 2) . "</span>";
                                $PSTP = '<span>Debit</span>';
                            } else {
                                // If the 'refunded_sts' column has a value, assign that value to $refunded_sts
                                $crdr = "<span style='color:blue;'>+" . number_format($row['amount'], 2) . "</span>";
                                $PSTP = '<span>Credit</span>';
                            }
                            $username = '<span>AGENT ID: '.$usr_d["username"].'</span>';

        		            // Display the table row
                                echo "<tr>
                                    <td style='display:none'>{$sl}</td>
                                    <td class='w-25 small' style='color:black;'>{$username}
                                    <br>{$usr_d['owner_name']}
                                    <br>" . date('d M Y | H:i:s A', strtotime($row['timestamp'])) . "</td>
                                    <td>
                                    <a class='text-primary' type='button'>{$row['reference']}</a></td>
                                    <td>{$crdr}</td>
                                    <td>" . number_format($row['balance'], 2) . "</td>
                                    <td class='w-50 small'>{$row["remark"]}</td>
                                    <td>{$row["status"]}</td>

                                </tr>";
                                $sl++;
                            }
                            
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<style>

/* Adjust scrollbar width */
.offcanvas .offcanvas-body::-webkit-scrollbar {
    width: 3px; /* Decrease the width of the scrollbar */
}

/* Change scrollbar color */
.offcanvas .offcanvas-body::-webkit-scrollbar-track {
    background-color: #f1f1f1; /* Set the background color of the track */
}

.offcanvas .offcanvas-body::-webkit-scrollbar-thumb {
    background-color: #11029e; /* Set the color of the scrollbar thumb */
    border-radius: 0px; /* Optional: round the corners of the thumb */
}




</style>

<?php
require_once('../layouts/mainFooter.php');
?>