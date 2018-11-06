<?php
	session_start();
	include("views/connection.php");
	include("views/header.php");
	include("views/navbar.php");
	if ($_SESSION['email']){

			$idToChange = $_GET['id_borrow'];
			$userToBorrow = $_SESSION['id'];
			$zero = 0;
			
		$userQueryBorrow = $db->prepare("SELECT * FROM users_svds WHERE id = $userToBorrow"); 
	 		$userQueryBorrow->execute();
	 		while($userQueryBorrowResult=$userQueryBorrow->fetch(PDO::FETCH_ASSOC)){
	 		$userToBorrowName = $userQueryBorrowResult['first_name'];
	 		$userToBorrowLastName = $userQueryBorrowResult['last_name'];
	 	}

	 	$borrowQuery = $db->prepare("SELECT * FROM borrow_svds WHERE id_borrow = $idToChange"); 
	 	$borrowQuery->execute();
	 	while($borrowQueryResult=$borrowQuery->fetch(PDO::FETCH_ASSOC)){
	 			$idEquipToBorrow = $borrowQueryResult['id_equip_borrow'];	 	
			 	$equipBorrowQuery = $db->prepare("SELECT * FROM equip_svds WHERE id_equip = $idEquipToBorrow");
			 	$equipBorrowQuery->execute();
			 	while($equipBorrowQueryResult=$equipBorrowQuery->fetch(PDO::FETCH_ASSOC)){
			 		$equipName= $equipBorrowQueryResult['name_e'];
			 	} 
	 	}


	 	if(isset($_POST['finish'])){

	 		$today= "";
			$today = date('Y-m-d');
			$start = date_create($today);
	 		$findEndDate = $_POST['endDate'];
	 		$end = date_create($findEndDate);
	 		$daysToEnd = date_diff($start,$end)->format('%a');
	 		$finishBorrow = $db->prepare("UPDATE borrow_svds SET start_date= :start_date, expire_date= :expire_date, isborrowed= :isborrowed, notify30= :notify30, notify20= :notify20, notify10= :notify10, confirmation_borrow= :confirmation_borrow WHERE id_borrow= $idToChange");
	 		$finishBorrow->bindParam(':start_date', $_POST['startDate']);
		    $finishBorrow->bindParam(':expire_date', $_POST['endDate']);
		    $finishBorrow->bindParam(':isborrowed', $zero);
		    $finishBorrow->bindParam(':notify30', $daysToEnd);
		    $finishBorrow->bindParam(':notify20', $daysToEnd);
		    $finishBorrow->bindParam(':notify10', $daysToEnd);
		    $finishBorrow->bindParam(':confirmation_borrow', $zero);
		    $finishBorrow->execute();

		    	echo '<a class="p-3 mb-2 bg-success text-white">Επιτυχείς καταχώρηση αποτελεσμάτων</a>';
		    	header("Location: active.php");
		    	 	

	 	}

			 	if(isset($_POST['delete'])){
			 			$deleteQuery = "DELETE  FROM borrow_svds WHERE id_borrow=  $idToChange";
						$deleteQuery_stmt = $db->prepare($deleteQuery);
						$deleteQuery_stmt->bindParam(':id_borrow', $idToDelete, PDO::PARAM_INT);
						$deleteQuery_stmt->execute();	

						header("Location: active_borrows.php");
			 	}
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
		<h3>Γεια σου <?php echo $userToBorrowName; ?>, έχεις επιλέξει τα παρακάτω εξαρτήματα:  </h3>
		<div class="row">
		  <div class="col-md-6">
			<form method="post">
				 <h3>Τροποποίησε τον επιλεγμένο δανεισμό</h3>
				  <div class="form-group">
				    <label for="id_equip_borrow">Εξαρτήματα: </label>
				    <?php 
				    	echo'
				    <input type="text" class="form-control" id="id_equip_borrow" value="'.$equipName.'">
				    ';
				    ?>
				  </div>
				  <div class="form-group">
				    <label for="startDate">Ημερομηνία έναρξης: </label><br>
				    <input type="date" id="startDate" value="<?php  echo date('Y-m-d'); ?>" name="startDate" min="2000-01-02">
				  </div>
				  <div class="form-group">
				    <label for="endDate">Ημερομηνία λήξης: </label><br>
				    <input type="date" id="endDate "name="endDate" min="2000-01-02">
				  </div>	
				  <button type="submit" name="delete" class="btn btn-danger">Διαγραφή Δανεισμού</button>			  
				  <button type="submit" name="finish" class="btn btn-primary">Ολοκλήρωση Δανεισμού</button>
		  </div>
		<div class="col-md-6">
		  		  
			</form>
		  </div>	
		</div>	
	</div>

</body>
</html>

<?php
	include("views/footer.php");
?>