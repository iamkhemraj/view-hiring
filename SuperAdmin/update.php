<?php
session_start();
$errors = [];

// Check if the super admin user is logged in
if (!isset($_SESSION['access_token']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'super admin') {
    header('Location: /view-hiring/profile.php'); // Redirect to the profile page
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
$result  = file_get_contents('http://localhost:5000/super_admin/getAllUserData', false, $context);
$userdetails    = json_decode($result, true);

if(!empty($userdetails)){     
	$userdata  =  isset($userdetails['userdata'][0]) ? $userdetails['userdata'][0] : '';
};
// Initialize variables for form fields
$name = '';
$email = '';
$password = '';
$password_confirmation = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirmation = $_POST['password_confirmation'] ?? '';
   $profile = $_FILES['profile']['tmp_name']?? '';
   
    $data = [
        'name' => $name,
        'email' => $email,
        'password' => $password,
        'password_confirmation' => $password_confirmation,
        'profile' => $profile,
    ];

    $token = $_SESSION['access_token'];
    $options = [
        'http' => [
            'header' => "Content-type: application/json\r\nAuthorization: Bearer $token\r\n",
            'method' => 'POST',
            'content' => json_encode($data),
            'ignore_errors' => true, // Capture response even if it fails
        ],
    ];

    $context     = stream_context_create($options);
    $result      = file_get_contents('http://localhost:5000/super_admin/updateProfile', false, $context);
    $errorsdata  = json_decode($result, true);
    $errorMessages = [];

    if (isset($errorsdata['name'])) {
        $errorMessages['name'] = implode(', ', $errorsdata['name']);
    }
    if (isset($errorsdata['email'])) {
        $errorMessages['email'] = implode(', ', $errorsdata['email']);
    }
    if (isset($errorsdata['password'])) {
        $errorMessages['password'] = implode(', ', $errorsdata['password']);
    }
    if (isset($errorsdata['password_confirmation'])) {
        $errorMessages['password_confirmation'] = implode(', ', $errorsdata['password_confirmation']);
    }
    if (isset($errorsdata['profile'])) {
        $errorMessages['profile'] = implode(', ', $errorsdata['profile']);
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
            header("location:javascript://history.go(-1)");
            exit();
        } else {
            // Handle error response from API if needed
        }
    } else {
        if ($status_code === 401) {
            $errors['invalid'] = 'Unauthorized. Please check your credentials.';
        } else {
            $errors['invalid'] = 'An error occurred. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <!-- Latest compiled and minified CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Latest compiled JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v6.5.2/css/all.css"
      >
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #343a40;
            color: #fff;
            border-radius: 15px 15px 0 0;
            font-weight: bold;
        }
        .card-body {
            padding: 30px;
        }
        .form-control {
            border-radius: 5px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            border-radius: 5px;
            width: 100%;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .link {
            color: #007bff;
        }
        .link:hover {
            color: #0056b3;
            text-decoration: underline;
        }
        .profile-picture {
            text-align: center;
        }
        .profile-picture img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
        .profile-picture .edit-btn {
            position: relative;
            top: -40px;
            left: 60px;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: 50%;
            padding: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">Update Profile</div>
                    <div class="card-body">
                      
                        <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" id="name" class="form-control" value="<?= isset($userdata['name']) ?$userdata['name'] : ''; ?>">
                                <?= isset($errors['name']) ? '<small class="text-danger">' . $errors['name'] . '</small>' : ''; ?>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="<?= isset($userdata['email']) ? $userdata['email'] : ''; ?>">
                                <?= isset($errors['email']) ? '<small class="text-danger">' . $errors['email'] . '</small>' : ''; ?>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control">
                                <?= isset($errors['password']) ? '<small class="text-danger">' . $errors['password'] . '</small>' : ''; ?>
                            </div>
                            <div class="mb-3">
                                <label for="cnfmpwd" class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="cnfmpwd" class="form-control">
                                <?= isset($errors['password_confirmation']) ? '<small class="text-danger">' . $errors['password_confirmation'] . '</small>' : ''; ?>
                            </div>
                            <div class="mb-3">
                                <label for="profile" class="form-label">Profile Picture</label>
                                <input type="file" name="profile" id="profile" class="form-control">
                                <?= isset($errors['profile']) ? '<small class="text-danger">' . $errors['profile'] . '</small>' : ''; ?>
                            </div>
                           
                            <div class="mb-3">
                                <input type="submit" value="Update Profile" class="btn btn-warning">
                            </div>
                            <div class="text-center">
                                <a href="javascript:window.history.back();" class="link">Go Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
