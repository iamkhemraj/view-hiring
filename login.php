<?php
require_once('global.php');
session_start();
$errors = [];
$unauthorized = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Server-side validation
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required';
    } elseif (strlen($password) > 8) {
        $errors['password'] = 'Password must be at most 8 characters long';
    }

    if (empty($errors)) {
        $data = ['email' => $email, 'password' => $password];
        $options = [
            'http' => [
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($data),
                'ignore_errors' => true, // Capture response even if it fails
            ],
        ];
        $context = stream_context_create($options);
        $result = file_get_contents( BASE_URL . '/api/login', false, $context);

        // Decode the JSON response
        $response = json_decode($result, true);

        // Get the response headers and status code
        $http_response_header = $http_response_header ?? [];
        $status_code = null;
        if (!empty($http_response_header)) {
            list($protocol, $code, $message) = explode(' ', $http_response_header[0], 3);
            $status_code = intval($code);
        }

        if ($result !== false) {
            if (isset($response['access_token'])) {
                $_SESSION['access_token'] = $response['access_token'];
                setcookie('access_token', $response['access_token'], time() + (86400 * 30), "/"); // 30 days expiration
                header('Location: index.php');
                exit();
            } else {
                // Handle errors returned by the API
                if (isset($response['errors'])) {
                    foreach ($response['errors'] as $key => $value) {
                        $errors[$key] = implode(', ', $value);
                    }
                } else {
                    $unauthorized = $response['error'] ?? 'Unauthorized';
                }
            }
        } else {
            if ($status_code === 401) {
                $unauthorized = 'Unauthorized';
            } else {
                $unauthorized = 'An error occurred. Please try again later.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header h4 m-0 text-center">Login</div>
                <div class="card-body">
                    <?php if (!empty($unauthorized)): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($unauthorized) ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="text" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>">
                            <?php if (isset($errors['email'])): ?>
                                <small class="text-danger"><?php echo htmlspecialchars($errors['email']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" maxlength="8">
                            <?php if (isset($errors['password'])): ?>
                                <small class="text-danger"><?php echo htmlspecialchars($errors['password']); ?></small>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button><br>
                        If you don't have any account <a href="register.php">Register</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
