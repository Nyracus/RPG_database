<?php
$conn = mysqli_connect("localhost", "root", "", "rpg");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
