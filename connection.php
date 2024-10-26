<?php
$con  = mysqli_connect('localhost','root','','vehicle');
if(mysqli_connect_errno())
{
    echo 'Database Connection Error';
}

// Enhanced UTF-8 setup
mysqli_set_charset($con, "utf8mb4");
mysqli_query($con, "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
mysqli_query($con, "SET CHARACTER SET utf8mb4");
mysqli_query($con, "SET collation_connection = utf8mb4_unicode_ci");




