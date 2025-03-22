<?php
$pageName = "Wallet Records"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');
?>
<div class="content-wrapper">
   <!-- Content -->
   <div class="container-xxl flex-grow-1 container-p-y">
      <!-- DataTales Example -->
      <div class="card shadow-lg sprint-box">
         <div class="card-header flex-column flex-md-row">
            <h4 style="color:black;"><b>Wallet Load Records</b></h4>
            <form method="post" action="">
               <div class="row">
                  <div class="col-3">
                     <div class="form-group">
                        <label class="form-label" style="color: black;">Filter</label>
                        <input type="text" class="form-control flatpickr-input" name="daterange" placeholder="<?php echo $date; ?> to YYYY-MM-DD" id="flatpickr-range" readonly="readonly">
                     </div>
                  </div>
                  <div class="col-7">
                     <div class="form-group">
                        <label class="form-label" style="color: black;">Search</label>
                        <input type="search" autocomplete="off" name="search" class="form-control border-danger" placeholder="Search By:- Reference Number / Mode / Remark" autofocus />
                     </div>
                  </div>
                  <div class="col-2">
                     <div class="dt-action-buttons text-start pt-md-0">
                        <label class="form-label" style="color: black;"> </label>
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
                        <th style="color: #fff;">ID / REF</th>
                        <th style="color: #fff;">Date</th>
                        <th style="color: #fff;">Mode</th>
                        <th style="color: #fff;">Type</th>
                        <th style="color: #fff;">Amount</th>
                        <th style="color: #fff;">Balance</th>
                        <th style="color: #fff;">Remark</th>
                        <th style="color: #fff;">Status</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php
                            $conn = connectDB();
                            
                            $stmt = $conn->prepare("select * from transactions WHERE userId = ? AND mode = 'QR Payment' ORDER BY `id` DESC");
                            $stmt->execute([getUsersInfo('id')]);
                            
                            
                            if (isset($_POST['daterange'])) {
                                $dateRange = $_POST['daterange'];
                                list($fromdate, $todate) = explode(' to ', $dateRange);
                            
                                $stmt = $conn->prepare("SELECT * FROM transactions WHERE date_time BETWEEN :fromdate AND :todate AND userId = :userId AND mode = 'QR Payment' ORDER BY `id` DESC");
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
                                $stmt = $conn->prepare("SELECT * FROM transactions WHERE userId = :userId AND mode = 'QR Payment' ORDER BY `id` DESC");
                                $stmt->execute([
                                    'userId' => getUsersInfo('id')
                                ]);
                            }
                            
                            $stmt = $conn->prepare("select * from transactions WHERE date_time between '".$fromdate."' AND '".$todate."' AND userId=? AND mode = 'QR Payment' ORDER BY `id` DESC");
                            $stmt->execute([getUsersInfo('id')]);
            
                            if(!empty($_POST['search'])){
            
                            $search = $_POST['search']; 
                            $stmt = $conn->prepare("select * from transactions WHERE reference LIKE '{$search}%' OR mode LIKE '{$search}%' OR remark LIKE '{$search}%' 
                             AND userId=? AND mode = 'QR Payment' ORDER BY `id` DESC");
                            $stmt->execute([getUsersInfo('id')]);
                            }
                            $sl=1;
                            while($row=$stmt->fetch()) {
                                
                            
                            
                            $usql = $conn->prepare("select * from users WHERE id = ?");
                            $usql->execute([$row['userId']]);
                            $usr_d=$usql->fetch();
                            
                            if ($row['type'] === "debit" || $row['type'] === "Debit") {
                                // If the 'refunded_sts' column is empty or contains a single space, set $refunded_sts to "N/A"
                                $crdr = "<span style='color:red;'>-" . number_format($row['amount'], 2) . "</span>";
                                $PSTP = '<span>debit</span>';
                            } else {
                                // If the 'refunded_sts' column has a value, assign that value to $refunded_sts
                                $crdr = "<span style='color:blue;'>+" . number_format($row['amount'], 2) . "</span>";
                                $PSTP = '<span>credit</span>';
                            }
                            $username = '<span>ID: '.$usr_d["username"].'</span>';
        		            // Display the table row
                                echo "<tr>
                                    <td style='display:none'>{$sl}</td>
                                    <td>TX{$row['reference']}<br>{$username}</td>
                                    <td>" . date('d M Y', strtotime($row['timestamp'])) . "</td>
                                    <td>{$row['mode']}</td>
                                    <td>{$PSTP}</td>
                                    <td>{$crdr}</td>
                                    <td>" . number_format($row['balance'], 2) . "</td>
                                    <td>{$row['remark']}</td>
                                    <td>{$row['status']}</td>

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
</div>

<?php
require_once('../layouts/mainFooter.php');
?>