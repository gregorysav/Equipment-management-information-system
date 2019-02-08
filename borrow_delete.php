<?php
//Access: Registered Users
include("variables_file.php");
echo '
	<!DOCTYPE html>
	<html lang="en">
';
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");
include("function_cron.php");

if (isset($_GET['id_borrow']) AND $type == 0){	
	$idToDelete= filter_var($_GET['id_borrow'],FILTER_SANITIZE_NUMBER_FLOAT);
	$deleteQuerySQL = "DELETE  FROM borrow_svds WHERE id_borrow=  :idToDelete";
	$deleteQuerySTMT = $db->prepare($deleteQuerySQL);
	$deleteQuerySTMT->bindParam(':idToDelete', $idToDelete, PDO::PARAM_INT);
	if ($deleteQuerySTMT->execute()) {
		echo '<p class="alert alert-success">Ο δανεισμός διαγράφηκε με επιτυχία</p>';
		header("Location: active.php");
		die("Ο δανεισμός διαγράφηκε με επιτυχία");
	}else {
		echo '<p class="alert alert-warning">Παρουσιάστηκε πρόβλημα κατά τη διαγραφή του δανεισμού</p>';
		header("Location: active.php");
		die("Παρουσιάστηκε πρόβλημα κατά τη διαγραφή του δανεισμού");
	}

	
}

if (isset($_GET['id_borrow']) AND ($type == 1 OR $type == 2 OR $type == 3)){	
	$idToDelete= filter_var($_GET['id_borrow'],FILTER_SANITIZE_NUMBER_FLOAT);
	$idToSendMessage= filter_var($_GET['id_user_borrow'],FILTER_SANITIZE_NUMBER_FLOAT);
	$dateDiff = 0;
	$borrowQuerySQL = "SELECT * FROM borrow_svds WHERE id_borrow= :idToFindBorrow"; 
	$borrowQuerySTMT = $db->prepare($borrowQuerySQL);
	$borrowQuerySTMT->bindParam(':idToFindBorrow', $idToDelete, PDO::PARAM_INT);
	$borrowQuerySTMT->execute();
	while ($borrowQuerySTMTResult=$borrowQuerySTMT->fetch(PDO::FETCH_ASSOC)){
		$equipmentQuerySQL = "SELECT * FROM equip_svds WHERE id_equip= :idToFindEquipment"; 
		$equipmentQuerySTMT = $db->prepare($equipmentQuerySQL);
		$equipmentQuerySTMT->bindParam(':idToFindEquipment', $borrowQuerySTMTResult['id_equip_borrow'], PDO::PARAM_INT);
		$equipmentQuerySTMT->execute();
		while ($equipmentQuerySTMTResult=$equipmentQuerySTMT->fetch(PDO::FETCH_ASSOC)){
			$equipmentName = $equipmentQuerySTMTResult['name_e'];
		}	
	}	
	$deleteQuerySQL = "UPDATE borrow_svds SET history_flag= :zero WHERE id_borrow= :idToDelete";
	$deleteQuerySTMT = $db->prepare($deleteQuerySQL);
	$deleteQuerySTMT->bindParam(':idToDelete', $idToDelete, PDO::PARAM_INT);
	$deleteQuerySTMT->bindParam(':zero', $zero, PDO::PARAM_INT);
	if ($deleteQuerySTMT->execute()){
		$userQuerySQL = "SELECT * FROM users_svds WHERE id= :idToSendMessage"; 
		$userQuerySTMT = $db->prepare($userQuerySQL);
		$userQuerySTMT->bindParam(':idToSendMessage', $idToSendMessage, PDO::PARAM_INT);
		$userQuerySTMT->execute();
		while ($userQuerySTMTResult=$userQuerySTMT->fetch(PDO::FETCH_ASSOC)){
			$aem = $userQuerySTMTResult['aem']; 
			$email = $userQuerySTMTResult['email'];
			$full_name = $userQuerySTMTResult['last_name'].' '.$userQuerySTMTResult['first_name'];
			$telephone = $userQuerySTMTResult['telephone'];
			$aem_borrow = $aemToSendMessage;
		}
		sendSMS($full_name, $telephone, $aem, $dateDiff, $equipmentName);
		$date=date('l jS \of F Y h:i:s A');
	    $crlf = chr(13) . chr(10);
	    $to = $email;
	    $subject = "[ILoan] Notification Email";
	    $message = 'Αυτοματοποιημένο μήνυμα i-loan"'.$crlf.''.$crlf.''.$crlf.'"Προς: [ '.$full_name.' ], AEM ['.$aem.'] "'.$crlf.'"Ο δανεισμός σας για ['.$equipmentName.'] έχει τερματιστεί από το διδάσκοντα. Μην απαντήσετε σε αυτό το email, γιατί δεν παρακολουθείται η συγκεκριμένη διεύθυνση."'.$crlf.''.$crlf.'"Παρακαλώ διατηρήστε αυτό το email στο αρχείο σας έως το τέλος του εξαμήνου".';
	    $headers = 'From: noreply@spam.vlsi.gr'."\r\n".'Reply-To: noreply@spam.vlsi.gr'."\r\n".'Content-Type: text/plain; charset=UTF-8' . "\r\n" .'MIME-Version: 1.0' .    "\r\n" .'Content-Transfer-Encoding: quoted-printable' . "\r\n" .'X-Mailer: PHP/'.phpversion();


	    if( mail($to,$subject,$message,$headers)){
	       echo "Το email στάλθηκε με επιτυχία";
	    }else{
	       echo "Το email δεν στάλθηκε με επιτυχία γιατί :" .$mail->ErrorInfo;
	    }
	}

	if (isset($_GET['id_equip']) AND ($type == 1 OR $type == 2 OR $type == 3)){
		$idEquipToUpdate= filter_var($_GET['id_equip'],FILTER_SANITIZE_NUMBER_FLOAT);
		$equipQuerySQL = "SELECT * FROM equip_svds WHERE id_equip= :idEquipToUpdate"; 
		$equipQuerySTMT = $db->prepare($equipQuerySQL);
		$equipQuerySTMT->bindParam(':idEquipToUpdate', $idEquipToUpdate, PDO::PARAM_INT);
		$equipQuerySTMT->execute();

		while ($equipQuerySTMTResult=$equipQuerySTMT->fetch(PDO::FETCH_ASSOC)){
			$newQuantity = $equipQuerySTMTResult['quantity'] + 1;
			$updateQuantitySQL = "UPDATE equip_svds SET quantity= :quantity WHERE id_equip= :idToUpdate";
	 		$updateQuantitySTMT = $db->prepare($updateQuantitySQL);
	 		$updateQuantitySTMT->bindParam(':idToUpdate', $idEquipToUpdate, PDO::PARAM_INT);
	 		$updateQuantitySTMT->bindParam(':quantity', $newQuantity);
		    $updateQuantitySTMT->execute();
		}
	}


	echo '<meta http-equiv="refresh" content="0; URL=equipment_return.php">';
	die("Δεν έχετε συνδεθεί");
}

include("views/footer.php");
echo '
	</body>
	</html>
';
?>
