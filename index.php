<?php
    include('header.php');
    if (!isset($_SESSION['access_token'])) {
        header('Location: login.php');
        exit();
    }
    $tokentime = $duration =  '' ;
    $token   = $_SESSION['access_token'];
    $options = [
        'http' => [
            'header' => "Authorization: Bearer $token\r\n",
            'method' => 'GET',
        ],
    ];

    $context     = stream_context_create($options);
    $result      = @file_get_contents(BASE_URL . '/api/tokenTime', false, $context);
    $tokenExpire = json_decode($result);
    $tokentime   = ($tokenExpire->response);
    //Check session is expire or not
    
    if (isset($_SESSION['last_activity'])) {
        $duration = time() - $_SESSION['last_activity'];
        if ($duration > $tokentime) {
        header('Location: logout.php');
        exit();
        }
    }

    $options = [
        'http' => [
            'header' => "Authorization: Bearer $token\r\n",
            'method' => 'GET',
        ],
    ];
    $context = stream_context_create($options);
    $result  = @file_get_contents(BASE_URL . '/api/profile', false, $context);

    if ($result === FALSE) {
        // If the token is expired or invalid
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit();
    }

    $user = json_decode($result, true);
    $_SESSION['role'] = $user['role'];

?>
  
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header h4 m-0 text-center text-dark "> Welcome To The <?= ucfirst($_SESSION['role']) ?> Dashboard </div>
                <div class="card-body">
                    <div id="clock">
                        <div class="time-segment">
                            <div id="hours">00</div>
                            <span>Hours</span>
                        </div>
                        <div class="time-segment">
                            <div id="minutes">00</div>
                            <span>Minutes</span>
                        </div>
                        <div class="time-segment">
                            <div id="seconds">00</div>
                            <span>Seconds</span>
                        </div>
                    </div>
                    <div class="profile"> <?php 
                        $profile = isset($user['profile']) ? $user['profile'] : '' ; 
                        $userRole = isset($user['role']) ? $user['role'] : '' ; 
                        
                        ?>
                        <img id="myImg" src="<?= !empty($profile) ? $profile : 'uploads/images.png' ?>" alt="" class="rounded-circle mb-3">
                    </div>
                    <div class="btn-group mb-3" role="group" aria-label="User Actions">
                        <a href="SuperAdmin/update.php" class="btn btn-outline-primary">Edit user</a>
                        <a  class="btn btn-outline-danger" onclick="logout()">Logout</a>
                    </div>
                    <table class="table table-bordered ">
                        <tr>
                            <th>Dashboard</th> 
                            <th colspan="7">Profile</th>
                        </tr>
                        <tr class="usert_tabs">
                            <th rowspan="7"><?php
                                switch ($userRole) {
                                    case 'admin':
                                        ?>
                                        <a href="SuperAdmin/userRegister.php" class="d-block btn btn-outline-primary">Register user</a>
                                        <a href="SuperAdmin/usersremove.php" class="d-block btn btn-outline-primary">Users Remove</a>
                                        <?php
                                        break;
                                
                                    case 'editor':
                                        ?>
                                        <a href="editor/uploaddocument.php" class="d-block btn btn-outline-primary">Upload document</a>
                                        <a href="editor/showdocument.php" class="d-block btn btn-outline-primary">Show Document</a>
                                        <?php
                                        break;
                                
                                    default:
                                        ?>
                                        <a href="SuperAdmin/adminRegister.php" class="d-block btn btn-outline-primary">Register admin</a>
                                        <a href="SuperAdmin/userManage.php" class="d-block btn btn-outline-primary">Manage user</a>
                                        <a href="SuperAdmin/userRegister.php" class="d-block btn btn-outline-primary">Register user</a>
                                        <a href="SuperAdmin/usersremove.php" class="d-block btn btn-outline-primary">Users Remove</a>
                                        <?php
                                        break;
                                }?>
                                <a href="usersdetails.php" class="d-block btn btn-outline-primary">Assigned User</a>        
                            </th>
                        </tr>
                        <tr class="username">
                            <td>
                            <p> <strong>Name:</strong>  <?= ucfirst(($user['name'])) ?></p>
                            <p><strong>E-mail: </strong><?= ucfirst($user['email']) ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> 

<script>
   // Initialize the countdown timer on page load
    function countdownDuration() {
        // Set the countdown duration in seconds (example: 30 minutes)
        const countdownDuration = <?= $tokentime; ?> * 1000; // Convert to milliseconds
        let endTime = localStorage.getItem('countdownEndTime');

        if (!endTime) {
            // Initialize endTime
            endTime = new Date().getTime() + countdownDuration;
            localStorage.setItem('countdownEndTime', endTime);
        }

        let x = setInterval(function() {
            // Get current time
            let now = new Date().getTime();
            let distance = endTime - now;

            let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            let seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById("hours").textContent = hours.toString().padStart(2, '0');
            document.getElementById("minutes").textContent = minutes.toString().padStart(2, '0');
            document.getElementById("seconds").textContent = seconds.toString().padStart(2, '0');

            // Refresh page after end countdown
            if (distance < 0) {
                clearInterval(x);
                document.getElementById("hours").textContent = "00";
                document.getElementById("minutes").textContent = "00";
                document.getElementById("seconds").textContent = "00";
                localStorage.removeItem('countdownEndTime');
                setTimeout(function() {
                    location.reload();
                }, 1000); // Refresh the page after 1 second
            }
        }, 1000);
    }
    // stop couter on logout
    function logout() {
        // Clear the countdown time on manual logout
        localStorage.removeItem('countdownEndTime');
        // Redirect to logout endpoint (example: '/logout')
        window.location.href = '/view-hiring/logout.php';
    }

    //call fucntion
    countdownDuration();
</script>

<?php include('footer.php'); ?>