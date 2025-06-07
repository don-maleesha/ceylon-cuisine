<?php
$password = "12345678";  // change this to your desired password
$hashed = password_hash($password, PASSWORD_DEFAULT);
echo "Hashed password: " . $hashed;
?>
