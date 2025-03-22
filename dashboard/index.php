<?php 
// error_reporting(E_ALL); // ini_set('display_errors', 1);
$pageName = "Home"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');

?>
<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    
    
    
    
    <div class="row">
    <div class="col-xs-12 col-md-9">
          <div class="row">
            <a class="col-sm-4 col-xl-3" href="aadaarPrint.php">
					<button class="sprint-category" type="submit" id="submitBtn">
					<div class="sprint-care"><b>Live</b></div>    
					<div class="sprint-box-top">
						<span class=""><img src="https://print.bestallhost.com/assets/img/icons/sprinticon/aadhaar_english_logo.svg" height="100" weight="100"></span>
						<span class="srvs-name">
						<p><b>Aadhaar Print Through Biometric</b></p>
						</span>
						</div>
						
						<div class="clear"></div>
					</button>
					
			</a>
			<a class="col-sm-4 col-xl-3" href="rashanAdvance.php">
					<button class="sprint-category" type="submit" id="submitBtn">
					<div class="sprint-care"><b>Live</b></div>    
					<div class="sprint-box-top">
						<span class=""><img src="https://print.bestallhost.com/assets/img/icons/sprinticon/logo-rashan.png" height="100" weight="100"></span>
						<span class="srvs-name">
						<p><b>Rashan Verification</b></p>
						</span>
						</div>
						<div class="clear"></div>
					</button>
			</a>
            <a class="col-sm-4 col-xl-3" href="voterAdvance.php">
					<button class="sprint-category" type="submit" id="submitBtn">
					<div class="sprint-care"><b>Live</b></div>    
					<div class="sprint-box-top">
						<span class=""><img src="https://print.bestallhost.com/assets/img/icons/sprinticon/logo-voter.svg" height="100" weight="100"></span>
						<span class="srvs-name">
						<p><b>Voter Crad Verification</b></p>
						</span>
						</div>
						<div class="clear"></div>
					</button>
			</a>
			<a class="col-sm-4 col-xl-3" href="KnowPAN.php">
					<button class="sprint-category" type="submit" id="submitBtn">
					<div class="sprint-care"><b>Live</b></div>    
					<div class="sprint-box-top">
						<span class=""><img src="https://print.bestallhost.com/assets/img/icons/sprinticon/search.svg" height="100" weight="100"></span>
						<span class="srvs-name">
						<p><b>Pan Find Through UID</b></p>
						</span>
						</div>
						<div class="clear"></div>
					</button>
			</a>
			<a class="col-sm-4 col-xl-3" href="voterMobileLinking.php">
					<button class="sprint-category" type="submit" id="submitBtn">
					<div class="sprint-care"><b>Live</b></div>    
					<div class="sprint-box-top">
						<span class=""><img src="https://print.bestallhost.com/assets/img/icons/sprinticon/link-voter.svg" height="100" weight="100"></span>
						<span class="srvs-name">
						<p><b>Voter Mobile Linking</b></p>
						</span>
						</div>
						<div class="clear"></div>
					</button>
			</a>
			<a class="col-sm-4 col-xl-3" href="licenceAdvance.php">
					<button class="sprint-category" type="submit" id="submitBtn">
					<div class="sprint-care"><b>Live</b></div>    
					<div class="sprint-box-top">
						<span class=""><img src="https://print.bestallhost.com/assets/img/icons/sprinticon/dl.png" height="100" weight="100"></span>
						<span class="srvs-name">
						<p><b>Driving Licence Verification</b></p>
						</span>
						</div>
						<div class="clear"></div>
					</button>
			</a>
			<a class="col-sm-4 col-xl-3">
					<button class="sprint-category" type="submit" id="submitBtn">
					<div class="sprint-care" style="background: #fa02e9;"><b>Close</b></div>    
					<div class="sprint-box-top">
						<span class=""><img src="https://print.bestallhost.com/assets/img/icons/sprinticon/search.svg" height="100" weight="100"></span>
						<span class="srvs-name">
						<p><b>Pan Verification Detailed</b></p>
						</span>
						</div>
						<div class="clear"></div>
					</button>
			</a>
			<a class="col-sm-4 col-xl-3" href="aushmanAdvance.php">
					<button class="sprint-category" type="submit" id="submitBtn">
					<div class="sprint-care"><b>Live</b></div>    
					<div class="sprint-box-top">
						<span class=""><img src="https://print.bestallhost.com/assets/img/icons/sprinticon/ayushman-thumbnail.bmp" height="100" weight="100"></span>
						<span class="srvs-name">
						<p><b>Ayushman Verification</b></p>
						</span>
						</div>
						<div class="clear"></div>
					</button>
			</a>
			<a class="col-sm-4 col-xl-3" href="e-kycPanApplication.php">
					<button class="sprint-category" type="submit" id="submitBtn">
					<div class="sprint-care"><b>Live</b></div>    
					<div class="sprint-box-top">
						<span class=""><img src="https://print.bestallhost.com/assets/img/icons/sprinticon/pan.svg" height="100" weight="100"></span>
						<span class="srvs-name">
						<p><b>Paperless Pan Card (Instant PAN)</b></p>
						</span>
						</div>
						<div class="clear"></div>
					</button>
			</a>
			<a class="col-sm-4 col-xl-3" href="rcAdvance.php">
					<button class="sprint-category" type="submit" id="submitBtn">
					<div class="sprint-care"><b>Live</b></div>    
					<div class="sprint-box-top">
						<span class=""><img src="../assets/img/icons/sprinticon/rc-preview.png" height="100" weight="100"></span>
						<span class="srvs-name">
						<p><b>Vehicle Registration Certificate</b></p>
						</span>
						</div>
						<div class="clear"></div>
					</button>
			</a>
		</div>
		</div>
    <div class="col-xs-12  col-md-3 updated-col">
       <div class="imp-updates">
            <div class="kavya-heading"></div>
            <h4 style="color:red">Important Updates</h4>
            <p style="color: black">All services are running properly, just wait for the next update for the physical pan card application.
            </p>
            <div></div>
        </div> 
        <div class="dash-divider"></div>
        <div class="kavya-heading">Recommended videos</div>
		 <div class="help-video">
            <a class="video GATag" data-gatag="How to apply instant pan card" target="_blank" href="">
                <img src="../assets/img/backgrounds/how-to-apply-instant-pan-card.png" class="video-thumbnail">
                <img src="../assets/img/backgrounds/iconfinder_youtube_317714.png" class="video-ico">
            </a>
            <p>How to apply instant pan card </p>
          
        </div>
        <div class="help-video">
            <div class="video">
			<a class="video GATag" data-gatag="Learn the easy way to print Aadhaar" target="_blank" href="">
                <img src="../assets/img/backgrounds/learn-the-easy-way-to-print-Aadhaar" class="video-thumbnail">
                <img src="../assets/img/backgrounds/iconfinder_youtube_317714.png" class="video-ico">
            </a>
			</div>
            <p>Learn the easy way to print Aadhaar </p>
          
        </div>
		<div class="help-video">
            <a class="video GATag" data-gatag="Update Aadhar Card Watch Full Video" target="_blank" href="">
                <img src="../assets/img/backgrounds/aadhaar-update.png" class="video-thumbnail">
                <img src="../assets/img/backgrounds/iconfinder_youtube_317714.png" class="video-ico">
            </a>
            <p>Update Aadhar Card Watch Full Video</p>
        </div>
		<div class="help-video">
            <a class="video GATag" data-gatag="How to apply physical PAN card, watch full video" target="_blank" href="">
                <img src="../assets/img/backgrounds/how-to-apply-physical-pan-card.png" class="video-thumbnail">
                <img src="../assets/img/backgrounds/iconfinder_youtube_317714.png" class="video-ico">
            </a>
            <p>How to apply physical PAN card, watch full video</p>
        </div>


<!--


-->
       
    </div>
</div>

</div>
<!-- / Content -->

            
        

<?php require_once('../layouts/mainFooter.php'); ?>