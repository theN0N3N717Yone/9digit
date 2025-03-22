<?php
try {
    require_once('connectivity_functions.php');

    $arr = array("status" => false, "msg" => 'Invalid Request');

    if (isset($_POST['id']) && !empty($_POST['id']) && !empty($_POST['pass']) && !empty($_POST['csrf'])) {

        $login_id = safe_str($_POST['id']);
        $lpwd = strip_tags($_POST['pass']);
        $csrf_token = strip_tags($_POST['csrf']);

        $conn = connectDB(); // Assuming you have a connectDB function

        $stmtA = $conn->prepare("SELECT * FROM `users` WHERE username=:username");
        $stmtA->bindParam(':username', $login_id, PDO::PARAM_STR);
        $stmtA->execute();
        $resultA = $stmtA->fetch(PDO::FETCH_ASSOC);

        if ($resultA['status'] === "suspended") {
            $arr = array("status" => false, "msg" => 'Sorry, your account is currently Suspended. Please contact our support team for assistance.');
            echo json_encode($arr);
            exit;
        }
        if ($resultA['status'] === "unapproved") {
            $arr = array("status" => false, "msg" => 'Your account is currently Unapproved. Please contact our support team for assistance.');
            echo json_encode($arr);
            exit;
        }
        if (strtolower($resultA['username']) === strtolower($login_id)) {
        // Check OTP status
        if ($resultA['otp'] === 'no') {
            $stmt = $conn->prepare("SELECT * FROM `users` WHERE username=:username AND status='approved'");
            $stmt->bindParam(':username', $login_id, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($stmt->rowCount() > 0 && pwd_verify($lpwd, $result['password'])) {

                // Set user data in the session
                $_SESSION['userAuth'] = base64_encode($result['username']);
                $_SESSION['login_time'] = time();

                // Set cookies
                $cookie_expire = time() + (86400 * 1);
                setcookie('userAuth', urlencode(base64_encode($result['username'])), $cookie_expire, '/', '', true, true);
                setcookie('login_time', time(), $cookie_expire, '/', '', true, true);
                
                // User is valid, proceed with login without OTP
                $arr = array("status" => true, "msg" => 'Login successful');
            } else {
                $arr = array("status" => false, "msg" => 'Credentials are invalid');
            }
        } else {
            // OTP status is 'yes', proceed with OTP verification
            $rand = generateNumericOTP(6);

            // Update OTP in the database
            $stmtUpdateOTP = $conn->prepare("UPDATE `users` SET login_otp=:login_otp WHERE id=:user_id");
            $stmtUpdateOTP->bindParam(':login_otp', $rand, PDO::PARAM_STR);
            $stmtUpdateOTP->bindParam(':user_id', $resultA['id'], PDO::PARAM_INT);
            $stmtUpdateOTP->execute();

            $number = $resultA['mobile_no'];
            $length = strlen($number);

            if ($length >= 4) {
                $hidden = str_repeat("*", $length - 4) . substr($number, -4);
            }

            // Rest of your code for sending SMS and email...
            $email_message = '<body style="font-family: Nunito, sans-serif; font-size: 15px; font-weight: 400; color: #333333;">
                <div style="max-width: 600px; margin: 50px auto; border-radius: 6px; overflow: hidden; background-color: #f4f4f4; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
                    <div style="padding: 20px; background-color: #3498db; text-align: center; color: #ffffff;">
                        <h1 style="font-size: 28px;">DelusePAN</h1>
                    </div>
                    <div style="padding: 20px; background-color: #ffffff; border-radius: 5px;">
                        <p style="font-size: 16px; line-height: 1.6; color: #333333;">Dear '.$resultA['owner_name'].',</p>
                        <p style="font-size: 16px; line-height: 1.6; color: #333333;">Your One-Time Password (OTP) for DelusePAN is: <strong style="color: #3498db;">'.$rand.'</strong>.</p>
                        <p style="font-size: 16px; line-height: 1.6; color: #333333;">Guidelines for OTP:</p>
                        <ul style="font-size: 16px; line-height: 1.6; color: #333333;">
                            <li>Use the OTP only for DelusePAN verification.</li>
                            <li>Do not share your OTP with anyone.</li>
                            <li>Its valid for a single use and expires in 10 minutes.</li>
                            <li>If you didnt request this OTP, ignore this message.</li>
                        </ul>
                        <p style="font-size: 16px; line-height: 1.6; color: #333333;">Thank you for choosing DelusePAN. If you have questions, contact us.</p>
                    </div>
                    <div style="padding: 15px; color: #ffffff; background-color: #3498db; text-align: center;">
                        <p style="font-size: 12px; margin: 0;">This is an automated email. Do not reply.</p>
                    </div>
                </div>
            </body>';
            $curl = curl_init();
    
            $data = array();
    
            $data['api_id'] = "APICKvq1Sz4128541";
    
            $data['api_password'] = "Sachin@123";
                
            $data['sms_type'] = "Transactional";
                    
            $data['sms_encoding'] = "Text";
    
            $data['sender'] = "POCKKT";
    
            $data['number'] = $number;
    
            $data['message'] = "Dear '".$resultA['owner_name']."' your OTP to login at Pockket Agent is $rand .";
    
            $data['template_id'] = "159462";
    
            $data_string = json_encode($data);
            
            $ch = curl_init('http://bulksms.pockket.co.in/api/send_sms');
    
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    
                'Content-Type: application/json',
    
                'Content-Length: ' . strlen($data_string))
    
            );
    
            $result = curl_exec($ch);

            $emailStatus = SendMail($resultA['email_id'], 'DelusePAN One-Time Password (OTP) Notification', $email_message);

            $arr = array("status" => true, "mail_status" => $emailStatus, "mobile_status" => $response, "mobile" => $hidden, "msg" => "OTP send successful");
        }
        } else {
            $arr = array("status" => false, "msg" => 'Credentials are invalid! please enter valid login Credentials');
            echo json_encode($arr);
            exit; 
        }
        $conn = null;
    }

    echo json_encode($arr);
} catch (Exception $e) {
    echo json_encode(array("status" => false, "msg" => 'Error: ' . $e->getMessage()));
}
?>
