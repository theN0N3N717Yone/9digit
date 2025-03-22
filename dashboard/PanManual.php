<?php
$pageName = "Ayushman Print Advance";
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');

// Function to fetch data from the API using cURL
function fetchData($panNumber) {
    $apiKey = '4176362613';
    $apiUrl = 'https://api.apnaindiaseva.in/pan-verification.php';
    $url = "$apiUrl?apikey=$apiKey&pan=$panNumber";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        // Handle curl error if needed
        echo 'Curl error: ' . curl_error($ch);
    }

    curl_close($ch);

    return $response;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the form is submitted
    if (isset($_POST['pan'])) {
        $panNumber = $_POST['pan'];
        $apiResponse = '{
"statusCode": 200,
"statusMessage": "Success",
"Pan_no": "GKIPK3367C",
"name": "Raj  KUMAR",
"father_name": "Alok Singh",
"gender": "MALE",
"dob": "19/06/1999"
}  ';
        
        //fetchData($panNumber);

        // Decode the JSON response
        $responseData = json_decode($apiResponse, true);

        if ($responseData && $responseData['statusCode'] === 200) {
            // Update the UI with the fetched data
            $Pan_no = $responseData['Pan_no'];
            $name = $responseData['name'];
            $fatherName = $responseData['father_name'];
            $gender = $responseData['gender'];
            $dob = $responseData['dob'];
        } else {
            // Handle API error if needed
            echo 'Error fetching data from API.';
        }
    }
}
?>
<style>
.panNo {
    position: absolute;
    top: 95px;
    left: 110px;
    font-size: 10px;
    font-weight: bold;
    width: 100px;
    letter-spacing: 1px;
    color:black;
}
.name {
    position: absolute;
    top: 130px;
    left: 38px;
    font-size: 10px;
    font-weight: bold;
    width: 100px;
    letter-spacing: 1px;
    color:black;
}
.fathername {
    position: absolute;
    top: 155px;
    left: 38px;
    font-size: 10px;
    font-weight: bold;
    width: 100px;
    letter-spacing: 1px;
    color:black;
}
.dob {
    position: absolute;
    top: 185px;
    left: 38px;
    font-size: 10px;
    font-weight: bold;
    width: 100px;
    letter-spacing: 1px;
    color:black;
}

</style>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-4">
            <div id="errors" class="card text-bg-primary border-0 w-100">
                <div class="card mx-2 mb-2 sprint-box mt-2">
                <form method="post">
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex align-items-center">
                                <h5 class="mb-0 mt-0 uidai">Enter your PAN number <span class="text-danger">*</span></h5>
                            </div>
                            <div class="mt-1">
                                <div class="input-group">
                                    <button class="btn bg-danger rounded-start" type="button" style="color:#ffffff">
                                        PAN
                                    </button>
                                    <input name="pan" type="text" autofocus maxlength="10" id="txtUID" class="form-control" autocomplete="off" placeholder="CLNPXXXX5J" />
                                </div>
                                <span style="font-size:12px; color:red;" id="basic-addon-search32"></span>
                            </div>
                            <button type="submit" class="btn btn-primary active mt-4">Get Details</button>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <?php if (isset($name)) : ?>
                        <img src="../assets/img/backgrounds/panbackggg.png" height="100%" width="570">
                        <div class="panNo"><?php echo strtoupper($Pan_no); ?></div>
                        <div class="name"><?php echo strtoupper($name); ?></div>
                        <div class="fathername"><?php echo strtoupper($fatherName); ?></div>
                        <div class="dob"><?php echo strtoupper($dob); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once('../layouts/mainFooter.php');
?>
