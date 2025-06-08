<?php
    define("DB_HOST", "localhost");
    define("DB_USER", "root");
    define("DB_PASS", "");
    define("DB_NAME", "ceylon_cuisine");

    // Establish a connection to the MySQL database using the defined variables.
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check if the connection was successful
    if (!$conn) {
        die("Connection Failed: " . mysqli_connect_error());
    }
?>