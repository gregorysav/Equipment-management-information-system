<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "md";

$db = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password,
array(PDO::ATTR_PERSISTENT => false));

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// mysqli_set_charset($db,"utf8");
 $db -> exec("set names utf8");
?>