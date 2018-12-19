<?php
include "variables_file.php";
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");
include ("functions.php");

echo '<h3 id="title">Σελίδα εκτύπωσης εντύπου χρέωσης εξοπλισμού</h3>';

echo '
	<div class="container" id="aemSearch">
		<form method="POST">
		<div class="form-group">
			<label for="idToPrintPDF">Δώσε το ΑΕΜ χρήστη για την εκτύπωση εντύπου: </label>
		    <input type="text" class="form-control" name="idToPrintPDF" id="idToPrintPDF" placeholder="ΑΕΜ">
	    	 <div id="aemTotal"></div>
	    </div>
		<button type="submit" name="search" class="btn btn-primary">Επόμενο</button>
		</form>
	</div>
';
if (isset($_POST['search'])){
	$_POST['idToPrintPDF'] = filter_var($_POST['idToPrintPDF'],FILTER_SANITIZE_NUMBER_FLOAT);
	if (isset($_POST['idToPrintPDF']) AND !empty($_POST['idToPrintPDF']) ){	
		$borrows = array();
		$pieces = explode(' ', $_POST['idToPrintPDF']);
		$_SESSION['aem_borrow'] = array_pop($pieces);
		$userQuerySQL = "SELECT * FROM borrow_svds WHERE aem_borrow= :idUser"; 
	 	$userQuerySTMT =$db->prepare($userQuerySQL);
	 	$userQuerySTMT->bindParam(':idUser', $_SESSION['aem_borrow'], PDO::PARAM_INT);
	 	$userQuerySTMT->execute();
	 	if ($userQuerySTMT->rowCount() > 0){
		 	while($userQuerySTMTResult=$userQuerySTMT->fetch(PDO::FETCH_ASSOC)){
	 			$_SESSION['borrowReason'] = $userQuerySTMTResult['borrow_reason']; 
	 			$_SESSION['start_date'] = date("d-m-Y", strtotime($userQuerySTMTResult['start_date'])); 
	 			$_SESSION['expire_date'] = date("d-m-Y", strtotime($userQuerySTMTResult['expire_date'])); 
				$equipQuerySQL = "SELECT * FROM equip_svds WHERE id_equip= :idEquip"; 
			 	$equipQuerySTMT =$db->prepare($equipQuerySQL);
			 	$equipQuerySTMT->bindParam(':idEquip', $userQuerySTMTResult['id_equip_borrow'], PDO::PARAM_INT);
			 	$equipQuerySTMT->execute();
			 	while($equipQuerySTMTResult=$equipQuerySTMT->fetch(PDO::FETCH_ASSOC)){
					$borrows[] = $equipQuerySTMTResult['name_e'];
				}
				$nameQuerySQL = "SELECT * FROM users_svds WHERE aem= :idUser"; 
			 	$nameQuerySTMT =$db->prepare($nameQuerySQL);
			 	$nameQuerySTMT->bindParam(':idUser', $_SESSION['aem_borrow'], PDO::PARAM_INT);
			 	$nameQuerySTMT->execute();
			 	while($nameQuerySTMTResult=$nameQuerySTMT->fetch(PDO::FETCH_ASSOC)){
					$_SESSION['fullName'] = $nameQuerySTMTResult['last_name'] .' '.$nameQuerySTMTResult['first_name'];  
				}
			}	
			echo '
				<div class="container">
				Ο χρήστης '.$_SESSION['fullName'].' με ΑΕΜ: '.$_SESSION['aem_borrow'].' έχει δανεισθεί τα παρακάτω εξαρτήματα:	
				<br>
				<form method="POST">
			';	
		foreach ($borrows as $equip) {
			echo '
				<input type="checkbox" name="check_list[]" value="'.$equip.'"> '.$equip.'<br>
			';	
		}
		echo'	
			<button type="submit" name="print" id="print" class="btn btn-primary">Εκτύπωση</button>
			</form>
			</div>
		';
	}else {
		echo '<p class="alert alert-info">Δεν υπάρχουν ενεργοί δανεισμοί για τον χρήστη με αυτό το ΑΕΜ.</p>';
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
		PDFPrint ($_SESSION['fullName'], $_SESSION['aem_borrow'], $itemsToPrint, $_SESSION['borrowReason'], $_SESSION['start_date'], $_SESSION['expire_date']);		
	}else {
		echo '<p class="alert alert-info">Δεν έχετε επιλέξει εξαρτήματα για εκτύπωση.</p>';	
	}
}




include("views/footer.php");

?>