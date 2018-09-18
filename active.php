<?php
	session_start();
	include("views/header.php");
	include("views/navbar.php");
	if ($_SESSION['email']){
		echo '
			<div class="container">
			<h2>Ενεργοί Δανεισμοί</h2>
			<button type="submit" id="active_borrows" class="btn btn-primary">Ενεργοί Δανεισμοί</button>
			<button type="submit" id="new_borrow" class="btn btn-primary">Νέος Δανεισμός</button> 
			</div>';





	} else {
		header("Location: index.php");
	}
	include("views/footer.php");
?>