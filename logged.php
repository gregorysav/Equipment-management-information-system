<?php
	session_start();
	include("header.php");
	if ($_SESSION['email']){
		echo '<p class="p-3 mb-2 bg-success text-white">You are successfully logged in as ' .$_SESSION['email']. '.</p>';
	} else {
		header("Location: index.php");
	}

	echo '<button id="logout" class="btn btn-primary">Log Out</button>';

	include("footer.php");
?>