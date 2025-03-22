<?php
$pageName = "DTH Recharge"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');
?>
<?php
$userId = getUsersInfo('id');
$userBalance = getUsersInfo('balance');

$success = 0;

// Check if the form has been submitted and all required fields are not empty
if (isset($_POST['recharge']) && !empty(get_safe($_POST['number'])) && !empty(get_safe($_POST['operator'])) && !empty(get_safe($_POST['amount']))) {
    $mobileNumber = get_safe($_POST['number']);

    // Check if the number was recharged within the last 3 minutes
    if (isRecentlyRecharged($mobileNumber)) {
        echo '<script>toastr.error("Duplicate Request. This number was recharged within the last 1 minutes. Please try again later");</script>';
    } else {
        // Record the recharge time for this number in the session
        $_SESSION['recharge_history'][$mobileNumber] = time();

        // Rest of your code for processing the recharge
        $amount = get_safe($_POST['amount']);

        if ($amount > $userBalance) {
            echo '<script>toastr.error("Insufficient Balance!");</script>';
        } else {
            $total_amount = $amount;

            // Debit
            $new_balance = $userBalance - $total_amount;
            $sqlu = $conn->prepare("UPDATE users SET balance=? WHERE id=?");
            $sqlu->execute([$new_balance, $userId]);
            // Debit
            
            // Insert a transaction record
            $txnsql = "INSERT INTO `transactions`(`date_time`, `timestamp`, `userId`, `mode`, `type`, `amount`,`balance`, `reference`, `remark`, `status`)
             VALUES (:date_time,:timestamp,:userId,:mode,:type,:amount,:balance,:reference,:remark,:status)";
            $mode = 'DTH Recharge';
            $type = 'debit';
            $remark = 'DTH Recharge Transaction - Requested number: ' . $_POST['number'] . ' opretor:' . Get_Operator($_POST['operator']) . ' (Amount RS: ' . $_POST['amount'] . ')';

            $status = 'success';
            
            $txn = $conn->prepare($txnsql);
            $txn->bindParam(":date_time", $date);
            $txn->bindParam(":timestamp", $timestamp);
            $txn->bindParam(":userId", $userId);
            $txn->bindParam(":mode", $mode);
            $txn->bindParam(":type", $type);
            $txn->bindParam(":amount", $total_amount);
            $txn->bindParam(":balance", $new_balance);
            $txn->bindParam(":reference", $reference);
            $txn->bindParam(":remark", $remark);
            $txn->bindParam(":status", $status);
            if ($txn->execute()) {
            
                $type = 'DTH Recharge';

                $status = 'pending';
                $remark = 'DTH Recharge Under Process';
                $ref_id = date('hismdy');
                $rch_sql = "INSERT INTO `recharges`(`web_url`,`order_id`, `number`, `operator`, `amount`, `debit_amt`, `balance`, `user_id`, `date_time`, `timestamp`, `ref_id`, `remark`, `status`, `type`) 
                            VALUES (:web_url,:order_id,:number,:operator,:amount,:debit_amt,:balance,:user_id,:date_time,:timestamp,:ref_id,:remark,:status,:type)";
                $rch_txn = $conn->prepare($rch_sql);
                
                $no = get_safe($_POST['number']);
                $op = Get_Operator($_POST['operator']);
                $rch_txn->bindParam(":web_url", $_SERVER['SERVER_NAME']);
                $rch_txn->bindParam(":order_id", $reference);
                $rch_txn->bindParam(":number", $no);
                $rch_txn->bindParam(":operator", $op);
                $rch_txn->bindParam(":amount", $amount);
                $rch_txn->bindParam(":debit_amt", $total_amount);
                $rch_txn->bindParam(":balance", $new_balance);
                $rch_txn->bindParam(":user_id", $userId);
                $rch_txn->bindParam(":date_time", $date);
                $rch_txn->bindParam(":timestamp", $timestamp);
                $rch_txn->bindParam(":ref_id", $ref_id);
                $rch_txn->bindParam(":remark", $remark);
                $rch_txn->bindParam(":status", $status);
                $rch_txn->bindParam(":type", $type);

                if ($rch_txn->execute()) {
                    $url = "https://recharge.pockket.co.in/recharge/api?username=500250&pwd=514633&circlecode=2&operatorcode=" . get_safe($_POST['operator']) . "&number=" . get_safe($_POST['number']) . "&amount=" . get_safe($_POST['amount']) . "&orderid={$reference}&format=json";
                    
                    // Initialize cURL session
                    $ch = curl_init();
                    
                    // Set cURL options
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    // Execute cURL session and get the result
                    $result = curl_exec($ch);
                    
                    // Close cURL session
                    curl_close($ch);
                    //echo $result;
                    // Convert the response to lowercase
                    $response = strtolower($result);
                    
                    // Process the response as needed
                    $operator = Get_Operator(get_safe($_POST['operator']));
                    $json_obj = json_decode($response, true);

                    $trnid = $json_obj['txid'];
                    $oprid = $json_obj['opid'];
                    $msg = $json_obj['opid'];
                    $trnstatus = $json_obj['status'];

                    if (strtolower($trnstatus) === "success") {
                        $rsql = $conn->prepare("UPDATE recharges SET ref_id=? , remark=? , status=?  WHERE order_id=?");
                        if ($rsql->execute([$oprid, 'Recharge ' . strtolower($trnstatus), 'success', $reference])) {

                            $usql = $conn->prepare("SELECT * FROM users WHERE id = ?");
                            $usql->execute([$userId]);
                            $usr_d = $usql->fetch();

                            $commissionRate = getUsersInfo(Get_Operator(get_safe($_POST['operator'])));
                            $total_commissiom = ($commissionRate / 100) * $_POST['amount'];

                            // Debit
                            $new_bal = $new_balance + $total_commissiom;
                            $sqlu = $conn->prepare("UPDATE users SET balance=?  WHERE id=?");
                            $sqlu->execute([$new_bal, $userId]);
                            // Debit
                            
                            
                            // Insert a transaction commission record
                            $commission_sql = "INSERT INTO `transactions`(`date_time`, `timestamp`, `userId`, `mode`, `type`, `amount`,`balance`, `reference`, `remark`, `status`)
                             VALUES (:date_time,:timestamp,:userId,:mode,:type,:amount,:balance,:reference,:remark,:status)";
                            $mode = 'Mobile Recharge Commission';
                            $type = 'credit';
                            $remark = 'DTH Recharge Commission Credited From' . $_POST['number'] . ', Operator:' . Get_Operator($_POST['operator']) . ', Amount Rs.' . $_POST['amount'];
                
                            $status = 'success';
                            
                            $commission = $conn->prepare($commission_sql);
                            $commission->bindParam(":date_time", $date);
                            $commission->bindParam(":timestamp", $timestamp);
                            $commission->bindParam(":userId", $userId);
                            $commission->bindParam(":mode", $mode);
                            $commission->bindParam(":type", $type);
                            $commission->bindParam(":amount", $total_commissiom);
                            $commission->bindParam(":balance", $new_bal);
                            $commission->bindParam(":reference", $reference);
                            $commission->bindParam(":remark", $remark);
                            $commission->bindParam(":status", $status);
                            $commission->execute();

                            // Redirect to the success page
                            echo '<div class="modal fade" id="recharge_success_modal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-sm modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-body text-center p-4">
                                                <img style="margin-top: -30px; margin-bottom: -30px;" src="../assets/img/rechage-page/success.gif" height="150" width="150">
                                                <h2 style="color: black;">'.strtoupper(Get_Operator($_POST['operator'])).'-'.$_POST['number'].'</h2>
                                                <h4 style="color: #131089;">
                                                    <b>DTH Recharge <br>successfully processed.</b>
                                                </h4>
                                                <p>Your reference ID is <strong class="badge bg-light text-dark shadow-sm me-2 fz-14">'.$oprid.'</strong>. Keep this ID for future reference. Thank you!</p>
                                                <div class="progress-container">
                                                    <div class="progress">
                                                        <div class="progress-bar" id="recharge_progress_bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                                <p>You will be redirected to the utility payment record page shortly.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <script>
                                    $(document).ready(function() {
                                        $("#recharge_success_modal").modal("show");
                                        setTimeout(function() {
                                            window.location.href = "rechargeRecords";
                                        }, 3000);
                                    });
                                </script>';
                            $success = 1;
                        } else {
                            echo '<script>toastr.error("Our Server! is Down!");</script>';
                            $success = 2;
                        }

                    } else if (strtolower($trnstatus) === "pending") {

                        $rsql = $conn->prepare("UPDATE recharges SET ref_id=? , remark=? , status=?  WHERE order_id=?");
                        $rsql->execute([$trnid, 'DTH Recharge ' . strtolower($trnstatus), 'Process', $reference]);
                        
                        echo '<script>toastr.info("DTH Recharge is Under Process")</script>';
                        


                    } else if (strtolower($trnstatus) === "failure"){

                        $rsql = $conn->prepare("UPDATE recharges SET remark=?, status=? WHERE order_id=?");
                        $rsql->execute(['DTH Recharge Failed', 'failed', $reference]);
                        
                        // Credit
                        $newbal = $new_balance + $total_amount;
                        $bsql = $conn->prepare("UPDATE users SET balance=?  WHERE id=?");
                        $bsql->execute([$newbal, $userId]);

                        // Insert a transaction failed creadit record
                        $FailedCreadit = "INSERT INTO `transactions`(`date_time`, `timestamp`, `userId`, `mode`, `type`, `amount`,`balance`, `reference`, `remark`, `status`)
                         VALUES (:date_time,:timestamp,:userId,:mode,:type,:amount,:balance,:reference,:remark,:status)";
                        $mode = 'DTH Recharge';
                        $type = 'credit';
                        $refunded_sts = 'DTH Recharge failed Refunded ' . $_POST['number'] . ', Opt:' . Get_Operator($_POST['operator']) . ', Amt Rs.' . $_POST['amount'] . ' On ' . date('Y-m-d H:i:s');
            
                        $status = 'success';
                        
                        $rcFailedCreadit = $conn->prepare($FailedCreadit);
                        $rcFailedCreadit->bindParam(":date_time", $date);
                        $rcFailedCreadit->bindParam(":timestamp", $timestamp);
                        $rcFailedCreadit->bindParam(":userId", $userId);
                        $rcFailedCreadit->bindParam(":mode", $mode);
                        $rcFailedCreadit->bindParam(":type", $type);
                        $rcFailedCreadit->bindParam(":amount", $total_amount);
                        $rcFailedCreadit->bindParam(":balance", $newbal);
                        $rcFailedCreadit->bindParam(":reference", $reference);
                        $rcFailedCreadit->bindParam(":remark", $refunded_sts);
                        $rcFailedCreadit->bindParam(":status", $status);
                        $rcFailedCreadit->execute();

                        echo '<script>toastr.error("Sorry DTH Recharge Failed")</script>';

                    }
                } else {
                    echo '<script>toastr.error("DTH Recharge Server is Down!")</script>';
                    $success = 4;
                }

            } else {
                echo '<script>toastr.error("Server is Down!");</script>';
                $success = 5;
            }
        }
    }
}

