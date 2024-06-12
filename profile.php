<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['access_token'])) {
    header('Location: login.php');
    exit();
}

// Retrieve the access token from the session
$token = $_SESSION['access_token'];

// Set up the context for the HTTP request to fetch the user profile
$options = [
    'http' => [
        'header' => "Authorization: Bearer $token\r\n",
        'method' => 'GET',
    ],
];
$context = stream_context_create($options);
$result = @file_get_contents('http://localhost:5000/api/profile', false, $context);

// Check if the request was successful
if ($result === FALSE) {
    // If the token is invalid or expired, redirect to login page
    header('Location: login.php');
    exit();
}

// Decode the response to get the user data
$user = json_decode($result, true);

// Store the user's role in the session
$_SESSION['role'] = $user['role'];

// Output or use the user data as needed
?>


<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    <!-- Latest compiled and minified CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Latest compiled JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
    label {
        display: block;
        width: 100%;
    }
    .logout {
        text-align: right;
        margin: 10px;
    }
    .profile {
        border: 0px solid;
        width: 102px;
        overflow: hidden;
        margin-left: 30px;
    }
    img#myImg {
        width: 100px;
        margin-bottom: 12px;
    }
    tr.usert_tabs th a {
        margin: 6px;
        width: 194px;
        padding: 6px;
        text-align: center;
        text-decoration: none;
    }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card bg-dark">
                    <div class="card-header h4 m-0 text-center text-dark bg-primary"> <?= ucfirst($_SESSION['role']) ?> </div>
                    <div class="card-body">
                        <div class="profile"> <?php 
                            $profile = isset($user['profile']) ? $user['profile'] : '' ; ?>
                            <img id="myImg" src="<?= !empty($profile) ? $profile : 'uploads/images.png' ?>" alt="" class="img-thumbnail mb-3">
                        </div>
                        <div class="btn-group mb-3" role="group" aria-label="User Actions">
                            <a href="SuperAdmin/update.php" class="btn btn-outline-primary">Edit user</a>
                            <a href="logout.php" class="btn btn-outline-danger">Logout</a>
                        </div>
                        <table class="table table-bordered table-dark">
                            <tr>
                                <th>Dashboard</th> 
                                <th colspan="7">Profile</th>
                            </tr>
                            <tr class="usert_tabs">
                                <th rowspan="7">
                                    <a href="SuperAdmin/adminRegister.php" class="d-block btn btn-outline-primary">Register admin</a>
                                    <a href="SuperAdmin/userRegister.php" class=" d-block btn btn-outline-primary">Register user</a>
                                    <a href="SuperAdmin/userManage.php" class=" d-block btn btn-outline-primary">Manage user</a>
                                    <a href="usersdetails.php" class=" d-block btn btn-outline-primary"> Assigned User </a>
                                    <a href="SuperAdmin/usersremove.php" class=" d-block btn btn-outline-primary">Users Remove </a>
                                   
                                </th>
                            </tr>
                            <tr class="username">
                                <td>Name: <?= htmlspecialchars($user['name']) ?></td>
                            </tr>
                            <tr class="useremail">
                                <td>E-mail: <?= htmlspecialchars($user['email']) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div
