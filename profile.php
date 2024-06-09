<?php
session_start();
if (!isset($_SESSION['access_token'])) {
    header('Location: login.php');
    exit();
}

$token = $_SESSION['access_token'];
$options = [
    'http' => [
        'header'  => "Authorization: Bearer $token\r\n",
        'method'  => 'GET',
    ],
];
$context  = stream_context_create($options);
$result = file_get_contents('http://localhost:5000/api/profile', false, $context);
$user = json_decode($result, true);
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
</head>
<body>
    <h1>User Profile</h1>
    <p>ID: <?= $user['id'] ?></p>
    <p>Name: <?= $user['name'] ?></p>
    <p>Email: <?= $user['email'] ?></p>
    <a href="logout.php">Logout</a>
</body>
</html>
