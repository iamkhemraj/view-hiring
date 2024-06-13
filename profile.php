<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['access_token'])  ) {
    header('Location: login.php');
    exit();
}

$token = $_SESSION['access_token'];
$options = [
    'http' => [
        'header' => "Authorization: Bearer $token\r\n",
        'method' => 'GET',
    ],
];
$context = stream_context_create($options);
$result = @file_get_contents('http://localhost:5000/api/profile', false, $context);

if ($result === FALSE) {
    // Assuming the API returns 401 Unauthorized if the token is expired
    header('Location: login.php');
    exit();
}

$user = json_decode($result, true); // decode user data

$_SESSION['role'] = $user['role']; // Store user role in the session
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
        height: 90px;
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
                <div class="card ">
                    <div class="card-header h4 m-0 text-center text-dark "> Welcome To The <?= ucfirst($_SESSION['role']) ?> Dashboard </div>
                    <div class="card-body">
                        <div class="profile"> <?php 
                            $profile = isset($user['profile']) ? $user['profile'] : '' ; 
                            $userRole = isset($user['role']) ? $user['role'] : '' ; 
                            
                            ?>
                            <img id="myImg" src="<?= !empty($profile) ? $profile : 'uploads/images.png' ?>" alt="" class="rounded-circle mb-3">
                        </div>
                        <div class="btn-group mb-3" role="group" aria-label="User Actions">
                            <a href="SuperAdmin/update.php" class="btn btn-outline-primary">Edit user</a>
                            <a href="logout.php" class="btn btn-outline-danger">Logout</a>
                        </div>
                        <table class="table table-bordered ">
                            <tr>
                                <th>Dashboard</th> 
                                <th colspan="7">Profile</th>
                            </tr>
                            <tr class="usert_tabs">
                                <th rowspan="7"><?php
                                    if($userRole != 'admin' && $userRole != 'editor'){?>
                                        <a href="SuperAdmin/adminRegister.php" class="d-block btn btn-outline-primary">Register admin</a>
                                        <a href="SuperAdmin/userManage.php" class="d-block btn btn-outline-primary">Manage user</a><?php
                                    }elseif($userRole != 'editor'){ ?>
                                        <a href="SuperAdmin/userRegister.php" class="d-block btn btn-outline-primary">Register user</a>
                                        <a href="SuperAdmin/usersremove.php" class="d-block btn btn-outline-primary">Users Remove</a> <?php
                                    }elseif($userRole == 'editor'){ ?>

                                        <a href="editor/uploaddocument.php" class="d-block btn btn-outline-primary">Upload document</a>        
                                        <a href="editor/showdocument.php" class="d-block btn btn-outline-primary">Show Document</a>        
                                    
                                    <?php

                                    } ?>
                                    <a href="usersdetails.php" class="d-block btn btn-outline-primary">Assigned User</a>        
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
