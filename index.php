<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  <h1>Wecome To The Hiring Management </h1>
  <fieldset style="width:20%;">
    <legend>Registeration Form</legend>
    <form action="http://localhost:8000/api/login" method="post" autocomplete="off">
      <label for="name">Name</label> </br>
      <input type="text" name="name" id="name"> </br>

      <label for="email">E-mail</label> </br>
      <input type="email" name="email" id="email"> </br>

      <label for="password">Password</label> </br>
      <input type="password" name="password" id="password"> </br>

      <label for="cnfmpwd">Confirm Password</label> </br>
      <input type="password" name="password_confirmation" id="cnfmpwd"> </br></br>

      <input type="submit" value="sumit"></br> </br>
      <a href="http://localhost/view-hiring/login.php">return to login</a>
    </form>
  </fieldset>
</body>
</html>