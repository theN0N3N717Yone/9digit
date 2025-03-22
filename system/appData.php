<?php
require_once('connectivity_functions.php');

$conn = connectDB();




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
<link rel="stylesheet" href="../assets/vender/bootstrap/css/bootstrap.min.css">

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">

   <link rel="stylesheet" href="../assets/vender/sidebar/demo.css">
   <!-- CSS -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.3/tiny-slider.css">

   <!-- JavaScript -->

   <link rel="stylesheet" href="../assets/vender/materialdesign/css/materialdesignicons.min.css">

   <link rel="stylesheet" href="../assets/css/style.css">
   <link rel="stylesheet" href="../assets/css/styl.css">

<style>
    .tab-pane {
        position: fixed;
        overflow: auto;
        width: 100%;
        max-height: 640px;
    }

    .tab-pane::-webkit-scrollbar,
    .tab-pane::-webkit-scrollbar {
        width: 0px; /* Adjust the width as needed */
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
        height: 3px; /* Adjust the width as needed */
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
        <ul class="nav doctor-profile-tabs flex-nowrap" role="tablist" id="pills-tab">
            <?php foreach ($obj['groupings'] as $index => $grouping): ?>
                <li class="nav-item" role="presentation p-2">
                    <?php
                    $targetId = strtolower(str_replace(' ', '-', $grouping['name'])) . '-' . $index;
                    ?>
                    <button type="button" class="nav-link <?= $index === 0 ? 'active' : '' ?>" role="tab"
                        data-bs-toggle="tab" data-bs-target="#<?= $targetId ?>"
                        aria-controls="<?= $targetId ?>" aria-selected="<?= $index === 0 ? 'true' : 'false' ?>"
                        style="border: none;">
                        <b><?= strtoupper($grouping['name']) ?></b>
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="tab-content">
        <?php foreach ($obj['groupings'] as $index => $grouping): ?>
            <?php
            $targetId = strtolower(str_replace(' ', '-', $grouping['name'])) . '-' . $index;
            ?>
            <div class="tab-pane fade <?= $index === 0 ? 'show active' : '' ?>" id="<?= $targetId ?>" role="tabpanel">
                <?php foreach ($grouping['productList'] as $product): ?>
                    <div class="link-dark ps-scroller" onclick="pickAmt('<?= $product['price'] ?>');" data-bs-dismiss="modal" aria-label="Close" data-target="#GetPlans">
                       <div class="bg-white d-flex align-items-center p-2">
                          <span>Plan Details : <?= $product['description'] ?></span>
                       </div>
                       <div class="d-flex align-items-center gap-3 p-2 mb-2 shadow-sm" style="margin-top: -10px;">
                           <div>
                              <p class="text-muted m-0"><span class="mdi mdi-clock-alert me-1 fs-6"></span> <?= $product['validity'] ?></p>
                           </div>
                           <div class="ms-auto">
                              <span class="text-danger px-2">₹ <?= number_format($product['price'], 2) ?></span>
                           </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="footer mt-auto fix-osahan-footer">
        <div class="d-flex shadow bottom-nav-main p-3 pt-1 pb-1 text-danger"><b>
            Disclaimer &nbsp;:&nbsp; While we support most recharges, we request you to verify with your operator once before proceeding.</b>
</div>
        
    </div>
</div>


<?php
}

?>