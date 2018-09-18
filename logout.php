<?php
	session_start();
	include("views/header.php");
	if(isset($_SESSION['email'])){

				
		session_destroy();
	}
	include("views/footer.php");
?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>

	<br><br><br>
	<div class="container">
		<div class="jumbotron">
		  <img src="images/goodbyeicon.jpg">
		  <p class="lead">Ευχαριστούμε που χρησιμοποιήσατε την ιστοσελίδα μας.</p>
		  <hr class="my-4">
		  <p>Σας περιμένουμε σύντομα ξανά κοντά μας.</p>
		  <p class="lead">
		    <a class="btn btn-primary btn-lg" href="index.php?logout=1" role="button">Συνδεθείτε Ξανά</a>
		  </p>
		</div>
	</div>	
</body>
</html>