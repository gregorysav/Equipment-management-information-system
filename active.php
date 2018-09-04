<?php
	session_start();
	include("views/header.php");
	include("views/navbar.php");
	if ($_SESSION['email']){
		echo 'Ενεργοί Δανεισμοί'.'<br>';
		echo '
			 <button type="submit" id="active_borrows" class="btn btn-primary">Ενεργοί Δανεισμοί</button>
		';

		echo '
			 <button type="submit" id="new_borrow" class="btn btn-primary">Νέος Δανεισμός</button>
		';





	} else {
		header("Location: index.php");
	}
	include("views/footer.php");
?>