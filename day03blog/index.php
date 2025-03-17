<?php 
include('db.php');
session_start();

//debugging
echo '<pre>';
var_dump($_SESSION); // This will show all session data
echo '</pre>';

//more debugging
//$_SESSION['name'] = 'TestTest';

// Query to get the last 5 articles
$sql = 'SELECT authorid, creationTime, title, body, id FROM articles ORDER BY creationTime DESC LIMIT 5';
$result = mysqli_query($conn, $sql);
$articles = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Free result and close connection
mysqli_free_result($result);
mysqli_close($conn);

// Check if user is logged in
$is_logged_in = isset($_SESSION['name']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Blog</title>
    <link rel="stylesheet" href="templates/styles.css">
</head>

<body>
    <?php include('templates/header.php'); ?>

    <div class="container">
        <h1>Welcome to my blog, read on!</h1>
<!--add an article, if logged in -->
        <?php if ($is_logged_in): ?>
            <div class="add-article-button-container">
                <a href="articleadd.php" class="large-button">Add a New Article</a>
            </div>
        <?php endif; ?>

        <!-- Articles Container -->
        <div class="articles-container">
            <?php foreach ($articles as $article): ?>
                <div class="article">
                    <h2><?php echo htmlspecialchars($article['title']); ?></h2>
                    <p><?php echo nl2br(htmlspecialchars(substr($article['body'], 0, 100))); ?>...</p>
                    <div class="article-action">
                        <a href="article.php?id=<?php echo $article['id']; ?>">More info</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php include('templates/footer.php'); ?>
</body>
</html>

