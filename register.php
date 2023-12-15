<?php
session_start();
require('connect.php');

$error = NULL;

//  Sanitize user input to escape HTML entities and filter out dangerous characters.
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$confirmpassword = filter_input(INPUT_POST, 'confirmpassword', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$lastName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$captcha = filter_input(INPUT_POST, 'captcha', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
    if ($_POST && !empty($_POST['email']) && !empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['confirmpassword']) && !empty($_POST['firstName']) && !empty($_POST['lastName'])) {
        
        if(isset($_SESSION["captcha"]) && $_SESSION["captcha"] != $captcha)
        {
            $error = "Invalid captcha";
        }

        if($confirmpassword != $password)
        {
          $error = "Passwords do not match";
        } else
        {
          $querySelect = "SELECT email FROM `user` WHERE email=:email OR username=:username";
        $statementSelect = $db->prepare($querySelect);

        $statementSelect->bindValue(":email", $email);
        $statementSelect->bindValue(":username", $username);

        $statementSelect->execute();

        if($statementSelect->rowCount() != 0)
        {
            $error = "Email address already used!";
        }
        if(!$error)
        {
          $query = "INSERT INTO `user` (`email`, `username`, `password`, `userType`, `firstName`, `lastName`) VALUES (:email, :username, :password, 'user', :firstName, :lastName)";
          $statement = $db->prepare($query);
      
          //  Bind values to the parameters
          $statement->bindValue(":email", $email);
          $statement->bindValue(":username", $username);
          $statement->bindValue(":password", password_hash($password, PASSWORD_DEFAULT));
          $statement->bindValue(":firstName", $firstName);
          $statement->bindValue(":lastName", $lastName);
      
          //  Execute the INSERT.
          //  execute() will check for possible SQL injection and remove if necessary
          if ($statement->execute()) {
              header("Location: login.php");
              exit; 
          }  
        }  
        }
    }

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign Up</title>
    <link rel="stylesheet" href="styles/register.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  </head>
  <body>
  <form class="row g-3" action="register.php" method="post">
  <div class="col-md-12">
    <label for="email" class="form-label">Email</label>
    <input type="email" class="form-control" id="email" name="email" value="<?= $email ?>">
  </div>
  <div class="col-md-6">
    <label for="password" class="form-label">Password</label>
    <input type="password" class="form-control" id="password" name="password">
  </div>
  <div class="col-md-6">
    <label for="confirmpassword" class="form-label">Confirm Password</label>
    <input type="password" class="form-control" id="confirmpassword" name="confirmpassword">
  </div>
  <div class="col-12">
    <label for="username" class="form-label">User Name</label>
    <input type="text" class="form-control" id="username" name="username">
  </div>
  <div class="col-md-6">
    <label for="firstName" class="form-label">First Name</label>
    <input type="text" class="form-control" id="firstName" name="firstName">
  </div>
  <div class="col-md-6">
    <label for="lastName" class="form-label">Last Name</label>
    <input type="text" class="form-control" id="lastName" name="lastName">
  </div>
  <div class="col-md-6">
    <img src="image.php" alt="">
    <input type="text" name="captcha">
  </div>
  <div class="col-12">
    <button type="submit" class="btn btn-primary">Register</button>
  </div>
  <div>
    <?php if($error): ?>
        <?= $error ?>
      <?php endif ?>
  </div>
</form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  </body>
</html>