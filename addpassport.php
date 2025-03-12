<?php

// connect to database
$conn = mysqli_connect('localhost', 'day01', 'day01', 'day01');

// check the connection
if (!$conn) {
    echo 'Connection error' . mysqli_connect_error();
}

// setting current todo as emptiness in the form
$passportNo = $photoFilePath = "";
$errors = array('passportNo' => '', 'photoFilePath' => '');

// validating of form was submitted && file uploaded
if (isset($_POST['submit'])) {
    // validating passportNo
    if (empty($_POST['passportNo'])) {
        $errors['passportNo'] = "Passport number is required.";
    } else {
        $passportNo = $_POST['passportNo'];
        /////add passport nomenclature validation: Field passportNo must be composed of two uppercase letters followed by 6 digits exactly. 
    }

    // uploading the file
    if (isset($_FILES['uploadfile']) && $_FILES['uploadfile']['error'] === 0) {
        // file values
        $fileTmpPath = $_FILES['uploadfile']['tmp_name'];
        $fileName = $_FILES['uploadfile']['name'];
        $fileSize = $_FILES['uploadfile']['size'];
        $fileType = $_FILES['uploadfile']['type'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

        // validating file types
        $allowedTypes = array('jpg', 'gif', 'png');
        if (!in_array($fileExtension, $allowedTypes)) {
            $errors['photoFilePath'] = "Only image files are allowed (JPG, GIF, PNG).";
        }

        ////// validate their width and height must be within 200-1000 pixels range

        // validating file size
        if ($fileSize > 2 * 1024 * 1024) {
            $errors['photoFilePath'] = "File size must be lower than 2MB.";
        }

        // store file in 'uploads' directory
        if (empty($errors)) {
            $uploadDir = 'uploads/'; 
            $destPath = $uploadDir . basename($fileName);

            ////// The file names will be constructed of passportNo and the extension will match the file type uploaded. E.g. AB12345678.jpg

            // Check if file already exists
            if (file_exists($destPath)) {
                $errors['photoFilePath'] = "The file already exists.";
            } else {
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $photoFilePath = $destPath;
                    echo "<h3>File Successfully Uploaded!</h3>";
                } else {
                    $errors['photoFilePath'] = "Error moving the uploaded file.";
                }
            }
        }
    } else {
        $errors['photoFilePath'] = "No file was uploaded or there was an upload error.";
    }

    // If there are no errors, you can insert the data into the database (optional)
    if (empty($errors)) {
        $passportNo = mysqli_real_escape_string($conn, $_POST['passportNo']);
        $photoFilePath = mysqli_real_escape_string($conn, $photoFilePath);

        // Insert into database (adjust the table and columns as per your database structure)
        $sql = "INSERT INTO passports (passport_no, photo_path) VALUES ('$passportNo', '$photoFilePath')";
        if (mysqli_query($conn, $sql)) {
            echo "<h3>Passport and photo information successfully added to the database.</h3>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}

// Close database connection
mysqli_close($conn);
?>
<!-- 
// validation, submission, closure 

   echo "<b>File to be uploaded: </b>" . $_FILES["uploadfile"]["name"] . "<br>";
   echo "<b>Type: </b>" . $_FILES["uploadfile"]["type"] . "<br>";
   echo "<b>File Size: </b>" . $_FILES["uploadfile"]["size"]/1024 . "<br>";
   echo "<b>Store in: </b>" . $_FILES["uploadfile"]["tmp_name"] . "<br>";

   if (file_exists($_FILES["uploadfile"]["name"])){
      echo "<h3>The file already exists</h3>";
   } else {
      move_uploaded_file($_FILES["uploadfile"]["tmp_name"], $_FILES["uploadfile"]["name"]);
      echo "<h3>File Successfully Uploaded</h3>";
   }

?> -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passport Submission App</title>
</head>

<body>
    <h2>Passport Submission App</h2>

    <section>
        <p4>Welcome! Please upload your passport information.</p4>
        <form class="white" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">

            <!--PassportNo-->
            <legend>Passport number: </legend>
            <input type="text" name="passportNo" value="<?php echo htmlspecialchars($passportNo); ?>">
            <div class="red-text"><?php echo $errors['passportNo']; ?></div>

            <!--Upload passport image-->
            <legend>Upload passport image here: </legend>
            <h2>File Upload Form</h2>
            <form method="POST" action="addpassport.php" enctype="multipart/form-data">
                <label for="file">File name:</label>
                <input type="file" name="uploadfile" />
                <input type="submit" name="submit" value="Upload" />
            </form>

            <div class="center">
                <input type="submit" name="submit" value="Create Task" class="btn brand z-depth-0">
            </div>
        </form>

    </section>
    <!--Footer-->
    <footer class="section">
        <div class="center grey-text">Copyright 2025 Anastassia Tarassova</div>
    </footer>

</body>

</html>