<?php
require_once('../system/connectivity_functions.php');

$authToken = base64_decode($_SESSION['userAuth']);
if (!isset($authToken) || getUsersInfo('username') !== $authToken) {
    header('Location: ../logout.php');
    exit();
}
$current_time = time();
$session_duration = $current_time - $_SESSION['login_time'];

if ($session_duration > 86400) { // 86400 seconds = 24 hours
    // Session has expired, log the user out
    session_unset();
    session_destroy();
    header('Location: ../auth-login.php?expired=1'); // Redirect to the login page
    exit();
}
$webName = getPortalInfo('webName');

// Calculate the midpoint of the string
$midpoint = ceil(strlen($webName) / 2);

// Split the string into two parts
$firstHalf = substr($webName, 0, $midpoint);
$secondHalf = substr($webName, $midpoint);

$authToken = base64_decode($_SESSION['userAuth']);
$sql = $conn->prepare("SELECT * FROM users WHERE username = :session_id");
$sql->bindParam(':session_id', $authToken, PDO::PARAM_STR);
$sql->execute();

// Fetch the data as an associative array
$userdata = $sql->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title><?php echo $pageName; ?></title>


    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/sprint-favicon.png" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="../assets/vendor/fonts/fontawesome.css" />
    <link rel="stylesheet" href="../assets/vendor/fonts/flag-icons.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/spinkit/spinkit.css">
    <link rel="stylesheet" href="../assets/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="../assets/vendor/libs/typeahead-js/typeahead.css">

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/rtl/kavya-all.css" />
    <link rel="stylesheet" href="../assets/vendor/css/rtl/theme-default.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    
    
    <link rel="stylesheet" href="../assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css">
    <link rel="stylesheet" href="../assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css">
    <link rel="stylesheet" href="../assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css">
    <link rel="stylesheet" href="../assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css">

    <link rel="stylesheet" href="../assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css">
    <link rel="stylesheet" href="../assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css">
    <link rel="stylesheet" href="../assets/vendor/libs/jquery-timepicker/jquery-timepicker.css">
    <link rel="stylesheet" href="../assets/vendor/libs/pickr/pickr-themes.css">
    <link rel="stylesheet" href="../assets/css/kavya-txn.css">

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    

    <!-- Other head elements -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>
    <script src="../assets/js/aoliist.js"></script>
    <style>
    /* Adjust scrollbar width */
body::-webkit-scrollbar {
    width: 0px; /* Decrease the width of the scrollbar */
}

/* Change scrollbar color */
body::-webkit-scrollbar-track {
    background-color: #f1f1f1; /* Set the background color of the track */
}

