<?php
include("variables_file.php");
include("views/header.php");
include("views/navbar.php");
	if ($_SESSION['email']){
		
	} else {
		header("Location: index.php");
	}
echo '
	<div class="container">
		<h1>Προφίλ Χρήστη</h1>
		<div class="jumbotron">
			<div class="prof-info">
			   <div class="info"><label> Όνομα :</label>  <span>'.$first_name.'</span></div>
					   <div class="info"><label> Επίθετο :</label>  <span>'.$last_name.'</span></div>
			   <div class="info"><label> ΑΕΜ :</label>  <span>'.$aem.'</span></div>
			   <div class="info"><label> Email :</label>  <span>'.$email.'</span></div>
			   <div class="info"><label> Τηλέφωνο :</label>  <span>'.$telephone.'</span></div>
			   <div class="info"><label> Ειδικότητα :</label>  <span>'.$isA.'</span></div>
			</div>
		</div>	
	</div>

';

include("views/footer.php");
?>