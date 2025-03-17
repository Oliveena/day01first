<?php
require_once 'db.php';

session_start();
// debugging
var_dump($_SESSION);

if (!isset($_GET['id'])) {
    echo "Error: Article ID not provided.";
    exit();
}

$articleId = $_GET['id'];

// get article from DB
$sql = "SELECT a.*, u.name AS author_name FROM articles a JOIN users u ON a.authorId = u.id WHERE a.id = '$articleId'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "Error: Article not found.";
    exit();
}

$article = mysqli_fetch_assoc($result);

// Fetching comments initially for display
$commentsSql = "SELECT c.*, u.name AS commenter_name FROM comments c JOIN users u ON c.authorId = u.id WHERE c.articleId = '$articleId' ORDER BY c.creationTime DESC";
$commentsResult = mysqli_query($conn, $commentsSql);
$comments = mysqli_fetch_all($commentsResult, MYSQLI_ASSOC);

mysqli_free_result($result);
mysqli_free_result($commentsResult);

// Check if user is logged in
$is_logged_in = isset($_SESSION['name']);

// Check if the user is logged in
if (!isset($_SESSION['name'])) {
    echo "<p style='color: red;'>You must be logged in to create an article!</p>";
    echo '<button><a href="login.php">Already got an account? Log in.</a></button>';
    echo '<button><a href="registration.php">First timer? Register here.</a></button>';
    exit(); // Prevent form from loading if no active session
}

$title = isset($_POST['title']) ? $_POST['title'] : '';
$body = isset($_POST['body']) ? $_POST['body'] : '';
$errorList = [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?></title>
</head>
<body>
    <?php include('templates/header.php'); ?>

    <h1><?php echo htmlspecialchars($article['title']); ?></h1>
    <p>Posted by <?php echo htmlspecialchars($article['author_name']); ?> on <?php echo htmlspecialchars($article['creationTime']); ?></p>

    <div class="article-body">
        <p><?php echo nl2br(htmlspecialchars($article['body'])); ?></p>
    </div>

    <div>
        <h3>Comments</h3>
        <?php if (empty($comments)): ?>
            <p>No comments so far. Be the first one!</p>
        <?php else: ?>
            <?php foreach ($comments as $comment): ?>
                <div>
                    <p><strong><?php echo htmlspecialchars($comment['commenter_name']); ?>:</strong> <?php echo nl2br(htmlspecialchars($comment['body'])); ?></p>
                    <p><em>Posted on <?php echo htmlspecialchars($comment['creationTime']); ?></em></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php
    function printForm($title = "", $body = "")
    {
        $title = htmlentities($title);
        $body = htmlentities($body);

        $form = <<<END
        <form method="POST">
            <h3>Leave a comment</h3>
            <h4>Comment title</h4>
            <input type="text" name="title" value="{$title}" placeholder="Enter comment title">
            <textarea name="body" rows="10" cols="50" placeholder="Write your comment here">{$body}</textarea>
            <input type="submit" name="submit" value="Add comment">
            <section class="comments">
            </section>
            <br><br>
        </form>
END;

        echo $form;
    }

    if (!isset($_POST['submit'])) {
        // visible until submission
        printForm($body);
    } else {
        // validate comment
        if (empty($body) || empty($title)) {
            $errorList[] = "Comment requires a title and a body of text.";
        } elseif (strlen($body) < 5) {
            $errorList[] = "Comment must be at least 10 characters long.";
        }

        // if no errors, move on to insertion into DB
        if (empty($errorList)) {

            $authorId = $_SESSION['id'];

            // does authorId exist in users table? 
            $sql = "SELECT * FROM users WHERE id = '$authorId'";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) == 0) {
                echo "Error: Invalid user ID.";
                exit();
            }

            $body = mysqli_real_escape_string($conn, $body);

            // current timestamp 
            $creationTime = date("Y-m-d H:i:s");

            // SQL INSERT query
            $sql = "INSERT INTO comments (articleId, authorId, creationTime, body) VALUES ('$articleId', '$authorId', '$creationTime', '$body')";

            if (mysqli_query($conn, $sql)) {
                // After inserting the comment, fetch updated comments
                $commentsSql = "SELECT c.*, u.name AS commenter_name FROM comments c JOIN users u ON c.authorId = u.id WHERE c.articleId = '$articleId' ORDER BY c.creationTime DESC";
                $commentsResult = mysqli_query($conn, $commentsSql);
                $comments = mysqli_fetch_all($commentsResult, MYSQLI_ASSOC);

                // Display success message and re-display comments
                echo "<h3>Your comment has been successfully posted!</h3>";

                // Display all comments including the new one
                echo "<h3>Comments</h3>";
                if (empty($comments)) {
                    echo "<p>No comments so far. Be the first one!</p>";
                } else {
                    foreach ($comments as $comment) {
                        echo "<div>";
                        echo "<p><strong>" . htmlspecialchars($comment['commenter_name']) . ":</strong> " . nl2br(htmlspecialchars($comment['body'])) . "</p>";
                        echo "<p><em>Posted on " . htmlspecialchars($comment['creationTime']) . "</em></p>";
                        echo "</div><br>";
                    }
                }
            } else {
                echo 'Error: ' . mysqli_error($conn);
            }
        }

        // Display errors
        if (!empty($errorList)) {
            foreach ($errorList as $error) {
                echo "<p style='color: red;'>{$error}</p>";
            }
        }

        // Display the form again with user input
        printForm($body);
    }

    include('templates/footer.php');

    mysqli_close($conn);
    ?>
</body>
</html>
