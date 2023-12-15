<?php
    session_start();
    require('connect.php');
    require('authenticate.php');
    
    if(isset($_SESSION["user"]))
    {
        $userid = $_SESSION["user"];
    } else
    {
        $_SESSION["message"] = "You are not logged in!";
        header("Location: index.php");
        exit;
    }

    // Sanitize the id. Like above but this time from INPUT_GET.
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    // Sanitize user input to escape HTML entities and filter out dangerous characters.
    $title  = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $command = filter_input(INPUT_POST, 'command', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $imagedelete = filter_input(INPUT_POST, 'deleteimage');

    if ($id) { // Retrieve quote to be edited, if id GET parameter is in URL.
        
        // Build the parametrized SQL query using the filtered id.
        $query = "SELECT * FROM review WHERE review_id = :review_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':review_id', $id, PDO::PARAM_INT);
        
        // Execute the SELECT and fetch the single row returned.
        $statement->execute();
        $blog = $statement->fetch();

        $query = "SELECT * FROM image WHERE review_id = :review_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':review_id', $id, PDO::PARAM_INT);
        
        // Execute the SELECT and fetch the single row returned.
        $statement->execute();
        $blogimage = $statement->fetch();
    }

    if(!$id || !$blog)
    {
        $_SESSION["message"] = "There is no existing post!";
        header("Location: index.php");
        exit; 
    }

    if($blog["user_id"] != $_SESSION["user"]) // and if the user is not an admin
    {
        $_SESSION["message"] = "WARNING: This post doesn't belong to you!";
        header("Location: index.php");
        exit; 
    }

    $error = "";

    if ($_POST) {
        if(!$title)
        {
            $error.= "<p>No title was given</p>";
        }

        if(!$content)
        {
            $error.= "<p>No content was displayed</p>";
        }
        $blog["title"] = $title;
        $blog["content"] = $content;

        require('fileUpload.php');

        if($image_upload_detected)
        {  
            echo "Image upload has been detected! ";
            // move_uploaded_file($_FILES["imagefile"]["tmp_name"], $_FILES["imagefile"]["name"]);

            if($validimage)
            {
                echo "Image was valid ";
                file_upload_path($_FILES["imagefile"]["name"]);
                $query = "INSERT INTO `image`(`review_id`, `filename`) VALUES (:review_id, :filename)";
                $statement = $db->prepare($query);
                $statement->bindValue(':review_id', $id, PDO::PARAM_INT);  
                $statement->bindValue(':filename', $_FILES["imagefile"]["name"]);
                $statement->execute();
            }  
        }

        if($command == "Delete")
        {
            $query= "DELETE FROM review WHERE review_id = :review_id";
            $statement = $db->prepare($query);
            $statement->bindValue(':review_id', $id, PDO::PARAM_INT);
            // Execute to DELETE.
            $statement->execute();
            
            // Redirect after delete.
            header("Location: index.php");
            exit; 
        }
        elseif($command == "Update" && !$error) {
            // Build the parameterized SQL query and bind to the above sanitized values.
            $query     = "UPDATE review SET title = :title, content = :content WHERE review_id = :review_id";
            $statement = $db->prepare($query);
            $statement->bindValue(':title', $title);        
            $statement->bindValue(':content', $content);
            $statement->bindValue(':review_id', $id, PDO::PARAM_INT);
            
            // Execute to Update.
            $statement->execute();
            /*
            $query = "INSERT INTO image (review_id, filename) VALUES (:review_id, :filename)";
            $statement = $db->prepare($query);
            $statement->bindValue(':review_id', $id, PDO::PARAM_INT);
            $statement->bindValue(':filename', $filename);

            $statement->execute();
*/
            if($imagedelete == "on")
            {
                $query = "DELETE FROM image WHERE review_id = :review_id";
                $statement = $db->prepare($query);
                $statement->bindValue(':review_id', $id, PDO::PARAM_INT);
                $statement->execute();
            }
            
            //Redirect after edit.
            header("Location: index.php");
            exit; 
        }
    } 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="styles/edit.css">    
  </head>
<body>
    <?php include("title.php") ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <?= $error ?>
    <form action="?id=<?=$id ?>" method="post" class="row g-3" enctype="multipart/form-data">
        <div class="form-group">
            <label for="exampleFormControlInput1" class="form-label">Title</label>
            <input type="text" class="form-control" id="exampleFormControlInput1" name="title" value="<?= $blog["title"] ?>">
        </div>
        <div class="form-group">
            <label for="exampleFormControlTextarea1" class="form-label">Content</label>
            <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="content"><?= $blog["content"] ?></textarea>
        </div>
        <input type="file" name="imagefile">
        <br>
        <?php if($blogimage): ?>
            <img src="images/<?= $blogimage["filename"] ?>" alt="">
            <div>
                <input type="checkbox" name="deleteimage" id="delete">
                <label for="delete">Delete Image</label>
            </div>
        <?php endif ?>
        <div class="row">
            <div class="col-2">
                <input type="submit" name="command" value="Update" />
            </div>
            <div class="col-2">
                <input type="submit" name="command" value="Delete">
            </div>
        </div>
    </form>   
</body>
</html>