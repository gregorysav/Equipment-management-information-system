<?php
	session_start();
	include("views/connection.php");
	include("views/header.php");
	include("views/navbar.php");
	if ($_SESSION['email']){

			$userToBorrow = $_SESSION['id'];
			$zero = 0;
			$one = 1;
			$equipNames = array();
			
			
			$userQueryBorrow = $db->prepare("SELECT * FROM users_svds WHERE id = $userToBorrow"); 
	 		$userQueryBorrow->execute();
	 		while($userQueryBorrowResult=$userQueryBorrow->fetch(PDO::FETCH_ASSOC)){
	 		$userToBorrowName = $userQueryBorrowResult['first_name'];
	 		$userToBorrowLastName = $userQueryBorrowResult['last_name'];
	 	}

	 	$borrowQuery = $db->prepare("SELECT * FROM borrow_svds WHERE aem_borrow = $userToBorrow"); 
	 	$borrowQuery->execute();
	 	while($borrowQueryResult=$borrowQuery->fetch(PDO::FETCH_ASSOC)){
	 			$idEquipToBorrow = $borrowQueryResult['id_equip_borrow'];

			 	
			 	$equipBorrowQuery = $db->prepare("SELECT * FROM equip_svds WHERE id_equip = $idEquipToBorrow");
			 	$equipBorrowQuery->execute();
			 	while($equipBorrowQueryResult=$equipBorrowQuery->fetch(PDO::FETCH_ASSOC)){
			 			$equipNames[] = $equipBorrowQueryResult['name_e'];
			 	} 
	 	}


	 	if(isset($_POST['finish'])){

		 	$today= "";
			$today = date('Y-m-d');
			$start = date_create($today);
	 		$findEndDate = $_POST['endDate'];
	 		$end = date_create($findEndDate);
	 		$daysToEnd = date_diff($start,$end)->format('%a');
	 		$finishBorrow = $db->prepare("UPDATE borrow_svds SET start_date= :start_date, expire_date= :expire_date, isborrowed= :isborrowed, notify30= :notify30, notify20= :notify20, notify10= :notify10 WHERE aem_borrow= $userToBorrow");
	 		$finishBorrow->bindParam(':start_date', $_POST['startDate']);
		    $finishBorrow->bindParam(':expire_date', $_POST['endDate']);
		    $finishBorrow->bindParam(':isborrowed', $one);
		    $finishBorrow->bindParam(':notify30', $daysToEnd);
		    $finishBorrow->bindParam(':notify20', $daysToEnd);
		    $finishBorrow->bindParam(':notify10', $daysToEnd);
		    $finishBorrow->execute();

		    $borrowedItem = "";

		    $borrowItemQuery = $db->prepare("SELECT * FROM borrow_svds"); 
		 	$borrowItemQuery->execute();
	 	while($borrowItemQueryResult=$borrowItemQuery->fetch(PDO::FETCH_ASSOC)){
	 		$borrowedItem = $borrowItemQueryResult['id_equip_borrow'];

	 		$quantityQuery = $db->prepare("SELECT * FROM equip_svds WHERE id_equip= $borrowedItem"); 
		 	$quantityQuery->execute();
		 while($quantityQueryResult=$quantityQuery->fetch(PDO::FETCH_ASSOC)){
		 	 $itemQuantity= $quantityQueryResult['quantity'] -1;
		 	 $borrowState = 0;
		 	 if ($itemQuantity == 0){
		 	 	$borrowState = 1;
		 	 }

             $newQuantity = $db->prepare("UPDATE equip_svds SET quantity= :quantity, isborrowed= :isborrowed WHERE id_equip= $borrowedItem");
	 		 $newQuantity->bindParam(':quantity', $itemQuantity);
		     $newQuantity->bindParam(':isborrowed', $borrowState);
		     $newQuantity->execute();


		 	 
		 	}	

	 	}



		    // $emailTo = "icte@uowm.gr";
      //       $subject = "Αν δουλεύει το site";
      //       $message = "Ολα οκ";
      //       $headers = "From: .$userToBorrowName";
            
      //           if (mail($emailTo, $subject, $message, $headers)) {
                
      //                $successMessage = '<div class="alert alert-success">Το μήνυμά σας έχει σταλθεί επιτυχώς, θα επικοινωνήσουμε μαζί σας σύντομα!</div>';
      //            }

		    	echo '<a class="p-3 mb-2 bg-success text-white">Επιτυχείς καταχώρηση αποτελεσμάτων</a>';
		    	 	

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
		<h3>Γεια σου <?php echo $userToBorrowName; ?>, έχεις επιλέξει τα παρακάτω εξαρτήματα:  </h3>
		<div class="row">
		  <div class="col-md-6">
			<form method="post">
				  <h3>Συμπλώσε τις προβλεπόμενες ημερομηνίες</h3>
				  <div class="form-group">
				    <label for="id_equip_borrow">Εξαρτήματα: </label>
				    <?php foreach ($equipNames as $row) {
				    	echo'
				    <input type="text" class="form-control" id="id_equip_borrow" value="'.$row.'">
				    ';
				    } ?>
				  </div>
				  <div class="form-group">
				    <label for="startDate">Ημερομηνία έναρξης: </label><br>
				    <input type="date" id="startDate "name="startDate" min="2000-01-02">
				  </div>
				  <div class="form-group">
				    <label for="endDate">Ημερομηνία λήξης: </label><br>
				    <input type="date" id="endDate "name="endDate" min="2000-01-02">
				  </div>				  
				  <button type="submit" name="finish" class="btn btn-primary">Ολοκλήρωση Δανεισμού</button>
		  </div>
		<div class="col-md-6">
		  		  
			</form>
		  </div>	
		</div>	
	</div>

</body>
</html>
