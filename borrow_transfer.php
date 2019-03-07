<?php
//Access: Administrator
include("variables_file.php");
include("checkUser.php");
echo '
	<!DOCTYPE html>
	<html lang="en">
';
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");
include("function_cron.php");
//  Η μεταβλητή $type έχει τεθεί από το $_SESSION['type'] και ελέγχει το επίπεδο δικαιωμάτων του συνδεδεμένου χρήστη
if ($type == 1 OR $type == 2 OR $type == 3){
	$idToChange = filter_var($_GET['id_borrow'],FILTER_SANITIZE_NUMBER_FLOAT);

	$borrowQuerySQL = "SELECT * FROM borrow_svds WHERE id_borrow= :idToChange";
	$borrowQuerySTMT = $db->prepare($borrowQuerySQL);
	$borrowQuerySTMT->bindParam(':idToChange', $idToChange, PDO::PARAM_INT);
	$borrowQuerySTMT->execute();

	if ($borrowQuerySTMT->rowCount() > 0){
		while($borrowQuerySTMTResult=$borrowQuerySTMT->fetch(PDO::FETCH_ASSOC)){
			if ($borrowQuerySTMTResult['extend_reason'] != NULL){
				$borrowReason = $borrowQuerySTMTResult['extend_reason'];
			}elseif ($borrowQuerySTMTResult['borrow_reason'] != NULL){
				$borrowReason = $borrowQuerySTMTResult['borrow_reason'];
			}else {
				$borrowReason = "Δεν δόθηκε λόγος δανεισμού.";
			}
			$idEquipToTransfer = $borrowQuerySTMTResult['id_equip_borrow'];	 	
		 	$equipTransferBorrowQuerySQL = "SELECT * FROM equip_svds WHERE id_equip = :idEquipToBorrow";
		 	$equipTransferBorrowQuerySTMT = $db->prepare($equipTransferBorrowQuerySQL);
		 	$equipTransferBorrowQuerySTMT->bindParam(':idEquipToBorrow', $idEquipToTransfer, PDO::PARAM_INT);
		 	$equipTransferBorrowQuerySTMT->execute();
		 	while($equipTransferBorrowQuerySTMTResult=$equipTransferBorrowQuerySTMT->fetch(PDO::FETCH_ASSOC)){
		 		$equipName= $equipTransferBorrowQuerySTMTResult['name_e']; 
		 	} 

			$idBorrower = $borrowQuerySTMTResult['id_user_borrow'];	
			$userQueryTransferBorrowSQL = "SELECT * FROM users_svds WHERE id = :idBorrower";
			$userQueryTransferBorrowSTMT = $db->prepare($userQueryTransferBorrowSQL); 
		 	$userQueryTransferBorrowSTMT->bindParam(':idBorrower', $idBorrower, PDO::PARAM_INT);
		 	$userQueryTransferBorrowSTMT->execute();
		 	while($userQueryTransferBorrowSTMTResult=$userQueryTransferBorrowSTMT->fetch(PDO::FETCH_ASSOC)){
		 		$userToTransferBorowName = $userQueryTransferBorrowSTMTResult['first_name'];
		 		$userToTransferBorrowLastName = $userQueryTransferBorrowSTMTResult['last_name'];
		 		$userToTransferBorrowAEM = $userQueryTransferBorrowSTMTResult['aem'];
		 	}
	 	}
	}else {
		echo 'Ο δανεισμός που ζητήσατε δεν βρέθηκε.';
		die();
	}
	if(isset($_POST['finish'])){

 		if(isset($_POST['aemBorrow'])){
 			$pieces = explode(' ', $_POST['aemBorrow']);
			$aemNewBorrow = array_pop($pieces);

			$findEndDate = $_POST['endDate'];
	 		$endDate = date_create($findEndDate);
	 		$daysToEnd = date_diff($startToday,$endDate)->format('%a');
	 		$dateDiff = $daysToEnd;
	 		if (isset($_POST['borrowReason'])){
 				$borrow_reason = filter_var($_POST['borrowReason'],FILTER_SANITIZE_STRING);
	 		}else{
	 			$borrow_reason = "Δεν δόθηκε λόγος δανεισμού";
	 		}
	 		
			$userToSendEmailAndSMSQuerySQL = "SELECT * FROM users_svds WHERE aem= :aemBorrower";
            $userToSendEmailAndSMSQuerySTMT = $db->prepare($userToSendEmailAndSMSQuerySQL);
            $userToSendEmailAndSMSQuerySTMT->bindParam(':aemBorrower', $aemNewBorrow, PDO::PARAM_INT);
               	if ($userToSendEmailAndSMSQuerySTMT->execute()) {
                   	while ($userToSendEmailAndSMSQuerySTMTResult=$userToSendEmailAndSMSQuerySTMT->fetch(PDO::FETCH_ASSOC)){
                   		$idToTransferBorrow = $userToSendEmailAndSMSQuerySTMTResult['id'];
						$email = $userToSendEmailAndSMSQuerySTMTResult['email'];
						$full_name = $userToSendEmailAndSMSQuerySTMTResult['last_name'].' '.$userToSendEmailAndSMSQuerySTMTResult['first_name'];
						$telephone = $userToSendEmailAndSMSQuerySTMTResult['telephone'];
						sendSMS($full_name, $telephone, $aemNewBorrow, $dateDiff, $equipName);
						sendEmail($email, $full_name, $aemNewBorrow, $dateDiff, $equipName);
				}
			}


	 		$start_date = filter_var($_POST['startDate'],FILTER_SANITIZE_NUMBER_FLOAT);
	 		$expire_date = filter_var($_POST['endDate'],FILTER_SANITIZE_NUMBER_FLOAT);
	 		$finishBorrowSQL = "INSERT INTO borrow_svds (id_user_borrow, id_equip_borrow, start_date, expire_date, history_flag, isborrowed, notify30, notify20, notify10, confirmation_borrow, borrow_reason) VALUES (:id_user_borrow, :id_equip_borrow, :start_date, :expire_date, :history_flag, :isborrowed, :notify30, :notify20, :notify10, :confirmation_borrow, :borrow_reason)";
	 		$finishBorrowSTMT = $db->prepare($finishBorrowSQL);
	 		$finishBorrowSTMT->bindParam(':id_user_borrow', $idToTransferBorrow);
	 		$finishBorrowSTMT->bindParam(':id_equip_borrow', $idEquipToTransfer);
	 		$finishBorrowSTMT->bindParam(':start_date', $start_date);
			$finishBorrowSTMT->bindParam(':expire_date', $expire_date);
			$finishBorrowSTMT->bindParam(':history_flag', $one);
	 		$finishBorrowSTMT->bindParam(':isborrowed', $one);
	 		$finishBorrowSTMT->bindParam(':notify30', $daysToEnd);
	 		$finishBorrowSTMT->bindParam(':notify20', $daysToEnd);
	 		$finishBorrowSTMT->bindParam(':notify10', $daysToEnd); 
	 		$finishBorrowSTMT->bindParam(':confirmation_borrow', $one); 
	 		$finishBorrowSTMT->bindParam(':borrow_reason', $borrow_reason);
		    if ($finishBorrowSTMT->execute()) {
				
				$UpdateBorrowQuerySQL = "UPDATE borrow_svds SET history_flag= :history_flag WHERE id_borrow= :idToChange";
				$UpdateBorrowQuerySTMT = $db->prepare($UpdateBorrowQuerySQL);
				$UpdateBorrowQuerySTMT->bindParam(':idToChange', $idToChange, PDO::PARAM_INT);
				$UpdateBorrowQuerySTMT->bindParam(':history_flag', $zero, PDO::PARAM_INT);
				$UpdateBorrowQuerySTMT->execute();	
			    
				
		    	echo '<p class="alert alert-success">Ο δανεισμός καταχωρήθηκε με επιτυχία</p>';
		    }else {
		    	echo '<p class="alert alert-warning">Ο δανεισμός δεν καταχωρήθηκε με επιτυχία</p>';
		    }
                         
		    echo '<meta http-equiv="refresh" content="0; URL=equipment_return.php">';
		    die("Δεν έχετε συνδεθεί");

 		}else {
 			header("Location: equipment_return.php");
		    die("Δεν έχετε δώσει ΑΕΜ για μεταφορά.");
 		}
 	}	

	echo '		
			<div class="container"> 	
				<h3>Έχεις επιλέξει το παρακάτω εξαρτήματα για μεταφορά:</h3>
				-'.$equipName.', το οποίο αυτή την περίοδο έχει δανεισθεί ο χρήστης '.$userToTransferBorrowLastName.' '.$userToTransferBorowName.' AEM= '.$userToTransferBorrowAEM.'
				<div class="row">
				  	<div class="col-md-6">
						<form method="post">
						 <h4>Αίητηση μεταφοράς επιλεγμένου δανεισμού<br>
						 Παρακαλώ συμπληρώστε τα απαραίτητα στοιχεία:</h4>
						  <div class="form-group">
						    <label for="startDate">Ημερομηνία έναρξης: </label><br>
						    <input type="date" id="startDate" value="'.$newTodayFormat.'" name="startDate" min="2000-01-02" required>
						  </div>
						  <div class="form-group">
						    <label for="endDate">Ημερομηνία λήξης: </label><br>
						    <input type="date" id="endDate "name="endDate" min="2000-01-02" value="'.$newEndDayFormat.'" required><br>
						    <label for="aemBorrow">Δώσε το ΑΕΜ Δανειστή στον οποίο θα γίνει η μεταφορά: </label><br>
	                        <input type="text" name="aemBorrow"  class="form-control input-lg" id="aemBorrow" autocomplete="off" placeholder="ΑΕΜ"/>
						    <div id="aemTotal"></div>
						    <label for="borrowExtend">Λόγος επέκτασης*: </label><br>		
						  	<textarea name="borrowReason" rows="4" cols="50" required>'.$borrowReason.'</textarea>
						  </div>  
						  <button type="submit" name="finish" class="btn btn-primary">Αίτηση Μεταφοράς</button>
						</form>
					</div>	
				</div>	
			</div>
			';
	include("views/footer.php");
	echo '
			</body>
			</html>
		';
}else {
    header("Location: index.php");
    die("Δεν δικαιώματα εισόδου σε αυτή τη σελίδα.");
}


?>