<?php

session_start();
require('connect.php');

$error = NULL;

// Build and prepare SQL String with :id placeholder parameter.
$query = "SELECT * FROM review WHERE review_id = :id LIMIT 1";
$statement = $db->prepare($query);

// Sanitize $_GET['id'] to ensure it's a number.
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$content = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if(!$id)
{
    $_SESSION["message"] = "Error occured as post was not selected!";
    header("Location: index.php");
    exit;
}

// Bind the :id parameter in the query to the sanitized
// $id specifying a binding-type of Integer.
$statement->bindValue('id', $id, PDO::PARAM_INT);
$statement->execute();

$row = $statement->fetch();

if($_POST)
{
    if(!$content)
    {
        $error = "Can't enter a comment without a comment!";
    }

    if(isset($_SESSION["user"]))
    {
        $userid = $_SESSION["user"];
        $username = $_SESSION["username"];
    } else
    {
        $_SESSION["message"] = "You are not logged in!";
        header("Location: login.php");
        exit;
    }

    if(!$error)
    {
        $query = "INSERT INTO comments (review_id, username, content) VALUES (:review_id, :username, :content)";
        $statement = $db->prepare($query);
        $statement->bindValue(':review_id', $id, PDO::PARAM_INT);
        $statement->bindValue(':username', $username);
        $statement->bindValue(':content', $content);
        $statement->bindValue(':image', $image);
        $statement->execute();
    }
}

$query = "SELECT * FROM comments WHERE review_id = :id ORDER BY comment_id DESC LIMIT 10";
$statement = $db->prepare($query);

$statement->bindValue('id', $id, PDO::PARAM_INT);
$statement->execute();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Welcome to my Blog!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="styles/post.css">
</head>
<body>
    <?php include("title.php") ?>
            <article class="blog-post">
                <h2 class="display-5 link-body-emphasis mb-1"><?= $row['title'] ?></h2>
                <p class="blog-post-meta"><?= $row['datatime'] ?></p>

                <p><?= $row['content'] ?></p>
                <img src="images/gallery/<?php echo $result; ?>.jpg">
                <a href="edit.php?id=<?= $row['review_id'] ?>">edit</a>
            </article>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <div class="row">
        <div class="col-lg-4">

        </div>
        <div class="col-lg-6">
            <form class="form-horizontal" action="?id=<?=$id ?>" method="POST">
                <div class="form-group">
                    <label class="col-lg-3 control-label">Add Comment</label>
                    <div class="col-lg-9">
                        <textarea class="form-control" name="comment" placeholder="Comment" id="" cols="10" rows="5"></textarea>
                    </div>
                </div>
                <input type="submit" name="command" value="Comment" class="btn btn-primary">
                <a href="dashboard" class="btn btn-default">Go Back</a>
            </form>
            <?php while ($row = $statement->fetch()) : ?>
                <?= $row["username"] ?> says
                <?= $row["content"] ?>
            <?php endwhile ?>
        </div>
    </div>
</body>
</html>