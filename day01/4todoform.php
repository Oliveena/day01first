<?php

// connect to database
$conn = mysqli_connect('localhost', 'day01', 'day01', 'day01');

// check the connection
if (!$conn) {
    echo 'Connection error' . mysqli_connect_error();
}

// retrieving existing todos from DB

// THREE STEPS: 
// 1) write query to get all todos
$sql = 'SELECT id, task, difficulty, isDone FROM todos ORDER BY id';

// 2) make query and get results 
$result = mysqli_query($conn, $sql);

// 3) fetch resulting rows as array
$todos = mysqli_fetch_all($result, MYSQLI_ASSOC);  // will make an associative array

// setting current todo as emptiness in the form
$task = $difficulty = $isDone = "";
$errors = array('task' => '', 'difficulty' => '');

// validating new todo
if (isset($_POST['submit'])) {  // was the form submitted?  $_POST is a *global*
    //check task
    if (empty($_POST['task'])) {
        $errors['task'] = "A task is required.";
    } else {
        $task = $_POST['task'];
        if (!preg_match('/^[a-zA-Z0-9\s\-_!@#%&*(),.?":{}|<>]{2,50}$/', $task)) {
            $errors['task'] = "Task must be between 2 and 50 characters long.";
            // debugging
        } else {
            echo 'regex validation successful, all good.';
        }
    }

    if (array_filter($errors)) { // no errors => returns false; error(s) => returns true)
        echo 'hey! there are errors in the form.';
    } else {
        $task = mysqli_real_escape_string($conn, $_POST['task']); //store the user email in DB
        $difficulty = mysqli_real_escape_string($conn, $_POST['difficulty']);
        $isDone = isset($_POST['isDone']) ? 1 : 0;
        //echo 'task added succesfully! good job <3';

        // create SQL insert: into these columns, insert these $values
        $sql = "INSERT INTO todos(task, difficulty, isDone) VALUES('$task', '$difficulty', '$isDone')";

        // save to DB and check (make query and get result)
        if (mysqli_query($conn, $sql)) {
            //succes
            header('Location: 4todoform.php');
        } else {
            //error
            echo 'query error: ' . mysqli_error($conn);
        }
    }
    // end of POST check

    // free the result from memory
    mysqli_free_result($result);

    // close the conenction 
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Tracker</title>
    <!-- Materialize 1.0.0 Compiled and minified CSS
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css"-->
</head>
<!--Header-->

<body class="grey lighten-4">


    <!--Body-->
    <h3 class="center">To Do Application</h3>

    <section class="container grey-text">
        <h4 class="center">Welcome! Please enter your todo.</h4>
        <form class="white" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">

            <!--Task text-->
            <legend>Task: </legend>
            <input type="text" name="task" value="<?php echo htmlspecialchars($task); ?>">
            <div class="red-text"><?php echo $errors['task']; ?></div>

            <!--Difficulty radio buttons-->
            <legend>Difficulty: </legend>
            <input type="radio" id="easy" name="difficulty" value="Easy" <?php echo ($difficulty === 'Easy') ? 'checked' : ''; ?>>
            <label for="easy">Easy</label><br>

            <input type="radio" id="medium" name="difficulty" value="Medium" <?php echo ($difficulty === 'Medium') ? 'checked' : ''; ?>>
            <label for="medium">Medium</label><br>

            <input type="radio" id="hard" name="difficulty" value="Hard" <?php echo ($difficulty === 'Hard') ? 'checked' : ''; ?>>
            <label for="hard">Hard</label><br>
            <!-- only one level of ddifficulty should be allowed -->
            <div class="red-text"><?php echo $errors['difficulty']; ?></div>

            <!--Is Done? checkbox-->
            <legend>Is done? </legend>
            <input type="checkbox" id="isDone" name="isDone" value="1" class="filled-in" <?php echo (isset($_POST['isDone']) && $_POST['isDone'] == 1) ? 'checked' : ''; ?>>
            <label for="isDone">Is Done?</label>

            <div class="center">
                <input type="submit" name="submit" value="Create Task" class="btn brand z-depth-0">
            </div>
        </form>
    </section>

    <!--Preexisting todos-->
    <div class="container">
        <div class="row">

            <?php foreach ($todos as $todo): ?>

                <div class="col s6 m3">
                    <div class="card z-depth-0">
                        <div class="card-content center">
                            <h4>Task: <?php echo htmlspecialchars($todo['task']); ?></h4>
                            <p>Difficulty: <?php echo htmlspecialchars($todo['difficulty']); ?></p>
                            <p>Is it done? <?php echo htmlspecialchars($todo['isDone']); ?></p>
                            <p>===================================================</p>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>

            <?php if (count($todos) >= 2) : ?>
                <h5>There are two or more todos.</h5>
            <?php else: ?>
                <h5>There are less than 2 todos. </h5>
            <?php endif; ?>
        </div>
    </div>

    <!--Footer-->
    <footer class="section">
        <div class="center grey-text">Copyright 2025 Anastassia Tarassova</div>
    </footer>

</body>

</html>