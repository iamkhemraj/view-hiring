<?php
session_start();
if (isset($_SESSION['access_token'])) {
    $token = $_SESSION['access_token'];
    $options = [
        'http' => [
            'header'  => "Authorization: Bearer $token\r\n",
            'method'  => 'POST',
        ],
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents('http://localhost:5000/api/logout', false, $context);

    // Optionally, check the result for any errors
    if ($result === FALSE) {
        die('Error logging out');
    }
}

// Destroy the session and redirect to login page
session_destroy();
header('Location: login.php');
exit();
?>
