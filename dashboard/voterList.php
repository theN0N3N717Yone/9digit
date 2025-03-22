<?php
// ini_set('display_errors', 1);  error_reporting(E_ALL);
$pageName = "AADHAAR Record"; // Replace this with the actual page name
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
                        <div class="col-3">
                            <div class="form-group">
                                <label class="form-label" style="color: black;">Filter</label>
                                <input type="text" class="form-control flatpickr-input" name="daterange" placeholder="<?php echo $date; ?> to YYYY-MM-DD" id="flatpickr-range" readonly="readonly">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label" style="color: black;">Search</label>
                                <input type="search" autocomplete="off" name="search" class="form-control border-danger"
                                    placeholder="Search By:- Epic Number / Name / Request ID"
                                    autofocus />
                            </div>
                        </div>
                        <div class="col-1">
                            <div class="form-group">
                                <label class="form-label"><i class="bx bx-filter"></i></label>
                                <input type="submit" class="btn btn-primary active" name="Filter" value="Filter">
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
                              <th style="color: #fff;">#</th>
                              <th style="color: #fff;">Customer Name</th>
                              <th style="color: #fff;">Id Number</th>
                              <th style="color: #fff;">Date_Time</th>
                              <th style="color: #fff;">Id_Type</th>
                              <th style="color: #fff;">Photo</th>
                              <th style="color: #fff;" class="cell-fit">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $conn = connectDB();
                            
                            $stmt = $conn->prepare("select * from printRecords WHERE userId = ? AND print_type ='Voter Card' ORDER BY `id` DESC");
                            $stmt->execute([getUsersInfo('id')]);
                            
                            
                            if (isset($_POST['daterange'])) {
                                $dateRange = $_POST['daterange'];
                                list($fromdate, $todate) = explode(' to ', $dateRange);
                            
                                $stmt = $conn->prepare("SELECT * FROM printRecords WHERE date BETWEEN :fromdate AND :todate AND userId = :userId AND print_type ='Voter Card' ORDER BY `id` DESC");
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
                                $stmt = $conn->prepare("SELECT * FROM printRecords WHERE userId = :userId AND print_type ='Voter Card' ORDER BY `id` DESC");
                                $stmt->execute([
                                    'userId' => getUsersInfo('id')
                                ]);
                            }
                            
                            $stmt = $conn->prepare("select * from printRecords WHERE date between '".$fromdate."' AND '".$todate."' AND userId=? AND print_type ='Voter Card' ORDER BY `id` DESC");
                            $stmt->execute([getUsersInfo('id')]);
            
                            if(!empty($_POST['search'])){
            
                            $search = $_POST['search']; 
                            $stmt = $conn->prepare("select * from printRecords WHERE idNumber LIKE '{$search}%' OR print_type LIKE '{$search}%' OR name LIKE '{$search}%' 
                             AND userId=? AND print_type ='Voter Card' ORDER BY `id` DESC");
                            $stmt->execute([getUsersInfo('id')]);
                            }
                            $sl=1;
                            while($row=$stmt->fetch()) {
                            $printdata = '<div class="dropdown-menu dropdown-menu-end">
                                    <a href=\'../printManagement/Voter?token=' . base64_encode($row['id']) . '&access_token=' . base64_encode($row['idNumber']) . '\'
                                    class="dropdown-item" target="_blanck">Old Voter Card</a>
                                    <a href=\'../printManagement/Voter_Add?token=' . base64_encode($row['id']) . '&access_token=' . base64_encode($row['idNumber']) . '\'
                                    class="dropdown-item" target="_blanck">New Voter Card</a>
                                  </div>';    
                            $printbtn = '<div class="d-flex align-items-center">
                                              <div class="dropdown"><a href="javascript:;" class="btn dropdown-toggle hide-arrow text-body p-0" data-bs-toggle="dropdown"><i class="bx bx-printer"></i></a>
                                                ' . $printdata . '
                                              </div>
                                            </div>';
                            if (!empty($row['photo'])) {
                                $photo = "<div class='user-profile-img'><img src='{$row['photo']}' class='rounded-circle' width='35' height='35'></div>";
                            } else {
                                $photo = "NULL";
                            }
                                
                            $usql = $conn->prepare("select * from users WHERE id = ?");
                            $usql->execute([$row['userId']]);
                            $usr_d=$usql->fetch();
                            
        		            // Display the table row
                                echo "<tr>
                                    <td>1</td>
                                      <td>" . strtoupper($row['name']) . "</td>
                                      <td><span class='badge bg-label-danger'>" . $row['idNumber'] . "</span></td>
                                      <td>" . date("d M Y h:i A", strtotime($row['time'])) . "</td>
                                      <td>" . strtoupper($row['print_type']) . "</td>
                                      <td>" . $photo . "</td>
                                      <td>" . $printbtn . "</td>

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