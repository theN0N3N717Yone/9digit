<?php
require_once('../layouts/mainHeader.php');
?>

<?php if($userdata['usertype']=="mainadmin" ){?> 

<?php
if(isset($_POST['action']) && !empty($_POST['data_id']) && !empty($_POST['status']) && !empty($_POST['email_id']) && !empty($_POST['remark']) ){
    

$allowedExts = array("pdf");	
$ackexn = end(explode(".", $_FILES["ack_pdf"]["name"]));

if($_POST['status']=='success'){
if ($_FILES["ack_pdf"]["type"] == "application/pdf" && $_FILES["ack_pdf"]["size"] < 3000000 && in_array($ackexn, $allowedExts) ) {	
$ack_pdf = $_POST['ack_no']."-".date('dmYhis')."_Ack.pdf";
$ack_link = 'https://'.$_SERVER['SERVER_NAME']."/downloads.php?files=$ack_pdf";
move_uploaded_file($_FILES["ack_pdf"]["tmp_name"],"../pan_doc/".$ack_pdf);
$status = 'success';
$nsdlsql = "UPDATE nsdlpancard SET  ack_no=:ack_no, ack_pdf=:ack_pdf, remark=:remark, status=:status, email_id=:email_id WHERE id=:id ";
$nsdl = $conn->prepare($nsdlsql);
$nsdl->bindParam(":ack_no", filter_var($_POST["ack_no"],FILTER_SANITIZE_STRING));
$nsdl->bindParam(":ack_pdf", $ack_link);
$nsdl->bindParam(":remark", filter_var($_POST["remark"],FILTER_SANITIZE_STRING));
$nsdl->bindParam(":status", $status);
$nsdl->bindParam(":email_id", filter_var($_POST["email_id"],FILTER_SANITIZE_STRING));
$nsdl->bindParam(":id", filter_var($_POST["data_id"],FILTER_SANITIZE_STRING));
if($nsdl->execute()){


$usql = $conn->prepare("select * from users WHERE id = ?");
$usql->execute([$_POST['user']]);
$usr_d=$usql->fetch();

$csql = $conn->prepare("select * from nsdlpancard WHERE id = ?");
$csql->execute([$_POST['data_id']]);
$pan_data=$csql->fetch();

if($pan_data['epan_flag']=='Y'){
$epan_flag ='p_nsdl';	
$coupon_type = 'P-PAN';
}else if($pan_data['epan_flag']=='N'){
$epan_flag ='e_nsdl';	
$coupon_type = 'E-PAN';	
}else{
$epan_flag = 0;	
$coupon_type = '';	
}

$url = $pan_data['form_pdf'];
$queryParams = parse_url($url, PHP_URL_QUERY);
parse_str($queryParams, $queryData);
$filename = isset($queryData['files']) ? $queryData['files'] : '';
echo "<script>
         toastr.success('Form Upload Successfully! " . $pan_data["name_card"] . "');
         window.open('../PDFMerger/merge.php?ack_no=" . $_POST['ack_no'] . "&form_pdf=../pan_doc/" . $filename . "&ack_pdf=../pan_doc/" . $ack_pdf . "', '_blank');
      </script>";

}else{
echo "<script>
             toastr.error('Failed to update form!');
        </script>";	
}
}else{
echo "<script>
             toastr.error('Invalid File Format or Size is Large!');
        </script>";	
}	
	
}
if($_POST['status']=='hold'){
$status = 'hold';
$nsdlsql = "UPDATE nsdlpancard SET remark=:remark, status=:status, email_id=:email_id WHERE id=:id ";
$nsdl = $conn->prepare($nsdlsql);
$nsdl->bindParam(":remark", filter_var($_POST["remark"],FILTER_SANITIZE_STRING));
$nsdl->bindParam(":status", $status);
$nsdl->bindParam(":email_id", filter_var($_POST["email_id"],FILTER_SANITIZE_STRING));
$nsdl->bindParam(":id", filter_var($_POST["data_id"],FILTER_SANITIZE_STRING));
if($nsdl->execute()){


$csql = $conn->prepare("select * from nsdlpancard WHERE id = ?");
$csql->execute([$_POST['data_id']]);
$pan_data=$csql->fetch();
echo "<script>
             toastr.success('Form Update Successfully! " . $pan_data["name_card"] . "');
        </script>";		

}else{
echo "<script>
             toastr.error('Failed to update form!');
        </script>";	
}

	
}else if($_POST['status']=='rejected'){

$status = 'rejected';
$nsdlsql = "UPDATE nsdlpancard SET  ack_no=:ack_no, remark=:remark, status=:status WHERE id=:id ";
$nsdl = $conn->prepare($nsdlsql);
$nsdl->bindParam(":ack_no", filter_var($_POST["ack_no"],FILTER_SANITIZE_STRING));
$nsdl->bindParam(":remark", filter_var($_POST["remark"],FILTER_SANITIZE_STRING));
$nsdl->bindParam(":status", $status);
$nsdl->bindParam(":id", filter_var($_POST["data_id"],FILTER_SANITIZE_STRING));
if($nsdl->execute()){
	

	
$csql = $conn->prepare("select * from nsdlpancard WHERE id = ?");
$csql->execute([$_POST['data_id']]);
$pan_data=$csql->fetch();

$usql = $conn->prepare("select * from users WHERE id = ?");
$usql->execute([$pan_data['user_id']]);
$usr_d=$usql->fetch();

if($pan_data['epan_flag']=='Y'){
$epan_flag ='p_nsdl';	
$coupon_type = 'CSF Pan';
}else if($pan_data['epan_flag']=='N'){
$epan_flag ='e_nsdl';	
$coupon_type = 'E-PAN';	
}else{
$epan_flag = 0;	
$coupon_type = '';	
}

$total_credit = $usr_d['balance'] + $usr_d[$epan_flag];
$sqlu = $conn->prepare("UPDATE users SET balance=?  WHERE id=?");
$sqlu->execute([$total_credit, $usr_d['id']]);	

$txnsql = "INSERT INTO `paymentreq`(`date_time`, `timestamp`, `user`, `bank`, `mode`, `type`, `amount`,`balance`, `reference`, `remark`, `status`, `refunded_sts`)
 VALUES (:date_time,:timestamp,:user,:bank,:mode,:type,:amount,:balance,:reference,:remark,:status,:refunded_sts)";
$mode = 'REFUNDED';	
$type = 'credit';
$st = 'YES';
$remark = $coupon_type.' Rejected, Refund Amount Rs.'.$usr_d[$epan_flag].', OrderId : '.$pan_data['order_id']. ' Applicant Name '. $pan_data['card_name'];
$status = 'success';
$sts_rmk = "Refund Success On $today TXNID $reference";
$txn = $conn->prepare($txnsql);
$txn->bindParam(":date_time", $date_time);
$txn->bindParam(":timestamp", $today);
$txn->bindParam(":user", $usr_d['username']);
$txn->bindParam(":bank", $userdata['username']);
$txn->bindParam(":mode", $mode);
$txn->bindParam(":type", $type);
$txn->bindParam(":amount",$usr_d[$epan_flag]);
$txn->bindParam(":balance", $total_credit);
$txn->bindParam(":reference", $reference);
$txn->bindParam(":remark", $remark);
$txn->bindParam(":status", $status);
$txn->bindParam(":refunded_sts", $sts_rmk);
$txn->execute();


echo "<script>
             toastr.error('Rejected update successfully! '" . $pan_data["name_card"] . "');
        </script>";	
        
        

}else{
echo "<script>
             toastr.error('Failed to update form!');
        </script>";	 	
}	
	

}	
	
}
?>	
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- DataTales Example -->
        <div class="card shadow-lg ps-background-table">
            <div class="card-header">
                <div style="color:black;"><b>Change Pan Request</b></div>
            </div>
            <div class="card-body">
                <div id="demo_info" class="box table-responsive text-nowrap">
                    <table id="example" class="table table-striped">
                        <thead style="background: #000cad;">
                            <tr>
                                <th style="color: #fff">DATE</th>
                                <th style="color: #fff">USER</th>
                                <th style="color: #fff">NAME</th>
                                <th style="color: #fff">TXN_ID</th>
                                <th style="color: #fff">AMOUNT</th>
                                <th style="color: #fff">PAAM</th>
                                <th style="color: #fff">UPDATE</th>
                            </tr>
                        </thead>
                        <tbody>
                
<?php

$stmt = $conn->prepare("SELECT * FROM nsdlpancard WHERE type = ? AND LOWER(status) = LOWER(?) ORDER BY `id` DESC");
$stmt->execute(['Correction pan', 'Process']);

$sl=1;
while($row=$stmt->fetch()) {
?>

<script>
function copyToClipboard(element) {
  var $temp = $("<input>");
  $("body").append($temp);
  $temp.val($(element).text()).select();
  document.execCommand("copy");
  alert("Successfully Copy to Clipboard!");
  $temp.remove();
}   
</script>

<?php

$usql = $conn->prepare("select * from users WHERE id = ?");
$usql->execute([$row['user_id']]);
$usr_d=$usql->fetch();

if(strtoupper($row['pan_number'])=="NEWPAN"){
$type = 'NEW PAN';	
$pan_number = '';
}else{
$type = 'CSF PAN';		
$pan_number = $row['pan_number'];
}



$arr->pan = $row['pan_number'];
$arr->f_name = $row['f_name'];
$arr->m_name = $row['m_name'];
$arr->l_name = $row['l_name'];
$arr->faf_name = $row['faf_name'];
$arr->fam_name = $row['fam_name'];
$arr->fal_name = $row['fal_name'];
$arr->dob = $row['dob'];
$arr->name_card = $row['name_card'];
$arr->tel_num_isdcode = '91';
$arr->tel_num_stdcode = '';
$arr->tel_num = $row['mob_num'];
$arr->add_comm = 'indian';
$arr->email_id = $row['email_id'];
$arr->user_state = $row['user_state'];
$arr->check_aadhaar_eid = 'A';
$arr->name_aadhaar = $row['name_aadhaar'];
$arr->aadhaarNo = $row['aadhaar_num'];
$arr->proof_id = $row['proof_id'];
$arr->proof_add = $row['proof_add'];
$arr->proof_dob = $row['proof_dob'];
$arr->gender = $row['gender'];
$copy_value = json_encode($arr);

if ($row['remark'] === 'From Upload Successfully' || $row['remark'] === 'Application Updated Successfully') {

$svg_color = "rgba(255, 0, 0, 1)";
		      echo "<tr>
					  <td style='color:black;'><b>" . strtoupper($row['type']) ."<br>" . date("d M h:i A", strtotime($row['timestamp'])) . "</b></td>
					  <td><b>" . strtoupper($usr_d['username']) ."<br>" . strtoupper($usr_d['owner_name']) ."<br>MOB - " . strtoupper($usr_d['mobile_no']) ."</b></td>
                      <td style='color:black;'><b>".strtoupper($row['name_card'])."<br>".strtoupper($row['faf_name'])." ".strtoupper($row['fam_name'])." ".strtoupper($row['fal_name'])."<br>MOB - ".strtoupper($row['mob_num'])."</b></td>
                      <td style='color:black;'><b>".strtoupper($row['order_id'])."</b></td> 
					  <td style='color:black;'><b>Rs.".strtoupper($row['amount'])."</b></td>
                      <td><p id='".$sl."' style='display:none;'>".$copy_value."</p>
                        <button class='btn btn-sm btn-icon btn-outline-danger active' onclick=\"copyToClipboard('#$sl');this.disabled=true;\"><i class='bx bx-copy'></i></button>
                        <a type='button' href='".$row['form_pdf']."' target='_bank' class='btn btn-sm btn-icon btn-outline-danger active'><i class='bx bx-file'></i></a>
                      </td>
                     
                            <td>
                            <button 
                                data-bs-toggle='modal' 
                                data-bs-target='#update_pan' 
                                data-id=" . base64_encode($row['id']) . "
                                data-token=" . base64_encode($row['order_id']) . "
                                data-email=" . base64_encode($row['email_id']) . "
                                data-type='update_pan'
                                class='btn btn-sm btn-icon btn-outline-danger active'><i class='bx bx-upload'></i></button>
                                <button 
                                data-bs-toggle='modal' 
                                data-bs-target='#view_application' 
                                data-id=" . base64_encode($row['id']) . "
                                data-token=" . base64_encode($row['order_id']) . "
                                data-type='pandata'
                                class='btn btn-sm btn-icon btn-outline-danger active view-data'><i class='bx bx-low-vision'></i></button>
                        </td>
                    </tr>";
$sl++;
}		}				
?>					
                  </tbody>
                </table>
<!-- Form Upload -->
<div class="modal fade" id="update_pan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header ps-background-table">
                <h5 class="modal-title">Update Form : <span style="color:red" id="access_token"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr class="border-danger">
            <div class="modal-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col mb-3">
                              <select name='status' class='form-control mb-2' required>
            				    <option value=''>:: Status ::</option>
            				    <option value='success'>Success</option>
            				    <option value='rejected'>Rejected</option>
            				    <option value='hold'>Under Observation</option>
            				  </select>
            				  <input type='text' name='remark' class='form-control mb-2' placeholder='Remark' required>  
            				  
            				  <input type='hidden' value='<?= $row['formpdf_link'] ?>' name='formpdf_link'>
            				  
            				  
            				  
                              <input type='hidden' id='userid' name='data_id'>
            				  <input type='hidden' name='user' id='token'>
            				  <input type='hidden' name='email_id' id='email'>
            				  <input type='text' name='ack_no' class='form-control mb-2' maxlength='15' placeholder='Ack No.'>
            				  <input class='form-control mb-2' id='customFile' name='ack_pdf' type='file'>
            				  
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" name="action" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div><!-- Modal Element -->

</div>
            </div>
          </div>
		  <?php }?>
        </div>
        </div>
        <!-- /.container-fluid -->
      <!-- End of Main Content -->
<?php
require_once('../layouts/mainFooter.php');
?>
<script>
  $(document).ready(function () {
    $('#update_pan').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget);
      var ddid = button.data('id');
      var token = button.data('token');
      var email = button.data('email');
      var type = button.data('type');
      var decodedid = atob(ddid);
      var decodedToken = atob(token);
      var decodedemail = atob(email);

      $('#userid').val(decodedid);
      $('#token').val(decodedToken);
      $('#access_token').text(decodedToken);
      $('#email').val(decodedemail);
    });
  });
</script>
