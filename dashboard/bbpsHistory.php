<?php
require_once('../layouts/mainHeader.php');
?>
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- DataTales Example -->
        <div class="card shadow-lg ps-background-table">
            <div class="card-header flex-column flex-md-row">
                <form method="post" action="">
                    <div class="row">
                        <div class="col-3">
                            <div class="form-group">
                                <label class="form-label" style="color: black;">Filter</label>
                                <input type="text" class="form-control form-control-sm flatpickr-input" name="daterange"
                                    placeholder="<?php echo $date; ?> to YYYY-MM-DD" id="flatpickr-range" readonly="readonly">
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                <label class="form-label" style="color: black;">Choose Status</label>
                                <select name="search_status" class="form-select form-select-sm">
                                     <option value="">:: Select ::</option>
                                     <option value="Failed">Failed</option>
                                     <option value="Process">Process</option>
                                     <option value="Pending">Pending</option>
                                     <option value="Success">Success</option>          
                                </select>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                <label class="form-label" style="color: black;">Choose Operator</label>
                                <select name="operator_id" class="w-full form-select form-select-sm text-sm">
                                  <option value="">:: Select ::</option>
                                  <option value="airtel">Airtel</option>
                                  <option value="jio">Jio</option>
                                  <option value="idea">Vodafone / Idea</option>
                                  <option value="4">BSNL TopUp</option>
                                  <option value="5">BSNL Special</option>
                                  <option value="13">Google Play Voucher</option>
                                  <option value="6">Airtel Digital Tv</option>
                                  <option value="7">Dish Tv</option>
                                  <option value="8">Sun Direct</option>
                                  <option value="9">Tata Sky</option>
                                  <option value="10">Videocon D2h</option>
                                  <option value="14">WBSEDCL</option>
                                  <option value="15">WESCO</option>
                                  <option value="16">UPPCL (URBAN)</option>
                                  <option value="17">UPPCL (RURAL)</option>
                                  <option value="18">CESC LTD</option>
                                  <option value="19">HPSEB</option>
                                  <option value="20">HESCOM</option>
                                  <option value="21">IPC (West Bengal)</option>
                                  <option value="22">IPC (Bihar)</option>
                                  <option value="23">DGVCL (Gujarat)</option>
                                  <option value="24">MGVCL (Gujarat)</option>
                                  <option value="25">PGVCL (Gujarat)</option>
                                  <option value="26">UGVCL (Gujarat)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-group">
                                <label class="form-label" style="color: black;">Search</label>
                                <input type="search" autocomplete="off" name="search" class="form-control form-control-sm border-danger"
                                    placeholder="Search By:- Mobile Number / Reference / Operator / Amount" autofocus />
                            </div>
                        </div>
                        <div class="col-5 mt-2 text-end">
                            <div class="dt-action-buttons text-start pt-3 pt-md-0">
                                <div class="dt-buttons">
                                    <button class="dt-button create-new btn btn-sm btn-primary" type="submit">Filter</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <?php if($userdata['usertype'] === 'retailer') { ?>
            <div class="card-body">
                <div id="demo_info" class="box table-responsive text-nowrap">
                    <table id="example" class="table table-striped">
                        <thead style="background: #000cad;">
                            <tr>
                                <th style="display:none">#</th>
                                <th style="color: #fff">Payment date</th>
                                <th style="color: #fff">Transaction ID</th>
                                <th style="color: #fff">Mobile Number</th>
                                <th style="color: #fff">Biller Name</th>
                                <th style="color: #fff">Amount</th>
                                <th style="color: #fff">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            
                            
                            $stmt = $conn->prepare("select * from recharges WHERE user_id=? ORDER BY `id` DESC");
                            $stmt->execute([$userdata['id']]);
                            
                            
                            if (isset($_POST['daterange'])) {
                                $dateRange = $_POST['daterange'];
                                list($fromdate, $todate) = explode(' to ', $dateRange);
                            
                                $stmt = $conn->prepare("SELECT * FROM recharges WHERE date_time BETWEEN :fromdate AND :todate AND user_id = :user_id ORDER BY `id` DESC");
                                $stmt->execute([
                                    'fromdate' => $fromdate,
                                    'todate' => $todate,
                                    'user_id' => $userdata['id']
                                ]);
                            } else {
                                // Default values if the form is not submitted
                                $fromdate = $date;
                                $todate = $date;
                            
                                // Fetch data without filtering
                                $stmt = $conn->prepare("SELECT * FROM recharges WHERE user_id = :user_id ORDER BY `id` DESC");
                                $stmt->execute([
                                    'user_id' => $userdata['id']
                                ]);
                            }
                            
                            $stmt = $conn->prepare("select * from recharges WHERE date_time between '".$fromdate."' AND '".$todate."' AND user_id=? ORDER BY `id` DESC");
                            $stmt->execute([$userdata['id']]);
            
                            if(!empty($_POST['search'])){
            
                            $search = $_POST['search']; 
                            $stmt = $conn->prepare("select * from recharges WHERE number LIKE '{$search}%' OR ref_id LIKE '{$search}%' AND user_id=? ORDER BY `id` DESC");
                            $stmt->execute([$userdata['id']]);
                            }
                            
                            if(!empty($_POST['operator_id'])){
            
                            $operator_id = $_POST['operator_id']; 
                            $stmt = $conn->prepare("select * from recharges WHERE operator=? AND user_id=? ORDER BY `id` DESC");
                            $stmt->execute([$operator_id, $userdata['id']]);
                            }
                            $sl=1;
                            while($row=$stmt->fetch()) {
                                
                            
                            
                            $usql = $conn->prepare("select * from users WHERE id = ?");
                            $usql->execute([$row['user_id']]);
                            $usr_d=$usql->fetch();
                            
                            if ($row['ref_id'] === '') {
                                $refId = 'N/A';
                            } else {
                                $refId = strtoupper($row['ref_id']);
                            }
                            
                            // Your commission percentage
                            if($row['status'] === "success"){
                            $commissionRate = $userdata[get_safe($row['operator'])];
                            $total_commissiom = ($commissionRate / 100) * $row['amount'];
                            } else {
                            $total_commissiom = 'N/A';    
                            }
        		            // Display the table row
                                echo "<tr>
                                    <td style='display:none'><b>" . $sl . "</td>
                                    <td class='small' style='color:black;'>" . date("d M Y", strtotime($row['timestamp'])) . "</td>
                                    <td class='small' style='color:black;'>
                                    <button class='btn btn-link' type='button' data-bs-toggle='offcanvas' data-bs-target='#rechargeApi' 
                                    aria-controls='rechargeApi'
                                    data-bs-refid='$refId'
                                    data-bs-status='" . strtoupper($row['status']) . "'
                                    data-bs-operator='" . strtoupper($row['operator']) . "'
                                    data-bs-commission='$total_commissiom'
                                    data-bs-date='" . date("d M Y", strtotime($row['timestamp'])) . "'
                                    data-bs-number='" . $row['number'] . "'
                                    data-bs-amount='" . strtoupper($row['amount']) . "'
                                    >$refId</button></td>
                                    <td class='small' style='color:black;'>" . $row['number'] . "</b></td>
                                    <td class='small' style='color:black;'>" . strtoupper($row['operator']) . "</td>
                                    <td class='small' style='color:black;'>" . strtoupper($row['amount']) . "</td>
                                    <td class='small' style='color:black'>" . strtoupper($row['status']) . "</td>
                                </tr>";
                                $sl++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php } ?>
            <?php if($userdata['usertype'] === 'mainadmin') { ?>
            <div class="card-body">
                <div id="demo_info" class="box table-responsive text-nowrap">
                    <table id="example" class="table table-striped">
                        <thead style="background: #000cad;">
                            <tr>
                                <th style="display:none">#</th>
                                <th style="color: #fff">Payment date</th>
                                <th style="color: #fff">Transaction ID</th>
                                <th style="color: #fff">Mobile Number</th>
                                <th style="color: #fff">Biller Name</th>
                                <th style="color: #fff">Amount</th>
                                <th style="color: #fff">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            
                            
                            $stmt = $conn->prepare("select * from recharges ORDER BY `id` DESC");
                            $stmt->execute();
                            
                            
                            if (isset($_POST['daterange'])) {
                                $dateRange = $_POST['daterange'];
                                list($fromdate, $todate) = explode(' to ', $dateRange);
                            
                                $stmt = $conn->prepare("SELECT * FROM recharges WHERE date_time BETWEEN :fromdate AND :todate ORDER BY `id` DESC");
                                $stmt->execute([
                                    'fromdate' => $fromdate,
                                    'todate' => $todate
                                ]);
                            } else {
                                // Default values if the form is not submitted
                                $fromdate = $date;
                                $todate = $date;
                            
                                // Fetch data without filtering
                                $stmt = $conn->prepare("SELECT * FROM recharges ORDER BY `id` DESC");
                                $stmt->execute();
                            }
                            
                            $stmt = $conn->prepare("select * from recharges WHERE date_time between '".$fromdate."' AND '".$todate."' ORDER BY `id` DESC");
                            $stmt->execute();
            
                            if(!empty($_POST['search'])){
            
                            $search = $_POST['search']; 
                            $stmt = $conn->prepare("select * from recharges WHERE number LIKE '{$search}%' OR ref_id LIKE '{$search}%' ORDER BY `id` DESC");
                            $stmt->execute();
                            }
                            if(!empty($_POST['operator_id'])){
            
                            $operator_id = $_POST['operator_id']; 
                            $stmt = $conn->prepare("select * from recharges WHERE operator=? ORDER BY `id` DESC");
                            $stmt->execute([$operator_id]);
                            }
                            $sl=1;
                            while($row=$stmt->fetch()) {
                                
                            $usql = $conn->prepare("select * from transactions WHERE reference = ?");
                            $usql->execute([$row['order_id']]);
                            $transactions = $usql->fetch();
                            
                            $usql = $conn->prepare("select * from users WHERE id = ?");
                            $usql->execute([$row['user_id']]);
                            $usr_d=$usql->fetch();
                            
                            if ($row['ref_id'] === '') {
                                $refId = 'N/A';
                            } else {
                                $refId = strtoupper($row['ref_id']);
                            }
                            
                            // Your commission percentage
                            if($row['status'] === "success"){
                            $commissionRate = $userdata[get_safe($row['operator'])];
                            $total_commissiom = ($commissionRate / 100) * $row['amount'];
                            } else {
                            $total_commissiom = 'N/A';    
                            }
        		            // Display the table row
                                echo "<tr>
                                    <td class='small' style='display:none'><b>" . $sl . "</td>
                                    <td class='w-25 small' style='color:black;'>AGENT ID: {$usr_d['username']}
                                    <br>{$usr_d['owner_name']}
                                    <br>" . date('d M Y | H:i:s A', strtotime($row['timestamp'])) . "</td>
                                    <td class='small' style='color:black;'>
                                    <button class='btn btn-link' type='button' data-bs-toggle='offcanvas' data-bs-target='#rechargeApi' 
                                    aria-controls='rechargeApi'
                                    data-refid='$refId'
                                    data-status='" . strtoupper($row['status']) . "'
                                    data-operator='" . strtoupper($row['operator']) . "'
                                    data-commission='+ " . $total_commissiom . "'
                                    data-date='" . date("d M Y | H:i:s A", strtotime($row['timestamp'])) . "'
                                    data-number='" . $row['number'] . "'
                                    data-amount='- " . strtoupper($row['amount']) . "'
                                    data-mode='" . $transactions['mode'] . "'
                                    data-rcm='" . $transactions['mode'] . "'
                                    data-remark='" . $transactions['remark'] . "'
                                    data-number='" . $row['number'] . "'
                                    data-opid='" . $refId . "'
                                    data-operator='" . strtoupper($row['operator']) . "'
                                    data-status='" . strtoupper($row['status']) . "'
                                    >$refId</button></td>
                                    <td class='small' style='color:black;'>" . $row['number'] . "</b></td>
                                    <td class='small' style='color:black;'>" . strtoupper($row['operator']) . "</td>
                                    <td class='small' style='color:black;'>" . strtoupper($row['amount']) . "</td>
                                    <td class='small' style='color:black'>" . strtoupper($row['status']) . "</td>
                                </tr>";
                                $sl++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    
<div class="offcanvas offcanvas-end" id="rechargeApi" aria-labelledby="rechargeApiLabel" data-bs-backdrop="false">
   <div class="offcanvas-header">
      <a onclick="printReceipt('refId')" class="offcanvas-title" id="refId" style="color: #0c008e;" href=""><i class="bx bx-printer"></i>
               Print customer receipt</a>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
   </div>
   <div class="offcanvas-body">
      <div class="row">
         <div class="pr-0 pb-0 col col-8">
            <span class="Title font-weight-bold">Trans ID:<span id="rechargeRefId"></span></span>
            <p class="caption mb-3" style="color: rgb(52, 168, 83);" id="status"></p>
         </div>
         <div class="text-right pb-0 col col-4">
            <div class="Title font-weight-bold text-end">Rs <b><span id="rechargeAmount1"></span></b></div>
         </div>
      </div>

      <div class="relative-card mb-2">
         <div>
            <span class="Title font-weight-bold mt-5" style="vertical-align: sub;">Wallet Movement</span>
            <button type="button" class="btn btn-icon btn-label-linkedin btn-xs ma-0 card-btn v-btn v-btn--depressed v-btn--flat v-btn--outlined theme--light v-size--default gray--text" data-bs-toggle="collapse" data-bs-target="#accordionPopoutOne" aria-expanded="false" aria-controls="accordionPopoutOne"><i class="bx bx-plus"></i></button>
         </div>
      </div>
      <!--*********************************************-->
      <div id="accordionPopoutOne" class="accordion-collapse collapse p-2" aria-labelledby="headingPopoutOne" data-bs-parent="#accordionPopout">
         <div class="accordion-body">
            <ul class="timeline">
               <li class="timeline-item timeline-item-transparent">
                  <span class="timeline-point-wrapper"><span class="timeline-point timeline-point-danger"></span></span>
                  <div class="timeline-event">
                     <div class="timeline-header mb-0">
                        <h6 class="mb-0" style="color: black;" id="rechargeMode"></h6>
                        <span style="color: red; font-weight: bold;" id="rechargeAmount2"></span>
                     </div>
                     <p class="mb-0">This Balance deducted from wallet</p>
                     <div class="d-flex">
                        <small id="rechargeDate1"></small>
                     </div>
                  </div>
               </li>
               <li class="timeline-item timeline-item-transparent">
                  <span class="timeline-point-wrapper"><span class="timeline-point timeline-point-primary active"></span></span>
                  <div class="timeline-event">
                     <div class="timeline-header mb-0">
                        <h6 class="mb-0" style="color: black;">Commission</h6>
                        <small style="color: black; font-weight: bold;" id="rechargeCommission"></small>
                     </div>
                     <p class="mb-0">Commission has been credited in the wallet</p>
                     <div class="d-flex">
                        <small id="rechargeDate2"></small>
                     </div>
                  </div>
               </li>
               <li class="timeline-item timeline-item-transparent">
                  <span class="timeline-point-wrapper"><span class="timeline-point timeline-point-info"></span></span>
                  <div class="timeline-event pb-0">
                     <div class="timeline-header mb-0">
                        <h6 class="mb-0" style="color: black;">Others</h6>
                        <small style="color: black; font-weight: bold;">0</small>
                     </div>
                     <p class="mb-0">Amount On Hold_Fy_Transaction</p>
                     <div class="d-flex">
                        <small id="rechargeDate3"></small>
                     </div>
                  </div>
               </li>
            </ul>
         </div>
      </div>

      <div class="overflow-hidden txn-detail pa-0 pt-2 v-card v-card--flat v-sheet theme--light" style="width: 100%;">
         <h2 class="Title font-weight-bold pl-0 pt-3 pb-3 pt-2" style="width: 100%;"> Transaction Details </h2>
         <div class="row">
            <div class="text-center mb-0 col col-2">
               <div class="logo">
                  <img alt="Product Icon" id="modeImage" src="" width="100%" height="100%">
               </div>
            </div>
            <div class="pl-0 col col-9">
               <p class="body-2 font-weight-bold mt-1 mb-0"><span id="remark"></span></p>
               <p class="caption gray mb-1"></p>
            </div>
            <div class="pt-0 pb-2 pr-0 col col-5">
               <div>
                  <p class="body-2 font-weight-bold mt-1 mb-0" id="number"></p>
                  <p class="caption mb-1 text--gray">Mobile Number</p>
               </div>
            </div>
            <div class="pt-0 pb-2 pr-0 col col-7">
               <div>
                  <p class="body-2 font-weight-bold mt-1 mb-0" id="opid"></p>
                  <p class="caption mb-1 text--gray">RRN ID</p>
               </div>
            </div>
            <div class="pt-0 pb-2 pr-0 col col-5">
               <div>
                  <p class="body-2 font-weight-bold mt-1 mb-0" id="operator"></p>
                  <p class="caption mb-1 text--gray">Operator</p>
               </div>
            </div>
            <div class="pt-0 pb-2 pr-0 col col-7">
               <div>
                  <p class="body-2 font-weight-bold mt-1 mb-0" id="rctype"></p>
                  <p class="caption mb-1 text--gray">TXN Type</p>
               </div>
            </div>
         </div>
         <div class="row"></div>
         <!---->
         <div class="row">
            <!---->
         </div>
         <!---->
      </div>

      <!--*********************************************-->
   </div>
</div>

<!-- JavaScript to update content -->
<?php
require_once('../layouts/mainFooter.php');
?>
<!-- JavaScript to update content -->
<script>
    // Ensure offcanvas is initialized properly
    var offcanvas = new bootstrap.Offcanvas(document.getElementById('rechargeApi'));

    // Listen for the shown.bs.offcanvas event
    offcanvas._element.addEventListener('shown.bs.offcanvas', function () {
        // Get the button that triggered the offcanvas
        var button = document.activeElement;

        // Update content based on data attributes of the button
        document.getElementById('rechargeRefId').innerText = button.getAttribute('data-refid');
        document.getElementById('rechargeAmount1').innerText = button.getAttribute('data-amount');
        document.getElementById('rechargeAmount2').innerText = button.getAttribute('data-amount');
        document.getElementById('rechargeCommission').innerText = button.getAttribute('data-commission');
        document.getElementById('rechargeDate1').innerText = button.getAttribute('data-date');
        document.getElementById('rechargeDate2').innerText = button.getAttribute('data-date');
        document.getElementById('rechargeDate3').innerText = button.getAttribute('data-date');
        document.getElementById('rechargeMode').innerText = button.getAttribute('data-mode');
        document.getElementById('rctype').innerText = button.getAttribute('data-mode');
        document.getElementById('remark').innerText = button.getAttribute('data-remark');
        document.getElementById('number').innerText = button.getAttribute('data-number');
        document.getElementById('opid').innerText = button.getAttribute('data-opid');
        document.getElementById('status').innerText = button.getAttribute('data-status');
        document.getElementById('operator').innerText = button.getAttribute('data-operator');
        var operator = button.getAttribute('data-operator');
        // Additional updates for other details
        
        var modeImage = document.getElementById('modeImage');
            var modeImages = {
                "AIRTEL": "../assets/img/rechage-page/airtel.png",
                "JIO": "../assets/img/rechage-page/jio.png",
                "IDEA": "../assets/img/rechage-page/idea.png",
            };

            if (modeImages.hasOwnProperty(operator)) {
                modeImage.src = modeImages[operator];
            } else {
                console.error("Mode image not found for mode:", operator);
            }
            
            
            
            
    });
</script>

