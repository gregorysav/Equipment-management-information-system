<?php
	session_start();
	include("views/connection.php");
	include("views/header.php");
	include("views/navbar.php");
	if ($_SESSION['email']){
		echo '
		<div class="container">
		<h2>Ενεργοί Δανεισμοί</h2>
		</div>';




	} else {
		header("Location: index.php");
	}
	
?>


<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>

	<div class="container">
		<div class="row">
			</div>
		    <div class="col-md-4">
		  		<h3>Δημιουργία δανεισμού</h3>
		  		<button type="submit" id="new_borrow" class="btn btn-primary">Νέος Δανεισμός</button>
		  	</div>
			<div class="col-md-8"><?php 
		  			$userToBorrow = $_SESSION['id'];
		$today = date('d-m-Y');
	    $start = date_create($today);
		echo '
			<div class="container">
 						<table class="table table-bordered">
						  <thead class="thead-dark">
						    <tr>
						      <th scope="col">Id Δανεισμού</th>
						      <th scope="col">Όνομα Εξαρτήματος</th>
						      <th scope="col">Ιδιοκτήτης</th>
						      <th scope="col">Μέρες που απομένουν</th>
						      <th scope="col">Ενέργεια</th>
						    </tr>
						  </thead>';
		$borrowQuery = $db->prepare("SELECT * FROM borrow_svds WHERE aem_borrow = $userToBorrow"); 
	 	$borrowQuery->execute();
	 	while($borrowQueryResult=$borrowQuery->fetch(PDO::FETCH_ASSOC)){
	 		$idEquipToBorrow = $borrowQueryResult['id_equip_borrow'];
	 		$endDate = $borrowQueryResult['expire_date'];
	 		$end = date_create($endDate);
	 		$days = date_diff($start,$end)->format('%a');
	 	$userQueryBorrow = $db->prepare("SELECT * FROM users_svds WHERE id = $userToBorrow"); 
	 		$userQueryBorrow->execute();
	 		while($userQueryBorrowResult=$userQueryBorrow->fetch(PDO::FETCH_ASSOC)){
	 		$userToBorrowName = $userQueryBorrowResult['first_name'];
	 		$userToBorrowLastName = $userQueryBorrowResult['last_name'];
	 	}

	 	$equipBorrowQuery = $db->prepare("SELECT * FROM equip_svds WHERE id_equip = $idEquipToBorrow");
		$equipBorrowQuery->execute();
		while($equipBorrowQueryResult=$equipBorrowQuery->fetch(PDO::FETCH_ASSOC)){
			 			$equipName = $equipBorrowQueryResult['name_e'];
			 			$equipOwnerName = $equipBorrowQueryResult['owner_name'];
			 			
		} 
		  					echo '
 						  <tbody>
					      <td>'.$borrowQueryResult['id_borrow'].' </td>
					      <td>'.$equipName.'</td>
					      <td>'.$equipOwnerName.'</td>
					      <td>'.$days.'</td>
					      <td><button type="submit" class="btn btn-danger deleteBorrow" ><a href=borrow_delete.php?id_borrow='.$borrowQueryResult['id_borrow'].'>Κατάργηση</a></button><br><button type="submit" class="btn btn-success manageBorrow" ><a href=borrow_change.php?id_borrow='.$borrowQueryResult['id_borrow'].'>Τροποποίηση</a></button></td></div></div>
					       ';

		}?>
			</div>		
		</div>		
	</div>			

</body>
</html>

<?php
	include("views/footer.php");
?>	