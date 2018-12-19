<?php
include("variables_file.php");
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");
include("functions.php");
	
 	$basketQuerySQL = "SELECT * FROM basket_svds WHERE id_user_basket= :idUser"; 
 	$basketQuerySTMT =$db->prepare($basketQuerySQL);
 	$basketQuerySTMT->bindParam(':idUser', $id, PDO::PARAM_INT);
 	$basketQuerySTMT->execute();
 	while($basketQuerySTMTResult=$basketQuerySTMT->fetch(PDO::FETCH_ASSOC)){
 		$equipNames[] = $basketQuerySTMTResult['name_basket'];			 	 
 	}
 	$itemsToPrint = "<ul>";
	foreach($equipNames as $selected){
		$itemsToPrint .= "<li>$selected</li>";
	}
	$itemsToPrint .= "</ul>";


 	if(isset($_POST['finish'])){

 		if(isset($_POST['aemBorrow'])){
 			$pieces = explode(' ', $_POST['aemBorrow']);
			$aem_borrow = array_pop($pieces);

			$findEndDate = $_POST['endDate'];
	 		$endDate = date_create($findEndDate);
	 		$daysToEnd = date_diff($startToday,$endDate)->format('%a');
	 		
	 		if (isset($_POST['borrowReason'])){
 				$borrow_reason = filter_var($_POST['borrowReason'],FILTER_SANITIZE_STRING);
	 		}else{
	 			$borrow_reason = "Δεν δόθηκε λόγος δανεισμού";
	 		}
	 		

	 		$start_date = filter_var($_POST['startDate'],FILTER_SANITIZE_NUMBER_FLOAT);
	 		$expire_date = filter_var($_POST['endDate'],FILTER_SANITIZE_NUMBER_FLOAT);
	 		$finishBorrowSQL = "UPDATE borrow_svds  SET aem_borrow= :aem_borrow, start_date= :start_date, expire_date= :expire_date, isborrowed= :isborrowed, notify30= :notify30, notify20= :notify20, notify10= :notify10, confirmation_borrow= :confirmation_borrow, borrow_reason= :borrow_reason WHERE isborrowed= :zero";
	 		$finishBorrowSTMT = $db->prepare($finishBorrowSQL);
	 		$finishBorrowSTMT->bindParam(':zero', $zero, PDO::PARAM_INT);
	 		$finishBorrowSTMT->bindParam(':aem_borrow', $aem_borrow);
	 		$finishBorrowSTMT->bindParam(':start_date', $start_date);
			$finishBorrowSTMT->bindParam(':expire_date', $expire_date);
	 		$finishBorrowSTMT->bindParam(':isborrowed', $one);
	 		$finishBorrowSTMT->bindParam(':notify30', $daysToEnd);
	 		$finishBorrowSTMT->bindParam(':notify20', $daysToEnd);
	 		$finishBorrowSTMT->bindParam(':notify10', $daysToEnd); 
	 		$finishBorrowSTMT->bindParam(':confirmation_borrow', $one); 
	 		$finishBorrowSTMT->bindParam(':borrow_reason', $borrow_reason);
		    $finishBorrowSTMT->execute();

		    $deleteQuerySQL = "DELETE  FROM basket_svds WHERE id_user_basket= :idUser";
			$deleteQuerySTMT = $db->prepare($deleteQuerySQL);
			$deleteQuerySTMT->bindParam(':idUser', $aem, PDO::PARAM_INT);
			$deleteQuerySTMT->execute();	
		    header("Location: new_borrow.php");
		    die("Δεν έχετε συνδεθεί");

 		}else{
 			$findEndDate = $_POST['endDate'];
	 		$endDate = date_create($findEndDate);
	 		$daysToEnd = date_diff($startToday,$endDate)->format('%a');
	 	
	 		if (isset($_POST['borrowReason'])){
	 			$borrow_reason = filter_var($_POST['borrowReason'],FILTER_SANITIZE_STRING);
	 		}else{
	 			$borrow_reason = "Δεν δόθηκε λόγος δανεισμού";
	 		}
	 	
	 		$start_date = filter_var($_POST['startDate'],FILTER_SANITIZE_NUMBER_FLOAT);
		 	$expire_date = filter_var($_POST['endDate'],FILTER_SANITIZE_NUMBER_FLOAT);
	 		$finishBorrowSQL = "UPDATE borrow_svds  SET  start_date= :start_date, expire_date= :expire_date, isborrowed= :isborrowed, notify30= :notify30, notify20= :notify20, notify10= :notify10, confirmation_borrow= :confirmation_borrow, borrow_reason= :borrow_reason  WHERE isborrowed= :zero AND aem_borrow= :aem_borrow";
	 		$finishBorrowSTMT = $db->prepare($finishBorrowSQL);
	 		$finishBorrowSTMT->bindParam(':zero', $zero, PDO::PARAM_INT);
	 		$finishBorrowSTMT->bindParam(':aem_borrow', $aem);
	 		$finishBorrowSTMT->bindParam(':start_date', $start_date);
			$finishBorrowSTMT->bindParam(':expire_date', $expire_date);
	 		$finishBorrowSTMT->bindParam(':isborrowed', $one);
	 		$finishBorrowSTMT->bindParam(':notify30', $daysToEnd);
	 		$finishBorrowSTMT->bindParam(':notify20', $daysToEnd);
	 		$finishBorrowSTMT->bindParam(':notify10', $daysToEnd); 
	 		$finishBorrowSTMT->bindParam(':confirmation_borrow', $zero);
	 		$finishBorrowSTMT->bindParam(':borrow_reason', $borrow_reason); 
		    $finishBorrowSTMT->execute();
	 		
		 	$deleteQuerySQL = "DELETE  FROM basket_svds WHERE id_user_basket= :idUser";
			$deleteQuerySTMT = $db->prepare($deleteQuerySQL);
			$deleteQuerySTMT->bindParam(':idUser', $aem, PDO::PARAM_INT);
			$deleteQuerySTMT->execute();

			echo '<div class="p-3 mb-2 bg-success text-white container">Επιτυχής καταχώρηση αποτελεσμάτων</div>';
			$_SESSION['start_date'] = date("d-m-Y", strtotime($_POST['startDate'])); 
 			$_SESSION['expire_date'] = date("d-m-Y", strtotime($_POST['endDate']));
			PDFPrint ($fullName, $aem, $itemsToPrint, $borrow_reason, $_SESSION['start_date'], $_SESSION['expire_date']);
 		}	



		}	    	 	

