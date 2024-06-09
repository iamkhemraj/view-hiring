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
    <!-- Latest compiled and minified CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Latest compiled JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
    label{
    display: block;
    width:100%;
    }
    </style>
</head>
<body>
    <div class="container mt-5">
		<div class="row justify-content-center">
			<div class="col-md-5">
				<div class="card">
					<div class="card-header h4 m-0 text-center"> User Profile </div>
					<div class="card-body">
                        <table class="table table-striped">
                            <tr>
                                <th>Id: </th> <td> <?= $user['id'] ?> </td>
                            </tr>
                            <tr>
                                <th>Name: </th> <td> <?= $user['name'] ?> </td>
                            </tr>
                            <tr>
                                <th>Email </th> <td> <?= $user['email'] ?> </td>
                            </tr>
                            <tr>
                                <th>Name </th> <td> <?= $user['name'] ?> </td>
                            </tr>
                        </table>
                        <a href="logout.php" class="btn btn-primary">Logout</a>
                    </div>
                </div>
			</div>
		</div>
	</div>
</body>
</html>
