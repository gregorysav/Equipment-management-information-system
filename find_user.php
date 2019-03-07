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
include("functions.php");
//  Η μεταβλητή $type έχει τεθεί από το $_SESSION['type'] και ελέγχει το επίπεδο δικαιωμάτων του συνδεδεμένου χρήστη
if ($type !=0){

	echo '
		<div class="container" id="aemSearchInputFinishPage">
			<form method="POST">
			<div class="form-group">
				<label for="idToPrintPDF">Δώσε το ΑΕΜ χρήστη για την εκτύπωση εντύπου: </label>
			    <input type="text" class="form-control" name="idToPrintPDF" id="idToPrintPDF" placeholder="ΑΕΜ">
		    	<div id="aemTotal"></div>
		    </div>
			<button type="submit" name="search" class="btn btn-dark">Επόμενο</button>
			</form>
		</div>
	';
	if (isset($_POST['search'])){
		$_POST['idToPrintPDF'] = filter_var($_POST['idToPrintPDF'],FILTER_SANITIZE_NUMBER_FLOAT);
		if (isset($_POST['idToPrintPDF']) AND !empty($_POST['idToPrintPDF']) ){	
			$borrows = array();
			$pieces = explode(' ', $_POST['idToPrintPDF']);
			$_SESSION['aem_borrow'] = array_pop($pieces);
			$findUserQuerySQL = "SELECT * FROM users_svds WHERE aem= :aemUser"; 
		 	$findUserQuerySTMT =$db->prepare($findUserQuerySQL);
		 	$findUserQuerySTMT->bindParam(':aemUser', $_SESSION['aem_borrow'], PDO::PARAM_INT);
		 	$findUserQuerySTMT->execute();
		 	while($findUserQuerySTMTResult=$findUserQuerySTMT->fetch(PDO::FETCH_ASSOC)){
		 		$_SESSION['fullName'] = $findUserQuerySTMTResult['last_name'] .' '.$findUserQuerySTMTResult['first_name'];
				$userQuerySQL = "SELECT * FROM borrow_svds WHERE id_user_borrow= :idUser"; 
			 	$userQuerySTMT =$db->prepare($userQuerySQL);
			 	$userQuerySTMT->bindParam(':idUser', $findUserQuerySTMTResult['id'], PDO::PARAM_INT);
			 	$userQuerySTMT->execute();
			 	if ($userQuerySTMT->rowCount() > 0){
				 	while($userQuerySTMTResult=$userQuerySTMT->fetch(PDO::FETCH_ASSOC)){
				 		if ($userQuerySTMTResult['history_flag'] == 1){
				 			if ($userQuerySTMTResult['extend_reason'] != NULL){
						  	 	$_SESSION['borrowReason'] = $userQuerySTMTResult['extend_reason']; 
						  	}elseif ($userQuerySTMTResult['borrow_reason'] != NULL){
						  		$_SESSION['borrowReason'] = $userQuerySTMTResult['borrow_reason'];	
						  	}else{
						  		$_SESSION['borrowReason'] = "Δεν υπάρχει λόγος δανειμού";
						  	}

				 			$_SESSION['start_date'] = date("d-m-Y", strtotime($userQuerySTMTResult['start_date'])); 
				 			$_SESSION['expire_date'] = date("d-m-Y", strtotime($userQuerySTMTResult['expire_date'])); 
							$equipQuerySQL = "SELECT * FROM equip_svds WHERE id_equip= :idEquip"; 
						 	$equipQuerySTMT =$db->prepare($equipQuerySQL);
						 	$equipQuerySTMT->bindParam(':idEquip', $userQuerySTMTResult['id_equip_borrow'], PDO::PARAM_INT);
						 	$equipQuerySTMT->execute();
						 	while($equipQuerySTMTResult=$equipQuerySTMT->fetch(PDO::FETCH_ASSOC)){
						 		$descriptionQuerySQL = "SELECT * FROM description_svds WHERE id_desc= :idDesc"; 
							 	$descriptionQuerySTMT =$db->prepare($descriptionQuerySQL);
							 	$descriptionQuerySTMT->bindParam(':idDesc', $equipQuerySTMTResult['short_desc_e'], PDO::PARAM_INT);
							 	$descriptionQuerySTMT->execute();
							 	while ($descriptionQuerySTMTResult=$descriptionQuerySTMT->fetch(PDO::FETCH_ASSOC)){
							 		$borrows[] = $equipQuerySTMTResult['name_e'].' (Αιτιολόγηση: '.$descriptionQuerySTMTResult['short_desc'].')';	
							 	}
							}
						}	
					}	
						echo '
							<div class="container">
							Ο χρήστης '.$_SESSION['fullName'].' με ΑΕΜ: '.$_SESSION['aem_borrow'].' έχει δανεισθεί τα παρακάτω εξαρτήματα:	
							<br>
							<form method="POST" target="_blank">
						';	
						foreach ($borrows as $equip) {
							echo '
								<input type="checkbox" name="check_list[]" value="'.$equip.'"> '.$equip.'<br>
							';	
						}
						echo'	
							<button type="submit" name="print" id="print" class="btn btn-dark">Εκτύπωση</button>
							<button type="submit" name="printReturn" id="printReturn" class="btn btn-dark">Έντυπο επιστροφής</button>
							</form>
							</div>
						';
				}else {
					echo '<p class="alert alert-info">Δεν υπάρχουν ενεργοί δανεισμοί για τον χρήστη με αυτό το ΑΕΜ.</p>';
				}
			}		
		}	 
	} 

	if(isset($_POST['print'])){
		if(!empty($_POST['check_list'])){
			$itemsToPrint = "<ul>";
			foreach($_POST['check_list'] as $selected){
				$itemsToPrint .= "<li>$selected</li>";
			}
			$itemsToPrint .= "</ul>";
			PDFPrint ($_SESSION['fullName'], $_SESSION['aem_borrow'], $type, $itemsToPrint, $_SESSION['borrowReason'], $_SESSION['start_date'], $_SESSION['expire_date']);		
		}else {
			echo '<p class="alert alert-info">Δεν έχετε επιλέξει εξαρτήματα για εκτύπωση.</p>';	
		}
	}

	if(isset($_POST['printReturn'])){
		if(!empty($_POST['check_list'])){
			$itemsToPrint = "<ul>";
			foreach($_POST['check_list'] as $selected){
				$itemsToPrint .= "<li>$selected</li>";
			}
			$itemsToPrint .= "</ul>";
			PDFPrintReturn ($_SESSION['fullName'], $_SESSION['aem_borrow'], $type, $itemsToPrint, $_SESSION['borrowReason'], $_SESSION['start_date'], $_SESSION['expire_date']);		
		}else {
			echo '<p class="alert alert-info">Δεν έχετε επιλέξει εξαρτήματα για εκτύπωση.</p>';	
		}
	}
}else {
    header("Location: index.php");
    die("Δεν δικαιώματα εισόδου σε αυτή τη σελίδα.");
}	

include("views/footer.php");
echo '
	</body>
	</html>
';
?>