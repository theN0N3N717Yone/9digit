<?php
$pageName = "Physical PAN Record";
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');


// Assuming you have a database connection established

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ackNo'], $_POST['panNumber'])) {
    $ackNo = $_POST['ackNo'];
    $pan = $_POST['panNumber'];
    $panNumber = "($pan)";
    // Update the database table with the remarks
    $sql = "UPDATE nsdlpancard SET remark = CONCAT('a new PAN is allotted by ITD.<br>', ?) WHERE ack_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$panNumber, $ackNo]);
    // Check if the update was successful
    if ($stmt->rowCount() > 0) {
        echo 'Remarks updated successfully.';
    } else {
        echo 'No rows updated.';
    }
}
?>

<!-- Your HTML and other PHP code -->
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
      <div class="col-12">
    <div class="card mb-4">
      <div class="card-widget-separator-wrapper">
        <div class="card-body card-widget-separator">
          <div class="row gy-4 gy-sm-1">
            <div class="col-sm-6 col-lg-3">
              <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                <div>
                  <h3 class="mb-1"><?php
                    $sql = $conn->prepare("SELECT COUNT(*) FROM nsdlpancard WHERE status = 'success' AND user_id = ? ");
                    $sql->execute([$userdata['id']]);
                    $count = $sql->fetchColumn();
                      echo $count;
                    ?></h3>
                  <p class="mb-0">Success Pan Card</p>
                </div>
                <span class="badge bg-label-secondary rounded p-2 me-sm-4">
                  <i class="bx bx-check bx-sm text-primary"></i>
                </span>
              </div>
              <hr class="d-none d-sm-block d-lg-none me-4">
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
                <div>
                  <h3 class="mb-1"><?php
                    $remark = "A new PAN is allotted by ITD.<br>(HWHPB1255A)";
                    $sql = $conn->prepare("SELECT COUNT(*) FROM nsdlpancard WHERE remark LIKE ? AND user_id = ?");
                    $sql->execute(["A new PAN is allotted by ITD.<br>(%)", $userdata['id']]);
                    $count = $sql->fetchColumn();
                    echo $count;
                    ?></h3>
                  <p class="mb-0">NSDL Allotted PAN</p>
                </div>
                <span class="badge bg-label-secondary rounded p-2 me-lg-4">
                  <i class="bx bx-check-double bx-sm text-primary active"></i>
                </span>
              </div>
              <hr class="d-none d-sm-block d-lg-none">
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="d-flex justify-content-between align-items-start border-end pb-3 pb-sm-0 card-widget-3">
                <div>
                  <h3 class="mb-1"><?php
                    $sql = $conn->prepare("SELECT COUNT(*) FROM nsdlpancard WHERE status = 'PROCESS' AND user_id = ? ");
                    $sql->execute([$userdata['id']]);
                    $count = $sql->fetchColumn();
                      echo $count;
                    ?></h3>
                  <p class="mb-0">Under Process</p>
                </div>
                <span class="badge bg-label-secondary rounded p-2 me-sm-4">
                  <i class="bx bx-time-five text-info active bx-sm"></i>
                </span>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <a href="PhysicalPan-Under-Observation" class="d-flex justify-content-between align-items-start">
                <div>
                  <h3 class="mb-1"><?php
                        $sql = $conn->prepare("SELECT COUNT(*) FROM nsdlpancard WHERE status = 'hold' AND user_id = ? ");
                        $sql->execute([$userdata['id']]);
                        $count = $sql->fetchColumn();
                        echo $count;
                        ?></h3>
                  <p class="mb-0">Under Observation</p>
                </div>
                <span class="badge bg-label-secondary rounded p-2">
                  <i class="bx bx-error-circle text-warning active bx-sm"></i>
                </span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-widget-separator-wrapper">
        <div class="card-body card-widget-separator">
          <div class="row gy-4 gy-sm-1">
            <div class="col-sm-6 col-lg-3">
              <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                <div>
                  <h3 class="mb-1"><?php
                    $sql = $conn->prepare("SELECT COUNT(*) FROM nsdlpancard WHERE status = 'rejected' AND user_id = ? ");
                    $sql->execute([$userdata['id']]);
                    $count = $sql->fetchColumn();
                      echo $count;
                    ?></h3>
                  <p class="mb-0">Recjected Pan Card</p>
                </div>
                <span class="badge bg-label-secondary rounded p-2 me-sm-4">
                  <i class="bx bx-x bx-sm text-danger"></i>
                </span>
              </div>
              <hr class="d-none d-sm-block d-lg-none me-4">
            </div>  
            <div class="col-sm-6 col-lg-3">
              <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                <div>
                  <h3 class="mb-1"><?php
                    $sql = $conn->prepare("SELECT COUNT(*) FROM nsdlpancard WHERE type = 'new pan' AND user_id = ? ");
                    $sql->execute([$userdata['id']]);
                    $count = $sql->fetchColumn();
                      echo $count;
                    ?></h3>
                  <p class="mb-0">New Pan Card</p>
                </div>
                <span class="badge bg-label-secondary rounded p-2 me-sm-4">
                  <i class="bx bx-check-double bx-sm text-primary active"></i>
                </span>
              </div>
              <hr class="d-none d-sm-block d-lg-none me-4">
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                <div>
                  <h3 class="mb-1"><?php
                    $sql = $conn->prepare("SELECT COUNT(*) FROM nsdlpancard WHERE type = 'Correction pan' AND user_id = ? ");
                    $sql->execute([$userdata['id']]);
                    $count = $sql->fetchColumn();
                      echo $count;
                    ?></h3>
                      <p class="mb-0">Correction Pan Card</p>
                </div>
                <span class="badge bg-label-secondary rounded p-2 me-sm-4">
                  <i class="bx bx-check-double bx-sm text-primary active"></i>
                </span>
              </div>
              <hr class="d-none d-sm-block d-lg-none me-4">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-lg-12 d-flex align-items-stretch">
            <div class="w-100">
                <div class="card-body">
                    <!-- Filter Form -->
                    <form id="searchForm">
                        <div class="row end">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="field" class="form-label">Select Field:</label>
                                    <select id="field" name="field" class="form-select">
                                        <option value="aadhaar_num">Aadhaar Number</option>
                                        <option value="name_aadhaar">Name</option>
                                        <option value="mob_num">Mobile Number</option>
                                        <option value="order_id">Order ID</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Enter Value:</label>
                                    <input class="form-control border border-danger" id="value" name="value" placeholder="Search By: Aadhaar Number / Name / Mobile Number / Order ID">
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
                </div>
                <div id="dataTableContainer"></div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
	<div class="card shadow border-primary border">
		<div class="card-body print-content">
			<div class="text-center">
				<h3 class="mb-1"><span id="status"></span></h3>
				<p class="text-muted">
					<span class="badge bg-label-info"><b class="text-danger" id="ref"></b></span>
				</p>
			</div>
			<p class="mt-3" style="color:black">Applicant Details</p>