body::-webkit-scrollbar-thumb {
    background-color: #11029e; /* Set the color of the scrollbar thumb */
    border-radius: 0px; /* Optional: round the corners of the thumb */
}
        .powered-by{
            position: absolute;
            font-size: 10px;
            font-weight: bold;
            width: 200px;
            margin-top: 40px;
            left: 16%; /* Adjust as needed */
            color: #0f02a3;
        }
        .sprint-logo-tx {
            font-weight: bold;
            color: red;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1), -2px -2px 4px rgba(0, 0, 0, 0.1);
            /* Adjust values as needed */
        }
        .sprint-box {
            background-image: url(../assets/img/banners/bg-balance.png);
            background-position: right;
            background-size: cover;
        }
        .xxctyOkaM8bvp4lm2amz {
            display: block;
            margin-bottom: 50px;
            text-align: center;
            height: 100px;
            margin-top: 3%;
        }
        .Xj6seOK2KDoCGIvsPERK {
            width: 120px;
        }
        .LFvZ84K5eG9rZIj8aH7y {
            font-size: 12px;
            text-align: center;
        }
        .blockquote {
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }
        blockquote {
            border-left: 4px solid red;
            padding: 8px;
        }
        blockquote {
            margin: 0 0 1rem;
        }
        .figure {
            display: inline-block;
        }
        /* Custom CSS for toastr messages */
        .my-toast-error {
            width: 1000px; /* Set the desired width */
        }
        .loader-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .loader {
            border: 4px solid #ff0000;
            border-top: 4px solid #06007d;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .loader-wrapper p {
            margin-top: 10px;
            font-size: 16px;
            color: #333;
        }
        .success-animation {
            width: 100px; /* Adjust the width as needed */
            height: 100px; /* Adjust the height as needed */
            margin: 0 auto; /* Center the animation horizontally */
        }
        #psfileInput {
            display: none;
        }
        #customFileInput {
            /* Style your custom element here */
            background-color: #EB1616;
            color: #fff;
            padding: 5px;
            cursor: pointer;
            height: 37px;
            border-radius: 5px;
        }
        .layout-menu-fixed-offcanvas .layout-menu {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            margin-right: 0 !important;
            margin-left: 0 !important;
        }
        .kavya-marquee {
            float: left;
            position: relative;
            width: 100%;
            margin-bottom: 5px;
            border: 1px solid #283897;
            background-color: #ffffff;
            border-radius: 5px; /* Rounded corners */
        }
        
        .marquee-title {
            position: absolute;
            padding: 8px 12px;
            font-size: 14px;
            color: #fff;
            background-color: #283897;
            z-index: 1;
            font-weight: 500;
        }
        
        .marquee-txt {
            padding: 6px;
            line-height: 20px;
            font-size: 14px;
            color: #424242;
        }
        
        .title-tip {
            width: 0;
            height: 0;
            top: 12px;
            right: -8px;
            position: absolute;
            border-style: solid;
            border-width: 7px 0px 7px 9px;
            border-color: transparent transparent transparent #283897;
        }
        
        .sprint-category:hover {
	opacity: 1 !important;
	box-shadow: rgb(45 45 45/ 5%) 0px 0px 0px, rgb(49 49 49/ 5%) 0px 2px 2px,
		rgb(42 42 42/ 5%) 0px 4px 4px, rgb(32 32 32/ 5%) 0px 8px 8px,
		rgb(49 49 49/ 5%) 0px 16px 16px, rgb(35 35 35/ 5%) 0px 30px 30px
		!important;
	z-index: 1;
	margin-bottom: 0px;
	height: 183px;
}
    .sprint-category {
        background: #ffffff;
    border: 1px solid #dbdce0;
    width: 100%;
    box-shadow: none !important;
    margin-bottom: 25px;
    border-radius: 15px;
    overflow: hidden;
    padding: 10px 0;
    transition: .5s ease;
    position: relative;
    height: 175px;
    position: relative;
}
.sprint-care {
    background: green;
    text-align: center;
    color: #fff;
    font-size: 12px;
    position: absolute;
    font-weight: bold;
    right: 0;
    top: 0;
    margin: 0 auto;
    width: 65px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-bottom-left-radius: 10px;
}
.sprint-box-top {
    float: left;
    padding: 5% 5% 1%;
    width: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    text-align: center;
}


