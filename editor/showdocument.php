<?php
include("../header.php");

// Check if the user is logged in
if (!isset($_SESSION['access_token'])  ) {
	header('Location: /view-hiring/index.php'); // Redirect to the profile page
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

	<div class="container mb-5"> <?php
		if(!empty($documentData)){
			foreach ($documentData as $documents) {
				$document = isset($documents['user_documents']) ? $documents['user_documents'] : ''; ?>
				<div class="card mx-auto m-5" style =" width: 830px ; height: 641px;">
					<div class="embed-responsive embed-responsive-16by9">
						<embed
							src="<?= !empty($document) ? $document : ''; ?>"
							type="application/pdf"
							class="embed-responsive-item"
							style=" width: 785px !important;
											margin: 23px;
											height: 594px;"
						></embed>
					</div>
				</div><?php 
			}
		}else{ ?>
			<div class="card " style="display:grid; place-content:center;" >
				<h2>No Document Found!</h2>
			</div><?php
		} ?>
	</div>
	<div class="text-center" >
		<a class="hover-link " style="cursor: pointer;" onclick="navigate()">Go to back</a>
	</div>
	<script>
		function navigate() {
			window.location.href = 'http://localhost/view-hiring/index.php';
		}
	</script>
	<?php
include("../footer.php");