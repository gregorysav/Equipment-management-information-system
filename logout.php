<?php
	session_start();
	include("views/header.php");
	if(isset($_SESSION['email'])){
		echo '<p class="p-3 mb-2 bg-success text-white">Έχετε αποσυνδεθεί </p>';
		echo '<a href="index.php?logout=1">Επιστροφή στη σελίδα εισόδου</a>';		
		session_destroy();
	}
	include("views/footer.php");
?>