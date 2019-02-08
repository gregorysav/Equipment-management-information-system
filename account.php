<?php
//Access: Registered Users
include("variables_file.php");
echo '
	<!DOCTYPE html>
	<html lang="en">
';
include("views/header.php");
include("views/navbar.php");
	
	echo '
		<div class="container personalInformation">
			<h2>Προσωπικά Στοιχεία Χρήστη</h2>
			<h4>Έχετε συνδεθεί ως: '.$fullName.'<hr></h4>
			<div class="jumbotron">
				<div class="prof-info">
				   <div class="info"><label> Όνομα Χρήστη:</label>  <span>'.$first_name.'</span></div>
						   <div class="info"><label> Επίθετο :</label>  <span>'.$last_name.'</span></div>
				   <div class="info"><label> ΑΕΜ :</label>  <span>'.$aem.'</span></div>
				   <div class="info"><label> Email :</label>  <span>'.$email.'</span></div>
				   <div class="info"><label> Τηλέφωνο Επικοινωνίας:</label>  <span>'.$telephone.'</span></div>
				   <div class="info"><label> Ειδικότητα :</label>  <span>'.$isA.'</span></div>
				</div>
			</div>	
		</div>
	';	
	

include("views/footer.php");
echo '
	</body>
	</html>
';
?>