<?php
session_start(); // Start the session

// Clear all session variables
$_SESSION = array();

// Delete the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Delete any additional cookies you may have set during login
setcookie('pansprint_session', '', time() - 3600, '/');
setcookie('csrf_token', '', time() - 3600, '/');
setcookie('session_users', '', time() - 3600, '/');
setcookie('login_time', '', time() - 3600, '/');

// Redirect to the auth-login.php page or any other page you prefer
header("Location: system/logout-success.php");
exit();
?>