<?php
$filename = 'E:/xampp new/htdocs/web _Projects/ceylon-cuisine/php/profile.php';
echo "Checking syntax for: $filename\n";
if (file_exists($filename)) {
    $cmd = "php -l \"$filename\"";
    $output = shell_exec($cmd);
    echo $output;
} else {
    echo "File not found!";
}
?>
