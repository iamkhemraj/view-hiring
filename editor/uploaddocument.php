<?php
include("../header.php");
session_start();
$errors = [];
$success_message = '';

// Check if the super admin user is logged in
if (!isset($_SESSION['access_token'])) {
    header('Location: /view-hiring/index.php'); // Redirect to the profile page
    exit();
}

$password_confirmation = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $document = $_POST['user_documents'] ?? '';

    // Prepare data for API request
    $data = [
        'user_documents' => $document,
    ];

    // Check if an image is uploaded
    if (!empty($_FILES['user_documents']['tmp_name'])) {
        $profile_tmp_name = $_FILES['user_documents']['tmp_name'];
        $profile_original_name = $_FILES['user_documents']['name'];
        $profile_mime_type = $_FILES['user_documents']['type'];
        $data['user_documents'] = new CURLFile($profile_tmp_name, $profile_mime_type, $profile_original_name);
    }

    // Prepare HTTP headers and options
    $token = $_SESSION['access_token'] ?? '';
    $headers = ['Authorization: Bearer ' . $token];

    // Initialize cURL session
    $curl = curl_init();
    // Set cURL options
    curl_setopt_array($curl, [
        CURLOPT_URL =>  BASE_URL . '/user/UserDocuments',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_FOLLOWLOCATION => true, // Follow redirects
        CURLOPT_SSL_VERIFYPEER => false, // For development only, disable SSL verification
    ]);

    // Execute cURL request
    $result = curl_exec($curl);
    curl_close($curl);
    // Handle API response
    if ($result !== false) {
        $errorsdata = json_decode($result, true);
       $success_message = isset($errorsdata['response']) ? $errorsdata['response'] : '' ;
        $errorMessages = [];
        if (isset($errorsdata['user_documents'])) {
            $errorMessages['user_documents'] = implode(', ', $errorsdata['user_documents']);
        }
        foreach ($errorMessages as $key => $value) {
            $errors[$key] = $value;
        }
    } else {
        // Handle API request failure
        $errors['invalid'] = 'An error occurred. Please try again later.';
    }
}

?>

<div class="container">
    <div class="row justify-content-center" style="margin: 34px;">
        <div class="col-md-6">
            <div class="card">
            <?= !empty($success_message) ? '<div class="alert alert-success">' . $success_message . '</div>' : ''; ?>
                <div class="card-header text-center">Upload Document </div>
                <div class="card-body">
                    
                    <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off" enctype="multipart/form-data">
                    
                        <div class="mb-3">
                            <label for="user_documents" class="form-label">Upload Document</label>
                            <input type="file" name="user_documents" id="user_documents" class="form-control">
                            <?= isset($errors['user_documents']) ? '<small class="text-danger">' . $errors['user_documents'] . '</small>' : ''; ?>
                        </div>
                        
                        <div class="mb-3">
                            <input type="submit" value="Upload" class="btn btn-primary">
                        </div>
                        <div class="text-center" >
                            <a class="hover-link " style="cursor: pointer;" onclick="navigate()">Go to  back</a>
                        </div>

                        <script>
                            function navigate() {
                            window.location.href = 'http://localhost/view-hiring/index.php';
                            }
                        </script>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include("../footer.php");