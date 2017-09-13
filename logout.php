<?php
	session_start();
	include("header.php");
	if(isset($_SESSION['email'])){
		echo '<p class="p-3 mb-2 bg-success text-white">You are successfully logged out </p>';
		echo '<a href="index.php?logout=1">Back to Log In</a>';		
		session_destroy();
	}
	include("footer.php");
?>