<?php
	session_start();
	include("header.php");
	include("navbar.php");
	if ($_SESSION['email']){
		echo 'Active Borrows';
	} else {
		header("Location: index.php");
	}
	include("footer.php");
?>