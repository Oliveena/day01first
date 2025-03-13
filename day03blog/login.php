<?php 

session_start();

// debugging
echo '<pre>';
var_dump($_SESSION); 
echo '</pre>';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
</head>

<body>

<?php include('templates/header.php'); ?>

    <h1>User Login</h1>
    <h2>Welcome!</h2>
    <section>

        <!--Start of PHP-->
        <?php
        require_once 'db.php';

        $email = isset($_POST['email']) ? $_POST['email'] : '';  //defining email variable
        $password = isset($_POST['password']) ? $_POST['password'] : '';  //defining password variable
        $errorList = []; // defining empty error list array

        function printForm($email = "", $password = "")
        {
            $email = htmlentities($email);     //checking for valid html if <> are part of the name
            $password = htmlentities($password);

            $form = <<< END
            <form enctype="multipart/form-data" method="POST">
            Email: 
            <input type="text" name="email" value="{$email}"><br><br>
            Password: 
            <input type="text" name="password" value="{$password}">
            <input type="submit" name="submit" value="Log In">
            <button><a href="registration.php">First timer? Registration is over here.</a></button>
            <button><a href="forgot_password.php">Forgot password?</a></button>
            </form>
END;
            /*TODO: create 'forgot_password.php' */
            echo $form;
        }

        if (!isset($_POST['submit'])) {
            // visible until submission
            printForm($email, $password);
        } else {
            if (empty($email)) {
                $errorList[] = "Email is required.";
            } elseif (preg_match('/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z]{2,})$/i', $email) !== 1) {
                $errorList['email'] = "Email seems incorrect. Please try again.";
                $email = "";  // reset email field
            } else {
                $email = $_POST['email'];
            }

            if (empty($password)) {
                $errorList[] = "Password is required.";
            }

            if (empty($errorList)) {
                //SQL query to check if email exists 
                $email = mysqli_real_escape_string($conn, $email);
                $sql = "SELECT * FROM users WHERE email = '$email'";
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) == 1) {
                    // if email exists in DB, fetch user data
                    $user = mysqli_fetch_assoc($result);

                    // comparing entered password with hashed password in DB
                    if (password_verify($password, $user['password'])) {
                        $_SESSION['name'] = $user['name'];
                        $_SESSION['id'] = $user['id'];

                        //debugging
                        var_dump($_SESSION); 

                        echo "<p>Login Successful! Redirecting...</p>";
                        header("Location: index.php");
                        exit();
                    } else {
                        $errorList[] = "Invalid password.";
                    }
                } else {
                    $errorList[] = "Email not found.";
                }
            }

            if (!empty($errorList)) {
                foreach ($errorList as $error) {
                    echo "<p>{$error}</p>";
                }
                // display again if errors
                printForm($email, $password);
            }
        }

        mysqli_close($conn);
        ?>
        <!--End of PHP-->

    </section>
    
    <?php include('templates/footer.php'); ?>
</body>

</html>