<?php

// connect to database
$conn = mysqli_connect('localhost', 'day01', 'day01', 'day01');

// check the connection
if (!$conn) {
    echo 'Connection error' . mysqli_connect_error();
}

?>