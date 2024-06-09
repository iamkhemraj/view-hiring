<?php 

  session_start();
  $errors = [];

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $name = $_POST['name'] ?? '';
      $email = $_POST['email'] ?? '';
      $password = $_POST['password'] ?? '';
      $password_confirmation = $_POST['password_confirmation'] ?? '';

		$data = ['name' => $name, 'email' => $email, 'password' => $password, 'password_confirmation' => $password_confirmation];
		$options = [
			'http' => [
				'header'  => "Content-type: application/json\r\n",
				'method'  => 'POST',
				'content' => json_encode($data),
				'ignore_errors' => true, // Capture response even if it fails
			],
		];
		$context  = stream_context_create($options);
		$result = file_get_contents('http://localhost:5000/api/create', false, $context);

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

		foreach($errorMessages as $key=>$value)
		{
			$errors[$key] = $value;
		}


		// Get the response headers and status code
		$http_response_header = $http_response_header ?? [];
		$status_code = null;
		if (!empty($http_response_header)) {
			list($protocol, $code, $message) = explode(' ', $http_response_header[0], 3);
			$status_code = intval($code);
		}
		// print_r($result);
		if ($result !== false) {
			$response = json_decode($result, true);
			if (isset($response['status'])) {
				header('Location: login.php'); // Redirect to profile.php
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
  <title>Document</title>
  <style>
    label{
      display: block;
      width:100%;
    }
  </style>
</head>
<body>
  <h1>Wecome To The Hiring Management </h1>
  <fieldset style="width:400px;">
    <legend>Registeration Form</legend>
    <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off">
      <label for="name">Name</label> </br>
      <input type="text" name="name" id="name">
      <?= isset($errors['name']) ? '<p style="color:#cd2322;margin:0px !important"> '.$errors['name'].'  </p>' : '' ; ?>

      <label for="email">E-mail</label> </br>
      <input type="email" name="email" id="email"> 
      <?= isset($errors['email']) ? '<p style="color:#cd2322;margin:0px !important"> '.$errors['email'].'  </p>' : '' ; ?>

      <label for="password">Password</label> </br>
      <input type="password" name="password" id="password"> 
      <?= isset($errors['password']) ? '<p style="color:#cd2322;margin:0px !important"> '.$errors['password'].'  </p>' : '' ; ?>

      <label for="cnfmpwd">Confirm Password</label> </br>
      <input type="password" name="password_confirmation" id="cnfmpwd"> <br>
      <?= isset($errors['password_confirmation']) ? '<p style="color:#cd2322;margin:0px !important"> '.$errors['password_confirmation'].'  </p>' : '' ; ?><br>

      <input type="submit" value="sumit"></br> </br>
      <a href="http://localhost/view-hiring/login.php">return to login</a>
    </form>
  </fieldset>
</body>
</html>
