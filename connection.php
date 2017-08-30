<?php
$servername = "shareddb1d.hosting.stackcp.net";
$username = "neavasi-323163b7";
$password = "6T7MVdjAtoh2";
$dbname = "neavasi-323163b7";
$db = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password,
array(PDO::ATTR_PERSISTENT => false));
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>