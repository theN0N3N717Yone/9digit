<?php
require_once('../layouts/mainHeader.php');
?>
<?php if($userdata['usertype']!="mainadmin" ){?> 
<?php
if(isset($_GET['datakey'])){
$stmt = $conn->prepare("select * from nsdlpancard WHERE id = ? AND order_id = ? ");
$stmt->execute([base64_decode($_GET['datakey']),base64_decode($_GET['keypass'])]);
$row=$stmt->fetch();	
$rowobj = json_decode($row['changes'],true);
if($row['id']==""){
echo '
<script>
alert("Application Data Not Available!");
window.location = "phygical-pan-record" 
</script>';	

}	
}


if(isset($_POST['action']) ){
//$allowed_extensions = array("jpg","jpeg","png","gif");	
$allowedExts = array("pdf");
$formexn = end(explode(".", $_FILES["form_pdf"]["name"]));
//$photoexn = end(explode(".", $_FILES["photo"]["name"]));
//$signexn = end(explode(".", $_FILES["sign"]["name"]));
if ($_FILES["form_pdf"]["type"] == "application/pdf" && 
$_FILES["form_pdf"]["size"] < 3000000 && 
in_array($formexn, $allowedExts) 
//&& in_array($photoexn, $allowed_extensions) &&
//in_array($signexn, $allowed_extensions)

) {	

$form_pdf = str_replace(' ','_',$_POST["name_card"])."-".rand(100000,999999)."_Form.pdf";
$form_link = 'https://'.$_SERVER['SERVER_NAME']."/downloads.php?files=$form_pdf";

move_uploaded_file($_FILES["form_pdf"]["tmp_name"],"../pan_doc/".$form_pdf);


$response->full_name = filter_var($_POST['full_name'],FILTER_SANITIZE_STRING);
$response->father_name = filter_var($_POST['father_name'],FILTER_SANITIZE_STRING);
$response->date_brith = filter_var($_POST['date_brith'],FILTER_SANITIZE_STRING);
$response->gender = filter_var($_POST['gender_c'],FILTER_SANITIZE_STRING);
$response->address = filter_var($_POST['address'],FILTER_SANITIZE_STRING);
$response->nofdoc = filter_var($_POST['nofdoc'],FILTER_SANITIZE_STRING);
$changes = json_encode($response);

	
$nsdlsql = "UPDATE nsdlpancard SET cat_applicant=:cat_applicant,pan_number=:pan_number,l_name=:l_name,f_name=:f_name,m_name=:m_name,name_card=:name_card,dob=:dob,gender=:gender,fal_name=:fal_name,
 faf_name=:faf_name,fam_name=:fam_name,aadhaar_num=:aadhaar_num,name_aadhaar=:name_aadhaar,mob_num=:mob_num,email_id=:email_id,address1=:address1,address2=:address2,address3=:address3,address4=:address4,address5=:address5,
 user_state=:user_state,pincode=:pincode,pan_type=:pan_type,r_title=:r_title,rl_name=:rl_name,rf_name=:rf_name,rm_name=:rm_name,r_address1=:r_address1,r_address2=:r_address2,r_address3=:r_address3,r_address4=:r_address4,r_address5=:r_address5,
 r_state=:r_state,r_pincode=:r_pincode,city=:city,area_code=:area_code,ao_type=:ao_type,rangecode=:rangecode,aocode=:aocode,proof_id=:proof_id,proof_add=:proof_add,proof_dob=:proof_dob,form_pdf=:form_pdf,changes=:changes,remark=:remark,status=:status WHERE id='".$row['id']."'";
$nsdl = $conn->prepare($nsdlsql);
$nsdl_remark = 'Application Updated Successfully';
$nsdl_status = 'holdprocess';
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
$nsdl->bindParam(":aadhaar_num", filter_var($_POST["aadhaar_num"],FILTER_SANITIZE_STRING));
$nsdl->bindParam(":name_aadhaar", filter_var($_POST["name_aadhaar"],FILTER_SANITIZE_STRING));
$nsdl->bindParam(":mob_num", filter_var($_POST["mob_num"],FILTER_SANITIZE_STRING));
$nsdl->bindParam(":email_id", filter_var($_POST["email_id"],FILTER_SANITIZE_STRING));
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
$nsdl->bindParam(":proof_id", $_POST["proof_id"]);
$nsdl->bindParam(":proof_add", $_POST["proof_add"]);
$nsdl->bindParam(":proof_dob", $_POST["proof_dob"]);
$nsdl->bindParam(":form_pdf",$form_link);
$nsdl->bindParam(":changes", $changes);
$nsdl->bindParam(":remark", filter_var($nsdl_remark,FILTER_SANITIZE_STRING));
$nsdl->bindParam(":status", filter_var($nsdl_status,FILTER_SANITIZE_STRING));

if($nsdl->execute()){
    
echo '<script>toastr.success("Application Update Successfully"); setTimeout(function() {
      // Close the current window
      window.close();
    }, 3000);</script>';	
redirect(3000,'index.php');

} else {
    
echo '<script>toastr.error("Invalid! Form Not Update","error")</script>';	
redirect(1500,'');

}
	

} else {
    
echo '<script>toastr.error("Invalid! File Format or Size is Large! Please Upload File Max 2MB","error")</script>';	
redirect(1500,'');

}
}
?>

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
          <!-- Basic Layout -->
          <div class="col-xxl">
            <div class="card mb-4">
              <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Clear Observation</h5> <small class="text-danger float-end" style="font-size: 15px">Reason = <?php echo $row['remark']; ?></small>
              </div>
              <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data">
                  <?php if($row['pan_number']=="newpan") { ?>  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="cat_applicant">Category of Applicant <span class="text-danger">*<span></label>
                    <input name="pan_number" class="form-control" type="hidden" value="newpan" required>
                    <div class="col-sm-9">
                      <select name="cat_applicant" class="select2 form-control"  required>
        				<option value="<?php echo $row['cat_applicant'];?>"><?php echo $row['cat_applicant'];?></option>
        				<option value="Individual">Individual</option>
        				<option value="Firm">Firm</option>
        				<option value="Body of Individuals">Body of Individuals</option>
        				<option value="Hindu Undivided Family">Hindu Undivided Family</option>
        				<option value="Association of Persons">Association of Persons</option>
        				<option value="Local Authority">Local Authority</option>
        				<option value="Trust">Trust</option>
        				<option value="Artificial Juridical Person">Artificial Juridical Person</option>
        				<option value="Government">Government</option>
        				<option value="Limited Liability Partnership">Limited Liability Partnership</option>
        			  </select>
                    </div>
                  </div>
                  <?php } else { ?>
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="pan_number">Pan Number <span class="text-danger">*<span></label>
                    <div class="col-sm-9">
                        <input name="cat_applicant" class="form-control" type="hidden" value="Individual" required>		
                        <input name="pan_number" class="form-control" value="<?php echo $row['pan_number'];?>"  placeholder="Enter Old Pan Number" maxlength="10" onkeyup="this.value = this.value.toUpperCase();" onblur="this.value = this.value.toUpperCase();" required>
                    </div>
                  </div>
                  <?php } ?>
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="l_name">Last Name/ Surname <span class="text-danger">*<span></label>
                    <div class="col-sm-9">
                      <div class="input-group input-group-merge">
                        <input type="text" class="form-control" placeholder="Last Name/ Surname" name="l_name" value="<?php echo $row['l_name'];?>" onkeyup="this.value = this.value.toUpperCase();" onblur="this.value = this.value.toUpperCase();" required>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="f_name">First Name</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" placeholder="First Name" name="f_name" value="<?php echo $row['f_name'];?>" onkeyup="this.value = this.value.toUpperCase();" onblur="this.value = this.value.toUpperCase();">
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="m_name">Middle Name</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" placeholder="Middle Name" name="m_name" value="<?php echo $row['m_name'];?>" onkeyup="this.value = this.value.toUpperCase();" onblur="this.value = this.value.toUpperCase();">
                    </div>
                  </div>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="name_card">Name on Card</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" placeholder="Name on Card" name="name_card" value="<?php echo $row['name_card'];?>" onkeyup="this.value = this.value.toUpperCase();" onblur="this.value = this.value.toUpperCase();" required>
                    </div>
                  </div>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="dob">Date of Birth / Incorporation <span class="text-danger">*<span></label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" placeholder="dd/MM/yyyy" name="dob"  value="<?php echo $row['dob'];?>" onclick=" var v = this.value; if (v.match(/^\d{2}$/) !== null) { this.value = v + '/'; } else if (v.match(/^\d{2}\/\d{2}$/) !== null) { this.value = v + '/'; }" onkeyup=" var v = this.value; if (v.match(/^\d{2}$/) !== null) { this.value = v + '/'; } else if (v.match(/^\d{2}\/\d{2}$/) !== null) { this.value = v + '/'; }" maxlength="10" required>
                    </div>
                  </div>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="gender">Gender <span class="text-danger">*<span></label>
                    <div class="col-sm-9">
                      <select name="gender" class="form-control" required>
        				<option value="<?php echo $row['gender'];?>"><?php echo $row['gender'];?></option>
        				<option value="Male">Male</option>
        				<option value="Female">Female</option>
        				<option value="TransGender">Transgender</option> 
        			  </select>
                    </div>
                  </div>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="fal_name">Father's Last Name <span class="text-danger">*<span></label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" placeholder="Father's Last Name" name="fal_name"  value="<?php echo $row['fal_name'];?>" onkeyup="this.value = this.value.toUpperCase();" onblur="this.value = this.value.toUpperCase();" required>
                    </div>
                  </div>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="faf_name">Father's First Name</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" placeholder="Father's First Name" name="faf_name"  value="<?php echo $row['faf_name'];?>" onkeyup="this.value = this.value.toUpperCase();" onblur="this.value = this.value.toUpperCase();">
                    </div>
                  </div>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="fam_name">Father's Middle Name</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" placeholder="Father's Middle Name" name="fam_name"  value="<?php echo $row['fam_name'];?>" onkeyup="this.value = this.value.toUpperCase();" onblur="this.value = this.value.toUpperCase();">
                    </div>
                  </div>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="aadhaar_num">Aadhaar Number <span class="text-danger">*<span></label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" value="<?php echo $row['aadhaar_num'];?>" placeholder="Aadhaar Number" name="aadhaar_num" onkeyup="this.value = this.value.toUpperCase();" onblur="this.value = this.value.toUpperCase();" maxlength="12" >
                    </div>
                  </div>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="name_aadhaar">Name As Per Aadhaar <span class="text-danger">*<span></label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" placeholder="Name As Per Aadhaar" name="name_aadhaar"  value="<?php echo $row['name_aadhaar'];?>" onkeyup="this.value = this.value.toUpperCase();" onblur="this.value = this.value.toUpperCase();" required>
                    </div>
                  </div>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="mob_num">Mobile Number <span class="text-danger">*<span></label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" placeholder="Mobile Number" name="mob_num"  value="<?php echo $row['mob_num'];?>" onkeyup="this.value = this.value.toUpperCase();" onblur="this.value = this.value.toUpperCase();" maxlength="10" required>
                    </div>
                  </div>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="email_id">Email Address <span class="text-danger">*<span></label>
                    <div class="col-sm-9">
                      <input type="email" class="form-control" placeholder="Email Address" name="email_id"  value="<?php echo $row['email_id'];?>" onkeyup="this.value = this.value.toUpperCase();" onblur="this.value = this.value.toUpperCase();" required>
                    </div>
                  </div>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="address1">Office / Center Name <span class="text-danger">*<span></label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" placeholder="Flat/Room/Door/Block No." name="address1" value="<?php echo $row['address1'];?>"  onkeyup="this.value = this.value.toUpperCase();" onblur="this.value = this.value.toUpperCase();" required>
                    </div>
                  </div>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="address2">Building/Village Name <span class="text-danger">*<span></label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" placeholder="Building/Village" name="address2" value="<?php echo $row['address2'];?>" onkeyup="this.value = this.value.toUpperCase();" onblur="this.value = this.value.toUpperCase();" required>
                    </div>
                  </div>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="address3">Road/Street/Lane/Post Office <span class="text-danger">*<span></label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" placeholder="Road/Street/Lane/Post Office" name="address3" value="<?php echo $row['address3'];?>"onkeyup="this.value = this.value.toUpperCase();" onblur="this.value = this.value.toUpperCase();" required>
                    </div>
                  </div>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="address4">Area/Locality/Sub-Division <span class="text-danger">*<span></label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" placeholder="Area/Locality/Sub-Division" name="address4" value="<?php echo $row['address4'];?>"onkeyup="this.value = this.value.toUpperCase();" onblur="this.value = this.value.toUpperCase();">
                    </div>
                  </div>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="address5">Town/City/District <span class="text-danger">*<span></label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" placeholder="Town/City/District" name="address5" value="<?php echo $row['address5'];?>" onkeyup="this.value = this.value.toUpperCase();" onblur="this.value = this.value.toUpperCase();" required>
                    </div>
                  </div>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="user_state">State/Union Territory <span class="text-danger">*<span></label>
                    <div class="col-sm-9">
                      <select id="user_state" name="user_state" class="select2 form-control" required>
        				<option value="">Please Select</option>
        				<option value="1">ANDAMAN AND NICOBAR ISLANDS</option>
        				<option value="2">ANDHRA PRADESH</option>
        				<option value="3">ARUNACHAL PRADESH</option>
        				<option value="4">ASSAM</option>
        				<option value="5">BIHAR</option>
        				<option value="6">CHANDIGARH</option>
        				<option value="33">CHHATISHGARH</option>
        				<option value="7">DADRA &amp; NAGAR HAVELI</option>
        				<option value="8">DAMAN &amp; DIU</option>
        				<option value="9">DELHI</option>
        				<option value="10">GOA</option>
        				<option value="11">GUJARAT</option>
        				<option value="12">HARYANA</option>
        				<option value="13">HIMACHAL PRADESH</option>
        				<option value="14">JAMMU &amp; KASHMIR</option>
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
        				<option value="29">TAMILNADU</option>
        				<option value="36">TELANGANA</option>
        				<option value="30">TRIPURA</option>
        				<option value="31">UTTAR PRADESH</option>
        				<option value="34">UTTARAKHAND</option>
        				<option value="32">WEST BENGAL</option>
        			  </select>
                    </div>
                  </div>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="Pincode">Pincode <span class="text-danger">*<span></label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" placeholder="Pincode" name="pincode" value="<?php echo $row['pincode'];?>" maxlength="6" onkeyup="this.value = this.value.toUpperCase();" onblur="this.value = this.value.toUpperCase();" required>
                    </div>
                  </div>
                  
                  <?php if($row['pan_number']=="newpan") { ?>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="pan_type">Pan Card Type <span class="text-danger">*<span></label>
                    <div class="col-sm-9">
                      <select id="pan_type" name="pan_type" class="form-control" onchange="TypeFunction(this.value)"  required="">
        				<option value="Normal">Normal</option>
        				<option value="Minor">Minor</option>
        			  </select>
                    </div>
                  </div>
                  
                  <?php } else { ?>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="nofdoc">Number of Documents <span class="text-danger">*<span></label>
                    <div class="col-sm-9">
                      <select id="nofdoc" name="nofdoc" class="form-control" required>
                    	<option value="">Select</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
        			  </select>
                    </div>
                  </div>
                  
                  <?php } ?>
                  
                  <script>
                      $(document).ready(function(){
                          document.getElementById("user_state").value  = "<?php echo $row['user_state'];?>";	
                          document.getElementById("pan_type").value  = "<?php if($row['pan_type']==""){ echo 'Normal'; } else{ echo $row['pan_type']; }?>";		
                          TypeFunction("<?php echo $row['pan_type'];?>");  
                      });
                  </script> 
                  
                  <?php if($row['pan_number']=="newpan") { ?>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="city">City</label>
                    <div class="col-sm-9">
                      <select class="select2 form-control" id="city" name="city" onchange="city_aoCode()" >
        				<option value="">Select City</option>
                        <option value="ABOHAR">ABOHAR</option>
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
                        <option value="ALWAR">ALWAR</option>
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
                        <option value="BEHROR">BEHROR</option>
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
                    </div>
                  </div>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="area_code">Area Code <span class="text-danger">*<span></label>
                    <div class="col-sm-9">
                      <select class="form-control" id="area_code" name="area_code" required="required" readonly required>
        				  <option><?php echo $row['area_code'];?></option>
        			  </select>
                    </div>
                  </div>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="ao_type">Ao Type <span class="text-danger">*<span></label>
                    <div class="col-sm-9">
                      <select class="form-control" id="aotype" name="ao_type" required="required" readonly required>
        				  <option><?php echo $row['ao_type'];?></option>
        			  </select>
                    </div>
                  </div>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="rangecode">Range Code <span class="text-danger">*<span></label>
                    <div class="col-sm-9">
                      <select class="form-control" id="rangecode" name="rangecode" required="required" readonly required>
        				  <option><?php echo $row['rangecode'];?></option>
        			  </select>
                    </div>
                  </div>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="aocode">Ao No <span class="text-danger">*<span></label>
                    <div class="col-sm-9">
                      <select class="form-control" id="aocode" name="aocode" required="required" readonly required>
        				  <option><?php echo $row['aocode'];?></option>
        			  </select>
                    </div>
                  </div>
                  
                  <?php } else {
        	
                    if($rowobj['full_name']=='Y'){
                        $full_name = 'checked';
                    }	
                    if($rowobj['father_name']=='Y'){
                        $father_name = 'checked';
                    }	
                    if($rowobj['date_brith']=='Y'){
                        $date_brith = 'checked';
                    }	
                    if($rowobj['gender']=='Y'){
                        $gender_c = 'checked';
                    }	
                    if($rowobj['address']=='Y'){
                        $address = 'checked';
                  }  ?>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="type_change">Changes Or Correction ? <span class="text-danger">*<span></label>
                    <div class="col-sm-9">
                      <input type="checkbox" name="full_name" value="Y" id="full_name" <?php echo $full_name;?>>
                        <label for="full_name">Full Name</label>
                    
                    	<input type="checkbox" name="father_name" value="Y" id="father_name" <?php echo $father_name;?>>
                        <label for="father_name">Father's Name</label>
                    	
                    	<input type="checkbox" name="date_brith" value="Y" id="date_brith" <?php echo $date_brith;?>>
                        <label for="date_brith">Date of Birth</label>
                    	
                    	<input type="checkbox" name="gender_c" value="Y" id="gender_c" <?php echo $gender_c;?>>
                        <label for="gender_c">Gender</label>
                    	
                    	<input type="checkbox" name="address" value="Y" id="address" <?php echo $address;?>>
                        <label for="address">Address</label>
                    </div>
                  </div>
                  
                  <?php } ?>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="proof_id">Proof of Identity <span class="text-danger">*<span></label>
                    <div class="col-sm-9">
                      <select id="proof_id" name="proof_id" class="form-control" required>
        				<option value="">Please Select</option>
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
                    </div>
                  </div>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="proof_add">Proof of Address <span class="text-danger">*<span></label>
                    <div class="col-sm-9">
                      <select id="proof_add" name="proof_add" class="form-control" required>
        				<option value="">Please Select</option>
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
        				<option>Consumer gas pdoection card or book or piped gas bill(Not more than 3 months old from date of application)</option>
        				<option>Water Bill (Not more than 3 months old from the date of application)</option>
        				<option>Broadband pdoection Bill (Not more than 3 months old from the date of application)</option>
        			  </select>
                    </div>
                  </div>
                  
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="proof_dob">Proof of DOB <span class="text-danger">*<span></label>
                    <div class="col-sm-9">
                      <select id="proof_dob" name="proof_dob" class="form-control" required>
        				<option value="">Please Select</option>
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
                    </div>
                  </div>
                  
                  <?php if($row['status']=="hold") { ?>
        
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label" for="form_pdf">Form PDF (200dpi) <span class="text-danger">*<span></label>
                    <div class="col-sm-9">
                        <input type="file" name="form_pdf" accept="application/pdf" class="form-control">
                    </div>
                  </div>
                  
                  <?php } ?>
                  
                  <div class="row justify-content-end">
                    <div class="col-sm-9">
                      <button name="action" type="submit" class="btn btn-primary active">Submit</button>
                    </div>
                  </div>
                  <script>
                    $(document).ready(function () {
                      // Setting value for nofdoc element
                      $("#nofdoc").val("<?php echo $rowobj['nofdoc'];?>");
                    
                      // Setting values for other elements
                      $("#proof_id").val("<?php echo $row['proof_id'];?>");
                      $("#proof_add").val("<?php echo $row['proof_add'];?>");
                      $("#proof_dob").val("<?php echo $row['proof_dob'];?>");
                      $("#r_title").val("<?php echo $row['r_title'];?>");
                      $("#rl_name").val("<?php echo $row['rl_name'];?>");
                      $("#rf_name").val("<?php echo $row['rf_name'];?>");
                      $("#rm_name").val("<?php echo $row['rm_name'];?>");
                      $("#r_address1").val("<?php echo $row['r_address1'];?>");
                      $("#r_address2").val("<?php echo $row['r_address2'];?>");
                      $("#r_address3").val("<?php echo $row['r_address3'];?>");
                      $("#r_address4").val("<?php echo $row['r_address4'];?>");
                      $("#r_address5").val("<?php echo $row['r_address5'];?>");
                      $("#r_state").val("<?php echo $row['r_state'];?>");
                      $("#r_pincode").val("<?php echo $row['r_pincode'];?>");
                    });
        
                  </script>
                </form>
              </div>
            </div>
          </div>
        </div>
        <?php }?>
    </div>
</div>
<?php
require_once('../layouts/mainFooter.php');
?>