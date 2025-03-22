<?php
$pageName = "Physical Pan Change"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');
?>
<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['epan_flag'])) {
    $epan_flag = $_POST['epan_flag'];
    $amount = ($epan_flag === 'Y') ? $userdata['p_nsdl'] : (($epan_flag === 'N') ? $userdata['e_nsdl'] : '');
} else {
    $amount = '';
}

$aadhaar = '';
$email = '';

// Check if 'new_application' is set, 'epan_flag' and 'amount' are not empty
if (isset($_POST['CSF_application']) && !empty($_POST['epan_flag']) && !empty($amount)) {
    // Get the values of 'aadhaar_num' and 'email_id' from the POST request
    $aadhaar = $_POST["aadhaar_num"];
    $email = $_POST["email_id"];

    // Check if the 'amount' is greater than the user's balance
    if ($amount > $userdata['balance']) {
        echo "<script>
             toastr.error('Insufficient Balance.');
        </script>";
    } else {
        // Data Insertion
        // Debit the user's balance
        $new_bal = $userdata['balance'] - $amount;
        $sqlu = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
        $sqlu->execute([$new_bal, $userdata['id']]);

        // Insert a transaction record
        $txnsql = "INSERT INTO `transactions`(`date_time`, `timestamp`, `userId`, `mode`, `type`, `amount`,`balance`, `reference`, `remark`, `status`)
         VALUES (:date_time,:timestamp,:userId,:mode,:type,:amount,:balance,:reference,:remark,:status)";
        $mode = 'PAN Correction';
        $type = 'debit';
        $remark = 'Physical Change-Correction Pan Transaction - Requested by: ' . $_POST["name_card"] . ' (UID Number: ' . $aadhaar . ')';
        $status = 'success';
        $userIdd = $userdata['id'];
        $txn = $conn->prepare($txnsql);
        $txn->bindParam(":date_time", $date);
        $txn->bindParam(":timestamp", $timestamp);
        $txn->bindParam(":userId", $userIdd);
        $txn->bindParam(":mode", $mode);
        $txn->bindParam(":type", $type);
        $txn->bindParam(":amount", $amount);
        $txn->bindParam(":balance", $new_bal);
        $txn->bindParam(":reference", $reference);
        $txn->bindParam(":remark", $remark);
        $txn->bindParam(":status", $status);
        if ($txn->execute()) {
            // Set variables for NSDL status and remark
            $nsdl_status = 'process';
            $nsdl_remark = 'From Upload Panding';
            $type = 'Correction pan';

            // Insert data into 'nsdlpancard' table
            $nsdlsql = "INSERT INTO `nsdlpancard`(`web_url`,`order_id`,`type`,`cat_applicant`,`pan_number`,`l_name`, `f_name`, `m_name`, `name_card`, `dob`, `gender`, `fal_name`, `faf_name`, `fam_name`, `aadhaar_num`, `name_aadhaar`, `mob_num`, `email_id`, `address1`, `address2`, `address3`, `address4`, `address5`, `user_state`, `pincode`, `pan_type`, `r_title`, `rl_name`, `rf_name`, `rm_name`, `r_address1`, `r_address2`, `r_address3`, `r_address4`, `r_address5`, `r_state`, `r_pincode`, `city`, `area_code`, `ao_type`, `rangecode`, `aocode`, `proof_id`, `proof_add`, `proof_dob`, `application_type`, `sig_type`, `epan_flag`, `amount`,`balance`, `title`, `user_id`,`date_time`,`timestamp`,`remark`,`status`)
                VALUES (:web_url,:order_id,:type,:cat_applicant,:pan_number,:l_name,:f_name,:m_name,:name_card,:dob,:gender,:fal_name,:faf_name,:fam_name,:aadhaar_num,:name_aadhaar,:mob_num,:email_id,:address1,:address2,:address3,:address4,:address5,:user_state,:pincode,:pan_type,:r_title,:rl_name,:rf_name,:rm_name,:r_address1,:r_address2,:r_address3,:r_address4,:r_address5,:r_state,:r_pincode,:city,:area_code,:ao_type,:rangecode,:aocode,:proof_id,:proof_add,:proof_dob,:application_type,:sig_type,:epan_flag,:amount,:balance,:title,:user_id,:date_time,:timestamp,:remark,:status)";
                $nsdl = $conn->prepare($nsdlsql);
                $nsdl->bindParam(":web_url", $_SERVER['SERVER_NAME']);
                $nsdl->bindParam(":order_id", filter_var($reference,FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":type", filter_var($type,FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":cat_applicant", filter_var($_POST["cat_applicant"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":pan_number", filter_var($_POST["pan_number"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":l_name", filter_var($_POST["l_name"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":f_name", filter_var($_POST["f_name"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":m_name", filter_var($_POST["m_name"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":name_card", filter_var($_POST["name_card"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":dob", filter_var($_POST["dob"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":gender", filter_var($_POST["gender"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":fal_name", filter_var($_POST["fal_name"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":faf_name", filter_var($_POST["faf_name"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":fam_name", filter_var($_POST["fam_name"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":aadhaar_num", filter_var($aadhaar,FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":name_aadhaar", filter_var($_POST["name_aadhaar"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":mob_num", filter_var($_POST["mob_num"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":email_id", filter_var($email,FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":address1", filter_var($_POST["address1"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":address2", filter_var($_POST["address2"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":address3", filter_var($_POST["address3"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":address4", filter_var($_POST["address4"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":address5", filter_var($_POST["address5"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":user_state", filter_var($_POST["user_state"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":pincode", filter_var($_POST["pincode"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":pan_type", filter_var($_POST["pan_type"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":r_title", filter_var($_POST["r_title"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":rl_name", filter_var($_POST["rl_name"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":rf_name", filter_var($_POST["rf_name"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":rm_name", filter_var($_POST["rm_name"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":r_address1", filter_var($_POST["r_address1"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":r_address2", filter_var($_POST["r_address2"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":r_address3", filter_var($_POST["r_address3"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":r_address4", filter_var($_POST["r_address4"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":r_address5", filter_var($_POST["r_address5"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":r_state", filter_var($_POST["r_state"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":r_pincode", filter_var($_POST["r_pincode"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":city", filter_var($_POST["city"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":area_code", filter_var($_POST["area_code"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":ao_type", filter_var($_POST["ao_type"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":rangecode", filter_var($_POST["rangecode"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":aocode", filter_var($_POST["aocode"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":proof_id", filter_var($_POST["proof_id"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":proof_add", filter_var($_POST["proof_add"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":proof_dob", filter_var($_POST["proof_dob"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":application_type", filter_var($_POST["application_type"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":sig_type", filter_var($_POST["sig_type"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":epan_flag", filter_var($_POST["epan_flag"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":amount", filter_var($amount,FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":balance", filter_var($new_bal,FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":title", filter_var($_POST["title"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":user_id", filter_var($userdata["id"],FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":date_time", $date);
                $nsdl->bindParam(":timestamp", $timestamp);
                $nsdl->bindParam(":remark", filter_var($nsdl_remark,FILTER_SANITIZE_STRING));
                $nsdl->bindParam(":status", filter_var($nsdl_status,FILTER_SANITIZE_STRING));
                if($nsdl->execute()){
                    
                 echo '<div class="modal fade" id="pan_success_modal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-sm modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-body text-center p-4">
                                        <h2 style="color: black;">Thanks! '.ucfirst($_POST["name_card"]).'</h2>
                                        <h4 style="color: #131089;">
                                            <b>Your PAN application has been submitted successfully.</b>
                                        </h4>
                                        <p>Your reference ID is <strong class="badge bg-light text-dark shadow-sm me-2 fz-14">'.$reference.'</strong> Keep this ID for future reference. Thank you!</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <script>
                    
                $(document).ready(function() {
                        $("#pan_success_modal").modal("show");
                        setTimeout(function() {
                    window.location.href = "";
                }, 5000);
                    });

                </script>';
            } else {
                echo "<script>toastr.error('Form submission failed.', 'error');</script>";
                redirect(1500,'');
            }
        }
    }
}
}
?>
<div class="container-xxl flex-grow-1 container-p-y">
        <div style="color:black; background: #f58f2d; panding:5px">
	    <h6 class="text-center p-2"><b style="color:black;">Application for Change / Correction in PAN Data<br class="mt-1">Applicants has to submit the printed signed application forms at nearest || Under section 139A of the Income-Tax Act, 1961</b></h6>
	</div>
<form id="msform" class="user" action="" method="POST" onsubmit="return Pan_new();">
    <table width="1000px" style="font-size: small; font-family: Arial; color:black;">
        <tbody>
            <tr>
                <td>
                    <table width="1000px" align="center">
                        <tbody>
                            <tr>
                                <th align="left" style="font-size: small; font-family: Arial; border: none;">Old Pan Number</th>
                                <td>
                                    <input name="cat_applicant" type="hidden" value="Individual">		
                                    <input id="pan_number" name="pan_number" style="height: 22px" maxlength="10" onchange="return validatePAN();" onblur="this.value = this.value.toUpperCase();" required>
                                </td>
                                <td align="left">
                                    <select name="title" id="title" required style="height: 22px">
                                        <option value="">Select Application Type</option>
                                        <option value="1">Mr / Shri</option>
                                        <option value="2">Mrs, Shrimati</option>
                                        <option value="3">Mis, Kumari</option>
                                    </select>
                                </td>
                                <td>
                                    <input id="date" name="date" readonly="readonly" style="height: 22px" type="text" value="<?php echo date("d-m-Y", strtotime($date)); ?>" size="25" maxlength="10">
                                </td>
                            </tr>
                            <tr>
                                <th align="left" style="border: none;"></th>
                                <td align="left" style="font-size: small;">Last Name/ Surname</td>
                                <td align="left" style="font-size: small;">First Name</td>
                                <td align="left" style="font-size: small;">Middle Name</td>
                            </tr>
                            <tr>
                                <th align="left" style="font-size: small; font-family: Arial; border: none;">Applicant's Name</th>
                                <td align="left">
                                    <input id="l_name" name="l_name" onkeyup="GetName(this.value)" oninput="this.value = this.value.toUpperCase()" required onblur="this.value = this.value.toUpperCase();" tabindex="2" type="text" value="" size="25" maxlength="75" style="height: 22px">
                                </td>
                                <td align="left">
                                    <input id="f_name" name="f_name" onkeyup="GetName(this.value)" oninput="this.value = this.value.toUpperCase()" onblur="this.value = this.value.toUpperCase();" tabindex="3" type="text" value="" size="25" maxlength="25" style="height: 22px">
                                </td>
                                <td align="left">
                                    <input id="m_name" name="m_name" onkeyup="GetName(this.value)" oninput="this.value = this.value.toUpperCase()" onblur="this.value = this.value.toUpperCase();" tabindex="4" type="text" value="" size="25" maxlength="25" style="height: 22px">
                                </td>
                            </tr>
                            <tr>
                                <th class="mt-2" align="left" style="font-size: small; font-family: Arial; border: none;">Name on Card</th>
                                <td align="left" colspan="3">
                                    <input type="text" class="mt-1" style="width: 550px; height: 22px" id="name_card" placeholder="" name="name_card" oninput="this.value = this.value.toUpperCase()" onblur="this.value = this.value.toUpperCase();" readonly>
                                </td>
                            </tr>
                            <tr>
                                <th class="mt-2" align="left" style="font-size: small; font-family: Arial; border: none;">Gander</th>
                                <td align="left">
                                    <select name="gender" id="gender" class="mt-1" style="height: 22px" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="TransGender">Transgender</option>
                                    </select>
                                </td>



                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <th align="left" style="font-size: small; font-family: Arial; border: none;" colspan="3">
                                    Whether mother is a single parent and you wish to apply for PAN by furnishing the name of your mother only?(please tick as applicable)
                                </th>
                                <td align="left">
                                    <input id="ymother" name="singleParentFlag" tabindex="26" size="25" maxlength="25" disabled="disabled" type="radio" value="Y">Yes <input id="nmother" name="singleParentFlag" tabindex="27" size="25" maxlength="25" checked="checked" disabled="disabled" type="radio" value="N">No
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <th align="left" style="border: none;"></th>
                                <td align="left" style="font-size: small;">Father's Last Name</td>
                                <td align="left" style="font-size: small;">First Name</td>
                                <td align="left" style="font-size: small;">Middle Name</td>
                            </tr>
                            <tr>
                                <th align="left" style="font-size: small; font-family: Arial; border: none;">Father's Name</th>
                                <td align="left">
                                    <input id="fal_name" name="fal_name" tabindex="6" oninput="this.value = this.value.toUpperCase()" required onblur="this.value = this.value.toUpperCase();" type="text" value="" size="25" maxlength="25" style="height: 22px">
                                </td>
                                <td align="left">
                                    <input id="faf_name" name="faf_name" tabindex="7" oninput="this.value = this.value.toUpperCase()" onblur="this.value = this.value.toUpperCase();" type="text" value="" size="25" maxlength="25" style="height: 22px">
                                </td>
                                <td align="left">
                                    <input id="fam_name" name="fam_name" tabindex="8" oninput="this.value = this.value.toUpperCase()" onblur="this.value = this.value.toUpperCase();" type="text" value="" size="25" maxlength="25" style="height: 22px">
                                </td>
                            </tr>
                            <!--- Added for Single Mother Parent:: start---->
                            <tr>
                                <th align="left" style="border: none;"></th>
                                <td align="left" style="font-size: small;">Mother's Last Name</td>
                                <td align="left" style="font-size: small;">First Name</td>
                                <td align="left" style="font-size: small;">Middle Name</td>
                            </tr>
                            <tr>
                                <th align="left" style="font-size: small; font-family: Arial; border: none;">Mother's Name</th>
                                <td align="left">
                                    <input id="opaMotherLastName" name="opaMotherLastName" tabindex="8" type="text" style="height: 22px" value="" size="25" maxlength="25" disabled="">
                                </td>
                                <td align="left">
                                    <input id="opaMotherFirstName" name="opaMotherFirstName" tabindex="8" type="text" style="height: 22px" value="" size="25" maxlength="25" disabled="">
                                </td>
                                <td align="left">
                                    <input id="opaMotherMiddleName" name="opaMotherMiddleName" tabindex="8" type="text" style="height: 22px" value="" size="25" maxlength="25" disabled="">
                                </td>
                            </tr>
                            <!--- Added for Single Mother Parent:: end---->
                            <tr>
                                <th align="left" style="font-size: small; font-family: Arial; border: none;">Date of Birth / Incorporation</th>
                                <td align="left">
                                    <input type="hidden" class="form-control" placeholder="dd/MM/yyyy" id="minor" name="application_type" value="normal">
                                    <input class="mt-1" id="dob" name="dob" tabindex="9" onclick=" var v = this.value; if (v.match(-/^\d{2}$/) !== null) { this.value = v + '-'; } else if (v.match(/^\d{2}\/\d{2}$/) !== null) { this.value = v + ' '; }" onkeyup=" var v = this.value; if (v.match(/^\d{2}$/) !== null) { this.value = v + '/'; } else if (v.match(/^\d{2}\/\d{2}$/) !== null) { this.value = v + '/'; }" maxlength="10" id="dob-input" required type="text" style="height: 22px" size="10" maxlength="10" autocomplete="off"> (dd/MM/yyyy)
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td style="font-size: small;">ISD CODE <br>
                                </td>
                                <td style="font-size: small;">STD CODE <br>
                                </td>
                                <td style="font-size: small;"> Telephone Number <br>
                                </td>
                            </tr>
                            <tr>
                                <th align="left" style="font-size: small; font-family: Arial; border: none;"> Contact Details </th>
                                <td>
                                    <input id="tel_num_isdcode" name="tel_num_isdcode" style="height: 22px" tabindex="10" type="text" value="91" maxlength="7" autocomplete="off" readonly> -
                                </td>
                                <td>
                                    <input id="tel_num_stdcode" name="tel_num_stdcode" style="height: 22px" tabindex="11" type="text" value="OPTIONAL" maxlength="7" autocomplete="off" readonly> -
                                </td>
                                <td>
                                    <input id="mob_num" name="mob_num" oninput="this.value = this.value.toUpperCase()" onblur="this.value = this.value.toUpperCase();" maxlength="10" required style="height: 22px" tabindex="12" type="text" autocomplete="off">
                                </td>
                            </tr>
                            <tr>
                                <th align="left" style="font-size: small; font-family: Arial; border: none;">Email ID</th>
                                <td align="left" colspan="3" "width:="" 202px"="">
                                    <input class="mt-1" id="email_id" name="email_id" tabindex="13" style="width: 550px; height: 22px" type="text" value="" onblur="this.value = this.value.toUpperCase();" required size="92" maxlength="75">
                                </td>
                            </tr>
                            <tr>
                                <th align="left" style="border: none;"></th>
                                <td align="left" style="font-size: small;">Building / Village Name</td>
                                <td align="left" style="font-size: small;">Road/Street/Lane/Post Office</td>
                                <td align="left" style="font-size: small;">Area/Locality/Sub-Division</td>
                            </tr>
                            <tr>
                                <th align="left" style="font-size: small; font-family: Arial; border: none;">Card Dispatched Address</th>
                                <td align="left">
                                    <input type="text" style="height: 22px" size="25" id="address2" name="address2" oninput="this.value = this.value.toUpperCase()" onblur="this.value = this.value.toUpperCase();" maxlength="25" required>
                                </td>
                                <td align="left">
                                    <input type="text" style="height: 22px" size="25" id="address3" name="address3" oninput="this.value = this.value.toUpperCase()" onblur="this.value = this.value.toUpperCase();" maxlength="25" required>
                                </td>
                                <td align="left">
                                    <input type="text" style="height: 22px" size="25" id="address4" name="address4" oninput="this.value = this.value.toUpperCase()" onblur="this.value = this.value.toUpperCase();" maxlength="25" required>
                                </td>
                            </tr>
                            <tr>
                                <th align="left" style="border: none;"></th>
                                <td align="left" style="font-size: small;">Town/City/District</td>
                                <td align="left" style="font-size: small;">Center / Shop Name.</td>
                            </tr>
                            <tr>
                                <th align="left" style="font-size: small; font-family: Arial; border: none;"></th>
                                <td align="left">
                                    <input type="text" style="height: 22px" size="25" id="address5" name="address5" oninput="this.value = this.value.toUpperCase()" onblur="this.value = this.value.toUpperCase();" maxlength="25" required>
                                </td>
                                <td align="left">
                                    <input type="text" style="height: 22px" size="25" id="address1" name="address1" oninput="this.value = this.value.toUpperCase()" onblur="this.value = this.value.toUpperCase();" maxlength="25" required>
                                </td>
                            </tr>
                            <tr>
                                <th style="border: none;">Number of Documents</th>
                                <td>
                                    <select style="height: 22px" id="nofdoc" name="nofdoc" required tabindex="16">
                                        <option value="Please Select">Please Select</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th style="border: none;">PAN Card dispatched State</th>
                                <td>
                                    <select style="height: 22px" id="user_state" name="user_state" required tabindex="16">
                                        <option value="Please Select">Please Select</option>
                                        <option value="1">ANDAMAN AND NICOBAR ISLANDS</option>
                                        <option value="2">ANDHRA PRADESH</option>
                                        <option value="3">ARUNACHAL PRADESH</option>
                                        <option value="4">ASSAM</option>
                                        <option value="5">BIHAR</option>
                                        <option value="6">CHANDIGARH</option>
                                        <option value="33">CHHATTISGARH</option>
                                        <option value="7">DADRA &amp; NAGAR HAVELI</option>
                                        <option value="8">DAMAN &amp; DIU</option>
                                        <option value="9">DELHI</option>
                                        <option value="10">GOA</option>
                                        <option value="11">GUJARAT</option>
                                        <option value="12">HARYANA</option>
                                        <option value="13">HIMACHAL PRADESH</option>
                                        <option value="14">JAMMU AND KASHMIR</option>
                                        <option value="35">JHARKHAND</option>
                                        <option value="15">KARNATAKA</option>
                                        <option value="16">KERALA</option>
                                        <option value="37">LADAKH</option>
                                        <option value="17">LAKHSWADEEP</option>
                                        <option value="18">MADHYA PRADESH</option>
                                        <option value="19">MAHARASHTRA</option>
                                        <option value="20">MANIPUR</option>
                                        <option value="21">MEGHALAYA</option>
                                        <option value="22">MIZORAM</option>
                                        <option value="23">NAGALAND</option>
                                        <option value="24">ODISHA</option>
                                        <option value="88">OUTSIDE INDIA</option>
                                        <option value="25">PONDICHERRY</option>
                                        <option value="26">PUNJAB</option>
                                        <option value="27">RAJASTHAN</option>
                                        <option value="28">SIKKIM</option>
                                        <option value="29">TAMIL NADU</option>
                                        <option value="36">TELANGANA</option>
                                        <option value="30">TRIPURA</option>
                                        <option value="31">UTTAR PRADESH</option>
                                        <option value="34">UTTARAKHAND</option>
                                        <option value="32">WEST BENGAL</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th style="border: none;">District / Town</th>
                                <td>
                                    <select style="height: 22px" id="city" id="id_h5_single" name="city" onchange="city_aoCode()" required="" tabindex="16">
                                        <option value="">Select City</option>
                                        <option value="ALWAR">ALWAR</option>
                                        <option value="BEHROR">BEHROR</option>
                                        <option value="ABU">ABU</option>
                                        <option value="ADILABAD">ADILABAD</option>
                                        <option value="ADONI">ADONI</option>
                                        <option value="AGAR">AGAR</option>
                                        <option value="AGARTALA">AGARTALA</option>
                                        <option value="AGRA">AGRA</option>
                                        <option value="AHMEDABAD">AHMEDABAD</option>
                                        <option value="AHMEDNAGAR">AHMEDNAGAR</option>
                                        <option value="AIZAWL">AIZAWL</option>
                                        <option value="AJMER">AJMER</option>
                                        <option value="AKOLA">AKOLA</option>
                                        <option value="ALAPPUZHA">ALAPPUZHA</option>
                                        <option value="ALIGARH">ALIGARH</option>
                                        <option value="ALIPURDUAR">ALIPURDUAR</option>
                                        <option value="ALLAHABAD">ALLAHABAD</option>
                                        <option value="ALMORA">ALMORA</option>
                                        <option value="ALUVA">ALUVA</option>
                                        <option value="ABOHAR">ABOHAR</option>
                                        <option value="AMALAPURAM">AMALAPURAM</option>
                                        <option value="AMBALA">AMBALA</option>
                                        <option value="AMBEDKAR NAGAR">AMBEDKAR NAGAR</option>
                                        <option value="AMBIKAPUR">AMBIKAPUR</option>
                                        <option value="AMETHI">AMETHI</option>
                                        <option value="AMRAVATI">AMRAVATI</option>
                                        <option value="AMRELI">AMRELI</option>
                                        <option value="AMRITSAR">AMRITSAR</option>
                                        <option value="AMROHA">AMROHA</option>
                                        <option value="ANAKAPALLI">ANAKAPALLI</option>
                                        <option value="ANAND">ANAND</option>
                                        <option value="ANANTAPUR">ANANTAPUR</option>
                                        <option value="ANANTNAG">ANANTNAG</option>
                                        <option value="ANDAMAN">ANDAMAN & NICOBAR</option>
                                        <option value="ANGUL">ANGUL</option>
                                        <option value="ARA">ARA</option>
                                        <option value="ASANSOL">ASANSOL</option>
                                        <option value="ASHOK NAGAR">ASHOK NAGAR</option>
                                        <option value="AURAIYA">AURAIYA</option>
                                        <option value="AURANGABAD">AURANGABAD</option>
                                        <option value="AZAMGARH">AZAMGARH</option>
                                        <option value="BADAUN">BADAUN</option>
                                        <option value="BADDI">BADDI</option>
                                        <option value="BAGALKOT">BAGALKOT</option>
                                        <option value="BAHRAICH">BAHRAICH</option>
                                        <option value="BAJPUR">BAJPUR</option>
                                        <option value="BALAGHAT">BALAGHAT</option>
                                        <option value="BALASORE">BALASORE</option>
                                        <option value="BALLIA">BALLIA</option>
                                        <option value="BALOTRA">BALOTRA</option>
                                        <option value="BALURGHAT">BALURGHAT</option>
                                        <option value="BANDA">BANDA</option>
                                        <option value="BANGALORE">BANGALORE</option>
                                        <option value="BANKURA">BANKURA</option>
                                        <option value="BANSWARA">BANSWARA</option>
                                        <option value="BAPATLA">BAPATLA</option>
                                        <option value="BARABANKI">BARABANKI</option>
                                        <option value="BARAMULLA">BARAMULLA</option>
                                        <option value="BARAN">BARAN</option>
                                        <option value="BARAUT">BARAUT</option>
                                        <option value="BARDOLI">BARDOLI</option>
                                        <option value="BAREILLY">BAREILLY</option>
                                        <option value="BARGARH">BARGARH</option>
                                        <option value="BARIPADA">BARIPADA</option>
                                        <option value="BARMER">BARMER</option>
                                        <option value="BARNALA">BARNALA</option>
                                        <option value="BARODA">BARODA</option>
                                        <option value="BARPETA">BARPETA</option>
                                        <option value="BASTI">BASTI</option>
                                        <option value="BATALA">BATALA</option>
                                        <option value="BATHINDA">BATHINDA</option>
                                        <option value="BEAWER">BEAWER</option>
                                        <option value="BEED">BEED</option>
                                        <option value="BEGUSARAI">BEGUSARAI</option>
                                        <option value="BEHERAMPUR">BEHERAMPUR</option>
                                        <option value="BELGAUM">BELGAUM</option>
                                        <option value="BELLARY">BELLARY</option>
                                        <option value="BETTIAH">BETTIAH</option>
                                        <option value="BETUL">BETUL</option>
                                        <option value="BHADOHI">BHADOHI</option>
                                        <option value="BHADRAK">BHADRAK</option>
                                        <option value="BHAGALPUR">BHAGALPUR</option>
                                        <option value="BHANDARA">BHANDARA</option>
                                        <option value="BHARATPUR">BHARATPUR</option>
                                        <option value="BHARUCH">BHARUCH</option>
                                        <option value="BHATAPARA">BHATAPARA</option>
                                        <option value="BHAVNAGAR">BHAVNAGAR</option>
                                        <option value="BHAWANIPATNA">BHAWANIPATNA</option>
                                        <option value="BHILAI">BHILAI</option>
                                        <option value="BHILWARA">BHILWARA</option>
                                        <option value="BHIMAVARAM">BHIMAVARAM</option>
                                        <option value="BHIWADI">BHIWADI</option>
                                        <option value="BHIWANI">BHIWANI</option>
                                        <option value="BHOPAL">BHOPAL</option>
                                        <option value="BHUBANESWAR">BHUBANESWAR</option>
                                        <option value="BIDAR">BIDAR</option>
                                        <option value="BIHARSHARIF">BIHARSHARIF</option>
                                        <option value="BIJAPUR">BIJAPUR</option>
                                        <option value="BIJNORE">BIJNORE</option>
                                        <option value="BIKANER">BIKANER</option>
                                        <option value="BILASPUR">BILASPUR</option>
                                        <option value="BINA">BINA</option>
                                        <option value="BOKARO">BOKARO</option>
                                        <option value="BOLANGIR">BOLANGIR</option>
                                        <option value="BONGAIGAON">BONGAIGAON</option>
                                        <option value="BULANDSHAHAR">BULANDSHAHAR</option>
                                        <option value="BUNDI">BUNDI</option>
                                        <option value="BURDWAN">BURDWAN</option>
                                        <option value="BURHANPUR">BURHANPUR</option>
                                        <option value="BUXAR">BUXAR</option>
                                        <option value="BYRNIHAT">BYRNIHAT</option>
                                        <option value="CHAMARAJA NAGAR">CHAMARAJA NAGAR</option>
                                        <option value="CHANDAUSI">CHANDAUSI</option>
                                        <option value="CHANDIGARH">CHANDIGARH</option>
                                        <option value="CHANDRAPUR">CHANDRAPUR</option>
                                        <option value="CHAPRA">CHAPRA</option>
                                        <option value="CHENNAI">CHENNAI</option>
                                        <option value="CHHATTARPUR">CHHATTARPUR</option>
                                        <option value="CHHINDWARA">CHHINDWARA</option>
                                        <option value="CHIKABALLAPUR">CHIKABALLAPUR</option>
                                        <option value="CHIKMAGALUR">CHIKMAGALUR</option>
                                        <option value="CHIRALA">CHIRALA</option>
                                        <option value="CHITRADURGA">CHITRADURGA</option>
                                        <option value="CHITTOOR">CHITTOOR</option>
                                        <option value="CHITTORGARH">CHITTORGARH</option>
                                        <option value="CHURU">CHURU</option>
                                        <option value="COIMBATORE">COIMBATORE</option>
                                        <option value="COOCH BEHAR">COOCH BEHAR</option>
                                        <option value="CUDDALORE">CUDDALORE</option>
                                        <option value="CUTTACK">CUTTACK</option>
                                        <option value="DAHOD">DAHOD</option>
                                        <option value="DALHOUSIE">DALHOUSIE</option>
                                        <option value="DAMAN">DAMAN</option>
                                        <option value="DAMOH">DAMOH</option>
                                        <option value="DARBHANGA">DARBHANGA</option>
                                        <option value="DARJEELING">DARJEELING</option>
                                        <option value="DASUYA">DASUYA</option>
                                        <option value="DAUSA">DAUSA</option>
                                        <option value="DAVANAGERE">DAVANAGERE</option>
                                        <option value="DEHRADUN">DEHRADUN</option>
                                        <option value="DELHI">DELHI</option>
                                        <option value="DEOBAND">DEOBAND</option>
                                        <option value="DEOGARH">DEOGARH</option>
                                        <option value="DEORIA">DEORIA</option>
                                        <option value="DEWAS">DEWAS</option>
                                        <option value="DHAMPUR">DHAMPUR</option>
                                        <option value="DHAMTARI">DHAMTARI</option>
                                        <option value="DHANBAD">DHANBAD</option>
                                        <option value="DHAR">DHAR</option>
                                        <option value="DHARAMSHALA">DHARAMSHALA</option>
                                        <option value="DHARMANAGAR">DHARMANAGAR</option>
                                        <option value="DHARMAPURI">DHARMAPURI</option>
                                        <option value="DHENKANAL">DHENKANAL</option>
                                        <option value="DHUBRI">DHUBRI</option>
                                        <option value="DHULE">DHULE</option>
                                        <option value="DIBRUGARH">DIBRUGARH</option>
                                        <option value="DIGBOI">DIGBOI</option>
                                        <option value="DIMAPUR">DIMAPUR</option>
                                        <option value="DINDIGUL">DINDIGUL</option>
                                        <option value="DULIAJAN">DULIAJAN</option>
                                        <option value="DUMKA">DUMKA</option>
                                        <option value="DUNGARPUR">DUNGARPUR</option>
                                        <option value="DURGAPUR">DURGAPUR</option>
                                        <option value="DWARKA">DWARKA</option>
                                        <option value="ELURU">ELURU</option>
                                        <option value="ERODE">ERODE</option>
                                        <option value="ETAH">ETAH</option>
                                        <option value="ETAWAH">ETAWAH</option>
                                        <option value="FAIZABAD">FAIZABAD</option>
                                        <option value="FARIDABAD">FARIDABAD</option>
                                        <option value="FARIDKOT">FARIDKOT</option>
                                        <option value="FARRUKHABAD">FARRUKHABAD</option>
                                        <option value="FATEHABAD">FATEHABAD</option>
                                        <option value="FATEHPUR">FATEHPUR</option>
                                        <option value="FEROZEPUR">FEROZEPUR</option>
                                        <option value="FIROZABAD">FIROZABAD</option>
                                        <option value="GADAG">GADAG</option>
                                        <option value="GANDHIDHAM">GANDHIDHAM</option>
                                        <option value="GANDHINAGAR">GANDHINAGAR</option>
                                        <option value="GANGTOK">GANGTOK</option>
                                        <option value="GAUTAM BUDH NAGAR">GAUTAM BUDH NAGAR</option>
                                        <option value="GAYA">GAYA</option>
                                        <option value="GHAZIABAD">GHAZIABAD</option>
                                        <option value="GHAZIPUR">GHAZIPUR</option>
                                        <option value="GIRIDIH">GIRIDIH</option>
                                        <option value="GOALPARA">GOALPARA</option>
                                        <option value="GOBINDGARH">GOBINDGARH</option>
                                        <option value="GODHRA">GODHRA</option>
                                        <option value="GOKAK">GOKAK</option>
                                        <option value="GOLAGHAT">GOLAGHAT</option>
                                        <option value="GONDA">GONDA</option>
                                        <option value="GONDIA">GONDIA</option>
                                        <option value="GORAKHPUR">GORAKHPUR</option>
                                        <option value="GUDIWADA">GUDIWADA</option>
                                        <option value="GUDUR">GUDUR</option>
                                        <option value="GULBARGA">GULBARGA</option>
                                        <option value="GUNA">GUNA</option>
                                        <option value="GUNTAKAL">GUNTAKAL</option>
                                        <option value="GUNTUR">GUNTUR</option>
                                        <option value="GURDASPUR">GURDASPUR</option>
                                        <option value="GURGAON">GURGAON</option>
                                        <option value="GURUVAYOOR">GURUVAYOOR</option>
                                        <option value="GUWAHATI">GUWAHATI</option>
                                        <option value="GWALIOR">GWALIOR</option>
                                        <option value="HALDIA">HALDIA</option>
                                        <option value="HALDWANI">HALDWANI</option>
                                        <option value="HAMIRPUR">HAMIRPUR</option>
                                        <option value="HANUMANGARH">HANUMANGARH</option>
                                        <option value="HAPUR">HAPUR</option>
                                        <option value="HARDA">HARDA</option>
                                        <option value="HARDOI">HARDOI</option>
                                        <option value="HARDWAR">HARDWAR</option>
                                        <option value="HASSAN">HASSAN</option>
                                        <option value="HATHRAS">HATHRAS</option>
                                        <option value="HAVERI">HAVERI</option>
                                        <option value="HAZARIBAGH">HAZARIBAGH</option>
                                        <option value="HIMMATNAGAR">HIMMATNAGAR</option>
                                        <option value="HINDUPUR">HINDUPUR</option>
                                        <option value="HINGOLI">HINGOLI</option>
                                        <option value="HISSAR">HISSAR</option>
                                        <option value="HOOGHLY">HOOGHLY</option>
                                        <option value="HOOGLY">HOOGLY</option>
                                        <option value="HOSHIARPUR">HOSHIARPUR</option>
                                        <option value="HOSPET">HOSPET</option>
                                        <option value="HOSUR">HOSUR</option>
                                        <option value="HUBLI">HUBLI</option>
                                        <option value="HYDERABAD">HYDERABAD</option>
                                        <option value="ICHALKARANJI">ICHALKARANJI</option>
                                        <option value="IMPHAL">IMPHAL</option>
                                        <option value="INDORE">INDORE</option>
                                        <option value="ITANAGAR">ITANAGAR</option>
                                        <option value="ITARSI">ITARSI</option>
                                        <option value="JABALPUR">JABALPUR</option>
                                        <option value="JAGDALPUR">JAGDALPUR</option>
                                        <option value="JAGRAON">JAGRAON</option>
                                        <option value="JAIPUR">JAIPUR</option>
                                        <option value="JAISALMER">JAISALMER</option>
                                        <option value="JAJPUR">JAJPUR</option>
                                        <option value="JALANDHAR">JALANDHAR</option>
                                        <option value="JALGAON">JALGAON</option>
                                        <option value="JALNA">JALNA</option>
                                        <option value="JALORE">JALORE</option>
                                        <option value="JALPAIGURI">JALPAIGURI</option>
                                        <option value="JAMMU">JAMMU</option>
                                        <option value="JAMNAGAR">JAMNAGAR</option>
                                        <option value="JAMSHEDPUR">JAMSHEDPUR</option>
                                        <option value="JANJGIR CHAMPA">JANJGIR CHAMPA</option>
                                        <option value="JAUNPUR">JAUNPUR</option>
                                        <option value="JEYPORE">JEYPORE</option>
                                        <option value="JHABUA">JHABUA</option>
                                        <option value="JHALAWAR">JHALAWAR</option>
                                        <option value="JHANSI">JHANSI</option>
                                        <option value="JHARSUGUDA">JHARSUGUDA</option>
                                        <option value="JHUNJHUNU">JHUNJHUNU</option>
                                        <option value="JIND">JIND</option>
                                        <option value="JODHPUR">JODHPUR</option>
                                        <option value="JORHAT">JORHAT</option>
                                        <option value="JUNAGADH">JUNAGADH</option>
                                        <option value="KADAPA">KADAPA</option>
                                        <option value="KAITHAL">KAITHAL</option>
                                        <option value="KAKINADA">KAKINADA</option>
                                        <option value="KALINGPONG">KALINGPONG</option>
                                        <option value="KALPETTA">KALPETTA</option>
                                        <option value="KALYAN">KALYAN</option>
                                        <option value="KANCHEEPURAM">KANCHEEPURAM</option>
                                        <option value="KANKER">KANKER</option>
                                        <option value="KANNAUJ">KANNAUJ</option>
                                        <option value="KANNUR">KANNUR</option>
                                        <option value="KANPUR">KANPUR</option>
                                        <option value="KAPURTHALA">KAPURTHALA</option>
                                        <option value="KARAIKUDI">KARAIKUDI</option>
                                        <option value="KARAULI">KARAULI</option>
                                        <option value="KARIMGANJ">KARIMGANJ</option>
                                        <option value="KARIMNAGAR">KARIMNAGAR</option>
                                        <option value="KARNAL">KARNAL</option>
                                        <option value="KARUR">KARUR</option>
                                        <option value="KARWAR">KARWAR</option>
                                        <option value="KASARGOD">KASARGOD</option>
                                        <option value="KASGANJ">KASGANJ</option>
                                        <option value="KASHIPUR">KASHIPUR</option>
                                        <option value="KATHUA">KATHUA</option>
                                        <option value="KATIHAR">KATIHAR</option>
                                        <option value="KATNI">KATNI</option>
                                        <option value="KATRA">KATRA</option>
                                        <option value="KAUSHAMBI">KAUSHAMBI</option>
                                        <option value="KAWARDHA">KAWARDHA</option>
                                        <option value="KENDRAPADA">KENDRAPADA</option>
                                        <option value="KEONJHAR">KEONJHAR</option>
                                        <option value="KHAMGAON">KHAMGAON</option>
                                        <option value="KHAMMAM">KHAMMAM</option>
                                        <option value="KHANDWA">KHANDWA</option>
                                        <option value="KHANNA">KHANNA</option>
                                        <option value="KHARGONE">KHARGONE</option>
                                        <option value="KHATAULI">KHATAULI</option>
                                        <option value="KHATIMA">KHATIMA</option>
                                        <option value="KHURDA">KHURDA</option>
                                        <option value="KISHENGARH">KISHENGARH</option>
                                        <option value="KOCHI">KOCHI</option>
                                        <option value="KODERMA">KODERMA</option>
                                        <option value="KOLAR">KOLAR</option>
                                        <option value="KOLHAPUR">KOLHAPUR</option>
                                        <option value="KOLKATA">KOLKATA</option>
                                        <option value="KOLLAM">KOLLAM</option>
                                        <option value="KOPPAL">KOPPAL</option>
                                        <option value="KORBA">KORBA</option>
                                        <option value="KOTA">KOTA</option>
                                        <option value="KOTDWAR">KOTDWAR</option>
                                        <option value="KOTHAGUDEM">KOTHAGUDEM</option>
                                        <option value="KOTTAYAM">KOTTAYAM</option>
                                        <option value="KOZHIKODE">KOZHIKODE</option>
                                        <option value="KRISHNAGIRI">KRISHNAGIRI</option>
                                        <option value="KUDAL">KUDAL</option>
                                        <option value="KULLU">KULLU</option>
                                        <option value="KUMBAKONAM">KUMBAKONAM</option>
                                        <option value="KURNOOL">KURNOOL</option>
                                        <option value="KURUKSHETRA">KURUKSHETRA</option>
                                        <option value="KUSHINAGAR">KUSHINAGAR</option>
                                        <option value="LAKHIMPUR">LAKHIMPUR</option>
                                        <option value="LAKHISARAI">LAKHISARAI</option>
                                        <option value="LALITPUR">LALITPUR</option>
                                        <option value="LATUR">LATUR</option>
                                        <option value="LUCKNOW">LUCKNOW</option>
                                        <option value="LUDHIANA">LUDHIANA</option>
                                        <option value="LUNAWADA">LUNAWADA</option>
                                        <option value="MACHILIPATNAM">MACHILIPATNAM</option>
                                        <option value="MADANAPALLE">MADANAPALLE</option>
                                        <option value="MADHUBANI">MADHUBANI</option>
                                        <option value="MADURAI">MADURAI</option>
                                        <option value="MAHABUBNAGAR">MAHABUBNAGAR</option>
                                        <option value="MAHASAMUND">MAHASAMUND</option>
                                        <option value="MAINPURI">MAINPURI</option>
                                        <option value="MAKRANA">MAKRANA</option>
                                        <option value="MALDA">MALDA</option>
                                        <option value="MALEGAON">MALEGAON</option>
                                        <option value="MALERKOTLA">MALERKOTLA</option>
                                        <option value="MANCHIRIYAL">MANCHIRIYAL</option>
                                        <option value="MANDI">MANDI</option>
                                        <option value="MANDLA">MANDLA</option>
                                        <option value="MANDSAUR">MANDSAUR</option>
                                        <option value="MANDYA">MANDYA</option>
                                        <option value="MANENDRAGARH">MANENDRAGARH</option>
                                        <option value="MANGALDOI">MANGALDOI</option>
                                        <option value="MANGALORE">MANGALORE</option>
                                        <option value="MANSA">MANSA</option>
                                        <option value="MARGAO">MARGAO</option>
                                        <option value="MARIGAON">MARIGAON</option>
                                        <option value="MATHURA">MATHURA</option>
                                        <option value="MAU">MAU</option>
                                        <option value="MEERUT">MEERUT</option>
                                        <option value="MEHSANA">MEHSANA</option>
                                        <option value="MERCARA">MERCARA</option>
                                        <option value="MIDNAPUR">MIDNAPUR</option>
                                        <option value="MIRZAPUR">MIRZAPUR</option>
                                        <option value="MODASA">MODASA</option>
                                        <option value="MOGA">MOGA</option>
                                        <option value="MORADABAD">MORADABAD</option>
                                        <option value="MORBI">MORBI</option>
                                        <option value="MORENA">MORENA</option>
                                        <option value="MOTIHARI">MOTIHARI</option>
                                        <option value="MUKTSAR">MUKTSAR</option>
                                        <option value="MUNGER">MUNGER</option>
                                        <option value="MURSHIDABAD">MURSHIDABAD</option>
                                        <option value="MUZAFFARNAGAR">MUZAFFARNAGAR</option>
                                        <option value="MUZAFFARPUR">MUZAFFARPUR</option>
                                        <option value="MYSORE">MYSORE</option>
                                        <option value="NABHA">NABHA</option>
                                        <option value="NADIA">NADIA</option>
                                        <option value="NADIAD">NADIAD</option>
                                        <option value="NAGAON">NAGAON</option>
                                        <option value="NAGAPATTINAM">NAGAPATTINAM</option>
                                        <option value="NAGAUR">NAGAUR</option>
                                        <option value="NAGERCOIL">NAGERCOIL</option>
                                        <option value="NAGPUR">NAGPUR</option>
                                        <option value="NAHAN">NAHAN</option>
                                        <option value="NAKODAR">NAKODAR</option>
                                        <option value="NALBARI">NALBARI</option>
                                        <option value="NALGONDA">NALGONDA</option>
                                        <option value="NAMAKKAL">NAMAKKAL</option>
                                        <option value="NANDED">NANDED</option>
                                        <option value="NANDURBAR">NANDURBAR</option>
                                        <option value="NANDYAL">NANDYAL</option>
                                        <option value="NANITAL">NANITAL</option>
                                        <option value="NARASARAOPET">NARASARAOPET</option>
                                        <option value="NARNAUL">NARNAUL</option>
                                        <option value="NARSINGPUR">NARSINGPUR</option>
                                        <option value="NASHIK">NASHIK</option>
                                        <option value="NAVSARI">NAVSARI</option>
                                        <option value="NAWANSHAHAR">NAWANSHAHAR</option>
                                        <option value="NAZIBABAD">NAZIBABAD</option>
                                        <option value="NEEM KA THANA">NEEM KA THANA</option>
                                        <option value="NEEMUCH">NEEMUCH</option>
                                        <option value="NELLORE">NELLORE</option>
                                        <option value="NIPPANI">NIPPANI</option>
                                        <option value="NIRMAL">NIRMAL</option>
                                        <option value="NIZAMABAD">NIZAMABAD</option>
                                        <option value="NOHAR">NOHAR</option>
                                        <option value="NOKHA">NOKHA</option>
                                        <option value="NORTH LAKHIMPUR">NORTH LAKHIMPUR</option>
                                        <option value="NURPUR">NURPUR</option>
                                        <option value="ONGOLE">ONGOLE</option>
                                        <option value="OOTY">OOTY</option>
                                        <option value="ORAI">ORAI</option>
                                        <option value="OSMANABAD">OSMANABAD</option>
                                        <option value="PALAKKAD">PALAKKAD</option>
                                        <option value="PALAKOL">PALAKOL</option>
                                        <option value="PALAMPUR">PALAMPUR</option>
                                        <option value="PALANPUR">PALANPUR</option>
                                        <option value="PALGHAR">PALGHAR</option>
                                        <option value="PALI">PALI</option>
                                        <option value="PANAJI">PANAJI</option>
                                        <option value="PANCHKULA">PANCHKULA</option>
                                        <option value="PANDHARPUR">PANDHARPUR</option>
                                        <option value="PANIPAT">PANIPAT</option>
                                        <option value="PANVEL">PANVEL</option>
                                        <option value="PARADEEP">PARADEEP</option>
                                        <option value="PARBHANI">PARBHANI</option>
                                        <option value="PARWANOO">PARWANOO</option>
                                        <option value="PATAN">PATAN</option>
                                        <option value="PATHANKOT">PATHANKOT</option>
                                        <option value="PATIALA">PATIALA</option>
                                        <option value="PATNA">PATNA</option>
                                        <option value="PERAMBALUR">PERAMBALUR</option>
                                        <option value="PETLAD">PETLAD</option>
                                        <option value="PHAGWARA">PHAGWARA</option>
                                        <option value="PHALODI">PHALODI</option>
                                        <option value="PHULBANI">PHULBANI</option>
                                        <option value="PILIBHIT">PILIBHIT</option>
                                        <option value="PITHORAGARH">PITHORAGARH</option>
                                        <option value="POLLACHI">POLLACHI</option>
                                        <option value="PORBANDAR">PORBANDAR</option>
                                        <option value="PRATAPGARH">PRATAPGARH</option>
                                        <option value="PRODDATUR">PRODDATUR</option>
                                        <option value="PUDUCHERRY">PUDUCHERRY</option>
                                        <option value="PUDUKKOTTAI">PUDUKKOTTAI</option>
                                        <option value="PUNE">PUNE</option>
                                        <option value="PURI">PURI</option>
                                        <option value="PURNEA">PURNEA</option>
                                        <option value="PURULIA">PURULIA</option>
                                        <option value="PUTTUR">PUTTUR</option>
                                        <option value="RAEBARELI">RAEBARELI</option>
                                        <option value="RAICHUR">RAICHUR</option>
                                        <option value="RAIGANJ">RAIGANJ</option>
                                        <option value="RAIGARH">RAIGARH</option>
                                        <option value="RAIPUR">RAIPUR</option>
                                        <option value="RAISEN">RAISEN</option>
                                        <option value="RAJAHMUNDRY">RAJAHMUNDRY</option>
                                        <option value="RAJGARH">RAJGARH</option>
                                        <option value="RAJKOT">RAJKOT</option>
                                        <option value="RAJNANDGAON">RAJNANDGAON</option>
                                        <option value="RAJPURA">RAJPURA</option>
                                        <option value="RAJSAMAND">RAJSAMAND</option>
                                        <option value="RAM NAGAR">RAM NAGAR</option>
                                        <option value="RAMANATHAPURAM">RAMANATHAPURAM</option>
                                        <option value="RAMGARH">RAMGARH</option>
                                        <option value="RAMNAGAR">RAMNAGAR</option>
                                        <option value="RAMPUR">RAMPUR</option>
                                        <option value="RAMPUR BUSHAHR">RAMPUR BUSHAHR</option>
                                        <option value="RANCHI">RANCHI</option>
                                        <option value="RATLAM">RATLAM</option>
                                        <option value="RATNAGIRI">RATNAGIRI</option>
                                        <option value="RAYAGADA">RAYAGADA</option>
                                        <option value="REWA">REWA</option>
                                        <option value="REWARI">REWARI</option>
                                        <option value="RISHIKESH">RISHIKESH</option>
                                        <option value="ROHTAK">ROHTAK</option>
                                        <option value="ROORKEE">ROORKEE</option>
                                        <option value="ROPAR">ROPAR</option>
                                        <option value="ROURKELA">ROURKELA</option>
                                        <option value="RUDRAPUR">RUDRAPUR</option>
                                        <option value="SAGAR">SAGAR</option>
                                        <option value="SAHARANPUR">SAHARANPUR</option>
                                        <option value="SAHARSA">SAHARSA</option>
                                        <option value="SAHIBGANJ">SAHIBGANJ</option>
                                        <option value="SALEM">SALEM</option>
                                        <option value="SAMANA">SAMANA</option>
                                        <option value="SAMASTIPUR">SAMASTIPUR</option>
                                        <option value="SAMBA">SAMBA</option>
                                        <option value="SAMBALPUR">SAMBALPUR</option>
                                        <option value="SAMBHAL">SAMBHAL</option>
                                        <option value="SANGAREDDY">SANGAREDDY</option>
                                        <option value="SANGLI">SANGLI</option>
                                        <option value="SANGRUR">SANGRUR</option>
                                        <option value="SASARAM">SASARAM</option>
                                        <option value="SATARA">SATARA</option>
                                        <option value="SATNA">SATNA</option>
                                        <option value="SAWAI MADHOPUR">SAWAI MADHOPUR</option>
                                        <option value="SEHORE">SEHORE</option>
                                        <option value="SENDHWA">SENDHWA</option>
                                        <option value="SEONI">SEONI</option>
                                        <option value="SHAHDOL">SHAHDOL</option>
                                        <option value="SHAHJAHANPUR">SHAHJAHANPUR</option>
                                        <option value="SHAJAPUR">SHAJAPUR</option>
                                        <option value="SHAMLI">SHAMLI</option>
                                        <option value="SHILLONG">SHILLONG</option>
                                        <option value="SHIMLA">SHIMLA</option>
                                        <option value="SHIMOGA">SHIMOGA</option>
                                        <option value="SHIVPURI">SHIVPURI</option>
                                        <option value="SIBSAGAR">SIBSAGAR</option>
                                        <option value="SIDDHARTH NAGAR">SIDDHARTH NAGAR</option>
                                        <option value="SIDDIPET">SIDDIPET</option>
                                        <option value="SIKAR">SIKAR</option>
                                        <option value="SILCHAR">SILCHAR</option>
                                        <option value="SILIGURI">SILIGURI</option>
                                        <option value="SILVASSA">SILVASSA</option>
                                        <option value="SINGRAULI">SINGRAULI</option>
                                        <option value="SIRHIND">SIRHIND</option>
                                        <option value="SIROHI">SIROHI</option>
                                        <option value="SIRSA">SIRSA</option>
                                        <option value="SIRSI">SIRSI</option>
                                        <option value="SITAMARHI">SITAMARHI</option>
                                        <option value="SITAPUR">SITAPUR</option>
                                        <option value="SIWAN">SIWAN</option>
                                        <option value="SOLAN">SOLAN</option>
                                        <option value="SOLAPUR">SOLAPUR</option>
                                        <option value="SONBHADRA">SONBHADRA</option>
                                        <option value="SONEPAT">SONEPAT</option>
                                        <option value="SRIGANGANAGAR">SRIGANGANAGAR</option>
                                        <option value="SRIKAKULAM">SRIKAKULAM</option>
                                        <option value="SRINAGAR">SRINAGAR</option>
                                        <option value="SULTANPUR">SULTANPUR</option>
                                        <option value="SUMERPUR">SUMERPUR</option>
                                        <option value="SUNAM">SUNAM</option>
                                        <option value="SUNDER NAGAR">SUNDER NAGAR</option>
                                        <option value="SURAT">SURAT</option>
                                        <option value="SURATGARH">SURATGARH</option>
                                        <option value="SURENDRANAGAR">SURENDRANAGAR</option>
                                        <option value="SURI">SURI</option>
                                        <option value="SURYAPET">SURYAPET</option>
                                        <option value="TADEPALLIGUDEM">TADEPALLIGUDEM</option>
                                        <option value="TAMBARAM">TAMBARAM</option>
                                        <option value="TANUKU">TANUKU</option>
                                        <option value="TARN TARAN">TARN TARAN</option>
                                        <option value="TEHRI">TEHRI</option>
                                        <option value="TENALI">TENALI</option>
                                        <option value="TEZPUR">TEZPUR</option>
                                        <option value="THANE">THANE</option>
                                        <option value="THANJAVUR">THANJAVUR</option>
                                        <option value="THENI">THENI</option>
                                        <option value="THIRUVALLA">THIRUVALLA</option>
                                        <option value="THIRUVANANTHAPURAM">THIRUVANANTHAPURAM</option>
                                        <option value="THODUPUZHA">THODUPUZHA</option>
                                        <option value="THRISSUR">THRISSUR</option>
                                        <option value="TIKAMGARH">TIKAMGARH</option>
                                        <option value="TINSUKIA">TINSUKIA</option>
                                        <option value="TIPTUR">TIPTUR</option>
                                        <option value="TIRUNELVELI">TIRUNELVELI</option>
                                        <option value="TIRUPATI">TIRUPATI</option>
                                        <option value="TIRUPPUR">TIRUPPUR</option>
                                        <option value="TIRUR">TIRUR</option>
                                        <option value="TIRUVALLUR">TIRUVALLUR</option>
                                        <option value="TIRUVANNAMALAI">TIRUVANNAMALAI</option>
                                        <option value="TIRUVARUR">TIRUVARUR</option>
                                        <option value="TONK">TONK</option>
                                        <option value="TRICHY">TRICHY</option>
                                        <option value="TUMKUR">TUMKUR</option>
                                        <option value="TUNI">TUNI</option>
                                        <option value="TUTICORIN">TUTICORIN</option>
                                        <option value="UDAIPUR">UDAIPUR</option>
                                        <option value="UDHAMPUR">UDHAMPUR</option>
                                        <option value="UDUPI">UDUPI</option>
                                        <option value="UJJAIN">UJJAIN</option>
                                        <option value="UMARIA">UMARIA</option>
                                        <option value="UNA">UNA</option>
                                        <option value="UNNAO">UNNAO</option>
                                        <option value="VAISHALI">VAISHALI</option>
                                        <option value="VALSAD">VALSAD</option>
                                        <option value="VAPI">VAPI</option>
                                        <option value="VARANASI">VARANASI</option>
                                        <option value="VELLORE">VELLORE</option>
                                        <option value="VIDISHA">VIDISHA</option>
                                        <option value="VIJAYANAGARAM">VIJAYANAGARAM</option>
                                        <option value="VIJAYAWADA">VIJAYAWADA</option>
                                        <option value="VIKARABAD">VIKARABAD</option>
                                        <option value="VILLUPURAM">VILLUPURAM</option>
                                        <option value="VIRUDHUNAGAR">VIRUDHUNAGAR</option>
                                        <option value="VISAKHAPATNAM">VISAKHAPATNAM</option>
                                        <option value="WARANGAL">WARANGAL</option>
                                        <option value="WARDHA">WARDHA</option>
                                        <option value="YADGIR">YADGIR</option>
                                        <option value="YAMUNANAGAR">YAMUNANAGAR</option>
                                        <option value="YAVATMAL">YAVATMAL</option>
                                        <option value="ZIRA">ZIRA</option>
                                    </select>
                                    <img class="ajax_loader" src="../bootstrap/img/rel_interstitial_loading.gif" style="display:none;">
                                </td>
                            </tr>
                            <tr>
                                <th style="border: none;">Pin Code</th>
                                <td>
                                    <input id="pincode" name="pincode" style="height: 22px" tabindex="15" type="text" value="" size="20" maxlength="6" autocomplete="off">
                                </td>
                            </tr>

                            <tr>
                                <th style="border: none;">Name As Per Aadhaar</th>
                                <td>
                                    <input type="text" id="name_aadhaar" style="height: 22px" name="name_aadhaar" oninput="this.value = this.value.toUpperCase()" onblur="this.value = this.value.toUpperCase();" required>
                                </td>
                            </tr>
                            <tr>
                                <th style="border: none;">Aadhaar Number</th>
                                <td align="left">
                                    <input type="text" style="height: 22px" name="aadhaar_num" maxlength="12" oninput="this.value = this.value.toUpperCase()" id="a_aadhaar_num" onkeyup="GetAadhar(this.value)" onblur="this.value = this.value.toUpperCase();" required>
                                </td>
                            </tr>
                            <tr>
                                <td style="border: none;" align="left" colspan="3">
                                    <input type="hidden" class="form-control" id="aadhaar_num" name="" oninput="this.value = this.value.toUpperCase()" onblur="this.value = this.value.toUpperCase();" required readonly>
                                </td>
                            </tr>
                            <tr>
                                <th align="left" style="font-size: small; font-family: Arial; border: none;">Signature Type</th>
                                <td align="left">
                                    <select id="sig_type" name="sig_type" style="height: 22px" tabindex="21">
                                        <option value="">Please Select</option>
                                        <option value="sig">हस्ताक्षर</option>
                                        <option value="thmb">बायें हाथ के अंगूठे का निशान</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th align="left" style="font-size: small; font-family: Arial; border: none;">Proof of Identity</th>
                                <td align="left" id="proof_id1" colspan="3">
                                    <select id="proof_id" name="proof_id" style="width: 500px !important; height: 22px" tabindex="22" title="Please Select">
                                        <option value="0">Please Select</option>
                                        <option>Certificate of Identity signed by a Gazetted Officer</option>
                                        <option>Certificate of Identity signed by a Member of Legislative Assembly</option>
                                        <option>Certificate of Identity signed by a Member of Parliament</option>
                                        <option>Certificate of Identity signed by a Municipal Councillor</option>
                                        <option>Driving License</option>
                                        <option>Passport</option>
                                        <option>Arm's license</option>
                                        <option>Central Government Health Scheme Card</option>
                                        <option>Ex-Servicemen Contributory Health Scheme photo card</option>
                                        <option>Bank certificate in Original on letter head from the branch (along with name and stamp of the issuing officer) containing duly attested photograph and bank account number of the applicant</option>
                                        <option>Photo identity Card issued by the Central Government or State Government or Public Sector Undertaking</option>
                                        <option>Pensioner Card having photograph of the applicant</option>
                                        <option>Elector's photo identity card</option>
                                        <option>Ration card having photograph of the applicant</option>
                                        <option>AADHAAR Card issued by the Unique Identification Authority of India</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th align="left" style="font-size: small; font-family: Arial; border: none;"> Proof of Address </th>
                                <td align="left" id="proof_add1" colspan="3">
                                    <select id="proof_add" name="proof_add" style="width: 500px !important; height: 22px" tabindex="23" title="Please Select">
                                        <option value="0">Please Select</option>
                                        <option>Latest property tax assessment order</option>
                                        <option>Depository account statement (Not more than 3 months old from the date of application)</option>
                                        <option>Credit card statement (Not more than 3 months old from the date of application)</option>
                                        <option>Bank account statement/passbook (Not more than 3 months old from the date of application)</option>
                                        <option>Landline Telephone Bill (Not more than 3 months old from the date of application)</option>
                                        <option>Certificate of Address signed by a Municipal Councillor</option>
                                        <option>Driving License</option>
                                        <option>Passport</option>
                                        <option>Property Registration Document</option>
                                        <option>Electricity Bill (Not more than 3 months old from the date of application)</option>
                                        <option>Bank Account Statement in the country of residence (Not more than 3 months old from the date of application)</option>
                                        <option>NRE bank account statement (Not more than 3 months old from the date of application)</option>
                                        <option>Employer certificate in original</option>
                                        <option>Elector's photo identity card</option>
                                        <option>Certificate of Address signed by a Gazetted Officer</option>
                                        <option>Passport of the spouse</option>
                                        <option>Post office passbook having address of the applicant</option>
                                        <option>Domicile certificate issued by the Government</option>
                                        <option>Allotment letter of accommodation issued by Central or State Government of not more than three years old</option>
                                        <option>Certificate of Address signed by a Member of Legislative Assembly</option>
                                        <option>Certificate of Address signed by a Member of Parliament</option>
                                        <option>AADHAAR Card issued by the Unique Identification Authority of India</option>
                                        <option>Consumer gas connection card or book or piped gas bill(Not more than 3 months old from date of application)</option>
                                        <option>Water Bill (Not more than 3 months old from the date of application)</option>
                                        <option>Broadband Connection Bill (Not more than 3 months old from the date of application)</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th align="left" style="font-size: small; font-family: Arial; border: none;"> Proof of DOB </th>
                                <td align="left" id="proof_dob1" colspan="3">
                                    <select id="proof_dob" name="proof_dob" style="width: 500px !important; height: 22px" tabindex="24">
                                        <option value="0">Please Select</option>
                                        <option>Birth Certificate issued by the Municipal Authority or any office authorized to issue Birth and Death Certificate by the Registrar of Birth and Death of the Indian Consulate</option>
                                        <option>Pension payment order</option>
                                        <option>Marriage certificate issued by Registrar of Marriages</option>
                                        <option>Matriculation certificate</option>
                                        <option>Passport</option>
                                        <option>Driving License</option>
                                        <option>Domicile certificate issued by the Government</option>
                                        <option>Affidavit sworn before a magistrate stating the date of birth</option>
                                        <option>Matriculation Marksheet of recognised board</option>
                                        <option>AADHAAR Card issued by the Unique Identification Authority of India</option>
                                        <option>Elector's photo identity card</option>
                                        <option>Photo identity Card issued by the Central Government or State Government or Public Sector Undertaking</option>
                                        <option>Central Government Health Scheme Card</option>
                                        <option>Ex-Servicemen Contributory Health Scheme photo card</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th align="left" style="font-size: small; font-family: Arial; border: none;"> Processing Fee </th>
                                <td align="left">
                                    <input type="hidden" name="epan_flag" value="Y">
                                    <input id="filing_fees" name="filing_fees" style="height: 22px" tabindex="25" readonly="readonly" type="text" value="<?= $userdata['p_nsdl'] ?>" size="5" maxlength="5"> in ( <span class="WebRupee">Rs</span> )
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <th align="left" style="font-size: small; font-family: Arial; border: none;">Changes Or Correction ?</th>
                                <td align="left" colspan="3">
                                    <input class="mt-1" id="full_name" name="full_name" tabindex="26" size="25" maxlength="25" checked="checked" onchange="checkEPAN();" type="checkbox" value="Y">
                                    Full Name
                                    <input class="mt-1" id="father_name" name="father_name" tabindex="26" size="25" maxlength="25" checked="checked" onchange="checkEPAN();" type="checkbox" value="Y">
                                    Father's Name
                                    <input class="mt-1" id="date_brith" name="date_brith" tabindex="26" size="25" maxlength="25" checked="checked" onchange="checkEPAN();" type="checkbox" value="Y">
                                    Date of Birth
                                    <input class="mt-1" id="gender_c" name="gender_c" tabindex="26" size="25" maxlength="25" checked="checked" onchange="checkEPAN();" type="checkbox" value="Y">
                                    Gender
                                    <input class="mt-1" id="address" name="address" tabindex="26" size="25" maxlength="25" checked="checked" onchange="checkEPAN();" type="checkbox" value="Y">
                                    Address
                                </td>
                            </tr>
                            <!--- Added for E-Pan:: end---->
                            <tr>
                                <td>
                                    <input type="text" name="citizen" size="25" maxlength="25" disabled="disabled" value="INDIAN" style="display: none">
                                </td>
                            </tr>
                            <!--- Added by Shreyoshi for AO Code:: start---->
                        </tbody>
                    </table>
                </td>

            </tr>
            <td>
                <button type="submit" name="CSF_application" id="submit-button" onclick="Pan_new()">Submit</button>
                <button type="button" onclick="redirectToHome()">Back</button>

                <script>
                    function redirectToHome() {
                        window.location.href = "home";
                    }
                </script>
            </td>
        </tbody>
    </table>
    </div>
</form>
</div>
<!-- / Content -->
<script type = "text/javascript" >
    function GetName(str) {
        var f = document.getElementById("f_name").value.trim();
        var m = document.getElementById("m_name").value.trim();
        var l = document.getElementById("l_name").value.trim();
        var full_name = (f + (m !== '' ? ' ' + m : '') + (l !== '' ? ' ' + l : '')).trim();
        var res = full_name.replace(/\s\s+/g, ' '); // Replace multiple spaces with a single space

        document.getElementById("name_card").value = res;
        document.getElementById("new_modle_name").innerHTML = res;

        document.getElementById("name_aadhaar").value = res;
    }

function GetcName(str) {
    var f = document.getElementById("first_name").value.trim();
    var m = document.getElementById("midil_name").value.trim();
    var l = document.getElementById("last_name").value.trim();
    var full_name = (f + (m !== '' ? ' ' + m : '') + (l !== '' ? ' ' + l : '')).trim();
    var res = full_name.replace(/\s\s+/g, ' '); // Replace multiple spaces with a single space

    document.getElementById("csfname_card").value = res;
    document.getElementById("csf_modle_name").innerHTML = res;
} 
</script>

<script type = "text/javascript">

    function GetcAadhar(str) {
        var a = document.getElementById("csf_aadhaar_num").value;
        var full_name = a;
        var res = full_name.replace("  ");

        document.getElementById("csf_modle_aadhaar").innerHTML = res;


    }

</script> 
<script>
function validatePAN() {
	var pan_number=document.getElementById("pan_number").value;
	var trimPan=pan_number.replace(/^\s+|\s+$/g,'');
	if(trimPan.length==10)
	{
		var letters = /^[A-Za-z]+$/;
		var numbers = /^[0-9]+$/;
		var pan1=trimPan.substring(0,4);
		var panCat=trimPan.substring(3,4);
		var pan2=trimPan.substring(4,5);
		var pan3=trimPan.substring(5,9);
		var pan4=trimPan.substring(9,10);
		if((pan1.match(letters)))
		{
			//if(pan2.match(letters) || pan2.match(numbers))
			if(pan2.match(letters))
			{
				if(pan3.match(numbers))
				{
					if(pan4.match(letters))
					{
						if(panCat=="P" || panCat=="p" || panCat=="F" || panCat=="f" || panCat=="A" || panCat=="a" || panCat=="B" || panCat=="b" || panCat=="C" || panCat=="c" || panCat=="E" || panCat=="e" || panCat=="G" || panCat=="g" || panCat=="H" || panCat=="h" || panCat=="J" || panCat=="j" || panCat=="L" || panCat=="l" || panCat=="T" || panCat=="t")
						{
						}
						else
						{
							alert("PAN Structure is not valid");
							document.getElementById("pan_number").focus();
							return false;
						}
					}
					else
					{
						alert("PAN Structure is not valid");
						document.getElementById("pan_number").focus();
						return false;
					}
				}
				else
				{
					alert("PAN Structure is not valid");
					document.getElementById("pan_number").focus();
					return false;
				}
			}
			else
			{
				alert("PAN Structure is not valid");
				document.getElementById("pan_number").focus();
				return false;
			}
		}
		else
		{
			alert("PAN Structure is not valid");
			document.getElementById("pan_number").focus();
			return false;
		}
	}
	else
	{
		alert("PAN Structure is not valid");
		document.getElementById("pan_number").focus();
		return false;
	}
	return true;
}

    function Pan_new() {

        var pan_number = document.getElementById("pan_number").value;
        var title = document.getElementById("title").value;
        var l_name = document.getElementById("l_name").value;
        var fal_name = document.getElementById("fal_name").value;
        var name_card = document.getElementById("name_card").value;
        var gender = document.getElementById("gender").value;
        var dob = document.getElementById("dob").value;
        var mob_num = document.getElementById("mob_num").value;
        var email_id = document.getElementById("email_id").value;
        var address2 = document.getElementById("address2").value;
        var address3 = document.getElementById("address3").value;
        var address4 = document.getElementById("address4").value;
        var address5 = document.getElementById("address5").value;
        var address1 = document.getElementById("address1").value;
        var user_state = document.getElementById("user_state").value;
        var city = document.getElementById("city").value; // Corrected variable name
        var pincode = document.getElementById("pincode").value;
        var name_aadhaar = document.getElementById("name_aadhaar").value;
        var a_aadhaar_num = document.getElementById("a_aadhaar_num").value;
        var sig_type = document.getElementById("sig_type").value;
        var proof_id = document.getElementById("proof_id").value;
        var proof_add = document.getElementById("proof_add").value;
        var proof_dob1 = document.getElementById("proof_dob1").value;
        var tel_num_isdcode = document.getElementById("tel_num_isdcode").value;
        var tel_num_stdcode = document.getElementById("tel_num_stdcode").value;

        if (pan_number === "") {
            alert("Please Enter Old Pan Number.");
            return false;
        }

        if (title === "") {
            alert("Please select Title.");
            return false;
        }
        
        if (l_name === "") {
            alert("Please enter Last Name/Surname.");
            return false;
        }
        
        if (name_card === "") {
            alert("Please enter Name on Card.");
            return false;
        }
        
        if (gender === "") {
            alert("Please select Gender.");
            return false;
        }
        
        if (fal_name === "") {
            alert("Please enter Father Last Name/Surname.");
            return false;
        }
        
        if (dob === "") {
            alert("Please enter Date of Birth/Incorporation.");
            return false;
        }
        
        if (tel_num_isdcode === "" || tel_num_stdcode === "" || mob_num === "") {
            alert("Please enter Contact Details (Telephone Number).");
            return false;
        }
        
        if (mob_num.length !== 10) {
            alert("Please enter a 10-digit Mobile Number.");
            return false;
        }
        
        if (email_id === "") {
            alert("Please enter Email ID.");
            return false;
        }
        
        if (address2 === "") {
            alert("Please enter Building/Village Name.");
            return false;
        }
        
        if (address3 === "") {
            alert("Please enter Road/Street/Lane/Post Office.");
            return false;
        }
        
        if (address4 === "") {
            alert("Please enter Area/Locality/Sub-Division.");
            return false;
        }
        
        if (address5 === "") {
            alert("Please enter Town/City/District.");
            return false;
        }
        
        if (address1 === "") {
            alert("Please enter Center / E-mitra Name.");
            return false;
        }
        
        if (user_state === "") {
            alert("Please select District / Town.");
            return false;
        }
        
        if (city === "") {
            alert("Please select District / Town.");
            return false;
        }
        
        if (pincode === "") {
            alert("Please enter Pin Code.");
            return false;
        }
        
        if (name_aadhaar === "") {
            alert("Please enter Name as Per Aadhaar.");
            return false;
        }
        
        if (a_aadhaar_num === "") {
            alert("Please enter Aadhaar Number.");
            return false;
        }
        
        if (a_aadhaar_num.length !== 12) {
            alert("Please enter a 12-digit Aadhaar Number.");
            return false;
        }
        
        if (sig_type === "") {
            alert("Please Signature Type Select");
            return false;
        }
        
        if (proof_id === "0") {
            alert("Please select Proof of Identity.");
            return false;
        }
        
        if (proof_add === "0") {
            alert("Please select Proof of Address.");
            return false;
        }
        
        if (proof_dob1 === "0") {
            alert("Please select Proof of DOB.");
            return false;
        }
        
        return true; // If all validations pass, allow the form to submit

    }
</script>

<?php
require_once('../layouts/mainFooter.php');
?>