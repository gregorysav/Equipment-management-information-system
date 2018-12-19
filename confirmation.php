<?php
include("variables_file.php");
include("views/connection.php"); 
include("views/header.php");
include("views/navbar.php");

	$userId= $_SESSION['aem'];
	$confirmQuerySQL = "SELECT * FROM borrow_svds WHERE confirmation_borrow = :zero";
	$confirmQuerySTMT = $db->prepare($confirmQuerySQL);
	$confirmQuerySTMT->bindParam(':zero', $zero, PDO::PARAM_INT);
    $confirmQuerySTMT->execute();   
    $confirmQuerySTMTRows = $confirmQuerySTMT->rowCount();
	if ($confirmQuerySTMTRows == 0){
				echo '<div class="container">Δεν βρέθηκαν δανεισμοί προς επιβεβαίωση.</div>';
	}else { 
		while($confirmQuerySTMTResult=$confirmQuerySTMT->fetch(PDO::FETCH_ASSOC)){
			$borrowExplanationMessage = "Αρχική Χρέωση : ".' '.$confirmQuerySTMTResult['borrow_reason'];	
			if ($confirmQuerySTMTResult['extend_reason'] != NULL){
				$borrowExplanationMessage = $confirmQuerySTMTResult['extend_reason'];
			}
			$userToBorrow = $confirmQuerySTMTResult['aem_borrow'];
			$borrowerQuerySQL = "SELECT * FROM users_svds WHERE id = :userToBorrow";
			$borrowerQuerySTMT = $db->prepare($borrowerQuerySQL);
			$borrowerQuerySTMT->bindParam(':userToBorrow', $userToBorrow, PDO::PARAM_INT); 
			$borrowerQuerySTMT->execute();
			while($borrowerQuerySTMTResult=$borrowerQuerySTMT->fetch(PDO::FETCH_ASSOC)){
				$idEquipToBorrow = $confirmQuerySTMTResult['id_equip_borrow'];	 	
				$equipBorrowQuerySQL = "SELECT * FROM equip_svds WHERE id_equip = :idEquipToBorrow";
				$equipBorrowQuerySTMT = $db->prepare($equipBorrowQuerySQL);
				$equipBorrowQuerySTMT->bindParam(':idEquipToBorrow', $idEquipToBorrow, PDO::PARAM_INT);
				$equipBorrowQuerySTMT->execute();
				while($equipBorrowQuerySTMTResult=$equipBorrowQuerySTMT->fetch(PDO::FETCH_ASSOC)){
			 		echo '
						<div class="container" id="confirmation">
						Ο χρήστης '.$borrowerQuerySTMTResult['last_name'].' '.$borrowerQuerySTMTResult['first_name'].' (ΑΕΜ= '.$userToBorrow.') θέλει να δανεισθεί:
						<ul>
							<li>'.$equipBorrowQuerySTMTResult['name_e'].'</li>
						</ul>
						από '.date('d/m/Y',strtotime($confirmQuerySTMTResult['start_date'])).' μέχρι '.date('d/m/Y',strtotime($confirmQuerySTMTResult['expire_date'])).' <br>
						<textarea id="borrowExplain">'.$borrowExplanationMessage.'</textarea><br>
						<button class="btn btn-primary confirm" id_to_confirm='.$confirmQuerySTMTResult['id_borrow'].'>Επιβεβαίωση</button>
						</div> 
					';	
				} 
			}	
		}		      
	}
					  	
include("views/footer.php");
?>