?>
    <div class="container-xxl flex-grow-1 container-p-y">
            <div class="col-md-12 col-lg-4 d-flex align-items-strech m-auto">
                <div class="card">
                    <form class="card-body" action="" method="POST">
                        <h6>DTH Recharge</h6>
                        <div class="row g-3">
                            <div class="col-md-12">

                                <input type="tel" name="number" id="number" placeholder="Enter Mobile Number" class="form-control" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onKeyPress="if(this.value.length==10) return false;" required>
                                
                                <div id="onactive">
                                    <select name="operator" id="opid" class="form-select select2 mt-3" required="">
                                        <option value="">Select Operator</option>
                                        <option value="ATV">Airtel Digital</option>
                                        <option value="STV">Sundirect</option>
                                        <option value="TTV">TataSky</option>
                                        <option value="VTV">Videocon</option>
                                        <option value="DTV">Dish TV</option>
                                    </select>
                                    <div class="input-group mt-3">
                                        <input class="form-control" type="tel" placeholder="Enter Amount" name="amount" id="amount" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pt-4">
                            <button type="button" class="btn btn-primary me-sm-3 me-1" data-toggle="modal" id="opstatus" onclick="GetRecharge()">Recharge Now</button>
                            <button type="reset" class="btn btn-label-secondary">Cancel</button>
                        </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-8" id="card-block">
                <div class="modal fade" id="Recharges" tabindex="-1" aria-labelledby="bottomAlignModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-sm modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="col-12">
                                    <div class="header-content position-relative d-flex align-items-center justify-content-between">
                                        <!-- Back Button -->
                                        <div class="back-button">
                                            <div class="text-start" id="opretor"></div>
                                        </div>

                                        <!-- Page Title -->
                                        <div class="page-heading">
                                            <h4 class="mb-0">Mobile Recharge</h4>
                                        </div>
                                    </div>
                                    <hr class="">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="">
                                            <div>
                                                <h6>Mobile Number</h6>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div>
                                                <h6 id="p_mobile"></h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="">
                                            <div>
                                                <h6>Operator</h6>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div>
                                                <h6 id="op"></h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="">
                                            <div>
                                                <h6>Amount</h6>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div>
                                                <h6 id="g_amount"></h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-2 mb-4 text-end">
                                        <button hrequired="required" type="submit" name="recharge" class="btn btn-primary active w-100" role="button">Continue</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    <?php
    require_once('../layouts/mainFooter.php');
    ?>


    <script>
        function GetRecharge() {
            var number = document.getElementById("number").value;
            var op_id = document.getElementById("opid").value;
            var amount = document.getElementById("amount").value;

            var opid = '';

            if (op_id == 'ATV') {
                opid = 'Airtel Digital TV';
                opimg = '<img src="../assets/img/icons/sprinticon/dth/kavya-airtel.webp" height="40" width="40" alt="Airtel Digital TV">'
            } else if (op_id == 'STV') {
                opid = 'Sundirect TV';
                opimg = '<img src="../assets/img/icons/sprinticon/dth/kavya-sundirect.avif" height="40" width="40" alt="Sundirect TV">'
            } else if (op_id == 'TTV') {
                opid = 'TataSky TV';
                opimg = '<img src="../assets/img/icons/sprinticon/dth/kavya-tatasky.webp" height="40" width="40" alt="TataSky TV">'
            } else if (op_id == 'VTV') {
                opid = 'Videocon TV';
                opimg = '<img src="../assets/img/icons/sprinticon/dth/kavya-d2h.webp" height="40" width="40" alt="Videocon TV">'
            } else if (op_id == 'DTV') {
                opid = 'Dish TV';
                opimg = '<img src="../assets/img/icons/sprinticon/dth/kavya-dishtv.webp" height="40" width="40" alt="Dish TV">'
            }

            if (number === "") {
                toastr.error("Please enter your Mobile No");
            } else if (number.length !== 10) {
                toastr.error("Enter 10 Digit Mobile No. is not valid !");
            } else if (op_id === "") {
                toastr.error("Please Select Operator!");
            } else if (amount === "") {
                toastr.error("Please Enter Amount");
            } else {
                bootstrap.Modal.getOrCreateInstance(document.getElementById('Recharges')).show();

                $('#opretor').html(opimg);
                $('#g_amount').html('₹. ' + amount);
                $('#op').html(opid);
                $('#p_mobile').html(number);
            }
        }
    </script>