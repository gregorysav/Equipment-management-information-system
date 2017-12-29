<?php
	session_start();
	include("header.php");
	include("navbar.php");
	if ($_SESSION['email']){
		echo '
		<div class="container">
		<p >Username: ' .$_SESSION['email']. '</p>
		<p> Password: ' .$_SESSION['password']. '</p>
		</div>';
	} else {
		header("Location: index.php");
	}
	include("footer.php");
?>