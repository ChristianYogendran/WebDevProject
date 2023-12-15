<?php
  
  $logoutVerify = filter_input(INPUT_GET, 'logout');

  if($logoutVerify == true)
  {
    unset($_SESSION["user"]);
  }

?>

<nav class="navbar bg-primary" data-bs-theme="dark">
<div class="container-fluid">
    <a class="navbar-brand" href="#">Content Management</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
      <div class="navbar-nav">
        <a class="nav-link active" aria-current="page" href="index.php">Home</a>
        <?php if(isset($_SESSION["user"])): ?>
        <a class="nav-link" href="?logout=true">Log Out</a>
        <?php else: ?>
        <a class="nav-link" href="login.php">Log In</a>
        <?php endif ?>
      </div>
      <form class="d-flex" role="search">
      <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
      <button class="btn btn-outline-success" type="submit">Search</button>
    </form>
    </div>
  </div>
</nav>

