<?php
session_start(); // Add this line at the beginning

date_default_timezone_set("Asia/Kolkata");   //India time (GMT+5:30)

// Function to establish a PDO database connection
function connectDB() {
    $host = "localhost"; // Replace with your database host
    $username = "ninedigi_print"; // Replace with your database username
    $password = "ninedigi_print"; // Replace with your database password
    $database = "ninedigi_new"; // Replace with your database name

    try {
        $dsn = "mysql:host=$host;dbname=$database";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $conn = new PDO($dsn, $username, $password, $options);
        return $conn;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Example usage:
$conn = connectDB();

// Perform database operations using $conn

// Close the connection when done (not strictly necessary with PDO as it will be automatically closed when the script ends)
// $conn = null;


$reference = time();
$date = date('Y-m-d');
$timestamp = date('m/d/Y h:i:s a', time());

function isUPI($upi,$allow) {
    $upi = trim($upi); // in case there's any whitespace
    $arr = explode("@",$upi);
    
    if(in_array($arr[1],$allow)){
     return true; 
    }
    
}

if (!function_exists('getUpiDetails')) {
    function getUpiDetails($uid, $type) {
        $conn = connectDB(); // Make $conn accessible inside the function
        
        $selectQuery = "SELECT paytm_business FROM users WHERE id = :user_id";
        $selectStatement = $conn->prepare($selectQuery);
        $selectStatement->bindParam(':user_id', $uid, PDO::PARAM_INT);
        $selectStatement->execute();

        $row = $selectStatement->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $paytmBusiness = json_decode($row['paytm_business'], true);
            return isset($paytmBusiness[$type]) ? $paytmBusiness[$type] : null;
        } else {
            return null; // User not found or an error occurred
        }
    }
}


// Portal information function
function getPortalInfo($type) {
    $conn = connectDB(); // Make $conn accessible inside the function

    $servername = filter_var($_SERVER['SERVER_NAME'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    try {
        $sql = $conn->prepare("SELECT * FROM `portalSettings` WHERE webUrl = :webUrl");
        $sql->bindParam(':webUrl', $servername, PDO::PARAM_STR);
        $sql->execute();
        
        $res = $sql->fetch(PDO::FETCH_ASSOC);
        return $res[$type];
    } catch (PDOException $e) {
        // Handle the SQL query error, log it, or return an appropriate response.
        return "Error executing SQL query: " . $e->getMessage();
    }
}
// Portal information function end

function getUsersData($id) {
    $conn = connectDB(); // Make $conn accessible inside the function
    $id = base64_decode($id);

    // Use prepared statements to prevent SQL injection
    $query = "SELECT * FROM `users` WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch the result as an associative array
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        return $result;
    }

    return null; // Return null if the user with the specified ID is not found
}


function getUsersInfo($type) {
    $conn = connectDB(); // Make $conn accessible inside the function

    try {
        $username = base64_decode($_SESSION['userAuth']); // Assign to a variable
        $sql = $conn->prepare("SELECT * FROM `users` WHERE username = :session_users");
        $sql->bindParam(':session_users', $username, PDO::PARAM_STR);
        $sql->execute();

        $res = $sql->fetch(PDO::FETCH_ASSOC);
        return $res[$type];
    } catch (PDOException $e) {
        return "Error executing SQL query: " . $e->getMessage();
    }
}


// Function to remove special characters and tags from a string
function get_safe($value){
    $search = array("\\", "\x00", "\n", "\r", "'", '"', "\x1a", "-", "+", "=");
    $replace = array("\\\\", "\\0", "\\n", "\\r", "\'", '\"', "\\Z", "", "", "");

    return strip_tags(str_replace($search, $replace, $value));
}

// Function to sanitize a string (similar to get_safe)
function safe_str($value){
    $search = array("\\", "\x00", "\n", "\r", "'", '"', "\x1a", "-", "+", "=");
    $replace = array("\\\\", "\\0", "\\n", "\\r", "\'", '\"', "\\Z", "", "", "");

    return strip_tags(str_replace($search, $replace, $value));
}

// Function to escape a string for safe use in a SQL query
function str_escape($value){
    global $conn; // Assuming $conn is a global variable
    return mysqli_real_escape_string($conn, $value);
}

// Function to replace occurrences of a substring in a string
function StrReplace($str, $fnd, $rep){
    return str_replace($fnd, $rep, $str);
}

function pwd_verify($password,$hash){
return password_verify($password,$hash);
}

function SendMail($email_to, $email_subject, $email_message) {
		$email = $email_to;
		if($email){
			$cc ='noreply@' . getPortalInfo('webUrl');
			$from_mail = 'cd@' . getPortalInfo('webUrl'); // Replace with your email address
			$to      = $email;	
			$from    = getPortalInfo('webName');
			$subject = $email_subject;
		    $email_message=$email_message;
			$header = "From: ".$from." <".$from_mail.">\r\nCC:".$cc."\r\n";
			$header .= "MIME-Version: 1.0\n" . "Content-type: text/html; charset=iso-8859-1\n" ;
			// Attempt to send the email
        if (mail($to, $subject, $email_message, $header)) {
            // Email sent successfully
            return 1;
        } else {
            // Email failed to send
            return 0;
        }
    } else {
        // Email address is not provided
        return 0;
    }
}
function registerMail($email_to, $email_subject, $email_message) {
    $email = $email_to;
    if ($email) {
        $cc ='onboarding@' . getPortalInfo('webUrl');
        $from_mail = 'cd@' . getPortalInfo('webUrl'); // Replace with your email address 
        $to      = $email;    
        $from    = getPortalInfo('webName');
        $subject = $email_subject;
        $email_message = $email_message;
        $header = "From: ".$from." <".$from_mail.">\r\nCC:".$cc."\r\n";
        $header .= "MIME-Version: 1.0\n" . "Content-type: text/html; charset=iso-8859-1\n";

        // Use the return value of the mail function
        $success = mail($to, $subject, $email_message, $header);

        if ($success) {
            // Email sent successfully
            return 1;
        } else {
            // Email failed to send
            return 0;
        }
    } else {
        // Handle the case when $email is empty
        return 0;
    }
}


// Send Whatsapp Message function
function whatsappMessage($mobile, $message) {
        $wapiUrl = getPortalInfo('wapiUrl');
        $wapiSender = getPortalInfo('wapiSender');
        $wapiToken = getPortalInfo('wapiToken');
        $body = array(
            "api_key" => $wapiToken,
            "sender" => $wapiSender,
            "number" => '91' . $mobile,
            "message" => $message
        );

        // Initialize cURL
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $wapiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => [
                "Accept: */*",
                "Content-Type: application/json",
            ],
        ]);

        // Execute cURL request
        $response = curl_exec($curl);
        $err = curl_error($curl);

        // Close cURL session
        curl_close($curl);


        if ($success) {
            // Email sent successfully
            return 1;
        } else {
            // Email failed to send
            return 0;
        }
}

