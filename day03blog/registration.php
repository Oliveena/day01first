<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
</head>

<body>

<?php include('templates/header.php'); ?>

    <h1>New User Registration</h1>

    <!--Start of PHP-->
    <?php
    require_once 'db.php';

    $username = isset($_POST['username']) ? $_POST['username'] : '';  //defining username variable
    $email = isset($_POST['email']) ? $_POST['email'] : '';  //defining email variable
    $password1 = isset($_POST['password1']) ? $_POST['password1'] : '';  //defining password1 variable
    $password2 = isset($_POST['password2']) ? $_POST['password2'] : '';  //defining password1 variable
    $errorList = []; // defining empty error list array

    function printForm($username = "", $email = "", $password1 = "", $password2 = "")
    {
        $username = htmlentities($username); //checking for valid html if <> are part of the name
        $email = htmlentities($email);
        $password1 = htmlentities($password1);
        $password2 = htmlentities($password2);

        //TODO: switch out 'text' to 'password' for passwords
        $form = <<< END
            <form enctype="multipart/form-data" method="POST">
            Select a username: 
            <input type="text" name="username" value="{$username}">
            Enter your email: 
            <input type="text" name="email" value="{$email}">
            Choose a password: 
            <input type="text" name="password1" value="{$password1}">
            Confirm your password: 
            <input type="text" name="password2" value="{$password2}">
            <input type="submit" name="submit" value="Register">
            <button><a href="login.php">Already got an account? Log in.</a></button>
            <button><a href="forgot_password.php">Forgot password?</a></button>
            </form>
END;
        echo $form;
    }

    if (!isset($_POST['submit'])) {
        // visible until submission
        printForm($username, $email, $password1, $password2);
    } else {
        // validate username
        if (empty($username)) {
            $errorList[] = "Username is required.";
        } elseif (preg_match('/^[a-z0-9]{4,20}$/i', $username) !== 1) {
            $errorList[] = "Username may be between 4 to 20 characters, and contain lowercase letters and numbers.";
        }
    
        // validate email
        if (empty($email)) {
            $errorList[] = "Email is required.";
        } elseif (preg_match('/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z]{2,})$/i', $email) !== 1) {
            $errorList[] = "Email seems incorrect. Please try again.";
        }
    
        // validate passwords
        if (empty($password1) || empty($password2)) {
            $errorList[] = "Both passwords are required.";
            //edited the 0-9 to d in regex because the 0 was messing up the logic
        } elseif (preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=<>?\/,\.]).{6,100}$/', $password1) !== 1) {
            // debugging
            //var_dump($password1);
            $errorList[] = "Password must be at least 6 characters long and must contain at least one uppercase letter, one lowercase letter, and one number or special character.";
        } elseif ($password1 !== $password2) {
            $errorList[] = "Both passwords must be identical!";
        }
    
        // if no errors, move on to insertion into DB
        if (empty($errorList)) {
            // check if username exists
            $query = "SELECT * FROM users WHERE name = '$username'";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                $errorList[] = "This username is already taken.";
            } else {
                $username = mysqli_real_escape_string($conn, $username);
                $email = mysqli_real_escape_string($conn, $email);
                $password1 = mysqli_real_escape_string($conn, $password1);
    
                // hashing the password
                $password1 = password_hash($password1, PASSWORD_BCRYPT);

                //var_dump($username, $email, $password1, $password2);
                //var_dump($errorList);
    
                // SQL INSERT query
                $sql = "INSERT INTO users (name, email, password) VALUES ('$username', '$email', '$password1')";
                if (mysqli_query($conn, $sql)) {
                    echo "<h3>Your account has been successfully registered!</h3><br><h4>Please proceed to log in.</h4><br><button><a href=\"login.php\">Log in</a></button>";
                    exit();
                } else {
                    echo 'Error: ' . mysqli_error($conn);
                }
            }
        }
    
        // display errors
        if (!empty($errorList)) {
            foreach ($errorList as $error) {
                echo "<p style='color: red;'>{$error}</p>";
            }
        }
    
        // display the form again with user input
        printForm($username, $email, $password1, $password2);
    }
    
    mysqli_close($conn);
    ?>
    <!--end of PHP-->

    <?php include('templates/footer.php'); ?>

</body>

</html>