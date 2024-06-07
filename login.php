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
    <legend>Login Form</legend>
    <form action="http://localhost:8000/api/login" method="post" >
      <label for="email">E-mail</label> </br>
      <input type="email" name="email" id="email"> </br>

      <label for="password">Password</label> </br>
      <input type="password" name="password" id="password"> </br>
      <input type="submit" value="sumit">
     
    </form>
    <a href="http://localhost:8000/api/profile">Get profile</a>
    <form action="http://localhost:8000/api/logout" method="post">
            <input type="submit" value="Logout">
    </form>
    
  </fieldset>
</body>
</html>