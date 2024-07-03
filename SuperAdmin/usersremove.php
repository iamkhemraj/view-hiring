<?php
include("../header.php");
$errors = [];

// Check if the super admin user is logged in
if (!isset($_SESSION['access_token'])) {
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
$context = stream_context_create($options);
if($_SESSION['role']=='admin'){
    $result = file_get_contents( BASE_URL . '/admin/adminShowUsers', false, $context);
} elseif ($_SESSION['role'] == 'super admin') {
    $result = file_get_contents( BASE_URL . '/api/showUsers', false, $context);
}
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
    $result = file_get_contents(BASE_URL . '/super_admin/delete', false, $context);
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

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="lastuser text-danger text-center" >
                    <?= isset($resultData['response']) ? '<div class="alert alert-danger">'.$resultData['response'].'</div>' : '' ; ?>
                </div>
                <div class="card-header h4 m-0 text-center">Users Remove</div>
                <div class="card-body">
                <form id="multiuserdeleteForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <table class="table table-striped text-right">
                        <tr>
                            <th>Multi/Delete</th>
                            <th>Sr No.</th>
                            <th>User Name</th>
                            <th>User Role</th>
                        </tr>
                        <?php if (!empty($allAssignedUsers)): ?>
                            <?php foreach ($allAssignedUsers as $key => $user): ?>
                                <tr>
                                    <td><input type="checkbox" name="userId[]" value = "<?= $user['id'] ?>"></td>
                                    <td><?= $key + 1 ?></td>
                                    <td><?= $user['name']; ?></td>
                                    <td><?= $user['role']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="5" style = margin-right:34px;>
                                    <input type="checkbox" name="usersId" id="check_all" > Check all 
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmAlldelete()">Delete</button>
                                </td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">No users found</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                      </form>
                        <div class="text-center" >
                            <a class="hover-link " style="cursor: pointer;" onclick="navigate()">Go to back</a>
                        </div>

                        <script>
                            function navigate() {
                                window.location.href = 'http://localhost/view-hiring/index.php';
                            }
                        </script>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function confirmAlldelete() {
        if (confirm("Are you sure you want to delete this user?")) {
            // If user confirms deletion, submit the form
            document.getElementById('multiuserdeleteForm').submit();

        }
    }
    $("#check_all").click(function(){
        $('input:checkbox').not(this).prop('checked', this.checked);
    });
</script>
<?php include('../footer.php');?>