function getUpiDetails($uid, $type) {
    $conn = connectDB();
    
    $selectQuery = "SELECT paytm_business FROM users WHERE id = :user_id";
    $selectStatement = $conn->prepare($selectQuery);
    $selectStatement->bindParam(':user_id', $uid, PDO::PARAM_INT);
    $selectStatement->execute();

    $row = $selectStatement->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $paytmBusiness = json_decode($row['paytm_business'], true);
        return isset($paytmBusiness[$type]) ? $paytmBusiness[$type] : null;
    } else {
        return null; // User not found or an error occurred
    }
}


function generateNumericOTP($n) { 
      
    // Take a generator string which consist of 
    // all numeric digits 
    $generator = "1357902468"; 
  
    // Iterate for n-times and pick a single character 
    // from generator and append it to $result 
      
    // Login for generating a random character from generator 
    //     ---generate a random number 
    //     ---take modulus of same with length of generator (say i) 
    //     ---append the character at place (i) from generator to result 
  
    $result = ""; 
  
    for ($i = 1; $i <= $n; $i++) { 
        $result .= substr($generator, (rand()%(strlen($generator))), 1); 
    } 
  
    // Return result 
    return $result; 
} 

function redirect($time='',$url){
echo '<script>
setTimeout(function(){
window.location = "'.$url.'" 
},'.$time.');
</script>'; 	
	
}

