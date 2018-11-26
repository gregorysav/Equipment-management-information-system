<?php
include("variables_file.php");
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");
	if ($_SESSION['email']){

		$userToBorrow = $id;

	 	$basketQuerySQL = "SELECT * FROM basket_svds WHERE id_user_basket= :idUser"; 
	 	$basketQuerySTMT =$db->prepare($basketQuerySQL);
	 	$basketQuerySTMT->bindParam(':idUser', $userToBorrow, PDO::PARAM_INT);
	 	$basketQuerySTMT->execute();
	 	while($basketQuerySTMTResult=$basketQuerySTMT->fetch(PDO::FETCH_ASSOC)){
	 		$equipNames[] = $basketQuerySTMTResult['name_basket'];
			 	 
	 	}


	 	if(isset($_POST['finish'])){

	 		if (isset($_POST['aemBorrow'])){
	 			$findEndDate = $_POST['endDate'];
		 		$endDate = date_create($findEndDate);
		 		$daysToEnd = date_diff($startToday,$endDate)->format('%a');
		 		$finishBorrowSQL = "UPDATE borrow_svds  SET aem_borrow= :aem_borrow, start_date= :start_date, expire_date= :expire_date, isborrowed= :isborrowed, notify30= :notify30, notify20= :notify20, notify10= :notify10, confirmation_borrow= :confirmation_borrow WHERE isborrowed= :zero";
		 		$finishBorrowSTMT = $db->prepare($finishBorrowSQL);
		 		$finishBorrowSTMT->bindParam(':zero', $zero, PDO::PARAM_INT);
		 		$finishBorrowSTMT->bindParam(':aem_borrow', $_POST['aemBorrow']);
		 		$finishBorrowSTMT->bindParam(':start_date', $_POST['startDate']);
				$finishBorrowSTMT->bindParam(':expire_date', $_POST['endDate']);
		 		$finishBorrowSTMT->bindParam(':isborrowed', $one);
		 		$finishBorrowSTMT->bindParam(':notify30', $daysToEnd);
		 		$finishBorrowSTMT->bindParam(':notify20', $daysToEnd);
		 		$finishBorrowSTMT->bindParam(':notify10', $daysToEnd); 
		 		$finishBorrowSTMT->bindParam(':confirmation_borrow', $one); 
			    $finishBorrowSTMT->execute();
	 		}
			$findEndDate = $_POST['endDate'];
	 		$endDate = date_create($findEndDate);
	 		$daysToEnd = date_diff($startToday,$endDate)->format('%a');
	 		$finishBorrowSQL = "UPDATE borrow_svds  SET aem_borrow= :aem_borrow, start_date= :start_date, expire_date= :expire_date, isborrowed= :isborrowed, notify30= :notify30, notify20= :notify20, notify10= :notify10, confirmation_borrow= :confirmation_borrow WHERE isborrowed= :zero";
	 		$finishBorrowSTMT = $db->prepare($finishBorrowSQL);
	 		$finishBorrowSTMT->bindParam(':zero', $zero, PDO::PARAM_INT);
	 		$finishBorrowSTMT->bindParam(':aem_borrow', $userToBorrow);
	 		$finishBorrowSTMT->bindParam(':start_date', $_POST['startDate']);
			$finishBorrowSTMT->bindParam(':expire_date', $_POST['endDate']);
	 		$finishBorrowSTMT->bindParam(':isborrowed', $one);
	 		$finishBorrowSTMT->bindParam(':notify30', $daysToEnd);
	 		$finishBorrowSTMT->bindParam(':notify20', $daysToEnd);
	 		$finishBorrowSTMT->bindParam(':notify10', $daysToEnd); 
	 		$finishBorrowSTMT->bindParam(':confirmation_borrow', $zero); 
		    $finishBorrowSTMT->execute();
	 		
		 	$deleteQuerySQL = "DELETE  FROM basket_svds WHERE id_user_basket= :idUser";
			$deleteQuerySTMT = $db->prepare($deleteQuerySQL);
			$deleteQuerySTMT->bindParam(':idUser', $aem, PDO::PARAM_INT);
			$deleteQuerySTMT->execute();

			echo '<div class="p-3 mb-2 bg-success text-white container">Επιτυχείς καταχώρηση αποτελεσμάτων</div>';
			header("Location: new_borrow.php");
		}	    	 	

	 	
} else {
		header("Location: index.php");
}

echo '
	<div class="container"> 	
		<h3>Γεια σου '.$last_name .' '.
	 		$first_name.', έχεις επιλέξει τα παρακάτω εξαρτήματα:  </h3>
		<div class="row">
			<div class="col-md-6">
			<form method="post">			
				<h3>Συμπλήρωσε τις προβλεπόμενες πληροφορίες</h3>
';
if ($type == 1){
	echo '
		<div class="form-group">
		<label for="aemBorrow">ΑΕΜ Δανειστή </label><br>
		<input type="text" class="form-control" id="aemBorrow" name="aemBorrow" required>
		</div>
	';	
}	

echo '				
				<div class="form-group">
					<label for="id_equip_borrow">Εξαρτήματα: </label>
';					    
			    foreach ($equipNames as $row) {
			    	echo'
			    	<input type="text" class="form-control" id="id_equip_borrow" value="'.$row.'">
			    	';
				} 
			echo ' 
				</div>
				<div class="form-group">
					<label for="startDate">Ημερομηνία έναρξης: </label><br>
				    <input type="date" id="startDate" value="'.$newTodayFormat.'" name="startDate" min="2000-01-02">
				</div>
				<div class="form-group">
				<label for="endDate">Ημερομηνία λήξης: </label><br>
				<input type="date" id="endDate" name="endDate" min="2000-01-02">
				</div>				  
				<button type="submit" name="finish" class="btn btn-primary">Ολοκλήρωση Δανεισμού</button>
			</div>
			</form>	
		</div>	
	</div>

';

include("views/footer.php");
?>