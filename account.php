<?php
	session_start();
	include("views/header.php");
	include("views/navbar.php");
	if ($_SESSION['email']){
		$id = $_SESSION['id'];
		$username = $_SESSION['email'];
		$aem = $_SESSION['aem'];
		$type = $_SESSION['type'];
		$last_name = $_SESSION['last_name'];
		$first_name = $_SESSION['first_name'];
		$email = $_SESSION['email'];
		$telephone = $_SESSION['telephone'];
		$type = $_SESSION['type'];
		$isA = "Σπουδαστής";
		if ($type == 1) {
			$isA = "Διδάσκοντας";
		}
	} else {
		header("Location: index.php");
	}
	include("views/footer.php");
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>

	
	<div class="container">
		<h1>Προφίλ Χρήστη</h1>
		<div class="jumbotron">
			<div class="prof-info">

			   <div class="info"><label><i class="fa fa-user"></i>Όνομα :</label>  <span><?php echo $first_name; ?></span></div>
					   <div class="info"><label><i class="fa fa-user"></i>Επίθετο :</label>  <span><?php echo $last_name; ?></span></div>
			   <div class="info"><label><i class="fa fa-calendar"></i>ΑΕΜ :</label>  <span><?php echo $aem; ?></span></div>
			   <div class="info"><label><i class="fa fa-male"></i>Email :</label>  <span><?php echo $email; ?></span></div>
			   <div class="info"><label><i class="fa fa-male"></i>Τηλέφωνο :</label>  <span><?php echo $telephone; ?></span></div>
			   <div class="info"><label><i class="fa fa-male"></i>Ειδικότητα :</label>  <span><?php echo $isA; ?></span></div>
			   
			</div>
		</div>	
	</div>


</body>
</html>