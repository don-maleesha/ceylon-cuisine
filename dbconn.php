<?php

    $servername = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'ceylon-cuisine';

    // Establish a connection to the MySQL database using the defined variables.
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check if the connection was successful.
    if (!$conn) {
        
        die ("Connection Failed" . mysqli_connect_error());

    }

?>