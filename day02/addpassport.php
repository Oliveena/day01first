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

        <!--START OF PHP -->
        <?php

        require_once 'db.php';

        $passportNo = isset($_POST['passportNo']) ? $_POST['passportNo'] : '';  //defining passportNo variable
        $errorList = []; // defining empty error list array

        function printForm($passportNo = "")
        {
            $passportNo = htmlentities($passportNo);     //checking for valid html if <> are part of the name
            // heredoc string example below
            $form = <<< END
            <h2>File Upload Form</h2>
             <form enctype="multipart/form-data" method="POST">
                Passport number: 
                <input type="text" name="passportNo" value="{$passportNo}"><br><br>
                Upload passport image: <input type="file" name="uploadfile"><br><br>
                <input type="submit" name="submit" value="Add Passport"><br><br>
            </form>
    END;
            echo $form;
        }

        function validateUploadedFile(&$newFilePath, $passportNo)
        {
            $uploadfile = $_FILES['uploadfile'];
            // does the uploadFile exist? 
            if ($uploadfile['error'] != UPLOAD_ERR_OK) {
                return "An error occurred during photo upload." . $uploadfile['error'];
            }
            // is the uploadFile smaller than 2Mb? 
            if ($uploadfile['size'] > 2 * 1024 * 1024) {
                return "File too large. Please keep it under 2Mb.";
            }
            // is the uploadFile width, height between 100 and 1000px? 
            $info = getimagesize($uploadfile['tmp_name']);
            if ($info[0] < 100 || $info[0] > 1000 || $info[1] < 100 || $info[1] > 1000) {
                return "Width and height must be between 200-1000px range.";
            }
            // is the file extension acceptable? 
            $ext = "";
            switch ($info['mime']) {
                case 'image/jpeg':
                    $ext = "jpg";
                    break;
                case 'image/gif':
                    $ext = "gif";
                    break;
                case 'image/png':
                    $ext = "png";
                    break;
                default:
                    return "Only image files are allowed (JPG, GIF, PNG). Thanks.";
            }

            // naming the file path for the upload
            $newFilePath = 'uploads/' . $passportNo . "." . $ext;
            return null; // if all is valid
        }

        // validating if form was submitted && file uploaded
        if (isset($_POST['submit'])) {
            // validating passportNo
            if (empty($passportNo)) {
                $errorList['passportNo'] = "Passport number is required.";
            } elseif (preg_match('/^[A-Z]{2}[0-9]{6}$/', $passportNo) !== 1) {
                $errorList['passportNo'] = "Passport number must be composed of two uppercase letters followed by 6 digits exactly.";
                $passportNo = "";  // reset passportNo field
            } else {
                $passportNo = $_POST['passportNo'];
            }

            // validating uploaded file
            if (isset($_FILES['uploadfile']) && $_FILES['uploadfile']['error'] === 0) {
                // debugging
                //var_dump($_FILES['uploadfile']);
                // file values
                $fileTmpPath = $_FILES['uploadfile']['tmp_name'];
                $fileName = $_FILES['uploadfile']['name'];
                $fileSize = $_FILES['uploadfile']['size'];
                $fileType = $_FILES['uploadfile']['type'];
                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

                // call file validation function 
                $newFilePath = "";
                $fileError = validateUploadedFile($newFilePath, $passportNo);

                // if error
                if ($fileError) {
                    $errorList['photoFilePath'] = $fileError;
                }

                // if all looks good, server accepts file
                if (empty($errorList)) {
                    if (file_exists($newFilePath)) {
                        $errorList['photoFilePath'] = "The file already exists.";
                    } else {
                        if (move_uploaded_file($fileTmpPath, $newFilePath)) {
                            echo "<h3>Success! File uploaded.</h3>";
                        } else {
                            $errorList['photoFilePath'] = 'Error accepting your uploaded file';
                        }
                    }
                }
            } else {
                $errorList['photoFilePath'] = "No file was uploaded, or upload error.";
            }

            // inserting data into DB
            if (empty($errorList)) {
                $passportNo = mysqli_real_escape_string($conn, $passportNo);
                $newFilePath = mysqli_real_escape_string($conn, $newFilePath);

                // SQL INSERT query
                if (!empty($passportNo) && !empty($newFilePath)) {
                    $sql = "INSERT INTO passports (passportNo, photoFilePath) VALUES ('$passportNo', '$newFilePath')";
                    if (mysqli_query($conn, $sql)) {
                        echo "<h3>Your passport number and photo have been successfully uploaded to the database!</h3>";
                    } else {
                        echo 'Error: ' . mysqli_error($conn);
                    }
                }
            } else {
                foreach ($errorList as $error) {
                    echo "<p>$error</p>";
                }
            }
        }

        if (empty($passportNo)) {
            printForm();
        } else {
            printForm($passportNo);
        }

        mysqli_close($conn);
        ?>
        <!--END OF PHP-->
    </section>
    <!--Footer-->
    <footer class="section">
        <div class="center grey-text">Copyright 2025 Anastassia Tarassova</div>
    </footer>

</body>

</html>
