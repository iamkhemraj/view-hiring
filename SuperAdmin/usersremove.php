<?php
session_start();
$errors = [];

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
$context = stream_context_create($options);
$result = file_get_contents('http://localhost:5000/api/showUsers', false, $context);
$user = json_decode($result, true);

$allAssignedUsers = !empty($user['showUsers']) ? $user['showUsers'] : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {



    $userId = $_POST['userId'] ?? '';
    
    $data = [
        'userId' => $userId,
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
    $result = file_get_contents('http://localhost:5000/super_admin/delete', false, $context);
    $resultData = json_decode(  $result , true);
   
    if ($result !== false) {
        $response = json_decode($result, true);
        if (isset($response['status'])) {
            header("Location: {$_SERVER['PHP_SELF']}");
            exit();
        } else {
            // Handle error response from API if needed
        }
    } else {
        $status_code = $http_response_header[0] ? explode(' ', $http_response_header[0])[1] : null;
        if ($status_code === '401') {
            $errors['invalid'] = 'Unauthorized. Please check your credentials.';
        } else {
            $errors['invalid'] = 'An error occurred. Please try again later.';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <div class="lastuser text-danger text-center" >
                      <?= isset($resultData['response']) ? $resultData['response'] : '' ; ?>
                    </div>
                    <div class="card-header h4 m-0 text-center">Users Details</div>
                    <div class="card-body">
                        <table class="table table-striped text-left">
                            <tr>
                                <th>Sr No.</th>
                                <th>User Name</th>
                                <th>Delete</th>
                            </tr>
                            <?php if (!empty($allAssignedUsers)): ?>
                                <?php foreach ($allAssignedUsers as $key => $user): ?>
                                    <tr>
                                        <td><?= $key + 1 ?></td>
                                        <td><?= $user['name'] ?></td>
                                        <td>
                                        <form id="deleteForm_<?= $user['id'] ?>" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                            <input type="hidden" name="userId" value="<?= $user['id'] ?>">
                                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $user['id'] ?>)">Delete</button>
                                        </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3">No users assigned</td>
                                </tr>
                            <?php endif; ?>
                        </table>
                        <a href="javascript:window.history.back();" class="text-secondary">Go to Back </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(userId) {
            if (confirm("Are you sure you want to delete this user?")) {
                // If user confirms deletion, submit the form
                document.getElementById('deleteForm_' + userId).submit();
            }
        }
    </script>
</body>
</html>


