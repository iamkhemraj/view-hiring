<?php
session_start();
$errors = [];
$unauthorized = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Server-side validation
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    }
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    }

    $data = ['email' => $email, 'password' => $password];
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
            'ignore_errors' => true, // Capture response even if it fails
        ],
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents('http://localhost:5000/api/login', false, $context);

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
        if (isset($response['access_token'])) {
            $_SESSION['access_token'] = $response['access_token'];
            header('Location: profile.php');
            exit();
        } else {
            $unauthorized = $errorsdata['error'] ?? '';
        }
    } else {
        if ($status_code === 401) {
            $unauthorized = $errorsdata['error'] ?? '';
        } else {
            $unauthorized = 'An error occurred. Please try again later.';
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
	<?php if (!empty($unauthorized)): ?>
        <div style="color: red;">
            <?= $unauthorized ?>
        </div>
    <?php endif; ?>
    <h1>Login</h1>
    <form method="POST" action="login.php">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" >
        <?= isset($errors['email']) ? '<p style="color:#cd2322;margin:0px !important"> '.$errors['email'].'  </p>' : '' ; ?>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" >
        <?= isset($errors['password']) ? '<p style="color:#cd2322;margin:0px !important"> '.$errors['password'].'  </p>' : '' ; ?>
        <br>
        <button type="submit">Login</button>
        <div>
          <p>You have not an account</p> <a href="index.php">Register</a>
        </div>
    </form>
</body>
</html>
