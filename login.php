<?php

session_start();
require('connect.php');

$error = NULL;

    
    if ($_POST && !empty($_POST['email']) && !empty($_POST['password'])) {
        //  Sanitize user input to escape HTML entities and filter out dangerous characters.
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $querySelect = "SELECT * FROM `user` WHERE email=:email";
        $statementSelect = $db->prepare($querySelect);

        $statementSelect->bindValue(":email", $email);

        $statementSelect->execute();

        $row = $statementSelect->fetch();
        
        if(!$row)
        {
          $error = "Email does not match";
        }else if(!password_verify($password, $row["password"]))
        {
          $error = "Passwords do not match ";
        } else
        {
          $_SESSION["user"] = $row["user_id"];
          $_SESSION["username"] = $row["username"];
          
          $_SESSION["message"] = "Login was successful!";

          header("Location: index.php");
          exit;
/*
        if($statementSelect->rowCount() == 0)
        {
          $statement = $db->prepare($query);
      
          //  Bind values to the parameters
          $statement->bindValue(":email", $email);
          $statement->bindValue(":password", $password);
        }
        */
    }
  }

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log In</title>
    <link rel="stylesheet" href="styles/register.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  </head>
  <body>
  <form class="row g-3" action="login.php" method="post">
  <div class="mb-3">
    <label for="email" class="form-label">Email Address</label>
    <input type="email" class="form-control" id="email" aria-describedby="emailHelp" name="email">
    <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
  </div>
  <div class="mb-3">
    <label for="password" class="form-label">Password</label>
    <input type="password" class="form-control" id="password" name="password">
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
  <div>
  <?php if($error): ?>
        <?= $error ?>
      <?php endif ?>
  </div>
  </form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  </body>
</html>