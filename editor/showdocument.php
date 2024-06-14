<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['access_token'])  ) {
    header('Location: login.php');
    exit();
}

$token        = $_SESSION['access_token'];
$options      = ['http' => [ 'header' => "Authorization: Bearer $token\r\n", 'method' => 'GET', ],];
$context      = stream_context_create($options);
$result       = @file_get_contents( BASE_URL . '/user/ShowDocument', false, $context);
$userDatas    = json_decode($result, true); // decode user data
$documentData = isset($userDatas['response']) ? $userDatas['response'] : ''; 

?>

<?= isset($errors['user']) ? '<p style="color:#cd2322;margin:0px !important">' . $errors['user'] . '</p>' : ''; ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Show Document</title>
	<!-- Latest compiled and minified CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- Latest compiled JavaScript -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<style>
		.card {
			width: 80%;
			height: 550px;
		}
		.embed-responsive {
			position: relative;
			display: block;
			width: 100%;
			padding: 0;
			overflow: hidden;
		}
		.embed-responsive::before {
			display: block;
			content: "";
		}
		.embed-responsive-16by9::before {
			padding-top: 56.25%;
		}
		.embed-responsive-item {
			position: absolute;
			top: 0;
			left: 0;
			bottom: 0;
			height: 100%;
			width: 100%;
			border: 0;
		}
	</style>
</head>
<body>
	<div class="container mt-2"> <?php
		if(!empty($documentData)){
			foreach ($documentData as $documents) {
				$document = isset($documents['user_documents']) ? $documents['user_documents'] : ''; ?>
				<div class="card mx-auto mb-3">
					<div class="embed-responsive embed-responsive-16by9">
						<embed
							src="<?= !empty($document) ? $document : ''; ?>"
							type="application/pdf"
							class="embed-responsive-item"
						></embed>
					</div>
				</div><?php 
			}
		}else{ ?>
			<div class="card " style="display:grid; place-content:center;" >
				<h2>No Document Found!</h2>
				<div class="text-center" >
					<a class="hover-link " style="cursor: pointer;" onclick="navigate()">Go to back</a>
				</div>
				<script>
					function navigate() {
						window.location.href = 'http://localhost/view-hiring/profile.php';
					}
				</script>
			</div><?php
		} ?>
		
	</div>
</body>
</html>
