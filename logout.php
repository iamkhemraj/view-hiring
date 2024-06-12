<?php
session_start();

if (isset($_SESSION['access_token'])) {
    $token = $_SESSION['access_token'];
    
    // Set up the context for the HTTP request
    $options = [
        'http' => [
            'header'  => "Authorization: Bearer $token\r\n",
            'method'  => 'POST',
        ],
    ];
    $context  = stream_context_create($options);
    
    // Send the logout request
    $result = file_get_contents('http://localhost:5000/api/logout', false, $context);

    // Optionally, check the result for any errors
    if ($result === FALSE) {
        // Log the error, show a message, or take other appropriate actions
        error_log('Error logging out');
        die('Error logging out');
    }
}

// Unset all of the session variables.
$_SESSION = [];

// If it's desired to kill the session, also delete the session cookie.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();

// Redirect to login page
header('Location: login.php');
exit();
?>