// Function to register a new user
function registerUser($userid, $mob, $email, $password) {
    $conn = connectDB();

    // Hash the password before storing it in the database
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Generate a random 6-digit OTP
    $otp = mt_rand(100000, 999999);

    // Set default values for OTP status and account status
    $otpstatus = 'no';
    $status = 'inactive';

    // Insert the user data into the users table
    $stmt = $conn->prepare("INSERT INTO users (userid, mob, email, password, otp, otpstatus, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$userid, $mob, $email, $hashedPassword, $otp, $otpstatus, $status]);

    // Close the connection
    $conn = null;

    // Send OTP to the user's email (In a real-world scenario, this should be handled separately)
    echo "OTP sent to $email: $otp";
}

// Function to log in a user
function loginUser($emailOrMob, $passwordOrOTP) {
    $conn = connectDB();

    // Retrieve user data based on the provided email or mobile number
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR mob = ?");
    $stmt->execute([$emailOrMob, $emailOrMob]);
    $user = $stmt->fetch();

    // Check if the user exists
    if ($user) {
        // If OTP status is 'yes', attempt OTP login
        if ($user['otpstatus'] == 'yes') {
            // Check if the provided OTP is valid
            if ($passwordOrOTP == $user['otp']) {
                // OTP is valid, perform actions like setting session variables, redirecting, etc.
                $_SESSION['user_id'] = $user['id'];
                echo "OTP Login successful!";
                header("Location: dashboard.php"); // Redirect to dashboard after successful login
                exit();
            } else {
                echo "Invalid OTP. Please try again.";
            }
        } else {
            // Password login
            // Check if the provided password is correct
            if (password_verify($passwordOrOTP, $user['password'])) {
                // Check account status
                if ($user['status'] == 'active') {
                    // User is valid, perform actions like setting session variables, redirecting, etc.
                    $_SESSION['user_id'] = $user['id'];
                    echo "Password Login successful!";
                    header("Location: dashboard.php"); // Redirect to dashboard after successful login
                    exit();
                } else {
                    echo "Account not active.";
                }
            } else {
                echo "Invalid password. Please try again.";
            }
        }
    } else {
        echo "User not found.";
    }

    // Close the connection
    $conn = null;
}

// Aadhaar verification API function
function performAadhaarVerification($aadhar, $bioenc) {
    
    $token = 'dafdb9046932b2aa';
    $domain = 'KavyaTECH';

    $curl = curl_init();
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://getnewapi.in/api_service/ad_aadhar_api.php',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => array('bioenc' => $bioenc,'aadhar' => $aadhar,'token' => $token,'domain' => $domain),
    ));
    
    $response = curl_exec($curl);


    return $response;
}

// Voter verification API function
function performVoterVerification($epic_no, $captchaID, $captcha) {
    
    $api_key = getPortalInfo('accessToken');
    $apiUrl = getPortalInfo('apiUrl');
    $psodr = time();
    
    // Example Request using cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl . '/serviceApi/V2/Voter-verification');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "apiKey=$api_key&order_id=$time&epic=$epic_no");

    $headers = array();
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

// Rashan verification API function
function performRashanVerification($rcno) {
    
    $api_key = getPortalInfo('accessToken');
    $url = getPortalInfo('apiUrl');
    $order = time();

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url.'/serviceApi/V1/RashanVerification');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "apiKey=$api_key&order_id=$order&rcno=$rcno");

    $headers = array('Content-Type: application/x-www-form-urlencoded');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

// Driving license verification API function
function performLicenceVerification($dlNumber, $dob) {
    $url = getPortalInfo('apiUrl');
    $api_key = getPortalInfo('accessToken');
    $psodr = time();

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url.'/serviceApi/V1/driving-licence-verification',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array(
            'apiKey' => $api_key,
            'order_id' => $psodr,
            'dlNo' => $dlNumber,
            'dob' => $dob,
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    return $response;
}

// PAN FIND API function
function performPanFIND($uidNumber) {
    
    $apiKey = getPortalInfo('accessToken');
    $apiUrl = getPortalInfo('apiUrl');
    $order = time();
    
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL =>  $apiUrl."/serviceApi/V1/panFind?apiKey=$apiKey&order_id=$order&uidNumber=$uidNumber",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $result = curl_exec($curl);
    curl_close($curl);

    return $result;
}

// Voter Mobile Linking api functions
define('API_ENDPOINT', 'https://getnewapi.in/api_service/voter_mobile_link.php');
define('API_TOKEN', 'b723ce51188aa6aa');

function voterMobileLinking($epic_no, $captchaID, $captcha, $mobile, $otp) {
    $data = array(
        'mobileno' => $mobile,
        'domain' => 'pansprint.in',
        'token' => API_TOKEN,
        'otp' => $otp,
        'id' => $captchaID,
        'captcha' => $captcha,
        'epicno' => $epic_no,
        'type' => 'otp',
    );

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => API_ENDPOINT,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded',
        ),
    ));

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($httpCode != 200) {
        // Handle error (e.g., log or throw an exception)
        echo "HTTP Error: $httpCode\n";
    }

    curl_close($curl);

    return $response;
}

function voterMobileLinkSendOTP($mobile) {
    $data = array(
        'mobileno' => $mobile,
        'domain' => 'pansprint.in',
        'token' => API_TOKEN,
        'type' => 'sendotp',
    );

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => API_ENDPOINT,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded',
        ),
    ));

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($httpCode != 200) {
        // Handle error (e.g., log or throw an exception)
        echo "HTTP Error: $httpCode\n";
    }

    curl_close($curl);

    return $response;
}


