<?php
session_start();
$errors = [];

// Check if the super admin user is logged in
if (!isset($_SESSION['access_token'])) {
    header('Location: /view-hiring/profile.php'); // Redirect to the profile page
    exit();
}

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

    $data = [
        'name' => $name,
        'email' => $email,
        'password' => $password,
        'password_confirmation' => $password_confirmation
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

    $context = stream_context_create($options);
    if($_SESSION['role'] == 'admin'){
        $result = file_get_contents('http://localhost:5000/admin/user/create', false, $context);
    }elseif($_SESSION['role'] == 'super admin'){
        $result = file_get_contents('http://localhost:5000/super_admin/user/register', false, $context);
    }

    $errorsdata = json_decode($result, true);
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
        $success_message = isset($response['response']) ? $response['response'] : '';
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
    <title>Register User</title>
    <!-- Latest compiled and minified CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Latest compiled JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        label {
            display: block;
            width: 100%;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
            <?= !empty($success_message) ? '<div class="alert alert-success">' . $success_message . '</div>' : ''; ?>
                <div class="card-header h4 m-0 text-center">Register User By <?= isset($_SESSION['role']) ? $_SESSION['role']:'';?></div>
                <div class="card-body">
                    <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" class="form-control">
                            <?= isset($errors['name']) ? '<p style="color:#cd2322;margin:0px !important">' . $errors['name'] . '</p>' : ''; ?>
                        </div>

                        <div class="form-group">
                            <label for="email">E-mail</label>
                            <input type="email" name="email" id="email" class="form-control" >
                            <?= isset($errors['email']) ? '<p style="color:#cd2322;margin:0px !important">' . $errors['email'] . '</p>' : ''; ?>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" value="">
                            <?= isset($errors['password']) ? '<p style="color:#cd2322;margin:0px !important">' . $errors['password'] . '</p>' : ''; ?>
                        </div>

                        <div class="form-group">
                            <label for="cnfmpwd">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="cnfmpwd" class="form-control" value="">
                            <?= isset($errors['password_confirmation']) ? '<p style="color:#cd2322;margin:0px !important">' . $errors['password_confirmation'] . '</p>' : ''; ?><br>
                        </div>

                        <div class="form-group">
                            <input type="submit" value="Submit" class="btn btn-primary form-control"></br>
                             	<!-- Link to go back to the previous page -->
                                 <div class="text-center" >
                                <a class="hover-link " style="cursor: pointer;" onclick="navigate()">Go to back</a>
                            </div>

                            <script>
                                function navigate() {
                                    window.location.href = 'http://localhost/view-hiring/profile.php';
                                }
                            </script>
                        </div>
                    </form>
                  
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
