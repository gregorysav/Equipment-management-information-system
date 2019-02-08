<?php
//Access: Registered Users
	if (isset($_SESSION['id']) AND ($_SESSION['type'] == 0 OR $_SESSION['type'] == 1 OR $_SESSION['type'] == 2 OR $_SESSION['type'] == 3 )) {
		
	}else {
		header("Location: login.php");
		die("Δεν έχετε συνδεθεί");
	}
?>