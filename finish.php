<?php
//Access: Registered Users
include("variables_file.php");
include("checkUser.php");
echo '
	<!DOCTYPE html>
	<html lang="en">
';
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");
include("functions.php");

 	if(isset($_POST['finish'])){

 		if(isset($_POST['aemBorrow'])){
 			$pieces = explode(' ', $_POST['aemBorrow']);
			$aem_borrow = array_pop($pieces);

			$findEndDate = $_POST['endDate'];
	 		$endDate = date_create($findEndDate);
	 		$daysToEnd = date_diff($startToday,$endDate)->format('%a');
	 		$dateDiff = $daysToEnd;
	 		if (isset($_POST['borrowReason'])){
 				$borrow_reason = filter_var($_POST['borrowReason'],FILTER_SANITIZE_STRING);
	 		}else{
	 			$borrow_reason = "Δεν δόθηκε λόγος δανεισμού";
	 		}
	 		
			$equipmentNameEmail = trim($_SESSION['equipmentToBorrow'], ',');
			$equipmentNameSMS = "Επιλεγμένα εξαρτήματα";
			$userToSendEmailAndSMSQuerySQL = "SELECT * FROM users_svds WHERE aem= :aemBorrower";
            $userToSendEmailAndSMSQuerySTMT = $db->prepare($userToSendEmailAndSMSQuerySQL);
            $userToSendEmailAndSMSQuerySTMT->bindParam(':aemBorrower', $aem_borrow, PDO::PARAM_INT);
               	if ($userToSendEmailAndSMSQuerySTMT->execute()) {
                   	while ($userToSendEmailAndSMSQuerySTMTResult=$userToSendEmailAndSMSQuerySTMT->fetch(PDO::FETCH_ASSOC)){
                   		$idToCompleteBorrow = $userToSendEmailAndSMSQuerySTMTResult['id'];
						$email = $userToSendEmailAndSMSQuerySTMTResult['email'];
						$full_name = $userToSendEmailAndSMSQuerySTMTResult['last_name'].' '.$userToSendEmailAndSMSQuerySTMTResult['first_name'];
						$telephone = $userToSendEmailAndSMSQuerySTMTResult['telephone'];
						sendSMS($full_name, $telephone, $aem_borrow, $dateDiff, $equipmentNameSMS);
						sendEmail($email, $full_name, $aem_borrow, $dateDiff, $equipmentNameEmail);
				}
			}


	 		$start_date = filter_var($_POST['startDate'],FILTER_SANITIZE_NUMBER_FLOAT);
	 		$expire_date = filter_var($_POST['endDate'],FILTER_SANITIZE_NUMBER_FLOAT);
	 		$finishBorrowSQL = "UPDATE borrow_svds  SET id_user_borrow= :id_user_borrow, start_date= :start_date, expire_date= :expire_date, history_flag= :history_flag, isborrowed= :isborrowed, notify30= :notify30, notify20= :notify20, notify10= :notify10, confirmation_borrow= :confirmation_borrow, borrow_reason= :borrow_reason WHERE isborrowed= :zero";
	 		$finishBorrowSTMT = $db->prepare($finishBorrowSQL);
	 		$finishBorrowSTMT->bindParam(':zero', $zero, PDO::PARAM_INT);
	 		$finishBorrowSTMT->bindParam(':id_user_borrow', $idToCompleteBorrow, PDO::PARAM_INT);
	 		$finishBorrowSTMT->bindParam(':start_date', $start_date);
			$finishBorrowSTMT->bindParam(':expire_date', $expire_date);
			$finishBorrowSTMT->bindParam(':history_flag', $one, PDO::PARAM_INT);
	 		$finishBorrowSTMT->bindParam(':isborrowed', $one, PDO::PARAM_INT);
	 		$finishBorrowSTMT->bindParam(':notify30', $daysToEnd, PDO::PARAM_INT);
	 		$finishBorrowSTMT->bindParam(':notify20', $daysToEnd, PDO::PARAM_INT);
	 		$finishBorrowSTMT->bindParam(':notify10', $daysToEnd, PDO::PARAM_INT); 
	 		$finishBorrowSTMT->bindParam(':confirmation_borrow', $one, PDO::PARAM_INT); 
	 		$finishBorrowSTMT->bindParam(':borrow_reason', $borrow_reason);
		    if ($finishBorrowSTMT->execute()) {				
		    	echo '<p class="alert alert-success">Ο δανεισμός καταχωρήθηκε με επιτυχία</p>';
		    }else {
		    	echo '<p class="alert alert-warning">Ο δανεισμός δεν καταχωρήθηκε με επιτυχία</p>';
		    }

		    $basketQuerySQL = "SELECT * FROM basket_svds INNER JOIN equip_svds on basket_svds.id_equip_basket = equip_svds.id_equip"; 
			$basketQuerySTMT = $db->prepare($basketQuerySQL);
			$basketQuerySTMT->execute();

			while ($basketQuerySTMTResult=$basketQuerySTMT->fetch(PDO::FETCH_ASSOC)){
				$newQuantity = $basketQuerySTMTResult['quantity'] - 1;
				$updateQuantitySQL = "UPDATE equip_svds SET quantity= :quantity WHERE id_equip= :idToUpdate";
		 		$updateQuantitySTMT = $db->prepare($updateQuantitySQL);
		 		$updateQuantitySTMT->bindParam(':idToUpdate', $basketQuerySTMTResult['id_equip_basket'], PDO::PARAM_INT);
		 		$updateQuantitySTMT->bindParam(':quantity', $newQuantity);
			    $updateQuantitySTMT->execute();
			}

		    $deleteQuerySQL = "DELETE  FROM basket_svds WHERE id_user_basket= :idUser";
			$deleteQuerySTMT = $db->prepare($deleteQuerySQL);
			$deleteQuerySTMT->bindParam(':idUser', $id, PDO::PARAM_INT);
			$deleteQuerySTMT->execute();	
			
                         
		    echo '<meta http-equiv="refresh" content="0; URL=new_borrow.php">';
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
	 		$finishBorrowSQL = "UPDATE borrow_svds  SET  start_date= :start_date, expire_date= :expire_date, isborrowed= :isborrowed, notify30= :notify30, notify20= :notify20, notify10= :notify10, confirmation_borrow= :confirmation_borrow, borrow_reason= :borrow_reason  WHERE isborrowed= :zero AND id_user_borrow= :id_user_borrow";
	 		$finishBorrowSTMT = $db->prepare($finishBorrowSQL);
	 		$finishBorrowSTMT->bindParam(':zero', $zero, PDO::PARAM_INT);
	 		$finishBorrowSTMT->bindParam(':id_user_borrow', $id);
	 		$finishBorrowSTMT->bindParam(':start_date', $start_date);
			$finishBorrowSTMT->bindParam(':expire_date', $expire_date);
	 		$finishBorrowSTMT->bindParam(':isborrowed', $one);
	 		$finishBorrowSTMT->bindParam(':notify30', $daysToEnd);
	 		$finishBorrowSTMT->bindParam(':notify20', $daysToEnd);
	 		$finishBorrowSTMT->bindParam(':notify10', $daysToEnd); 
	 		$finishBorrowSTMT->bindParam(':confirmation_borrow', $zero);
	 		$finishBorrowSTMT->bindParam(':borrow_reason', $borrow_reason); 
		    if ($finishBorrowSTMT->execute()) {
		    	echo '<p class="alert alert-success">Ο δανεισμός καταχωρήθηκε με επιτυχία</p>';
		    }else {
		    	echo '<p class="alert alert-warning">Ο δανεισμός δενκαταχωρήθηκε με επιτυχία</p>';
		    }
	 		
	 		$basketQuerySQL = "SELECT * FROM basket_svds INNER JOIN equip_svds on basket_svds.id_equip_basket = equip_svds.id_equip"; 
			$basketQuerySTMT = $db->prepare($basketQuerySQL);
			$basketQuerySTMT->execute();
			while ($basketQuerySTMTResult=$basketQuerySTMT->fetch(PDO::FETCH_ASSOC)){
				$newQuantity = $basketQuerySTMTResult['quantity'] - 1;
				$updateQuantitySQL = "UPDATE equip_svds SET quantity= :quantity WHERE id_equip= :idToUpdate";
		 		$updateQuantitySTMT = $db->prepare($updateQuantitySQL);
		 		$updateQuantitySTMT->bindParam(':idToUpdate', $basketQuerySTMTResult['id_equip_basket'], PDO::PARAM_INT);
		 		$updateQuantitySTMT->bindParam(':quantity', $newQuantity);
			    $updateQuantitySTMT->execute();
			    $itemsToPrint .= $basketQuerySTMTResult['name_basket'] ."<br>";
			}    
			
		 	$deleteQuerySQL = "DELETE  FROM basket_svds WHERE id_user_basket= :idUser";
			$deleteQuerySTMT = $db->prepare($deleteQuerySQL);
			$deleteQuerySTMT->bindParam(':idUser', $id, PDO::PARAM_INT);
			$deleteQuerySTMT->execute();

			echo '<div class="p-3 mb-2 bg-success text-white container">Επιτυχής καταχώρηση αποτελεσμάτων</div>';
			$_SESSION['start_date'] = date("d-m-Y", strtotime($_POST['startDate'])); 
 			$_SESSION['expire_date'] = date("d-m-Y", strtotime($_POST['endDate']));
			PDFPrint ($fullName, $aem, $type, $itemsToPrint, $borrow_reason, $_SESSION['start_date'], $_SESSION['expire_date']);
 		}	



	}	    	 	

