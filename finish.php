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

	 	$basketQuery = $db->prepare("SELECT * FROM basket_svds"); 
	 	$basketQuery->execute();
	 	while($basketQueryResult=$basketQuery->fetch(PDO::FETCH_ASSOC)){
	 			$equipNames[] = $basketQueryResult['name_basket'];
			 	 
	 	}


	 	if(isset($_POST['finish'])){

		 	$today= "";
			$today = date('Y-m-d');
			$start = date_create($today);
	 		$findEndDate = $_POST['endDate'];
	 		$end = date_create($findEndDate);
	 		$daysToEnd = date_diff($start,$end)->format('%a');
	 		$finishBorrow = $db->prepare("UPDATE borrow_svds  SET aem_borrow= :aem_borrow, start_date= :start_date, expire_date= :expire_date, isborrowed= :isborrowed, notify30= :notify30, notify20= :notify20, notify10= :notify10, confirmation_borrow= :confirmation_borrow WHERE isborrowed= $zero");
	 		$finishBorrow->bindParam(':aem_borrow', $userToBorrow);
	 		$finishBorrow->bindParam(':start_date', $_POST['startDate']);
		    $finishBorrow->bindParam(':expire_date', $_POST['endDate']);
		    $finishBorrow->bindParam(':isborrowed', $one);
		    $finishBorrow->bindParam(':notify30', $daysToEnd);
		    $finishBorrow->bindParam(':notify20', $daysToEnd);
		    $finishBorrow->bindParam(':notify10', $daysToEnd);
		    $finishBorrow->bindParam(':confirmation_borrow', $zero);
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

	 	$deleteQuery = "DELETE  FROM basket_svds";
		$deleteQuery_stmt = $db->prepare($deleteQuery);
		$deleteQuery_stmt->execute();

		    	echo '<a class="p-3 mb-2 bg-success text-white">Επιτυχείς καταχώρηση αποτελεσμάτων</a>';
		    	 	

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
				    <input type="date" id="startDate" value="<?php echo date('Y-m-d'); ?>"name="startDate" min="2000-01-02">
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

<?php
	include("views/footer.php");
?>