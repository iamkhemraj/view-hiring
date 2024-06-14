<?php
require_once('global.php');
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
$context  = stream_context_create($options);
$result   = file_get_contents( BASE_URL . '/assigned-users', false, $context);
$user     = json_decode($result, true);
$allAssignedUsers = !empty($user) && isset($user['allAssignedUsers']) ? $user['allAssignedUsers'] : [];
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
                    <div class="card-header h4 m-0 text-center">Users Details</div>
                    <div class="card-body">
                        <table class="table table-striped text-center">
                            <tr>
                                <th>User Name</th>
                                <th>Admin Name</th>
                            </tr>
                            <?php if (!empty($allAssignedUsers)): ?>
                                <?php foreach ($allAssignedUsers as $allAssignedUser): ?>
                                    <tr>
                                        <td>
                                            <?php 
                                            $user = isset($allAssignedUser['user']) ? $allAssignedUser['user'] : '';
                                            echo isset($user['name']) ? htmlspecialchars($user['name']) : '';
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $admin = isset($allAssignedUser['admin']) ? $allAssignedUser['admin'] : '';
                                            echo isset($admin['name']) ? htmlspecialchars($admin['name']) : '';
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2">No users assigned</td>
                                </tr>
                            <?php endif; ?>
                        </table>
                        <div class="text-center" >
                                <a class="hover-link " style="cursor: pointer;" onclick="navigate()">Go to back</a>
                            </div>

                            <script>
                                function navigate() {
                                    window.location.href = 'http://localhost/view-hiring/profile.php';
                                }
                            </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
