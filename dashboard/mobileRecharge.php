<?php
$pageName = "Mobile Recharge"; // Replace this with the actual page name
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
            $mode = 'Mobile Recharge';
            $type = 'debit';
            $remark = 'Recharge Transaction - Requested number: ' . $_POST['number'] . ' Opretor: ' . ucfirst(Get_Operator($_POST['operator'])) . ' (Amount RS: ' . $_POST['amount'] . ')';

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
            
                $type = 'Recharge';

                $status = 'pending';
                $remark = 'Recharge Under Process';
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
                    $url = getPortalInfo('rcUrl')."/recharge/api?username=" . getPortalInfo('rcUsername') . "&pwd=" . getPortalInfo('rcToken') . "&circlecode=2&operatorcode=" . get_safe($_POST['operator']) . "&number=" . get_safe($_POST['number']) . "&amount=" . get_safe($_POST['amount']) . "&orderid={$reference}&format=json";
                    
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
                            $remark = 'Recharge Commission Credited From ' . $_POST['number'] . ', Operator: ' . Get_Operator($_POST['operator']) . ', Amount Rs. ' . $_POST['amount'];
                
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
                                                    <b>Recharge <br>successfully processed.</b>
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
                                            window.location.href = "bbpsHistory";
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
                        $rsql->execute([$trnid, 'Recharge ' . strtolower($trnstatus), 'Process', $reference]);
                        
                        echo '<script>toastr.info("Recharge is Under Process")</script>';
                        


                    } else if (strtolower($trnstatus) === "failure"){

                        $rsql = $conn->prepare("UPDATE recharges SET remark=?, status=? WHERE order_id=?");
                        $rsql->execute(['Recharge Failed', 'failed', $reference]);
                        
                        // Credit
                        $newbal = $new_balance + $total_amount;
                        $bsql = $conn->prepare("UPDATE users SET balance=?  WHERE id=?");
                        $bsql->execute([$newbal, $userId]);

                        // Insert a transaction failed creadit record
                        $FailedCreadit = "INSERT INTO `transactions`(`date_time`, `timestamp`, `userId`, `mode`, `type`, `amount`,`balance`, `reference`, `remark`, `status`)
                         VALUES (:date_time,:timestamp,:userId,:mode,:type,:amount,:balance,:reference,:remark,:status)";
                        $mode = 'Mobile Recharge';
                        $type = 'credit';
                        $refunded_sts = 'Recharge failed Refunded ' . $_POST['number'] . ', Opt: ' . Get_Operator($_POST['operator']) . ', Amt Rs. ' . $_POST['amount'] . ' On ' . date('Y-m-d H:i:s');
            
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

                        echo '<script>toastr.error("Sorry Recharge Failed")</script>';

                    }
                } else {
                    echo '<script>toastr.error("Recharge Server is Down!")</script>';
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
        <div class="row">
            <div class="col-md-12 col-lg-4 mb-4 mb-md-0">
                <div class="card">
                    <form class="card-body" action="" method="POST">
                        <h6>Mobile Recharge</h6>
                        <div class="row g-3">
                            <div class="col-md-12">

                                <input type="tel" name="number" id="number" placeholder="Enter Mobile Number" class="form-control" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onKeyPress="if(this.value.length==10) return false;" required>
                                
                                <div id="onactive" class="d-none">
                                    <select name="operator" id="opid" class="form-select select2 mt-3" required="">
                                        <option value="">Select Operator</option>
                                        <option pram="Airtel" value="A">Airtel</option>
                                        <option pram="Idea" value="V">Vodafone Idea</option>
                                        <option pram="Jio" value="RC">JIO</option>
                                    </select>

                                    <input type="text" id="Circle" name="circle" class="form-control mt-3" readonly>


                                    <div class="input-group mt-3">
                                        <input class="form-control" type="tel" placeholder="Enter Amount" name="amount" id="amount" required>
                                    </div>
                                </div>
                                <div class="loading-spinner text-center mt-3" style="display: none;">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p>Fetching Data...</p>
                                </div>
                            </div>
                        </div>
                        <div class="pt-4">
                            <button type="button" class="btn btn-primary me-sm-3 me-1" data-toggle="modal" id="opstatus" onclick="GetRecharge()" disabled>Recharge Now</button>
                            <button type="reset" class="btn btn-label-secondary">Cancel</button>
                        </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-8" id="card-block">
                <div class="card h-100">
                    <div id="plans" ></div>
                    <h3 class="card-body text-center" id="blankPage"><img src="../assets/img/rechage-page/recharge-offer-646966546544.png" height="100%" width="550"></h3>
                    <div class="alert alert-dark mb-0" role="alert" style="border-radius: 0; font-size:12px">
                        <span style="color:black"><b>Disclaimer</b></span> : While we support most recharges, we request you to verify with your operator once before proceeding.
                    </div>
                </div>
                <button id="spinbtn" class="btn btn-primary btn-card-block-overlay" style="display:none;"></button>


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
                                                <h6>Circle</h6>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div>
                                                <h6 id="circle"></h6>
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
    </div>
    <?php
    require_once('../layouts/mainFooter.php');
    ?>


    <script>
        function pickAmt(amt) {
            document.getElementById("amount").value = amt;
        }

        function getselectamount(amount) {
            $('#amount').val(amount);
        }

        function GetRecharge() {
            var number = document.getElementById("number").value;
            var op_id = document.getElementById("opid").value;
            var amount = document.getElementById("amount").value;

            var opid = '';

            if (op_id == 'A' || op_id == '15') {
                opid = 'Airtel';
            } else if (op_id == '4') {
                opid = 'BSNL';
            } else if (op_id == 'V') {
                opid = 'Idea';
            } else if (op_id == 'RC') {
                opid = 'Jio';
            }

            if (number === "") {
                swal("Please enter your Mobile No", "", "error");
            } else if (number.length !== 10) {
                swal("Enter 10 Digit Mobile No", " Mobile No. is not valid !", "info");
            } else if (op_id === "") {
                swal("Please Select Operator!", "", "");
            } else if (amount === "") {
                $("#amount_error").html('<div style="background:" class="alert alert-default alert-dismissible fade show" role="alert"><span class="alert-inner--text"><strong>Please Enter Amount</strong></span> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button> </div>');
            } else {
                bootstrap.Modal.getOrCreateInstance(document.getElementById('Recharges')).show();

                $('#p_amount').html(amount);
                $('#g_amount').html('₹. ' + amount);
                $('#p_operator').html(opid);
                $('#p_mobile').html(number);
            }
        }
    </script>

    <script>
        $(document).ready(function() {
            $('#number').keyup(function() {
                var textValue = $(this).val();

                if (textValue.length >= 10) {
                    $('.loading-spinner').show();

                    // Delay the execution of GetPlans() by 3 seconds
                    setTimeout(function() {
                        GetPlans();
                    }, 2000);

                    setTimeout(function() {
                        $('.loading-spinner').hide();
                        $.ajax({
                            url: '../system/getData.php?mobile=' + textValue + '&dataType=recharge_function',
                            type: 'GET',
                            success: function(response) {
                                var result = jQuery.parseJSON(response);

                                $('#onactive').removeClass('d-none');
                                $('#opstatus').prop('disabled', false);
                                $('#onfild').show();
                                $('#Circle').val(result['Circle']);
                                $('#circle').html(result['Circle']);

                                if (result['Operator'] == 'Vodafone Idea') {
                                    $('#opretor').html('<img src="../assets/img/rechage-page/idea.png" width="50" height="50" class="img-fluid rounded-md ch-60">');
                                    $('#opid option[pram=Idea]').attr('selected', 'selected');
                                    $('#op').html('VI Pripaid');
                                }

                                if (result['Operator'] == 'Airtel') {
                                    $('#opid option[pram=Airtel]').attr('selected', 'selected');
                                    $('#opretor').html('<img src="../assets/img/rechage-page/airtel.png" width="50" height="50" class="img-fluid">');
                                    $('#op').html('Airtel Pripaid');
                                }

                                if (result['Operator'] == 'Jio') {
                                    $('#opid option[pram=Jio]').attr('selected', 'selected');
                                    $('#opretor').html('<img src="../assets/img/rechage-page/jio.png" width="50" height="50" class="img-fluid rounded-md ch-60">');
                                    $('#op').html('JIO Pripaid');
                                }
                                if (result['postpaid'] == true) {
                                    swal('Postpaid Recharge Not Allowed',result['Operator'] + ' Postpaid','error');
                                    $('#onactive').addClass('d-none');
                                }
                                
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.log(textStatus, errorThrown);
                            }
                        });
                    }, 1000);
                }
            });
        });

        function GetPlans() {
            $("#spinbtn").click();
            var number = document.getElementById("number").value;
            var op_id = document.getElementById("opid").value;
            var scl = document.getElementById("Circle").value;
            var opid = '';

            if (op_id == 'A' || op_id == '15') {
                opid = 'Airtel';
            } else if (op_id == '4') {
                opid = 'BSNL';
            } else if (op_id == 'V') {
                opid = 'Idea';
            } else if (op_id == 'RC') {
                opid = 'Jio';
            }

            if (number === "") {
                swal("Please enter your Mobile No", "", "error");
            } else if (number.length !== 10) {
                swal("Enter 10 Digit Mobile No", " Mobile No. is not valid !", "info");
            } else if (op_id === "") {
                swal("Please Select Operator!", "", "info");
            } else {
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("plans").innerHTML = this.responseText;
                        $("#blankPage").addClass('d-none');
                    }
                };
                xhttp.open("GET", "../system/getData.php?number=" + number + "&opid=" + opid + "&scl=" + scl, true);
                xhttp.send();
            }
        }
    </script>