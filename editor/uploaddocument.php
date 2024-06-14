<?php
include("../global.php");
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
            color: 000;
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
</body>
</html>
