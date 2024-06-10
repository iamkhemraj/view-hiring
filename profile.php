<?php
session_start();
if (!isset($_SESSION['access_token'])) {
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
$context   = stream_context_create($options);
$result    = file_get_contents('http://localhost:5000/api/profile', false, $context);
$user      = json_decode($result, true);

// Store the user's role in the session
$_SESSION['role'] = $user['role'];

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
        border-radius: 100px;
        overflow: hidden;
    }
    img#myImg {
        width: 100px;
        margin-bottom: 12px;
    }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header h4 m-0 text-center"> <?= ucfirst($_SESSION['role']) ?> </div>
                    <div class="card-body">
                        <div class="logout">
                            <a href="logout.php" class="btn btn-warning">Logout</a>
                        </div>
                        <div class="profile">
                             <?php 
                                $profile = isset($user['profile']) ? $user['profile'] : '' ;
                              ?>
                           <img id="myImg" src="uploads/<?= !empty($profile) ? $profile : 'images.png' ?>" alt="<?= $profile ?>">
                        </div>
                        <table class="table table-striped">
                            <tr>
                                <th>Dashboard</th> 
                                <th colspan="7">Profile</th>
                            </tr>
                            <tr>
                                <th rowspan="7">
                                    <a href="SuperAdmin/adminRegister.php" class="text-warning d-block">Register admin</a>
                                    <a href="SuperAdmin/userRegister.php" class="text-warning d-block">Register user</a>
                                    <a href="SuperAdmin/userManage.php" class="text-warning d-block">Manage user</a>
                                    <a href="usersdetails.php" class="text-warning d-block">Users Details</a>
                                    <a href="SuperAdmin/update.php" class="text-warning d-block">Update user</a>
                                </th>
                               
                            </tr>
                            <tr>
                                <td>Name: <?= $user['name'] ?></td>
                            </tr>
                            <tr>
                                <td>E-mail: <?= $user['email'] ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
