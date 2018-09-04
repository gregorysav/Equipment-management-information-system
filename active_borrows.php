<?php
	session_start();
	include("views/connection.php");
	include("views/header.php");
	include("views/navbar.php");
	if ($_SESSION['email']){

		echo "here are your active borrows";








} else {
		header("Location: index.php");
	}
	include("views/footer.php");
?>		