<?php
include("variables_file.php");
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");
	if ($_SESSION['email']){

		$idToChange = filter_var($_GET['id_borrow'],FILTER_SANITIZE_NUMBER_FLOAT);
		$userToBorrow = $_SESSION['aem'];

		$userQueryBorrowSQL = "SELECT * FROM users_svds WHERE id = :userToBorrow";
		$userQueryBorrowSTMT = $db->prepare($userQueryBorrowSQL); 
	 	$userQueryBorrowSTMT->bindParam(':userToBorrow', $userToBorrow, PDO::PARAM_INT);
	 	$userQueryBorrowSTMT->execute();
	 	while($userQueryBorrowSTMTResult=$userQueryBorrowSTMT->fetch(PDO::FETCH_ASSOC)){
	 		$userToBorrowName = $userQueryBorrowSTMTResult['first_name'];
	 		$userToBorrowLastName = $userQueryBorrowSTMTResult['last_name'];
	 	}

	 	$borrowQuerySQL = "SELECT * FROM borrow_svds WHERE id_borrow = :idToChange";
	 	$borrowQuerySTMT = $db->prepare($borrowQuerySQL);
	 	$borrowQuerySTMT->bindParam(':idToChange', $idToChange, PDO::PARAM_INT); 
	 	$borrowQuerySTMT->execute();
	 	while($borrowQuerySTMTResult=$borrowQuerySTMT->fetch(PDO::FETCH_ASSOC)){
	 			$idEquipToBorrow = $borrowQuerySTMTResult['id_equip_borrow'];	 	
			 	$equipBorrowQuerySQL = "SELECT * FROM equip_svds WHERE id_equip = :idEquipToBorrow";
			 	$equipBorrowQuerySTMT = $db->prepare($equipBorrowQuerySQL);
			 	$equipBorrowQuerySTMT->bindParam(':idEquipToBorrow', $idEquipToBorrow, PDO::PARAM_INT);
			 	$equipBorrowQuerySTMT->execute();
			 	while($equipBorrowQuerySTMTResult=$equipBorrowQuerySTMT->fetch(PDO::FETCH_ASSOC)){
			 		$equipName= $equipBorrowQuerySTMTResult['name_e'];
			 	} 
	 	}


	 	if(isset($_POST['finish'])){

	 		$findEndDate = $_POST['endDate'];
	 		$end = date_create($findEndDate);
	 		$daysToEnd = date_diff($startToday,$end)->format('%a');
	 		$start_date = filter_var($_POST['startDate'],FILTER_SANITIZE_STRING); 
	 		$expire_date = filter_var($_POST['endDate'],FILTER_SANITIZE_STRING);
	 		$extend_reason = filter_var($_POST['extendReason'],FILTER_SANITIZE_STRING);
	 		$finishBorrowSQL = "UPDATE borrow_svds SET start_date= :start_date, expire_date= :expire_date, isborrowed= :isborrowed, notify30= :notify30, notify20= :notify20, notify10= :notify10, confirmation_borrow= :confirmation_borrow, extend_reason= :extend_reason WHERE id_borrow= :idToChange";
	 		$finishBorrowSTMT = $db->prepare($finishBorrowSQL);
	 		$finishBorrowSTMT->bindParam(':idToChange', $idToChange, PDO::PARAM_INT);
	 		$finishBorrowSTMT->bindParam(':start_date', $start_date);
		    $finishBorrowSTMT->bindParam(':expire_date', $endDate);
		    $finishBorrowSTMT->bindParam(':isborrowed', $zero);
		    $finishBorrowSTMT->bindParam(':notify30', $daysToEnd);
		    $finishBorrowSTMT->bindParam(':notify20', $daysToEnd);
		    $finishBorrowSTMT->bindParam(':notify10', $daysToEnd);
		    $finishBorrowSTMT->bindParam(':confirmation_borrow', $zero);
		    $finishBorrowSTMT->bindParam(':extend_reason', $extend_reason);
		    $finishBorrowSTMT->execute();

		    	echo '<a class="p-3 mb-2 bg-success text-white">Επιτυχείς καταχώρηση αποτελεσμάτων</a>';
		    	header("Location: active.php");
		    	die("Δεν έχετε συνδεθεί");	    	 	

	 	}

			 	
} else {
		header("Location: index.php");
		die("Δεν έχετε συνδεθεί");
	}

echo '		
	<div class="container"> 	
		<h3>Γεια σου <?php echo $userToBorrowName; ?>, έχεις επιλέξει το παρακάτω εξαρτήματα:  </h3>
		<div class="row">
		  	<div class="col-md-6">
				<form method="post">
				 <h3>Αίητηση τροποποίησης επιλεγμένου δανεισμού</h3>
				  <div class="form-group">
				    <label for="id_equip_borrow">Εξαρτήματα: </label>
				    <li id="id_equip_borrow" value="'.$equipName.'">'.$equipName.'</li>				    
				  </div>
				  <div class="form-group">
				    <label for="startDate">Ημερομηνία έναρξης: </label><br>
				    <input type="date" id="startDate" value="'.$newTodayFormat.'" name="startDate" min="2000-01-02">
				  </div>
				  <div class="form-group">
				    <label for="endDate">Ημερομηνία λήξης: </label><br>
				    <input type="date" id="endDate "name="endDate" min="2000-01-02"><br>
				    <label for="borrowExtend">Λόγος επέκτασης: </label><br>		
				  	<textarea name="extendReason" rows="4" cols="50"> 
					</textarea>
				  </div>  
				  <button type="submit" name="finish" class="btn btn-primary">Αίτηση Επέκτασης</button>
				</form>
			</div>	
		</div>	
	</div>

';


include("views/footer.php");
?>