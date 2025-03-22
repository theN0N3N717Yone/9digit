<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
$pageName = "Print Records";
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');
$message = "";
// Function to fetch records based on ID number or user ID
function getRecords($searchTerm)
{
    try {
        $conn = connectDB(); // Assuming $conn is your PDO database connection
        $userIds = getUsersInfo('id');
        // Check if $searchTerm exists and is not empty
        if (!empty($searchTerm)) {
            $query = "SELECT id, name, idNumber, time, print_type, photo FROM printRecords WHERE idNumber = :searchTerm AND print_type <> 'Get Pan Number' AND userId = :userid";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
            $stmt->bindParam(':userid', $userIds, PDO::PARAM_STR);
            
            $stmt->execute();
        if(!empty($stmt)){
            

        $uniqueRecords = array();
        while ($record = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $printType = $record['print_type'];

            // Check if print_type is not already in the array
            if (!isset($uniqueRecords[$printType])) {
                $uniqueRecords[$printType] = $record;
            }
        }

        return array_values($uniqueRecords); // Return array values to reset keys
        } else {
            // Handle the case when $searchTerm is empty
            $message = "Data not found Please provide a valid idNumber.";
        }
        } else {
            // Handle the case when $searchTerm is empty
            $message = "Search term is empty. Please provide a idNumber.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage(); // Output detailed error message for debugging
        die(); // Stop script execution
    }
}

?>

<!-- Your HTML and other PHP code -->
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6 col-lg-12 d-flex align-items-stretch">
            <div class="w-100">
                <div class="card-body">
                    <!-- Filter Form -->
                    <form id="filterForm" method="GET">
                        <div class="row end">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Enter Your ID Number:</label>
                                    <input class="form-control border border-danger" id="search" name="search" placeholder="Search By: ID Number">
                                </div>
                            </div>
                            <div class="col-md-2" style="margin-top: 29px">
                                <!-- Added text-center class to center form elements -->
                                <div class="mb-3">
                                    <button class="btn btn-primary" type="submit">Apply Filter</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <?php
                    // Fetch records when the form is submitted
                    if (isset($_GET['search'])) {
                        $searchTerm = $_GET['search'];
                        $records = getRecords($searchTerm);

                        // Check if any records are found
                        if (count($records) > 0) {
                            // Display the table head
                            echo '<div class="col-md-6 col-lg-12 mb-0">
                                    <div class="card">
                                      <div class="card-datatable table-responsive">
                                        <table class="invoice-list-table table">
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
                                          <tbody class="table-border-bottom-0">';

                            // Display the fetched records in a table
                            foreach ($records as $record) {
                                switch ($record['print_type']) {
                            case 'Aadhaar Card':
                                $printdata = '<div class="dropdown-menu dropdown-menu-end">
                                    <a href=\'../printManagement/aadhaar?token=' . base64_encode($record['id']) . '&access_token=' . base64_encode($record['idNumber']) . '\'
                                    class="dropdown-item" target="_blanck">Old Aadhaar Card</a>
                                    <a href=\'../printManagement/aadhaar-pvc?token=' . base64_encode($record['id']) . '&access_token=' . base64_encode($record['idNumber']) . '\'
                                    class="dropdown-item" target="_blanck">PVC Aadhaar Card</a>
                                  </div>';
                                break;    
                                
                            case 'Voter Card':
                                $printdata = '<div class="dropdown-menu dropdown-menu-end">
                                    <a href=\'../printManagement/Voter?token=' . base64_encode($record['id']) . '&access_token=' . base64_encode($record['idNumber']) . '\'
                                    class="dropdown-item" target="_blanck">Old Voter Card</a>
                                    <a href=\'../printManagement/Voter_Add?token=' . base64_encode($record['id']) . '&access_token=' . base64_encode($record['idNumber']) . '\'
                                    class="dropdown-item" target="_blanck">New Voter Card</a>
                                  </div>';
                                break;
                                
                            case 'Manual Pan Generate':
                                $printdata = '<div class="dropdown-menu dropdown-menu-end">
                                    <a href=\'../printManagement/utiPAN?token=' . base64_encode($record['id']) . '&access_token=' . base64_encode($record['idNumber']) . '\'
                                    class="dropdown-item" target="_blanck">UTI Print</a>
                                    <a href=\'../printManagement/nsdlPAN?token=' . base64_encode($record['id']) . '&access_token=' . base64_encode($record['idNumber']) . '\'
                                    class="dropdown-item" target="_blanck">NSDL Print</a>
                                  </div>';
                                break;
                                
                            case 'Rashan Card Print':
                                $printdata = "<div class='dropdown-menu dropdown-menu-end'>
                                    <a href='../printManagement/rashan?token=" . base64_encode($record['id']) . "&access_token=" . base64_encode($record['idNumber']) . "'
                                       class='dropdown-item' target='_blanck'><i class='bx bx-printer me-1'></i> Rashan Card</a>
                                  </div>";
                                break;
                        
                            case 'Ayushman Print':
                                $printdata = "<div class='dropdown-menu dropdown-menu-end'>
                                    <a href='../printManagement/ayushman?token=" . base64_encode($record['id']) . "&access_token=" . base64_encode($record['idNumber']) . "' target='_blank' class='dropdown-item' target='_blanck'><i class='bx bx-printer me-1'></i> Ayushman</a>
                                  </div>";
                                break;
                        
                            case 'Driving Licence':
                                $printdata = "<div class='dropdown-menu dropdown-menu-end'>
                                    <a href='../printManagement/licenceHd?token=" . base64_encode($record['id']) . "&access_token=" . base64_encode($record['idNumber']) . "' target='_blank' class='dropdown-item' target='_blanck'><i class='bx bx-printer me-1'></i> Licence</a>
                                  </div>";
                                break;
                                
                            case 'Vehicle RC':
                                $printdata = "<div class='dropdown-menu dropdown-menu-end'>
                                    <a href='../printManagement/rcAdvance?token=" . base64_encode($record['id']) . "&access_token=" . base64_encode($record['idNumber']) . "' target='_blank' class='dropdown-item' target='_blanck'><i class='bx bx-printer me-1'></i> Vehicle HD RC </a>
                                  </div>";
                                break;

                                
                            // Add cases for other card types as needed
                            default:
                                $printdata = '<button type="button" class="btn btn-sm btn-primary">Print Card</button>';
                            }     

                                if (!empty($record['photo'])) {
                                    $photo = "<div class='user-profile-img'><img src='{$record['photo']}' class='rounded-circle' width='35' height='35'></div>";
                                } else {
                                    $photo = "NULL";
                                }
                                $printbtn = '<div class="d-flex align-items-center">
                                              <div class="dropdown"><a href="javascript:;" class="btn dropdown-toggle hide-arrow text-body p-0" data-bs-toggle="dropdown"><i class="bx bx-printer"></i></a>
                                                ' . $printdata . '
                                              </div>
                                            </div>';
                                echo '
                                    <tr>
                                      <td>1</td>
                                      <td>' . strtoupper($record['name']) . '</td>
                                      <td><span class="badge bg-label-danger">' . $record['idNumber'] . '</span></td>
                                      <td>' . date("d M Y h:i A", strtotime($record['time'])) . '</td>
                                      <td>' . strtoupper($record['print_type']) . '</td>
                                      <td>' . $photo . '</td>
                                      <td>' . $printbtn . '</td>
                                    </tr>';
                            }

                            // Close the table
                            echo '</tbody>
                                  </table>
                                </div>
                              </div>
                            </div>';
                        } else {
                            // Display a message when no records are found
                            echo '<div class="col-md-6 col-lg-12 mb-0">
                                    <div class="">
                                      <div class="table-responsive">
                                        <table class="invoice-list-table table">
                                  <tbody class="table-border-bottom-0">
                                    <tr class="text-center">
                                      <td colspan="8">
                                      
                                      <img src="../assets/img/icons/sprinticon/notfound.svg" height="100" width="100"><br>
                                      <h4 style="color: red; margin-top: -10px"><b>DATA NOT FOUND PLEASE PROVIDE A VALID ID NUMBER.</b></h4>
                                      </td>
                                      
                                    </tr>
                                  </tbody>
                                  </table>
                                </div>
                              </div>
                            </div>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Include your JavaScript scripts here -->
<?php
require_once('../layouts/mainFooter.php');
?>
