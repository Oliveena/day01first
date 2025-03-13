<?php
require_once 'db.php';

session_start();
// Check if the user is logged in
if (!isset($_SESSION['userId'])) {
    echo "<p style='color: red;'>You must be logged in to create an article!</p>";
    echo '<button><a href="login.php">Already got an account? Log in.</a></button>';
    echo '<button><a href="registration.php">First timer? Register here.</a></button>';
    exit(); // Prevent form from loading if no active session
}

$title = isset($_POST['title']) ? $_POST['title'] : '';
$body = isset($_POST['body']) ? $_POST['body'] : '';
$errorList = [];

function printForm($title = "", $body = "")
{
    $title = htmlentities($title);
    $body = htmlentities($body);

    $form = <<< END
        <form method="POST">
        <h3>Title</h3>
        <input type="text" name="title" value="{$title}" placeholder="Enter article title">

        <h3>Content</h3>
        <textarea name="body" rows="10" cols="50" placeholder="Write your article content here">{$body}</textarea>

        <br><br>
        <input type="submit" name="submit" value="Create">
        </form>
END;

    echo $form;
}

if (!isset($_POST['submit'])) {
    // visible until submission
    printForm($title, $body);
} else {
    // validate title
    if (empty($title)) {
        $errorList[] = "Title is required.";
    } elseif (strlen($title) < 10) {
        $errorList[] = "Article title must be at least 10 characters long.";
    }

    // validate body
    if (empty($body)) {
        $errorList[] = "Content is required.";
    } elseif (strlen($body) < 50) {
        $errorList[] = "Article content must be at least 50 characters long.";
    }

    // if no errors, move on to insertion into DB
    if (empty($errorList)) {
        session_start();  // start session to access logged-in user's ID

        $title = mysqli_real_escape_string($conn, $title);
        $body = mysqli_real_escape_string($conn, $body);

        // current timestamp 
        $creationTime = date("Y-m-d H:i:s");

        // SQL INSERT query
        $sql = "INSERT INTO articles (authorId, creationTime, title, body) VALUES ('$authorId', '$creationTime', '$title', '$body')";
        if (mysqli_query($conn, $sql)) {
            echo "<h3>Your article has been successfully created!</h3><br><button><a href=\"view_article.php?id=" . mysqli_insert_id($conn) . "\">View Article</a></button>";
            exit();
        } else {
            echo 'Error: ' . mysqli_error($conn);
        }
    }

    // display errors
    if (!empty($errorList)) {
        foreach ($errorList as $error) {
            echo "<p style='color: red;'>{$error}</p>";
        }
    }

    // display the form again with user input
    printForm($title, $body);
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Article</title>
</head>

<body>

    <?php include('templates/header.php'); ?>

    <h1>Create New Article</h1>

    <?php

    ?>

    <?php include('templates/footer.php'); ?>

</body>

</html>