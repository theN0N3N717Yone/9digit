<?php 
$pageName = "Portal Settings"; // Replace this with the actual page name
$_SESSION['userAuth'] = "User Authentication";
require_once('../layouts/mainHeader.php');
if(getUsersInfo('usertype') === "mainadmin"){

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = connectDB();
    $userID = '1';

    if (!isset($_POST['webName'], $_POST['poweredBy'], $_POST['copyRight'], $_POST['webMobile'], $_POST['webEmail'], $_POST['webSocket'], $_POST['webUrl'], $_POST['updated_at'], $_POST['sender_email'])) {

    } else {
        $webName = safe_str($_POST['webName']);
        $poweredBy = safe_str($_POST['poweredBy']);
        $copyRight = safe_str($_POST['copyRight']);
        $webMobile = safe_str($_POST['webMobile']);
        $webEmail = safe_str($_POST['webEmail']);
        $webSocket = safe_str($_POST['webSocket']);
        $webUrl = safe_str($_POST['webUrl']);
        $updated_at = safe_str($_POST['updated_at']);
        $sender_email = safe_str($_POST['sender_email']);
        $supportMob = safe_str($_POST['supportMob']);
        $mob1 = safe_str($_POST['mob1']);
        $mob2 = safe_str($_POST['mob2']);
        $mob3 = safe_str($_POST['mob3']);

        $updateQuery = "UPDATE `portalSettings` SET `webSocket`=:webSocket, `webUrl`=:webUrl, `webName`=:webName, `poweredBy`=:poweredBy, `copyRight`=:copyRight, `webMobile`=:webMobile, `webEmail`=:webEmail, `updated_at`=:updated_at, `sender_email`=:sender_email, `supportMob`=:supportMob, `mob1`=:mob1, `mob2`=:mob2, `mob3`=:mob3 WHERE id=:id";

        $statementPortalInfo = $conn->prepare($updateQuery);
        $statementPortalInfo->bindParam(':webSocket', $webSocket, PDO::PARAM_STR);
        $statementPortalInfo->bindParam(':webUrl', $webUrl, PDO::PARAM_STR);
        $statementPortalInfo->bindParam(':webName', $webName, PDO::PARAM_STR);
        $statementPortalInfo->bindParam(':poweredBy', $poweredBy, PDO::PARAM_STR);
        $statementPortalInfo->bindParam(':copyRight', $copyRight, PDO::PARAM_STR);
        $statementPortalInfo->bindParam(':webMobile', $webMobile, PDO::PARAM_STR);
        $statementPortalInfo->bindParam(':webEmail', $webEmail, PDO::PARAM_STR);
        $statementPortalInfo->bindParam(':updated_at', $updated_at, PDO::PARAM_STR);
        $statementPortalInfo->bindParam(':sender_email', $sender_email, PDO::PARAM_STR);
        $statementPortalInfo->bindParam(':supportMob', $supportMob, PDO::PARAM_STR);
        $statementPortalInfo->bindParam(':mob1', $mob1, PDO::PARAM_STR);
        $statementPortalInfo->bindParam(':mob2', $mob2, PDO::PARAM_STR);
        $statementPortalInfo->bindParam(':mob3', $mob3, PDO::PARAM_STR);
        
        $statementPortalInfo->bindParam(':id', $userID, PDO::PARAM_INT);

        if ($statementPortalInfo->execute()) {
            echo '<script>toastr.success("Portal Information updated successfully.");</script>';
            redirect(1500, ''); // Redirect to some page after successful update
        } else {
            $errorInfo = $statementPortalInfo->errorInfo();
            echo '<script>toastr.error("Portal Information updating error: ' . $errorInfo[2] . '");</script>';
            redirect(1500, ''); // Redirect to some page after update error
        }
    }

    if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
        $faviconData = base64_encode(file_get_contents($_FILES['favicon']['tmp_name']));

        // Update the favicon in the database
        $updateFaviconQuery = "UPDATE `portalSettings` SET `favicon`=:favicon WHERE id=:id";
        $statementFavicon = $conn->prepare($updateFaviconQuery);
        $statementFavicon->bindParam(':favicon', $faviconData, PDO::PARAM_LOB);
        $statementFavicon->bindParam(':id', $userID, PDO::PARAM_INT);

        if ($statementFavicon->execute()) {
            echo '<script>toastr.success("Favicon updated successfully.");</script>';
            redirect(1500, ''); // Redirect to some page after successful update
        } else {
            $errorInfo = $statementFavicon->errorInfo();
            echo '<script>toastr.error("Favicon updating error: ' . $errorInfo[2] . '");</script>';
            redirect(1500, ''); // Redirect or perform any other action after favicon update error
        }
    }

    if (isset($_POST['apiUpdate']) && isset($_POST['apiUrl']) && isset($_POST['accessToken'])) {
        $apiUrl = safe_str($_POST['apiUrl']);
        $accessToken = safe_str($_POST['accessToken']);

        // Update API credentials in the database
        $updateApiQuery = "UPDATE `portalSettings` SET `apiUrl`=:apiUrl, `accessToken`=:accessToken WHERE id=:id";
        $statementApi = $conn->prepare($updateApiQuery);
        $statementApi->bindParam(':apiUrl', $apiUrl, PDO::PARAM_STR);
        $statementApi->bindParam(':accessToken', $accessToken, PDO::PARAM_STR);
        $statementApi->bindParam(':id', $userID, PDO::PARAM_INT);

        if ($statementApi->execute()) {
            echo '<script>toastr.success("API credentials updated successfully.");</script>';
            redirect(1500, ''); // Redirect to some page after successful update
        } else {
            $errorInfo = $statementApi->errorInfo();
            echo '<script>toastr.error("API credentials updating error: ' . $errorInfo[2] . '");</script>';
            redirect(1500, ''); // Redirect or perform any other action after API credentials update error
        }
    }
    
    if (isset($_POST['rcApiUpdate']) && isset($_POST['rcUrl']) && isset($_POST['rcToken']) && isset($_POST['rcUsername'])) {
        $rcUrl = safe_str($_POST['rcUrl']);
        $rcToken = safe_str($_POST['rcToken']);
        $rcUsername = safe_str($_POST['rcUsername']);

        // Update API credentials in the database
        $updateApiQuery = "UPDATE `portalSettings` SET `rcUrl`=:rcUrl, `rcToken`=:rcToken, `rcUsername`=:rcUsername WHERE id=:id";
        $statementApi = $conn->prepare($updateApiQuery);
        $statementApi->bindParam(':rcUrl', $rcUrl, PDO::PARAM_STR);
        $statementApi->bindParam(':rcToken', $rcToken, PDO::PARAM_STR);
        $statementApi->bindParam(':rcUsername', $rcUsername, PDO::PARAM_STR);
        $statementApi->bindParam(':id', $userID, PDO::PARAM_INT);

        if ($statementApi->execute()) {
            echo '<script>toastr.success("API recharge credentials updated successfully.");</script>';
            redirect(1500, ''); // Redirect to some page after successful update
        } else {
            $errorInfo = $statementApi->errorInfo();
            echo '<script>toastr.error("API credentials updating error: ' . $errorInfo[2] . '");</script>';
            redirect(1500, ''); // Redirect or perform any other action after API credentials update error
        }
    }
    
    
    if (isset($_POST['whatsappApiUpdate']) && isset($_POST['wapiUrl']) && isset($_POST['wapiToken']) && isset($_POST['wapiSender'])) {
        $wapiUrl = $_POST['wapiUrl'];
        $wapiToken = safe_str($_POST['wapiToken']);
        $wapiSender = safe_str($_POST['wapiSender']);

        // Update API credentials in the database
        $updateApiQuery = "UPDATE `portalSettings` SET `wapiUrl`=:wapiUrl, `wapiToken`=:wapiToken, `wapiSender`=:wapiSender WHERE id=:id";
        $statementApi = $conn->prepare($updateApiQuery);
        $statementApi->bindParam(':wapiUrl', $wapiUrl, PDO::PARAM_STR);
        $statementApi->bindParam(':wapiToken', $wapiToken, PDO::PARAM_STR);
        $statementApi->bindParam(':wapiSender', $wapiSender, PDO::PARAM_STR);
        $statementApi->bindParam(':id', $userID, PDO::PARAM_INT);

        if ($statementApi->execute()) {
            echo '<script>toastr.success("API whatsapp credentials updated successfully.");</script>';
            redirect(1500, ''); // Redirect to some page after successful update
        } else {
            $errorInfo = $statementApi->errorInfo();
            echo '<script>toastr.error("API credentials updating error: ' . $errorInfo[2] . '");</script>';
            redirect(1500, ''); // Redirect or perform any other action after API credentials update error
        }
    }
}
?>
<div class="container-xxl flex-grow-1 container-p-y">
   <div class="col-sm-12 col-xl-12">
        <div class="card rounded h-100 p-4">
            <h6 class="mb-4">
                <button class="btn btn-danger account-image-reset active" type="button" data-bs-toggle="offcanvas" data-bs-target="#printApi" aria-controls="printApi">Print Api Credentials Update</button>
                <button class="btn btn-danger account-image-reset active" type="button" data-bs-toggle="offcanvas" data-bs-target="#rechargeApi" aria-controls="rechargeApi">Recharge Api Credentials Update</button>
                 <button class="btn btn-danger account-image-reset active" type="button" data-bs-toggle="offcanvas" data-bs-target="#whatsappApi" aria-controls="whatsappApi">Whatsapp Api Credentials Update</button>
            </h6>
            <form class="was-validated" id="addWallet" action="" method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                  <div class="col">
                      <label for="webName" class="form-label">Company Name</label>
                    <input type="text" class="form-control" name="webName" id="webName" value="<?= getPortalInfo('webName')?>" placeholder="Company Name" aria-label="Company Name">
                  </div>
                  <div class="col">
                      <label for="poweredBy" class="form-label">Powered By</label>
                    <input type="text" class="form-control" name="poweredBy" id="poweredBy" value="<?= getPortalInfo('poweredBy')?>" placeholder="poweredBy" aria-label="poweredBy">
                  </div>
                  <div class="col">
                      <label for="copyRight" class="form-label">Copy Right</label>
                    <input type="text" class="form-control" name="copyRight" id="copyRight" value="<?= getPortalInfo('copyRight')?>" placeholder="Copy Right" aria-label="Copy Right">
                  </div>
                  <div class="col">
                      <label for="webMobile" class="form-label">Mobile Number</label>
                    <input type="tel" class="form-control" name="webMobile" id="webMobile" value="<?= getPortalInfo('webMobile')?>" placeholder="Mobile Number" aria-label="Mobile Number">
                  </div>
                  <div class="col">
                      <label for="webEmail" class="form-label">Email address</label>
                    <input type="email" class="form-control" name="webEmail" id="webEmail" value="<?= getPortalInfo('webEmail')?>" placeholder="Email address" aria-label="Email address">
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col">
                    <label for="webSocket" class="form-label">Socket</label>
                        <select class="form-select" name="webSocket" id="webSocket" aria-label="Socket">
                            <option value="http" <?= (getPortalInfo('webSocket') === 'http') ? 'selected' : '' ?>>http</option>
                            <option value="https" <?= (getPortalInfo('webSocket') === 'https') ? 'selected' : '' ?>>https</option>
                        </select>
                    </div>
                  <div class="col">
                      <label for="webUrl" class="form-label">Web URL</label>
                    <input type="text" class="form-control" name="webUrl" id="webUrl" value="<?= getPortalInfo('webUrl')?>" placeholder="Web URL" aria-label="Web URL">
                  </div>
                  <div class="col">
                      <label for="updated_at" class="form-label">Last Update</label>
                    <input type="text" class="form-control flatpickr-input" name="updated_at" id="flatpickr-multi" value="<?= getPortalInfo('updated_at')?>" placeholder="Last Update" readonly="readonly">
                  </div>
                  <div class="col">
                    <input type="hidden" class="form-control" name="sender_email" id="sender_email" value="<?= getPortalInfo('sender_email')?>" placeholder="Last Update" aria-label="Sender Email">
                  </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                     <label for="fevicon" class="form-label">Fevicon Update</label>
                     <input type="file" class="form-control" name="favicon" id="favicon" accept="image/png" />
                  </div>
                  <div class="col"></div>
                  <div class="col"></div>
                  
                  <li class="small text-uppercase mt-4" style="color:black;">
        				<span class="menu-header-text"><b>Support / Query Numbers :-</b></span>
            	  </li>
                    <div class="row mb-6 mt-4">
                  <div class="col">
                      <label for="supportMob" class="form-label">Callback Whatsapp Number</label>
                    <input type="text" class="form-control" name="supportMob" id="supportMob" value="<?= getPortalInfo('supportMob')?>" placeholder="Company Name" aria-label="Company Name">
                  </div>
                  <div class="col">
                      <label for="mob1" class="form-label">Query No 1</label>
                    <input type="text" class="form-control" name="mob1" id="mob1" value="<?= getPortalInfo('mob1')?>" placeholder="mob1" aria-label="poweredBy">
                  </div>
                  <div class="col">
                      <label for="mob2" class="form-label">Query No 2</label>
                    <input type="text" class="form-control" name="mob2" id="mob2" value="<?= getPortalInfo('mob2')?>" placeholder="mob2" aria-label="mob2">
                  </div>
                  <div class="col">
                      <label for="mob3" class="form-label">Query No 3</label>
                    <input type="tel" class="form-control" name="mob3" id="mob3" value="<?= getPortalInfo('mob3')?>" placeholder="mob3" aria-label="mob3">
                  </div>
                </div>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>
    <!-- Off-canvas Form for Editing Print API -->
    <div class="offcanvas offcanvas-end rounded h-100 border border-primary" tabindex="-1" id="printApi" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header">
            <h5 id="offcanvasRightLabel">Print Api Credentials Update</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close" style="background-color:black; color:#fff"></button>
        </div>
        <div class="offcanvas-body">
            
            <!-- Print API Editing Form -->
            
            <form class="was-validated" method="POST" action="">
                <div class="row">
                    <div class="mb-3 col-md-12">
                        <label for="name" class="form-label">Base Url</label>
                        <input class="form-control" type="text" id="apiUrl" name="apiUrl" value="<?= getPortalInfo('apiUrl') ?>" placeholder="https://example.com" required />
                    </div>
                    <div class="mb-3 col-md-12">
                        <label for="email" class="form-label">APIkey</label>
                        <input class="form-control" type="text" id="accessToken" name="accessToken" value="<?= getPortalInfo('accessToken') ?>" placeholder="**********" required />
                    </div>
                    <div class="mb-3 col-md-12">
                        <button class="btn btn-primary" type="submit" name="apiUpdate">Submit</button>
                    </div>
                </div>
            </form>
            
            <!-- \ Print API Editing Form -->
            
        </div>
    </div>
    
    <!-- Off-canvas Form for Editing Recharge API -->
    <div class="offcanvas offcanvas-end rounded h-100 border border-primary" tabindex="-1" id="rechargeApi" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header">
            <h5 id="offcanvasRightLabel">Recharge Api Credentials Update</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close" style="background-color:black; color:#fff"></button>
        </div>
        <div class="offcanvas-body">
            
            <!-- Recharge API Editing Form -->
            
            <form class="was-validated" method="POST" action="">
                <div class="row">
                    <div class="mb-3 col-md-12">
                        <label for="name" class="form-label">CallBack Url</label>
                        <input class="form-control border border-primary active" type="text" value="https://<?= getPortalInfo('webUrl') ?>/system/recharge-callback.php" placeholder="https://example.com" required />
                    </div>
                    <div class="mb-3 col-md-12">
                        <label for="name" class="form-label">Base Url</label>
                        <input class="form-control" type="text" id="rcUrl" name="rcUrl" value="<?= getPortalInfo('rcUrl') ?>" placeholder="https://example.com" required />
                    </div>
                    <div class="mb-3 col-md-12">
                        <label for="email" class="form-label">UserName</label>
                        <input class="form-control" type="text" id="rcUsername" name="rcUsername" value="<?= getPortalInfo('rcUsername') ?>" placeholder="**********" required />
                    </div>
                    <div class="mb-3 col-md-12">
                        <label for="email" class="form-label">Token</label>
                        <input class="form-control" type="text" id="rcToken" name="rcToken" value="<?= getPortalInfo('rcToken') ?>" placeholder="**********" required />
                    </div>

                    <div class="mb-3 col-md-12">
                        <button class="btn btn-primary" type="submit" name="rcApiUpdate">Submit</button>
                    </div>
                </div>
            </form>
            
            <!-- \ Recharge API Editing Form -->
            
        </div>
    </div>
    
    <!-- Off-canvas Form for Editing Recharge API -->
    <div class="offcanvas offcanvas-end rounded h-100 border border-primary" tabindex="-1" id="whatsappApi" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header">
            <h5 id="offcanvasRightLabel">Whatsaap Api Credentials Update</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close" style="background-color:black; color:#fff"></button>
        </div>
        <div class="offcanvas-body">
            
            <!-- Recharge API Editing Form -->
            
            <form method="POST" action="">
                <div class="row">
                    <div class="mb-3 col-md-12">
                        <label for="wapiUrl" class="form-label">Base Url</label>
                        <input class="form-control" type="url" id="wapiUrl" name="wapiUrl" value="<?= getPortalInfo('wapiUrl') ?>" placeholder="https://example.com" required />
                    </div>
                    <div class="mb-3 col-md-12">
                        <label for="wapiToken" class="form-label">Token</label>
                        <input class="form-control" type="text" id="wapiToken" name="wapiToken" value="<?= getPortalInfo('wapiToken') ?>" placeholder="**********" required />
                    </div>
                    <div class="mb-3 col-md-12">
                        <label for="wapiSender" class="form-label">Sender Number</label>
                        <input class="form-control" type="text" id="wapiSender" name="wapiSender" value="<?= getPortalInfo('wapiSender') ?>" placeholder="**********" required />
                    </div>

                    <div class="mb-3 col-md-12">
                        <button class="btn btn-primary" type="submit" name="whatsappApiUpdate">Submit</button>
                    </div>
                </div>
            </form>
            
            <!-- \ Recharge API Editing Form -->
            
        </div>
    </div>

<!-- Not authorized! -->
<?php } else { ?>
    <div class="misc-wrapper text-center" style="margin-top: 100px">
        <span class="text-danger"><i class="bi bi-exclamation-triangle display-1 text-primary"></i></span>
        <h2 class="mb-2 mx-2">You are not authorized!</h2>
        <p class="mb-4 mx-2 text-danger">You do not have permission to view this page using the credentials that you have provided while login. <br> Please contact your site administrator.</p>
        <a href="dashboard" class="btn btn-primary">Back to home</a>
    </div>
<?php } ?>
<!-- \ Not authorized! -->

<?php 
require_once('../layouts/mainFooter.php');
?>