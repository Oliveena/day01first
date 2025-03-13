<?php

// connect to database
$conn = mysqli_connect('localhost', 'day03', '-Uq_CQMeF9QPe9P', 'day03');

// check the connection
if (!$conn) {
    echo 'Connection error' . mysqli_connect_error();
}

?>