// Vahical verification function
function performVehicleVerification($vehicleNo) {
    
    $type= 'details'; // ? pdf and details
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://getnewapi.in/api_service/vehicle_api.php?rcno=$vehicleNo&type=$type&token=61a3f9f088814ba0&domain=KavyaTECH");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    $response = curl_exec($ch);
    curl_close($ch);


    return $response;
}

// Vahical verification function
function performAyushmanVerification($flno, $stid) {
    
    $apiKey = getPortalInfo('accessToken');
    $apiUrl = getPortalInfo('apiUrl');
    $order = time();
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,  $apiUrl.'/serviceApi/V1/ayushmanVerification');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "apiKey=$apiKey&order_id=$order&uidNumber=$flno&stateId=$stid");
    
    $headers = array();
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);


    return $result;
}

// Recharge opretors function
function Get_Operator($operator_id) {
    switch ($operator_id) {
        case "A":
        $operatorName= "airtel";
        break;
        case "V":
        $operatorName= "idea";
        
        break;
        case "RC":
        $operatorName= "jio";
        
        break;
        case "4":
        $operatorName= "bsnl_topup";
        break;
        case "5":
        $operatorName= "bsnl_special";
        break;
        case "ABC":
        $operatorName= "airtel_postpaid";
        break;
        case "ABC":
        $operatorName= "idea_postpaid";
        break;
        case "ABC":
        $operatorName= "vodafone_postpaid";
        break;
        case "ATV":
        $operatorName= "airtel_dth";
        break;
        case "STV":
        $operatorName= "sun_dth";
        break;
        case "TTV":
        $operatorName= "tatasky_dth";
        break;
        case "VTV":
        $operatorName= "videocon_dth";
        break;
        case "DTV":
        $operatorName= "dish_dth";
        break;
       default:
       $operatorName= "";
    }
    return $operatorName;
}

// Function to check if a number was recharged within the last 1 minutes
function isRecentlyRecharged($mobileNumber) {
    if (isset($_SESSION['recharge_history'][$mobileNumber])) {
        $lastRechargeTime = $_SESSION['recharge_history'][$mobileNumber];
        if (time() - $lastRechargeTime < 180) { // 60 seconds = 3 minutes
            return true;
        }
    }
    return false;
}

// Function to check if service is active for the user
function isServiceActive($conn, $username, $service_name) {
    $stmt = $conn->prepare("SELECT ekycPan_status FROM users WHERE id = :id AND ekycPan_status = :ekycPan_status");
    $stmt->bindParam(':id', $username);
    $stmt->bindParam(':ekycPan_status', $service_name);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return ($row && $row['ekycPan_status'] == 'active');
}
// e-Kyc Pan Apply function
function performEkycNewApplication($number, $panMode, $orderid) {
    
    $apiKey = getPortalInfo('accessToken');
    $apiUrl = getPortalInfo('apiUrl');

// Prepare POST data
$data = array(
    'apiKey' => $apiKey,
    'order_id' => $orderid,
    'mobileNo' => $number,
    'appMode' => $panMode
);

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $apiUrl.'/serviceApi/V1/pan/newGenerateurl.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute cURL request
$response = curl_exec($ch);

// Check for errors
if($response === false) {
    echo 'cURL Error: ' . curl_error($ch);
}

// Close cURL session
curl_close($ch);

    return $response;
}

// e-Kyc Incomplete pAN function
function performIncompleteApplication($orderid) {
    
    $apiKey = getPortalInfo('accessToken');
    $apiUrl = getPortalInfo('apiUrl');

// Prepare POST data
$data = array(
    'apiKey' => $apiKey,
    'order_id' => $orderid,
);

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $apiUrl.'/serviceApi/V1/pan/incomplete.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute cURL request
$response = curl_exec($ch);

// Check for errors
if($response === false) {
    echo 'cURL Error: ' . curl_error($ch);
}

// Close cURL session
curl_close($ch);

    return $response;
}

// e-Kyc correction pan function
function performCorrectionApplication($number, $panMode, $orderid) {
    
    $apiKey = getPortalInfo('accessToken');
    $apiUrl = getPortalInfo('apiUrl');

    // Prepare POST data
    $data = array(
        'apiKey' => $apiKey,
        'order_id' => $orderid,
        'mobileNo' => $number,
        'appMode' => $panMode
    );
    
    // Initialize cURL session
    $ch = curl_init();
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $apiUrl.'/serviceApi/V1/pan/correction.php');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    // Execute cURL request
    $response = curl_exec($ch);
    
    // Check for errors
    if($response === false) {
        echo 'cURL Error: ' . curl_error($ch);
    }
    
    // Close cURL session
    curl_close($ch);

    return $response;
}