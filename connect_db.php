<?php
$conn = @mysqli_connect('localhost', 'root', '','bus-tracking') or die ('connot connect to host.');

@mysqli_query($conn, "SET NAMES 'utf8'");
@mysqli_query($conn, "SET CHARACTER SET 'utf8'");

date_default_timezone_set("Asia/Manila");
?>