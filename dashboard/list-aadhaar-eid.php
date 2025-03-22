<?php
// ini_set('display_errors', 1);  error_reporting(E_ALL);
$pageName = "EID TO AADHAAR Record"; // Replace this with the actual page name
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
                                    placeholder="Search By:- EID Number / UID Number"
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
                                <th style="display: none;">#</th>
                                <th style="color: #fff;">Date_Time</th>
                                <th style="color: #fff;">EID</th>
                                <th style="color: #fff;">AADHAAR</th>
                                <th style="color: #fff;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $conn = connectDB();
                            
                            $stmt = $conn->prepare("select * from printRecords WHERE userId = ? AND print_type ='Get UID Number' ORDER BY `id` DESC");
                            $stmt->execute([getUsersInfo('id')]);
                            
                            
                            if (isset($_POST['daterange'])) {
                                $dateRange = $_POST['daterange'];
                                list($fromdate, $todate) = explode(' to ', $dateRange);
                            
                                $stmt = $conn->prepare("SELECT * FROM printRecords WHERE date BETWEEN :fromdate AND :todate AND userId = :userId AND print_type ='Get UID Number' ORDER BY `id` DESC");
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
                                $stmt = $conn->prepare("SELECT * FROM printRecords WHERE userId = :userId AND print_type ='Get UID Number' ORDER BY `id` DESC");
                                $stmt->execute([
                                    'userId' => getUsersInfo('id')
                                ]);
                            }
                            
                            $stmt = $conn->prepare("select * from printRecords WHERE date between '".$fromdate."' AND '".$todate."' AND userId=? AND print_type ='Get UID Number' ORDER BY `id` DESC");
                            $stmt->execute([getUsersInfo('id')]);
            
                            if(!empty($_POST['search'])){
            
                            $search = $_POST['search']; 
                            $stmt = $conn->prepare("select * from printRecords WHERE idNumber LIKE '{$search}%' OR print_type LIKE '{$search}%' OR printData LIKE '{$search}%' 
                             AND userId=? AND print_type ='Get UID Number' ORDER BY `id` DESC");
                            $stmt->execute([getUsersInfo('id')]);
                            }
                            $sl=1;
                            while($row=$stmt->fetch()) {
                                
                            
                            
                            $usql = $conn->prepare("select * from users WHERE id = ?");
                            $usql->execute([$row['userId']]);
                            $usr_d=$usql->fetch();
                            
        		            // Display the table row
                                echo "<tr>
                                    <td style='display:none'>{$sl}</td>
                                    <td class='w-25'>" . date('d M Y | H:i:s A', strtotime($row['time'])) . "</td>
                                    <td id='idNumber'>{$row['idNumber']} <i class='bx bx-copy bx-10px'></i></td>
                                    <td id='printData'>{$row['printData']} <i class='bx bx-copy bx-10px'></i></td>
                                    <td><span class='badge  bg-label-dark'>Success</span></td>

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
<script>
document.querySelectorAll('td').forEach(td => {
  td.addEventListener('click', () => {
    const textToCopy = td.innerText;
    navigator.clipboard.writeText(textToCopy)
      .then(() => {
        alert('Copied to clipboard: ' + textToCopy);
      })
      .catch(err => {
        console.error('Failed to copy: ', err);
      });
  });
});
</script>
<?php
require_once('../layouts/mainFooter.php');
?>