<div class="card shadow border-danger border">
<div class="card-body">
	<div class="">
		<div class="d-flex justify-content-between align-items-center">
			<h5 class="card-title m-0" style="color:black">Name</h5>
			<h6 class="m-0" id="instatusIcon"></h6>
		</div>
		<h3 class="mt-2" id="cardName"></h3>
	</div>
	<div class="d-flex justify-content-between align-items-center">
		<span class="card-title m-0" style="color:black" id="timeStamp"></span>
    		<span class="m-0" style="color:#060094" id="panType"><b></b></span>
    	</div>
    	<hr>
    	<div class="d-flex justify-content-between flex-wrap gap-2">
				<div class="d-flex flex-wrap">
					<div>
						<p style="color:black" class="mb-0">PAN NUMBER</p>
					</div>
				</div>
				<div class="d-flex flex-wrap align-items-center cursor-pointer">
					<span>
						<b><b style="color:red" id="panNumber"></b></b>
					
				</span></div>
			</div>
			<hr>
			<div class="d-flex justify-content-between flex-wrap gap-2">
				<div class="d-flex flex-wrap">
					<div>
						<p style="color:black" class="mb-0">GANDER</p>
					</div>
				</div>
				<div class="d-flex flex-wrap align-items-center cursor-pointer">
					<span id="gender"></span></div>
			</div>
			<hr>
				<div class="d-flex justify-content-between flex-wrap gap-2">
					<div class="d-flex flex-wrap">
						<div>
							<p style="color:black" class="mb-0">DATE OF BIRTH</p>
						</div>
					</div>
					<div class="d-flex flex-wrap align-items-center cursor-pointer">
						<span id="dob"></span></div>
				</div>
			<hr>
			<div class="d-flex justify-content-between flex-wrap gap-2">
				<div class="d-flex flex-wrap">
					<div>
						<p style="color:black" class="mb-0">FATHER NAME</p>
					</div>
				</div>
				<div class="d-flex flex-wrap align-items-center cursor-pointer">
					<span id="fatherName"></span></div>
			</div>
			<hr>
				<div class="d-flex justify-content-between flex-wrap gap-2">
					<div class="d-flex flex-wrap">
						<div>
							<p style="color:black" class="mb-0">AADHAAR NUMBER</p>
						</div>
					</div>
					<div class="d-flex flex-wrap align-items-center cursor-pointer">
						<span id="uidNumber"></span></div>
				</div>
				<hr>
					<div class="d-flex justify-content-between flex-wrap gap-2">
						<div class="d-flex flex-wrap">
							<div>
								<p style="color:black" class="mb-0">MOBILE NUMBER</p>
							</div>
						</div>
						<div class="d-flex flex-wrap align-items-auto cursor-pointer">
							<span id="mobileNumber"></span></div>
					</div>
				<hr>
					<div class="d-flex justify-content-between flex-wrap gap-2">
						<div class="d-flex flex-wrap">
							<div>
								<p style="color:black" class="mb-0">EMAIL ID</p>
							</div>
						</div>
						<div class="d-flex flex-wrap align-items-auto cursor-pointer">
							<span id="emailId"></span></div>
					</div>
				</div>
			</div>
			<p class="mt-3" style="color:black">Or Details
			
			</p><div class="card shadow">
				<div class="card-body">
					<ul class="timeline" style="margin-bottom: -45px">
					    <li class="timeline-item timeline-item-transparent border-left-dashed">
							<span class="timeline-point-wrapper">
								<span class="timeline-point timeline-point-primary"></span>
							</span>
							<div class="timeline-event">
								<div class="timeline-header">
									<h6 class="mb-0" style="color:red;"><b>Debit Amount</b></h6>
									<span style="color:red;" id="debitAmt"></span>
								</div>
							</div>
						</li>
						<li class="timeline-item timeline-item-transparent border-left-dashed">
							<span class="timeline-point-wrapper">
								<span class="timeline-point timeline-point-primary"></span>
							</span>
							<div class="timeline-event">
								<div class="timeline-header">
									<h6 class="mb-0" style="color:#060094"><b>Closing Blance</b></h6>
									<span style="color:green;" id="mainAmt"></span>
								</div>
							</div>
						</li>
						<li class="timeline-item timeline-item-transparent border-left-dashed">
							<span class="timeline-point-wrapper">
								<span class="timeline-point timeline-point-primary"></span>
							</span>
							<div class="timeline-event">
								<div class="timeline-header">
									<h6 class="mb-0" style="color:#060094"><b>Remark</b></h6>
									<span style="color:black" id="remark"></span>
								</div>
							</div>
						</li>
						<li class="timeline-item timeline-item-transparent border-transparent">
							<span class="timeline-point-wrapper">
								<span class="timeline-point timeline-point-primary"></span>
							</span>
							<div class="timeline-event">
								<div class="timeline-header">
									<h6 class="mb-0" style="color:#060094"><b>Document Status</b></h6>
									<span style="color:black"><b class="text-danger" id="hcopyStatus"></b></span>
								</div>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
	</div>

        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    // Handle form submission
    $('#searchForm').submit(function(event) {
        event.preventDefault(); // Prevent default form submission

        var field = $('#field').val(); // Get the entered order ID
        var value = $('#value').val(); // Get the entered order ID

        // AJAX request to fetch PAN card data based on order ID
        $.ajax({
            url: '../system/panData.php', // Replace with your backend endpoint
            type: 'POST',
            dataType: 'json',
            data: {field: field, value: value},
            success: function(response) {
                // Check if response is successful
                if (response.success) {
                    function ucfirst(str) {
                        return str.charAt(0).toUpperCase() + str.slice(1);
                    }
                    // Display fetched data in table format
                    var panData = response.data;
                    var resultHtml = '<div class="card-body">';
                    resultHtml += '<div id="demo_info" class="box table-responsive text-nowrap">';
                    resultHtml += '<table id="example" class="table table-striped text-nowrap">';
                    resultHtml += '<thead style="background: #000cad;">';
                    resultHtml += '<tr>';
                    resultHtml += '<th style="display:none;">#</th>  ';
                    resultHtml += '<th style="color:#fff;">DATE</th>';
                    resultHtml += '<th style="color: #fff">Application_info</th>';
                    resultHtml += '<th style="color: #fff">ACK</th>';
                    resultHtml += '<th style="color: #fff; width: 50px;">responce_Type</th>';
                    resultHtml += '<th style="color: #fff">Status</th>';
                    resultHtml += '<th style="color: #fff">Action</th>';
                    resultHtml += '</tr>';
                    resultHtml += '</thead>';
                    resultHtml += '<tbody>';
                    
                    
                    resultHtml += '<tr>';
                    resultHtml += '<td>' + panData.order_id + '<br>' + panData.timestamp + '</td>';
                    resultHtml += '<td>' + panData.name_card + '<br>' + panData.faf_name + panData.fam_name + panData.fal_name +'<br>' + panData.aadhaar_num + '</td>';
                    if (panData.ack_no !== '') {
                        resultHtml += '<td>' + panData.ack_no + '<br><button class="btn btn-danger active btn-sm pan-status me-1" data-ackNo="' + panData.ack_no + '">Track Status</button><a href="' + panData.ack_pdf + '" class="btn btn-primary active btn-sm" target="_blank">Receipt</a></td>';
                    } else {
                        resultHtml += '<td>Not Generated<br><a href="../printManagement/nsdlreceipt.php?order_id=' + panData.order_id + '" class="btn btn-primary active btn-sm" target="_blank">Receipt</a></td>';
                    }
                    resultHtml += '<td style="width: 100px">' + ucfirst(panData.remark) + '</td>';
                    resultHtml += '<td>' + ucfirst(panData.status) + '</td>';
                    resultHtml += '<td><button class="btn btn-danger active btn-sm view-receipt" data-receipt=\'' + JSON.stringify(panData) + '\'>View</button></td>';
                    resultHtml += '</tr>';
                    resultHtml += '</tbody>';
                    resultHtml += '</table>';
                    resultHtml += '</div>';
                    resultHtml += '</div>';
                    $('#dataTableContainer').html(resultHtml);
                    
                    // Event listener for "Track Status" buttons
                    document.querySelectorAll(".pan-status").forEach(function(button) {

                        button.addEventListener("click", function() {
                            var ackNo = this.getAttribute("data-ackNo");
                    
                            // Perform status tracking logic here
                            var xhr = new XMLHttpRequest();
                            var url = '../system/trackPanStatus';
                            var params = 'ack_no=' + ackNo;
                    
                            xhr.open('POST', url, true);
                            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                    
                            xhr.onreadystatechange = function() {
                                if(xhr.readyState == XMLHttpRequest.DONE) {
                                    if(xhr.status == 200) {
                                        
                                        function refreshTable() {

                                            // Clear existing content of the table
                                            var table = document.getElementById("example");
                                            table.innerHTML = '';
                                        
                                            // Append updated content to the table
                                            table.innerHTML = newData;
                                        }

                                        
                                        var responseData = xhr.responseText;
                                        var responseParts = responseData.split("#####");
                    
                                        if (responseParts.length >= 4) {
                                            var ackNo = responseParts[1];
                                            var panNumber = responseParts[2];
                                            var allottedDate = responseParts[3];
                    
                                            toastr.success("AckNo: " + ackNo + "  PAN Number: " + panNumber + "  Allotted Date: " + allottedDate + "  Pan Card Issued");
                    
                                            // Assuming you have jQuery available for AJAX
                                            $.ajax({
                                                type: 'POST',
                                                url: 'PhysicalPAN-Record.php', // URL to the PHP script that updates the database
                                                data: { ackNo: ackNo, panNumber: panNumber },
                                                success: function(response) {
                                                    // Handle success response here
                                                    console.log(response);
                                                },
                                                error: function(xhr, status, error) {
                                                    // Handle error here
                                                    console.error(error);
                                                }
                                            });
                                        } else {
                                            alert(responseData);
                                        }
                                    } else {
                                        alert('Error: ' + xhr.status);
                                    }
                                }
                            };
                    
                            xhr.send(params);
                        });
                    });

                    // Add click event for the view receipt button or show application data
                    $('.view-receipt').click(function() {
                        var receiptData = $(this).data('receipt');
                        $('#cardName').html(receiptData.name_card);
                        
                        // Convert the timestamp string to a Date object
                        var timestampDate = new Date(receiptData.timestamp);
                        
                        // Format the date and time components
                        var day = timestampDate.getDate();
                        var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                                          "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                        var month = monthNames[timestampDate.getMonth()];
                        var year = timestampDate.getFullYear();
                        var hours = timestampDate.getHours();
                        var minutes = timestampDate.getMinutes();
                        var ampm = hours >= 12 ? 'PM' : 'AM';
                        hours = hours % 12;
                        hours = hours ? hours : 12; // Handle midnight (12 AM)
                        
                        // Construct the formatted string
                        var formattedTimestamp = day + '-' + month + ' ' + year + ' | ' + hours + ':' + (minutes < 10 ? '0' : '') + minutes + ' ' + ampm;
                        
                        // Display the formatted timestamp
                        $('#timeStamp').html(formattedTimestamp);
                        
                        if(receiptData.type === "new pan"){
                            $('#panType').html('Application Type : New PAN');
                        } else {
                            $('#panType').html('Application Type : Correction PAN');
                        }
                        if(receiptData.pan_number === "newpan"){
                            $('#panNumber').html('NEW REQUEST');
                        } else {
                            $('#panNumber').html('<b>' + receiptData.pan_number + '</b>');
                        }
                        
                        if (receiptData.status && /process/i.test(receiptData.status)) {
                            $('#instatusIcon').html('<i class="bx bx-time-five bx-md" style="color:#D0cd1b"></i>');
                        } else if (receiptData.status && /success/i.test(receiptData.status)) {
                            $('#instatusIcon').html('<i class="bx bx-check-circle bx-md" style="color:green"></i>');
                        } else if (receiptData.status && /rejected/i.test(receiptData.status)) {
                            $('#instatusIcon').html('<i class="bx bx-x-circle bx-md" style="color:red"></i>');
                        }
                        $('#gender').html('<b>' + receiptData.gender + '</b>');
                        $('#dob').html('<b>' + receiptData.dob + '</b>');
                        $('#fatherName').html('<b>' + panData.faf_name + panData.fam_name + panData.fal_name + '</b>');
                        $('#uidNumber').html('<b>' + receiptData.aadhaar_num + '</b>');
                        $('#mobileNumber').html('<b>' + receiptData.mob_num + '</b>');
                        $('#emailId').html('<b>' + receiptData.email_id + '</b>');
                        $('#debitAmt').html('<b>₹ ' + receiptData.amount + '</b>');
                        $('#mainAmt').html('<b>₹ ' + receiptData.balance + '</b>');
                        $('#remark').html('<b>' + ucfirst(receiptData.remark) + '</b>');
                        $('#status').html('<b>' + ucfirst(receiptData.status) + '</b>');
                        $('#ref').html('Reference Number : ' + receiptData.order_id);
                        
                        if(receiptData.hard_copy === 0){
                            $('#hcopyStatus').html('<b>Hard copy not yet received by district officer</b>');
                        }else{
                            $('#hcopyStatus').html('<b>Hard received by district officer</b>');
                        }
                        $('#receiptModal').modal('show');
                    });

                } else {
                    // Display error message if data not found
                    $('#dataTableContainer').html('<p>No data found for the provided Order ID.</p>');
                }
            },
            error: function(xhr, status, error) {
                // Handle error
                console.error(error);
                $('#dataTableContainer').html('<p>Error occurred while fetching data.</p>');
            }
        });
    });
});

</script>
<?php
require_once('../layouts/mainFooter.php');
?>
