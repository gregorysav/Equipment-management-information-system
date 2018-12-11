<?php

	if (isset($_SESSION['id']) AND ($_SESSION['type'] == 0 OR $_SESSION['type'] == 1 )) {
		
	}else {
		header("Location: login.php");
		die("Δεν έχετε συνδεθεί");
	}
?>