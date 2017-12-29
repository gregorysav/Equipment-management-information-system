<?php
	session_start();
	include("header.php");
	include("navbar.php");
	if ($_SESSION['email']){
		echo '
			<h2>You can Manage your equipment here</h2>
		    <button type="submit" id="add_equipment" class="btn btn-success btn-info">Add Equipment</button>
	        <button type="submit" id="modify_equipment"class="btn btn-primary btn-danger">Delete / Modify Equipment</button>
	
		';
	} else {
		header("Location: index.php");
	}
	include("footer.php");
?>