.sprint-category::before {
	content: '';
	position: absolute;
	z-index: -2;
	left: -100%;
	top: -350%;
	width: 300%;
	height: 800%;
	background-color: #399953;
	background-repeat: no-repeat;
	background-size: 50% 50%, 50% 50%;
	background-position: 0 0, 100% 0, 100% 100%, 0 100%;
	background-image: linear-gradient(#ff4d4d, #ff4d4d),
		linear-gradient(#ff4d4d, #ff4d4d), linear-gradient(#4d4dff, #4d4dff),
		linear-gradient(#4d4dff, #4d4dff);
	animation: rotate 4s linear infinite;
	/* background-image: linear-gradient(#399953, #399953), linear-gradient(#fbb300, #fbb300), linear-gradient(#d53e33, #d53e33), linear-gradient(#377af5, #377af5);
            animation: rotate 4s linear infinite; */
}

sprint-category::after {
	content: '';
	position: absolute;
	z-index: -1;
	left: 1px;
	top: 1px;
	width: calc(100% - 2px);
	height: calc(100% - 2px);
	background: white;
	border-radius: 15px;
	border-top-left-radius: 0 !important;
	border-top-right-radius: 0 !important;
}

@keyframes rotate { 100% {
	transform: rotate(1turn);
}

}
.kavya-heading {
    font-size: 16px;
    font-weight: 600;
    margin: 20px 0 0;
}
.help-video {
    margin: 20px 0;
}
.video {
    width: 100%;
    height: 135px;
    background: #000;
    border-radius: 5px;
    margin: 10px 0 6px;
    overflow: hidden;
    border: 1px solid #a6a6a6;
    text-align: center;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    text-decoration: none;
}
.video-thumbnail {
    width: 100%;
    height: 100%;
}

.video-ico {
    position: absolute;
    width: 40px;
}

.updated-col {
    width: 24%;
    background: #ffffff;
    border-radius: 15px;
    border: 1px solid #dbdce0;
}
    </style>
  </head>

  <body>
      <div class="loader-wrapper">
        <div class="loader"></div>
        <p><?= getPortalInfo('webName')?>...</p>
    </div>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->

        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme vertical">
          <div class="app-brand demo">
              <a href="index" class="app-brand-link">
              <span class="app-brand-logo demo">
                <img src="../assets/img/icons/brands/sp.png" width="200">
        
              </span>
              </a>
            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
              <i class="bx bx-chevron-left bx-sm align-middle"></i>
            </a>
          </div>

          <div class="menu-inner-shadow"></div>
          
          <ul class="menu-inner py-1 ps ps--active-y">
            
            
            <!-- ---------- Side menu start ---------- -->
            
              
            <!-- Dashboard -->
            <li class="menu-item" onclick="activateMenuItem(this)">
              <a href="index.php" class="menu-link">
                <img class="menu-icon tf-icons" src="../assets/img/icons/sprinticon/dashboard.svg">
                <div data-i18n="Dashboard">Dashboard</div>
              </a>
            </li>
            <?php if(getUsersInfo('usertype') === 'mainadmin'){ ?>
            <!-- UserManegement -->
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <img class="menu-icon tf-icons" src="../assets/img/icons/sprinticon/users.svg">
                <div data-i18n="Wallet Management">Users Management</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="createUser.php" class="menu-link">
                    <div data-i18n="Add Wallet">Create User</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="userPanding.php" class="menu-link">
                    <div data-i18n="Balance Transfer">Pending Users</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="userList.php" class="menu-link">
                    <div data-i18n="Wallet Records">User List</div>
                  </a>
                </li>
              </ul>
            </li>
            <?php } ?>
            <!-- Add Money -->
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <img class="menu-icon tf-icons" src="../assets/img/icons/sprinticon/wallet-sprint.svg">
                <div data-i18n="Wallet Management">Wallet Management</div>
              </a>
              <ul class="menu-sub">
               <?php if(getUsersInfo('usertype') !== 'mainadmin'){ ?>   
                <li class="menu-item">
                  <a href="addMoney.php" class="menu-link">
                    <div data-i18n="Add Wallet">Add Wallet</div>
                  </a>
                </li>
                <?php } ?>
                <?php if(getUsersInfo('usertype') === 'mainadmin'){ ?>
                <li class="menu-item">
                  <a href="transferBalance.php" class="menu-link">
                    <div data-i18n="Balance Transfer">Balance Transfer</div>
                  </a>
                </li>
                <?php } ?>
                <li class="menu-item">
                  <a href="MoneyRecords.php" class="menu-link">
                    <div data-i18n="Wallet Records">Wallet Records</div>
                  </a>
                </li>
                <?php if(getUsersInfo('usertype') === 'mainadmin'){ ?>
                <li class="menu-item">
                  <a href="GatewaySetting.php" class="menu-link">
                    <div data-i18n="Gateway Setting">Getway Setting</div>
                  </a>
                </li>
                <?php } ?>
              </ul>
            </li>
            <!-- Utilitys -->
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <img class="menu-icon tf-icons" src="../assets/img/icons/sprinticon/kavya-bbps-menu.svg">
                <div data-i18n="Utility">Utility</div>
                <img class="menu-icon ms-auto" src="../assets/img/icons/sprinticon/kavyaBbps.png">
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="mobileRecharge.php" class="menu-link">
                    <div data-i18n="Mobile Recharge">Mobile Recharge</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="dthRecharge.php" class="menu-link">
                    <div data-i18n="DTH Recharge">DTH Recharge</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="bbpsHistory.php" class="menu-link">
                    <div data-i18n="TXN History">TXN History</div>
                  </a>
                </li>
              </ul>
            </li>
            <!-- Paperless PAN Management -->
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <img class="menu-icon tf-icons" src="../assets/img/icons/sprinticon/pan.svg">
                <div data-i18n="Physical PAN">Physical PAN</div>
                <img class="menu-icon ms-auto" src="../assets/img/icons/sprinticon/nsdl.png">
              </a>
              <?php if(getUsersInfo('usertype') === 'mainadmin'){ ?>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="new-pan-request.php" class="menu-link">
                    <div data-i18n="NEW Request">NEW Request <?php
                        $sql = $conn->prepare("SELECT COUNT(*) FROM nsdlpancard WHERE remark = ? AND type = ?");
                        $sql->execute(['From Upload Successfully','new pan']);
                        $count = $sql->fetchColumn();
                            echo $count;
                        ?></div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="change-pan-request.php" class="menu-link">
                    <div data-i18n="CSF Request">CSF Request <?php
                        $sql = $conn->prepare("SELECT COUNT(*) FROM nsdlpancard WHERE remark = ? AND type = ?");
                        $sql->execute(['From Upload Successfully','Correction pan']);
                        $count = $sql->fetchColumn();
                            echo $count;
                        ?></div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="PhysicalPan-Under-Observation.php" class="menu-link">
                    <div data-i18n="Clear Observation">Clear Observation <?php
                        $sql = $conn->prepare("SELECT COUNT(*) FROM nsdlpancard WHERE remark = ? AND type = ?");
                        $sql->execute(['From Upload Successfully','holdprocess']);
                        $count = $sql->fetchColumn();
                            echo $count;
                        ?></div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="Physical-pan-record.php" class="menu-link">
                    <div data-i18n="PAN Record">PAN Record</div>
                  </a>
                </li>
              </ul>
              <?php } ?>
              <?php if(getUsersInfo('usertype') !== 'mainadmin'){ ?>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="PhysicalPanNew.php" class="menu-link">
                    <div data-i18n="Indian Citizen (F49A)">Indian Citizen (F49A)</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="PhysicalPanChange.php" class="menu-link">
                    <div data-i18n="PAN Change Request">PAN Change Request</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="PhysicalPanUpload.php" class="menu-link">
                    <div data-i18n="Form Upload">Form Upload</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="PhysicalPan-Under-Observation.php" class="menu-link">
                    <div data-i18n="Under Observation">Under Observation <?php
                        $getc = getUsersInfo('id');
                        $sql = $conn->prepare("SELECT COUNT(*) FROM nsdlpancard WHERE status = 'hold' AND user_id = ? ");
                        $sql->execute([$getc]);
                        $count = $sql->fetchColumn();
                        echo $count;
                        ?></div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="PhysicalPAN-Record.php" class="menu-link">
                    <div data-i18n="PAN Record">PAN Record</div>
                  </a>
                </li>
              </ul>
            </li>
            <?php } ?>
            
            <!-- Aadhaar Print Capacitor -->
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <img class="menu-icon tf-icons" src="../assets/img/icons/sprinticon/aadhaar_english_logo.svg">
                <div data-i18n="Aadhaar Advance">Aadhaar Advance</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="aadaarPrint.php" class="menu-link">
                    <div data-i18n="Aadhaar Print">Aadhaar Print</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="aadaarManual.php" class="menu-link">
                    <div data-i18n="Aadhaar Manual Print">Aadhaar Manual Print</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="aadaarList.php" class="menu-link">
                    <div data-i18n="Aadhaar LIST">Aadhaar LIST</div>
                  </a>
                </li>
              </ul>
            </li>
            <!-- Voter Print Capacitor -->
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <img class="menu-icon tf-icons" src="../assets/img/icons/sprinticon/logo-voter.svg">
                <div data-i18n="Voter Advance">Voter Advance</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="voterAdvance.php" class="menu-link">
                    <div data-i18n="Voter Print">Voter Print</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="voterManual.php" class="menu-link">
                    <div data-i18n="Voter Original PDF">Voter Original PDF</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="voterList.php" class="menu-link">
                    <div data-i18n="Voter LIST">Voter LIST</div>
                  </a>
                </li>
              </ul>
            </li>
            <!-- Rashan Print Capacitor -->
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <img class="menu-icon tf-icons" src="../assets/img/icons/sprinticon/logo-rashan.png">
                <div data-i18n="Rashan Advance">Rashan Advance</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="rashanAdvance.php" class="menu-link">
                    <div data-i18n="Rashan Card Print">Rashan Card Print</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="rashanList.php" class="menu-link">
                    <div data-i18n="Rashan LIST">Rashan LIST</div>
                  </a>
                </li>
              </ul>
            </li>
            <!-- Ayushman Print Capacitor -->
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <img class="menu-icon tf-icons" src="../assets/img/icons/sprinticon/ayushman-thumbnail.bmp">
                <div data-i18n="Ayushman Advance">Ayushman Advance</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="aushmanAdvance.php" class="menu-link">
                    <div data-i18n="Ayushman Print">Ayushman Print</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="ayushmanList.php" class="menu-link">
                    <div data-i18n="Ayushman LIST">Ayushman LIST</div>
                  </a>
                </li>
              </ul>
            </li>
            <!-- Licence Print Capacitor -->
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <img class="menu-icon tf-icons" src="../assets/img/icons/sprinticon/dl.png">
                <div data-i18n="Licence Advance">Licence Advance</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="licenceAdvance.php" class="menu-link">
                    <div data-i18n="Driving Licence Print">Driving Licence Print</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="licenceList.php" class="menu-link">
                    <div data-i18n="Licence LIST">Licence LIST</div>
                  </a>
                </li>
              </ul>
            </li>
            <!-- Vehicle Rc Print Capacitor -->
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <img class="menu-icon tf-icons" src="../assets/img/icons/txnMode-icon/car-legal-document-report-agreement-check-loan-form-list-approved-vehicle-registration-tax-purchase.png">
                <div data-i18n="RC Advance">RC Advance</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="rcAdvance.php" class="menu-link">
                    <div data-i18n="Vehicle RC Print">Vehicle RC Print</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="rcList.php" class="menu-link">
                    <div data-i18n="RC LIST">RC LIST</div>
                  </a>
                </li>
              </ul>
            </li>
            <!-- Manual PAN Capacitor -->
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <img class="menu-icon tf-icons" src="../assets/img/icons/sprinticon/pan.svg">
                <div data-i18n="Manual PAN">Manual PAN</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="manual-pan-generate.php" class="menu-link">
                    <div data-i18n="Manual PAN Print">Manual PAN Print</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="mpanList.php" class="menu-link">
                    <div data-i18n="RC LIST">Manual PAN LIST</div>
                  </a>
                </li>
              </ul>
            </li>
            <!-- Job Card Capacitor -->
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <img class="menu-icon tf-icons" src="../assets/img/icons/sprinticon/printer.svg">
                <div data-i18n="Job Card">Job Card</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="jobCard.php" class="menu-link">
                    <div data-i18n="Job Card Print">Job Card Print</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="jobList.php" class="menu-link">
                    <div data-i18n="Job Card LIST">Job Card LIST</div>
                  </a>
                </li>
              </ul>
            </li>
            <!-- PAN Find Capacitor -->
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <img class="menu-icon tf-icons" src="../assets/img/icons/sprinticon/search.svg">
                <div data-i18n="Aadhaar To PAN">Aadhaar To PAN</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="KnowPAN.php" class="menu-link">
                    <div data-i18n="Aadhar To Pan FIND">Aadhar To Pan FIND</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="pan-find-list.php" class="menu-link">
                    <div data-i18n="Find LIST">Find LIST</div>
                  </a>
                </li>
              </ul>
            </li>
            <!-- Voter Mobile Link Capacitor -->
            <li class="menu-item">
              <a href="voterMobileLinking.php" class="menu-link">
                <img class="menu-icon tf-icons" src="../assets/img/icons/sprinticon/link-voter.svg">
                <div data-i18n="Voter Mobile Linking">Voter Mobile Linking</div>
              </a>
            </li>

            

            <!-- EID TO AADHAAR NUMBER -->
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <img class="menu-icon tf-icons" src="../assets/img/icons/sprinticon/aadhaar_english_logo.svg">
                <div data-i18n="EID to Aadhaar">EID to Aadhaar</div>
                <img class="menu-icon ms-auto" src="../assets/img/icons/sprinticon/newBadge.svg">
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="eid-aadhaar.php" class="menu-link">
                    <div data-i18n="Eid to Aadhaar No.">Eid to Aadhaar No.</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="list-aadhaar-eid.php" class="menu-link">
                    <div data-i18n="Aadhaar number List">Aadhaar number List</div>
                  </a>
                </li>
              </ul>
            </li>
            <!-- Paperless PAN Management -->
            <li class="menu-item">
              <a href="e-kycPanApplication.php" class="menu-link">
                <img class="menu-icon tf-icons" src="../assets/img/icons/sprinticon/pan.svg">
                <div data-i18n="Paperless PAN">Paperless PAN</div>
                <img class="menu-icon ms-auto" src="../assets/img/icons/sprinticon/nsdl.png">
              </a>
            </li>
            <!-- Transaction Records -->
            <li class="menu-item">
              <a href="TransactionRecords.php" class="menu-link">
                <img class="menu-icon tf-icons" src="../assets/img/icons/sprinticon/history.svg">
                <div data-i18n="Transaction Records">Transaction Records</div>
              </a>
            </li>
            <?php if(getUsersInfo('usertype') === 'mainadmin'){ ?>
            <li class="menu-item">
              <a href="portalSetting.php" class="menu-link">
                <img class="menu-icon tf-icons" src="../assets/img/icons/sprinticon/setting.svg">
                <div data-i18n="Portal Settings">Portal Settings</div>
              </a>
            </li>
            <?php } ?>
            <li class="menu-item">
              <a href="javascript:void(0);" onclick="confirmLogout()" class="menu-link">
                <img class="menu-icon tf-icons" src="../assets/img/icons/sprinticon/logout.svg">
                <div data-i18n="Logout">Logout</div>
              </a>
            </li>
          </ul>
        </aside>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->

          <nav
            class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
            id="layout-navbar">
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
              <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="bx bx-menu bx-sm"></i>
              </a>
            </div>

            <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
              <!-- Search -->
              <div class="navbar-nav align-items-center">
                <div class="nav-item d-flex align-items-center">
                  <i class="bx bx-search fs-4 lh-0"></i>
                  <input
                    type="text"
                    class="form-control border-0 shadow-none ps-1 ps-sm-2"
                    placeholder="Search..."
                    aria-label="Search..." />
                </div>
              </div>
              <!-- /Search -->

              <ul class="navbar-nav flex-row align-items-center ms-auto">
                <!-- Place this tag where you want the button to render. -->
                <li class="nav-item lh-1 me-3">
                  <button type="button" class="btn btn-facebook"><i class="tf-icons bx bx-wallet me-1"></i><?= number_format(getUsersInfo('balance'), 2) ?></button>
                </li>

                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                  <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                      <img src="../assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="#">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar avatar-online">
                              <img src="../assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <span class="fw-medium d-block"><?= getUsersInfo('owner_name'); ?></span>
                            <small class="text-muted"><?= ucfirst(getUsersInfo('usertype')); ?></small>
                          </div>
                        </div>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="userProfile.php">
                        <i class="bx bx-user me-2"></i>
                        <span class="align-middle">My Profile</span>
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="userProfile.php">
                        <i class="bx bx-cog me-2"></i>
                        <span class="align-middle">Settings</span>
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="userCertificate.php">
                        <span class="d-flex align-items-center align-middle">
                          <i class="flex-shrink-0 bx bx-check-shield me-2"></i>
                          <span class="flex-grow-1 align-middle ms-1">Certificate</span>
                        </span>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="javascript:void(0);" onclick="confirmLogout()">
                        <i class="bx bx-power-off me-2"></i>
                        <span class="align-middle">Log Out</span>
                      </a>
                    </li>
                  </ul>
                </li>
                <!--/ User -->
              </ul>
            </div>
          </nav>

          <!-- / Navbar -->
          <!-- Content wrapper -->
          <div class="content-wrapper">