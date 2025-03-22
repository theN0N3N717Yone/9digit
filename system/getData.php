<?php
require_once('connectivity_functions.php');

$conn = connectDB();

if (isset($_GET['dataType']) && $_GET['dataType'] === 'txn' && isset($_GET['txnId']) && isset($_GET['token'])) {
    $txnId = base64_decode($_GET['txnId']);
    $token = base64_decode($_GET['token']);

    $sql = "SELECT * FROM `transactions` WHERE remark = :txnId AND reference = :token ORDER BY `id`";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':txnId', $txnId, PDO::PARAM_INT);
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->execute();

    $sl = 1;
    while ($rows = $stmt->fetch(PDO::FETCH_ASSOC)) {

$status = "<b>".strtoupper($rows['status'])."</b>";

if($rows['ack_no'] == 0){
   $info = "<b class='text-primary'>ORDER ID : ".$rows['reference']."</b>"; 
} else {
   $info = "<b class='text-primary'>Acknowledgment Number : ".$rows['ack_no']."</b>"; 
}
if ($rows['status'] && preg_match('/success/i', $rows['status'])) {
    $status_svg = "<i class='bx bx-check-circle bx-md' style='color:#02752a'></i>";
}
if ($rows['status'] && preg_match('/process/i', $rows['status'])) {
    $status_svg = "<i class='bx bx-time-five bx-md' style='color:#D0cd1b'></i>";
}
if ($rows['status'] && preg_match('/rejected/i', $rows['status'])) {
    $status_svg = "<i class='bx bx-x-circle bx-md' style='color:#FF0004'></i>";
}

echo'<div class="modal-body p-0">
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	<div class="card shadow border-primary border">
		<div class="card-body">
			<div class="text-center">
				<h3 class="mb-1">
					<span>'.$status.'</span>
				</h3>
				<p class="text-muted">
					<span class="badge bg-label-info">'.$info.'</span>
				</p>
			</div>
			<p class="mt-3" style="color:black">Applicant Details</p>
<div class="card shadow border-danger border">
<div class="card-body">
	<div class="">
		<div class="d-flex justify-content-between align-items-center">
			<h5 class="card-title m-0" style="color:black">Name</h5>
			<h6 class="m-0">
				'.$status_svg.'
			</h6>
		</div>
		<h3 class="mt-2">
			<b>'.$rows['name_card'].'</b>
		</h3>
	</div>
	<div class="d-flex justify-content-between align-items-center">
		<span class="card-title m-0" style="color:black">'.date('d-M y | h:i A', strtotime($rows['timestamp'])).'</span>
    		<span class="m-0" style="color:#060094"><b>APP TYPE : '.strtoupper($rows['type']).'</b></span>
    	</div>
    	<hr>
    	<div class="d-flex justify-content-between flex-wrap gap-2">
				<div class="d-flex flex-wrap">
					<div>
						<p style="color:black" class="mb-0">USER ID</p>
					</div>
				</div>
				<div class="d-flex flex-wrap align-items-center cursor-pointer">
					<span>
						<b>'.getUsersInfo('username').'</b>
					</spam>
				</div>
			</div>
				<hr>
					<div class="d-flex justify-content-between flex-wrap gap-2">
						<div class="d-flex flex-wrap">
							<div>
								<p style="color:black" class="mb-0">MODE</p>
							</div>
						</div>
						<div class="d-flex flex-wrap align-items-auto cursor-pointer">
							<span>
								<b>'.$rows['mode'].'</b>
							</spam>
						</div>
					</div>
				</div>
			</div>
			<p class="mt-3" style="color:black">Or Details
			</>
			<div class="card shadow">
				<div class="card-body">
					<ul class="timeline" style="margin-bottom: -45px">
					    <li class="timeline-item timeline-item-transparent border-left-dashed">
							<span class="timeline-point-wrapper">
								<span class="timeline-point timeline-point-primary"></span>
							</span>
							<div class="timeline-event">
								<div class="timeline-header">
									<h6 class="mb-0" style="color:red;"><b>Debit Amount</b></h6>
									<span style="color:red;"><b>Rs.'.number_format($rows['amount'],2).'</b></span>
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
									<span style="color:green;"><b>Rs.'.number_format($rows['balance'],2).'</b></span>
								</div>
							</div>
						</li>
					</ul>
				</div>
			</div>
			
			
			<div class="card shadow mt-4">
				<div class="card-body">
					<div class="timeline-event">
						<div class="timeline-header text-center">
							<h6 class="mb-0" style="color:#060094"><b>Remark</b></h6>
							<span style="color:red"><b>'.$rows['remark'].'</b></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>';
}
}


if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['access_token'])) {
    $id = $_GET['access_token'];
    $user = getUsersData($id);

    if ($user) {
        // Return user data as JSON
        echo json_encode($user);
    } else {
        // Return an error message or handle it as needed
        echo json_encode(['error' => 'User not found']);
    }
}

// HOLD REASION
if (isset($_GET['dataType']) && $_GET['dataType'] === 'panreason' && isset($_GET['txnId']) && isset($_GET['token'])) {

    // Decode parameters
    $txnId = base64_decode($_GET['txnId']);
    $token = base64_decode($_GET['token']);

    // Prepare SQL statement
    $sql = "SELECT * FROM `nsdlpancard` WHERE id = :txnId AND order_id = :token ORDER BY `id`";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':txnId', $txnId, PDO::PARAM_INT);
    $stmt->bindParam(':token', $token, PDO::PARAM_INT);

    // Execute query
    $stmt->execute();

    // Fetch and display results
    $sl = 1;
    while ($rows = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<h2 style="color:black"><b>NAME - ' . $rows['name_card'] . '</b></h2><b><h4 style="color:red">' . $rows['remark'] . '</h4>';
    }
}

