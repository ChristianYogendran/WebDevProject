<?php
    session_start();
    require('connect.php');

    $orderBy = "datatime";
    

    if(isset($_GET['sort'])){
      $orderBy = $_GET['sort'];
    }

    $sortOrder = "ASC";

    if($orderBy == "datatime" || $orderBy == "lastupdated"){
      $sortOrder = "DESC";
    }

    $query = "SELECT * , (SELECT image.filename FROM image WHERE image.review_id = review.review_id LIMIT 1) filename FROM `review` ORDER BY $orderBy $sortOrder"; 
    if(isset($_POST['search']))
    {
      $search = $_POST['search'];
      $query = "SELECT * , (SELECT image.filename FROM image WHERE image.review_id = review.review_id LIMIT 1) filename FROM `review` WHERE title LIKE '%$search%' ORDER BY $orderBy $sortOrder";
    }
  
    // A PDO::Statement is prepared from the query.
    $statement = $db->prepare($query);

    // Execution on the DB server is delayed until we execute().
    $statement->execute();

    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="">
    <link rel="stylesheet" href="styles/main.css">   
</head>
<body>
    <?php include("title.php") ?>
    <table width=100%>
  <tr>
  <td>&nbsp;</td>
    <td>Sort By</td>
    <td>
      <form action="index.php" method="get">
        <select name="sort" id="sort" width="300" style="width: 300px">
        <option value="lastupdated" <?= $orderBy == "lastupdated" ? "selected" : NULL ?>>Last updated</option>
        <option value="datatime" <?= $orderBy == "datatime" ? "selected" : NULL ?>>Date posted</option>
        <option value="title" <?= $orderBy == "title" ? "selected" : NULL ?>>Title</option>
        <option value="content" <?= $orderBy == "content" ? "selected" : NULL ?>>Content</option>
      </select><input type="submit" value="submit">
      </form>
    </td>
    <td>&nbsp;</td>
  </tr>
    <select id="item_name" name="item_name">
      <dl>
          <dt>Technology</dt>
              <dd><option value="1">Internet Explorer</option></dd>
              <dd><option value="2">Brave</option></dd>
              <dd><option value="3">Google Chrome</option></dd>
          <dt>Programming Languages</dt>
              <dd><option value="4">Java</option></dd>
              <dd><option value="5">C Sharp</option></dd>
              <dd><option value="6"></option></dd>
      </dl>
  </select>
  <form action="index.php" method="post">
    <input id="search" name="search" type="text" placeholder="Type here" value="">
    <input id="submit" type="submit" value="Search">
  </form>
</table>
    <section>
    <?php if(isset($_SESSION["message"])): ?>
        <h3>
            <?= $_SESSION["message"] ?>
        </h3>
        
        <?php unset($_SESSION["message"]); endif ?>
    <?php while ($row = $statement->fetch()) : ?>
      <div class="col-md-6">
      <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
        <div class="col p-4 d-flex flex-column position-static">
          <h3 class="mb-0"><a href="post.php?id=<?= $row['review_id'] ?>"><?= $row['title'] ?></a></h3>
          <div class="mb-1 text-body-secondary"><?= $row['datatime'] ?></div>
          <p class="card-text mb-auto"><?= $row['content'] ?></p>
          <a href="post.php?id=<?= $row['review_id'] ?>" class="icon-link gap-1 icon-link-hover stretched-link">
            Continue reading
            <svg class="bi"><use xlink:href="#chevron-right"></use></svg>
          </a>
        </div>
        <?php if($row['filename']) :?>
        <div class="col-auto d-none d-lg-block">
          <img width="200" height="250" src="images/<?= $row['filename'] ?>" alt="">
        </div>
        <?php endif ?>
      </div>
    </div>
    <?php endwhile ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </section>
  </body>

</html>