echo '
	<div class="container"> 	
		<h3>Έχεις επιλέξει τα παρακάτω εξαρτήματα:  </h3>
		<div class="row">
			<div class="col-md-6">
			<form method="post">			
				<h4>Συμπλήρωσε τις πληροφορίες</h4>
';

echo '				
				<div class="form-group">
					<label for="id_equip_borrow">Εξαρτήματα: </label>
										
';				
				checkQuantity();	    
			    if (isset($_SESSION['message']) AND !empty($_SESSION['message'])){
			    	echo $_SESSION['message'];
			    }

				$basketQuerySQL = "SELECT * FROM basket_svds WHERE id_user_basket= :idUser"; 
 				$basketQuerySTMT =$db->prepare($basketQuerySQL);
 				$basketQuerySTMT->bindParam(':idUser', $id, PDO::PARAM_INT);
 				$basketQuerySTMT->execute();
 				$basketQuerySTMTResult=$basketQuerySTMT->rowCount();
				if ($basketQuerySTMTResult == 0){
 					header("Refresh:0; url=new_borrow.php");
 					die("Αφαιρέσατε όλα τα εξαρτήματα.");
				}else{
	 				while($basketQuerySTMTResult=$basketQuerySTMT->fetch(PDO::FETCH_ASSOC)){
	 					echo '
	 						<p id="id_equip_borrow" title="Αφαίρεση" value="'.$basketQuerySTMTResult['name_basket'].'"><a id="finishRemove" href="actions.php?action=removeFromFinish&id_basket='.$basketQuerySTMTResult['id_basket'].'&id_user_basket='.$_SESSION['id'].'&id_equip_basket='.$basketQuerySTMTResult['id_equip_basket'].'"><span class="fa fa-times"></span></a> '.$basketQuerySTMTResult['name_basket'].'</p>	

	 					';			 	 
	 				}
 				}
echo ' 
			
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
if ($type == 1 OR $type == 2 OR $type == 3){
	echo '
		<label for="aemBorrow">ΑΕΜ Δανειστή </label><br>
		<input type="text" name="aemBorrow"  class="form-control input-lg" id="aemBorrow" autocomplete="off" placeholder="ΑΕΜ"/>
		<div id="aemTotal"></div>
	';	
}
echo '				
				<label for="borrow_reason">Δώσε το λόγο δανεισμού: </label><br>
				<textarea name="borrowReason" rows="4" cols="50"></textarea>
';
if (isset($_SESSION['message']) AND !empty($_SESSION['message'])){
	echo '
					<button type="submit" name="finish" id="finishBorrow" class="btn btn-primary" disabled>Ολοκλήρωση Δανεισμού</button>
				</div>
				</form>	
			</div>	
		</div>
	';

}else{
	echo '								  
					<button type="submit" name="finish" id="finishBorrow" class="btn btn-primary">Ολοκλήρωση Δανεισμού</button>
				</div>
				</form>	
			</div>	
		</div>

	';
}	

include("views/footer.php");
echo '
	</body>
	</html>
';
?>