echo '
	<div class="container"> 	
		<h3>Γεια σου '.$last_name .' '.
	 		$first_name.', έχεις επιλέξει τα παρακάτω εξαρτήματα:  </h3>
		<div class="row">
			<div class="col-md-6">
			<form method="post">			
				<h4>Συμπλήρωσε τις πληροφορίες</h4>
';

echo '				
				<div class="form-group">
					<label for="id_equip_borrow">Εξαρτήματα: </label>
					<ul>
';					    
			    foreach ($equipNames as $row) {
			    	echo'
			    	<li id="id_equip_borrow" value="'.$row.'">'.$row.'</li>
			    	';
				} 

echo ' 
				</ul>
				</div>
				<div class="form-group">
					<label for="startDate">Ημερομηνία έναρξης: </label><br>
				    <input type="date" id="startDate" value="'.$newTodayFormat.'" name="startDate" min="2000-01-02">
				</div>
				<div class="form-group">
				<label for="endDate">Ημερομηνία λήξης: </label><br>
				<input type="date" id="endDate" value="'.$newEndDayFormat.'" name="endDate" min="2000-01-02">
				</div>
';
if ($type == 1){
	echo '
		<label for="aemBorrow">ΑΕΜ Δανειστή </label><br>
		<input type="text" name="aemBorrow"  class="form-control input-lg" id="aemBorrow" autocomplete="off" placeholder="ΑΕΜ"/>
		 <div id="aemTotal"></div>
	';	
}
echo '				
				<label for="borrow_reason">Δώσε το λόγο δανεισμού: </label><br>
				<textarea name="borrowReason" rows="4" cols="50"></textarea>				  
				<button type="submit" name="finish" id="finishBorrow" class="btn btn-primary">Ολοκλήρωση Δανεισμού</button>
			</div>
			</form>	
		</div>	
	</div>

';

include("views/footer.php");
?>