<?php
	session_start();
	include("views/header.php");
	include("views/navbar.php");
	if ($_SESSION['email']){
		echo '
			<h2>Σελίδα Διαχείρισης Εξαρτημάτων</h2>
		    <button type="submit" id="add_equipment" class="btn btn-success btn-info">Προσθήκη Εξαρτήματος</button>
	        <button type="submit" id="modify_equipment"class="btn btn-primary btn-danger">Διαγραφή / Τροποποίηση Εξαρτήματος</button>
	
		';
	} else {
		header("Location: index.php");
	}
	include("views/footer.php");
?>