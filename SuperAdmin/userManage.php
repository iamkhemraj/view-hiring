
<?php
include("../header.php");

if (!isset($_SESSION['access_token']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'super admin') {
    header('Location: /view-hiring/index.php'); // Redirect to the profile page
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
$result    = file_get_contents( BASE_URL . '/super_admin/getUserAdminData', false, $context);
$userdetails  = json_decode($result, true);

if(!empty($userdetails)){     
	$userDatas  =  isset($userdetails['Users']) ? $userdetails['Users'] : '';
	$adminDatas =  isset($userdetails['Admins']) ? $userdetails['Admins'] : '';
}




// Initialize variables for form fields
$user  = '';
$admin = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$user  = $_POST['user'] ?? '';
	$admin = $_POST['admin'] ?? '';

    $data = [
        'user'  => $user,
        'admin' => $admin,
    ];

    $token = $_SESSION['access_token'];
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\nAuthorization: Bearer $token\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
            'ignore_errors' => true, // Capture response even if it fails
        ],
    ];

    $context = stream_context_create($options);
    $result  = file_get_contents(BASE_URL . '/super_admin/assign-user', false, $context);

    $errorsdata    = json_decode($result, true);
    $errorMessages = [];

    if (isset($errorsdata['user'])) {
        $errorMessages['user'] = implode(', ', $errorsdata['user']);
    }
    if (isset($errorsdata['admin'])) {
        $errorMessages['admin'] = implode(', ', $errorsdata['admin']);
    }
  
    foreach ($errorMessages as $key => $value) {
        $errors[$key] = $value;
    }

    // Get the response headers and status code
    $http_response_header = $http_response_header ?? [];
    $status_code = null;
    if (!empty($http_response_header)) {
        list($protocol, $code, $message) = explode(' ', $http_response_header[0], 3);
        $status_code = intval($code);
    }

    if ($result !== false) {
        $response = json_decode($result, true);
        if (isset($response['status'])) {
            header('Location: /view-hiring/login.php'); // Redirect to login.php
            exit();
        } else {
            $message = $errorsdata['message'] ?? '';
        }
    } else {
        if ($status_code === 401) {
            $errors['invalid'] = 'Unauthorized. Please check your credentials.';
        } else {
			$message = $errorsdata['message'] ?? '';
        }
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header h4 m-0 text-center"> User Manage by Super Admin</div>
				
                <div class="card-body">
				<form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off">
					<div class="row">
						<div class="col-md-6">  
							<div class="form-group">
								<label for="user">Choose User's Name:</label>
								
								<select name="user" id="user" class="form-control"> 
									<option value="" disabled selected></option> <?php
									foreach($userDatas as $userData){ 
										$user =  isset($userData['id']) ? $userData['id']: '';
										$name   =  isset($userData['name']) ? $userData['name']: ''; ?>
										<option value="<?= $user ?> "><?= $name ;?> </option> <?php
									} ?>
								</select>
								<?= isset($errors['user']) ? '<p style="color:#cd2322;margin:0px !important">' . $errors['user'] . '</p>' : ''; ?>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="admin">Choose Admin's Name:</label>
								<select name="admin" id="admin" class="form-control"> 
									<option value="" disabled selected></option> <?php
									foreach($adminDatas as $adminData){ 
										$admin =  isset($adminData['id']) ? $adminData['id']: '';
										$name    =  isset($adminData['name']) ? $adminData['name']: ''; ?>
										<option value="<?= $admin ?> "><?= $name ;?> </option> <?php
									} ?>
								</select>
								<?= isset($errors['admin']) ? '<p style="color:#cd2322;margin:0px !important">' . $errors['admin'] . '</p>' : ''; ?>
							</div>
						</div>
					</div> <br>
					
					<div class="form-group">
						<input type="submit" value="Submit" class="btn btn-primary form-control"></br>
							<!-- Link to go back to the previous page -->
                            <div class="text-center" >
                                <a class="hover-link " style="cursor: pointer;" onclick="navigate()">Go to back</a>
                            </div>

                            <script>
                                function navigate() {
                                    window.location.href = 'http://localhost/view-hiring/index.php';
                                }
                            </script>
					</div><br>
					<?php if (!empty($message)):  ?>
					<div style="color: green; margin-top: 7px;">
						<?= $message ?>
					</div>
				<?php endif; ?>
				</form>
				
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include("../footer.php");