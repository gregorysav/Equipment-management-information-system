<?php
//Access: Public/Everyone
echo '
	<!DOCTYPE html>
	<html lang="en">
';
include("views/header.php");
	if(isset($_SESSION)){
		session_unset();
		session_destroy();
	}

echo '
	<br><br><br>
	<div class="container">
		<div class="jumbotron">
			<img src="images/goodbyeicon.jpg" alt="">
			<div class="row">
				<div class="col-md-8">
				  	<p class="lead">Ευχαριστούμε που χρησιμοποιήσατε την ιστοσελίδα μας.</p>
				  	<hr class="my-4">
				  	<p>Σας περιμένουμε σύντομα ξανά κοντά μας.</p>
				  	<p class="lead">
				    <a class="btn btn-primary btn-lg" href="index.php?logout=1" role="button">Συνδεθείτε Ξανά</a>
				  	</p>
				</div>  	
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