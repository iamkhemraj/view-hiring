<?php
require_once('header.php');

// Fetch  all assigned  users deatails 
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
$context  = stream_context_create($options);
$result   = file_get_contents( BASE_URL . '/assigned-users', false, $context);
$user     = json_decode($result, true);
$allAssignedUsers = !empty($user) && isset($user['allAssignedUsers']) ? $user['allAssignedUsers'] : [];
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header h4 m-0 text-center">Users Details</div>
                <div class="card-body">
                    <table class="table table-striped text-center">
                        <tr>
                            <th>User Name</th>
                            <th>Admin Name</th>
                        </tr>
                        <?php if (!empty($allAssignedUsers)): ?>
                            <?php foreach ($allAssignedUsers as $allAssignedUser): ?>
                                <tr>
                                    <td>
                                        <?php 
                                        $user = isset($allAssignedUser['user']) ? $allAssignedUser['user'] : '';
                                        echo isset($user['name']) ? htmlspecialchars($user['name']) : '';
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $admin = isset($allAssignedUser['admin']) ? $allAssignedUser['admin'] : '';
                                        echo isset($admin['name']) ? htmlspecialchars($admin['name']) : '';
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2">No users assigned</td>
                            </tr>
                        <?php endif; ?>
                    </table>
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
<?php include('footer.php');?>