if (isset($_GET['dataType']) && $_GET['dataType'] === 'recharge_function' && isset($_GET['mobile'])) {
$mobile = $_GET['mobile'];
    
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://digitalapiproxy.paytm.com/v1/mobile/getopcirclebyrange?channel=web&version=2&number=$mobile&child_site_id=1&site_id=1&locale=en-in",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
}
if(isset($_GET['opid'])){
    $oid = $_GET['opid'];
    $Circle = $_GET['scl'];
    
$headers = array(
  'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
  'X-Csrf-Token: 41qJOQJn-RlvyG_4LJLrZ9sVAhbr-3VBMGhc',
  'X-Xsrf-Token: 41qJOQJn-RlvyG_4LJLrZ9sVAhbr-3VBMGhc',
);

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://digitalcatalog.paytm.com/dcat/v1/browseplans/mobile/7166?channel=web&version=2&child_site_id=1&site_id=1&locale=en-in&operator='.$oid.'&pageCount=1&itemsPerPage=20&sort_price=asce&pagination=1&circle='.$Circle.'',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => $headers,
));

$response = curl_exec($curl);

curl_close($curl);

//$curl = curl_init();

//curl_setopt_array($curl, array(
//  CURLOPT_URL => "https://www.mplan.in/api/plans.php?apikey=36d434428be638b12d2c13ff489dc6a2&cricle=$Circle&operator=$oid",
//  CURLOPT_RETURNTRANSFER => true,
//  CURLOPT_ENCODING => "",
//  CURLOPT_MAXREDIRS => 10,
//  CURLOPT_TIMEOUT => 0,
//  CURLOPT_FOLLOWLOCATION => true,
//  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//  CURLOPT_CUSTOMREQUEST => "GET",
//));

//$response = curl_exec($curl);

//curl_close($curl);

$obj = json_decode($response, true); 

//echo $response;
?>
<style>
    .tab-pane {
        position: relative;
        overflow: auto;
        width: 100%;
        max-height: 320px;
    }

    .tab-pane::-webkit-scrollbar,
    .tab-pane::-webkit-scrollbar {
        width: 4px; /* Adjust the width as needed */
    }

    .tab-pane::-webkit-scrollbar-thumb,
    .tab-pane::-webkit-scrollbar-thumb {
        background-color: #000285; /* Change the color to your desired color */
    }

    .tab-pane::-webkit-scrollbar-track,
    .tab-pane::-webkit-scrollbar-track {
        background-color: #ff0000; /* Change the color to your desired color */
    }

    .nav-container {
        overflow-x: auto;
        white-space: nowrap;
        scrollbar-width: thin; /* Firefox */
        scrollbar-color: #000285 #ff0000; /* thumb and track colors */
    }

    /* Webkit (Chrome, Safari) */
    .nav-container::-webkit-scrollbar {
        height: 6px; /* Adjust the width as needed */
        width: 0;
    }

    .nav-container::-webkit-scrollbar-track {
        background-color: #ff0000; /* Change the color to your desired color */
    }

    .nav-container::-webkit-scrollbar-thumb {
        background-color: #000285; /* Change the color to your desired color */
    }
</style>
<div class="nav-align-top">
    <div class="nav-container nav-scroller">
        <ul class="nav nav-tabs flex-nowrap" role="tablist">
            <?php foreach ($obj['groupings'] as $index => $grouping): ?>
                <li class="nav-item" role="presentation p-2">
                    <?php
                    $targetId = strtolower(str_replace(' ', '-', $grouping['name'])) . '-' . $index;
                    ?>
                    <button type="button" class="nav-link <?= $index === 0 ? 'active' : '' ?>" role="tab"
                            data-bs-toggle="tab" data-bs-target="#<?= $targetId ?>"
                            aria-controls="<?= $targetId ?>" aria-selected="<?= $index === 0 ? 'true' : 'false' ?>">
                        <?= $grouping['name'] ?>
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="tab-content p-0">
        <?php foreach ($obj['groupings'] as $index => $grouping): ?>
            <?php
            $targetId = strtolower(str_replace(' ', '-', $grouping['name'])) . '-' . $index;
            ?>
            <div class="tab-pane fade <?= $index === 0 ? 'show active' : '' ?>" id="<?= $targetId ?>"
                 role="tabpanel">
                <p>
                    <?php foreach ($grouping['productList'] as $product): ?>
                        <div class="alert ps-scroller mb-2" role="alert" style="border-radius: 0; background:#f2f2f2;">
                            <div class="alert-text">
                                <span>Plan Details : <?= $product['description'] ?></span>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="text-start">
                                        <a href="javascript:void(0)"
                                           class="btn btn-sm btn-outline-primary mt-2"
                                           onclick="pickAmt('<?= $product['price'] ?>');"
                                           data-bs-dismiss="modal" aria-label="Close"
                                           data-target="#GetPlans">₹ <?= number_format($product['price'], 2) ?>
                                        </a>
                                    </span>
                                    <div class="text-end">
                                        <span class="text">Validity : <?= $product['validity'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
</div>


<?php
}

?>