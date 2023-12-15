<?php

require('connect.php');
require('authenticate.php');
require('fileUpload.php');

session_start();

if(isset($_SESSION["user"]))
{
    $userid = $_SESSION["user"];
} else
{
    $_SESSION["message"] = "You are not logged in!";
    header("Location: index.php");
    exit;
}

if ($_POST && !empty($_POST['title']) && !empty($_POST['content'])) {
    //  Sanitize user input to escape HTML entities and filter out dangerous characters.
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    //  Build the parameterized SQL query and bind to the above sanitized values.
    $query = "INSERT INTO review (title, content, user_id) VALUES(:title, :content, :user_id)";
    $statement = $db->prepare($query);

    //  Bind values to the parameters
    $statement->bindValue(":title", $title);
    $statement->bindValue(":content", $content);
    $statement->bindValue(":user_id", $_SESSION["user"]);

    if(isset($_FILES["imagefile"]))
    {  
        // move_uploaded_file($_FILES["imagefile"]["tmp_name"], $_FILES["imagefile"]["name"]);
        $validImage = file_is_an_image($_FILES["imagefile"]["tmp_name"], $_FILES["imagefile"]["name"]);

        if($validImage)
        {
            file_upload_path($_FILES["imagefile"]["name"]);
            $query = "INSERT INTO `image`(`review_id`, `filename`) VALUES (:review_id, :filename)";
            $statement = $db->prepare($query);
            $statement->bindValue(':review_id', $id, PDO::PARAM_INT);  
            $statement->bindValue(':filename', $_FILES["imagefile"]["name"]);
            $statement->execute();
        }  
    }

    //  Execute the INSERT.
    //  execute() will check for possible SQL injection and remove if necessary
    if ($statement->execute()) {
        header("Location: index.php");
        exit; 
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Review</title>
    <link rel="stylesheet" href="styles/main.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
        <?php include("title.php") ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
        <form action="create.php" method="post" class="row g-3">
            <div class="form-group">
                <label for="exampleFormControlInput1" class="form-label">Title</label>
                <input type="text" class="form-control" id="exampleFormControlInput1" name="title">
            </div>
            <div class="form-group">
                <label for="exampleFormControlTextarea1" class="form-label">Content</label>
                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="content"></textarea>
            </div>
            <input type="file" name="imagefile">
            <input type="submit" name="command" value="Update Blog" />
        </form>
        
</body>
</html>