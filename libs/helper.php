<?php

if (isset($_POST['submit'])) {
  $v1 = "FirstUser";
  $v2 = "MyPassword";
  $v3 = $_POST['text'];
  $v4 = $_POST['pwd'];
  if ($v1 == $v3 && $v2 == $v4) {
      $_SESSION['luser'] = $v1;
      $_SESSION['start'] = time(); // Taking now logged in time.
      // Ending a session in 30 minutes from the starting time.
      $_SESSION['expire'] = $_SESSION['start'] + (30 * 60);
      header('Location: http://localhost/somefolder/homepage.php');
  } else {
      echo "Please enter the username or password again!";
  }
}