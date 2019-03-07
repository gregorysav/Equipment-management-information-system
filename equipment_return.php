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

	echo '
		<h2 id="title">Σελίδα Ενεργών Δανεισμών</h2>
		<div class="container" id="searchHolder">
			<form class="form-group form-inline" id="returnOptions" method="POST">
				<label for="controlSelect">Επιλογές Αναζήτησης</label>
				<select class="dropdown" id="controlSelect" name="search">
					<option value="0" selected>Επιλογές</option>
					<option value="1">Όνομα Δανειστή</option>
					<option value="2">ΑΕΜ</option>
					<option value="3">Όνομα Εξαρτήματος</option>
					<option value="4">Εύρος Ημερομηνιών</option>
				</select>
			</form>
		</div><br>	
	';
	$limitPagination = 10;
//  Η μεταβλητή $_GET['p'] και ελέγχει τη σελίδα που βρισκόμαστε βάση του pagination	
	if (isset($_GET['p'])){
    	$pageOfPagination = filter_var($_GET['p'],FILTER_SANITIZE_NUMBER_FLOAT);
        $startPagination = ($pageOfPagination- 1) * $limitPagination;
    }

	$baseQuerySQL = "SELECT * FROM borrow_svds INNER JOIN equip_svds on borrow_svds.id_equip_borrow = equip_svds.id_equip WHERE history_flag= :history_flag ORDER BY notify10 ASC LIMIT :startPagination, :limitPagination"; 
	$baseQuerySTMT = $db->prepare($baseQuerySQL);
	$baseQuerySTMT->bindParam(':history_flag', $one, PDO::PARAM_INT);
	$baseQuerySTMT->bindParam(':startPagination', $startPagination, PDO::PARAM_INT);
	$baseQuerySTMT->bindParam(':limitPagination', $limitPagination, PDO::PARAM_INT); 
	$baseQuerySTMT->execute();
	if ($baseQuerySTMT->rowCount() == 0){
		echo '<p class="alert alert-warning">Η λίστα ενεργών δανεισμών είναι κενή.</p>';
	} else {
		echo "<br>";
		$tableTop = '
			<div class="container returnEquipmentTable">
			<div class="table-responsive">
			<table class="table table-bordered">	
			<thead class="thead-dark">
			<tr>
	    		<th>Ονοματεπώνυμο</th>
	    		<th>ΑΕΜ</th>
	    		<th>Εξάρτημα</th>
	    		<th>Έναρξη-Λήξη</th>
	    		<th>Προθεσμία</th>
	    		<th>Επιβεβαίωση</th>
	    		<th>Αιτιολόγηση</th>
	    		<th>Υπενθύμιση</th>
	    		<th>Ενέργειες</th>
	  		</tr>
	  		</thead>
		';


	if (isset($_POST['search'])){
		if ($_POST['search'] == "1"){
			echo '<div class="container selectOptionInput">
			 		<h4 id="title">Δώσε Όνομα Δανειστή</h4>
				  	<form name="form" method="post"><input type="text" name="borrowerNameQuery"  class="form-control input-lg selectOption" id="borrowerNameQuery" autocomplete="off" placeholder="Όνομα Δανειστή" required/></form>
				</div><br>
			';    	
		}elseif ($_POST['search'] == "2"){
			echo '<div class="container selectOptionInput">
					<h4 id="title">Δώσε ΑΕΜ Δανειστή</h4>
					<form name="form" method="post"><input type="text" name="borrowerAEMQuery"  class="form-control input-lg selectOption" id="borrowerAEMQuery" autocomplete="off" placeholder="Όνομα ΑΕΜ Δανειστή" required/></form>
			    </div><br>
			';
		}elseif ($_POST['search'] == "3"){
			echo '<div class="container selectOptionInput">
					<h4 id="title">Δώσε Όνομα Εξαρτήματος</h4>
					<form name="form" method="post"><input type="text" name="equipmentNameQuery" class="form-control input-lg selectOption" id="equipmentNameQuery" autocomplete="off" placeholder="Όνομα εξαρτήματος"/ required></form>
		    	</div><br>
			';
		}elseif ($_POST['search'] == "4"){
			echo '<div class="container selectOptionInput">
					<h4 id="title">Δώσε Χρονικό Εύρος Δανεισμού</h4>  
	              	<form name="form" method="post">
	                	Από:<input type="date" id="startDate" name="startDate" class="form-control input-lg"/><br>
	                	Εώς:<input type="date" id="endDate" name="endDate" class="form-control input-lg"/>
	                	<input class="btn btn-dark" type="submit" name="search" value="Αναζήτηση" required/>
	            	</form>
	            </div><br>
			';
		}
	}
//  Η μεταβλητή $url έχει τεθεί από το $_SERVER['REQUEST_URI'] και ελέγχει το ακριβές url που έχει η σελίδα που βρισκόμαστε	
	$url = $_SERVER['REQUEST_URI'];
		$value=(explode("=", $url));
		if (isset($value[1]) AND $value[1] == ""){
			echo $tableTop;
		while($baseQuerySTMTResult=$baseQuerySTMT->fetch(PDO::FETCH_ASSOC)){
			if ($baseQuerySTMTResult['history_flag'] != 0 AND $baseQuerySTMTResult['confirmation_borrow'] == 1){
				$now = new DateTime();
				$dateToCheck = new DateTime($baseQuerySTMTResult['expire_date']);
				if($dateToCheck >= $now) {
					$borrowState = '<tr id="borrowStateActive">';
				}else {
						$borrowState ='<tr id="borrowStateNotActive">';
				}
				$nowDate =  strtotime(date("d-m-Y"));
				$dateToCheck = strtotime(date('d-m-Y',strtotime($baseQuerySTMTResult['expire_date'])));
				$dateDiff = round(($dateToCheck - $nowDate) / 86400);

				$nameQuerySQL = "SELECT * FROM users_svds WHERE id= :borrowUser"; 
	      		$nameQuerySTMT = $db->prepare($nameQuerySQL);
	      		$nameQuerySTMT->bindParam(':borrowUser', $baseQuerySTMTResult['id_user_borrow']);
	      		$nameQuerySTMT->execute();
	      		while($nameQuerySTMTResult=$nameQuerySTMT->fetch(PDO::FETCH_ASSOC)){
	      			$last_name= $nameQuerySTMTResult['last_name'];
	      			$first_name= $nameQuerySTMTResult['first_name'];
	      			$aem = $nameQuerySTMTResult['aem'];
	      			$telephone= $nameQuerySTMTResult['telephone'];
	      			$email= $nameQuerySTMTResult['email'];
	      			$full_name= $last_name.' '.$first_name;
	      		}
	      		$popoverMessage = "User : $last_name $first_name <br> AEM: $aem <br> Phone number: $telephone <br> Email: $email";
				echo '
					'.$borrowState.'
			    	<td class="showBorrowerInfo" data-toggle="popover" title="Πληροφορίες" data-html="true" data-placement="top" data-content="'.$popoverMessage.'"><a href=borrow_user.php?id_user='.$aem.'>'.$last_name.' '.$first_name.'</a></td>
			    	<td><a href=borrow_user.php?id_user='.$aem.'>'.$aem.'</a></td>
			    	<td><a href=equipment_details.php?id_equip='.$baseQuerySTMTResult['id_equip'].'>'.$baseQuerySTMTResult['name_e'] .'</a></td>
			    	<td>'.date('d/m/Y',strtotime($baseQuerySTMTResult['start_date'])).' εώς '.date('d/m/Y',strtotime($baseQuerySTMTResult['expire_date'])).'</td>
			    	<td>'.$dateDiff.' ημέρες</td>
			    ';
			    if ($baseQuerySTMTResult['confirmation_borrow'] == 1){
			    	echo '<td>Επιβεβαιώθηκε</td>';
			  	}else{
			  		echo '<td id="notAprrovedBorrow"><a href=confirmation.php>Εκκρεμεί</a></td>';
			  	}
			  	if ($baseQuerySTMTResult['extend_reason'] != NULL){
			  		echo '	 
			  			<td>'.$baseQuerySTMTResult['extend_reason'] .'</td>
			  	 	';
			  	}elseif ($baseQuerySTMTResult['borrow_reason'] != NULL){
			  		echo '	 
			  			<td>'.$baseQuerySTMTResult['borrow_reason'] .'</td>
			  	 	';	
			  	}else{
			  		echo '	 
			  			<td>Δεν υπάρχει λόγος δανειμού.</td>
			  	 	';
			  	}
			  	echo '
			  		<td><form method="POST"><input type="hidden" name="full_name" value="'.$full_name.'"><input type="hidden" name="telephone" value="'.$telephone.'"><input type="hidden" name="aem_borrow" value="'.$aem.'"><input type="hidden" name="dateDiff" value='.$dateDiff.'><input type="hidden" name="equipmentName" value="'.$baseQuerySTMTResult['name_e'].'"><input type="hidden" name="email" value="'.$email.'"><button class="btn btn-dark" name="reminder">Αποστολή</button></form></td>
			  	 	<td id="buttonToReturnEquipment"><a href=borrow_delete.php?id_borrow='.$baseQuerySTMTResult['id_borrow'].'&id_equip='.$baseQuerySTMTResult['id_equip'].'&id_user_borrow='.$$baseQuerySTMTResult['id_user_borrow'].' class="btn btn-dark returnEquipment" id_equip='.$baseQuerySTMTResult['id_equip'].'>Επιστροφή</a><a href=borrow_change.php?id_borrow='.$baseQuerySTMTResult['id_borrow'].' class="btn btn-dark" >Επεξεργασία</a><a href=borrow_transfer.php?id_borrow='.$baseQuerySTMTResult['id_borrow'].' class="btn btn-dark" >Μεταφορά</a></td>
			  		</tr>
			  		</body>
				';
				}
			}
		}

	if (isset($_POST['equipmentNameQuery'])){
						
		$equipmentName = filter_var($_POST['equipmentNameQuery'],FILTER_SANITIZE_STRING);
		$searchEquipmentQuerySQL = "SELECT * FROM equip_svds WHERE name_e LIKE :keyword";
		$searchEquipmentQuerySTMT = $db->prepare($searchEquipmentQuerySQL);	
		$searchEquipmentQuerySTMT->bindParam(':keyword', $equipmentName); 
		$searchEquipmentQuerySTMT->execute();
		if ($searchEquipmentQuerySTMT->rowCount() > 0){
    		while($searchEquipmentQuerySTMTResult=$searchEquipmentQuerySTMT->fetch(PDO::FETCH_ASSOC)){
    				
	    		$baseQuerySQL = "SELECT * FROM borrow_svds INNER JOIN equip_svds on borrow_svds.id_equip_borrow = equip_svds.id_equip WHERE id_equip_borrow= :idEquip ORDER BY notify10 ASC"; 
				$baseQuerySTMT = $db->prepare($baseQuerySQL);
				$baseQuerySTMT->bindParam(':idEquip', $searchEquipmentQuerySTMTResult['id_equip']);
				$baseQuerySTMT->execute();
	    		if ($baseQuerySTMT->rowCount() > 0 ){
	    			echo $tableTop;
	    			while($baseQuerySTMTResult=$baseQuerySTMT->fetch(PDO::FETCH_ASSOC)){
	    				if ($baseQuerySTMTResult['history_flag'] != 0 AND $baseQuerySTMTResult['confirmation_borrow'] == 1){
		    				$now = new DateTime();
							$dateToCheck = new DateTime($baseQuerySTMTResult['expire_date']);
							if($dateToCheck >= $now) {
	    						$borrowState ='<tr id="borrowStateActive">';
							}else {
	  							$borrowState ='<tr id="borrowStateNotActive">';
							}
							$nowDate =  strtotime(date("d-m-Y"));
							$dateToCheck = strtotime(date('d-m-Y',strtotime($baseQuerySTMTResult['expire_date'])));
							$dateDiff = round(($dateToCheck - $nowDate) / 86400);

							$nameQuerySQL = "SELECT * FROM users_svds WHERE id= :borrowUser"; 
				      		$nameQuerySTMT = $db->prepare($nameQuerySQL);
				      		$nameQuerySTMT->bindParam(':borrowUser', $baseQuerySTMTResult['id_user_borrow']);
				      		$nameQuerySTMT->execute();
				      		while($nameQuerySTMTResult=$nameQuerySTMT->fetch(PDO::FETCH_ASSOC)){
				      			$last_name= $nameQuerySTMTResult['last_name'];
				      			$first_name= $nameQuerySTMTResult['first_name'];
				      			$aem = $nameQuerySTMTResult['aem'];
				      			$telephone= $nameQuerySTMTResult['telephone'];
				      			$email= $nameQuerySTMTResult['email'];
				      			$full_name= $last_name.' '.$first_name;
				      		}
				      		$popoverMessage = "User : $last_name $first_name <br> AEM: $aem <br> Phone number: $telephone <br> Email: $email";
							echo '
								'.$borrowState.'
								<td class="showBorrowerInfo" data-toggle="popover" title="Πληροφορίες" data-html="true" data-placement="top" data-content="'.$popoverMessage.'"><a href=borrow_user.php?id_user='.$aem.'>'.$last_name.' '.$first_name.'</a></td>
			    				<td><a href=borrow_user.php?id_user='.$aem.'>'.$aem.'</a></td>
						    	<td><a href=equipment_details.php?id_equip='.$baseQuerySTMTResult['id_equip'].'>'.$baseQuerySTMTResult['name_e'] .'</a></td>
						    	<td>'.date('d/m/Y',strtotime($baseQuerySTMTResult['start_date'])).' εώς '.date('d/m/Y',strtotime($baseQuerySTMTResult['expire_date'])).'</td>
						    	<td>'.$dateDiff.' ημέρες</td>
						    ';
						    if ($baseQuerySTMTResult['confirmation_borrow'] == 1){
						    	echo '<td>Επιβεβαιώθηκε</td>';
						  	}else{
						  		echo '<td id="notAprrovedBorrow"><a href=confirmation.php>Εκκρεμεί</a></td>';
						  	}
						  	if ($baseQuerySTMTResult['extend_reason'] != NULL){
						  		echo '	 
						  			<td>'.$baseQuerySTMTResult['extend_reason'] .'</td>
						  	 	';
						  	}elseif ($baseQuerySTMTResult['borrow_reason'] != NULL){
						  		echo '	 
						  			<td>'.$baseQuerySTMTResult['borrow_reason'] .'</td>
						  	 	';	
						  	}else{
						  		echo '	 
						  			<td>Δεν υπάρχει λόγος δανειμού.</td>
						  	 	';
						  	}
						  	echo '
						  		<td><form method="POST"><input type="hidden" name="full_name" value="'.$full_name.'"><input type="hidden" name="telephone" value="'.$telephone.'"><input type="hidden" name="aem_borrow" value="'.$aem.'"><input type="hidden" name="dateDiff" value='.$dateDiff.'><input type="hidden" name="equipmentName" value="'.$baseQuerySTMTResult['name_e'].'"><input type="hidden" name="email" value="'.$email.'"><button class="btn btn-dark" name="reminder">Αποστολή</button></form></td>
						  	 	<td id="buttonToReturnEquipment"><a href=borrow_delete.php?id_borrow='.$baseQuerySTMTResult['id_borrow'].'&id_equip='.$baseQuerySTMTResult['id_equip'].'&id_user_borrow='.$baseQuerySTMTResult['id_user_borrow'].' class="btn btn-dark returnEquipment" id_equip='.$baseQuerySTMTResult['id_equip'].'>Επιστροφή</a><a href=borrow_change.php?id_borrow='.$baseQuerySTMTResult['id_borrow'].' class="btn btn-dark">Επεξεργασία</a><a href=borrow_transfer.php?id_borrow='.$baseQuerySTMTResult['id_borrow'].' class="btn btn-dark" >Μεταφορά</a></td>
						  		</tr>
							';
						}	
					}
	    		}else {
	    			echo '<br><span class="alert alert-warning">Δεν υπάρχει ενεργός δανεισμός για αυτό το εξάρτημα.</span><br>';
	    		}	
			}	
		}else{
			echo '<br><span class="alert alert-warning">Το εξάρτημα που αναζητήσατε δεν υπάρχει.</span><br>';
		}
	}elseif (isset($_POST['borrowerAEMQuery'])){
					
		$borrowerAEM = filter_var(($_POST['borrowerAEMQuery']),FILTER_SANITIZE_NUMBER_FLOAT);
		$userQuerySQL = "SELECT * FROM users_svds WHERE aem= :aemToFindUser"; 
		$userQuerySTMT = $db->prepare($userQuerySQL);
		$userQuerySTMT->bindParam(':aemToFindUser', $borrowerAEM);
		$userQuerySTMT->execute();
		while($userQuerySTMTResult=$userQuerySTMT->fetch(PDO::FETCH_ASSOC)){
			$idToFindUser = $userQuerySTMTResult['id'];
			$last_name= $userQuerySTMTResult['last_name'];
  			$first_name= $userQuerySTMTResult['first_name'];
  			$aem = $userQuerySTMTResult['aem'];
  			$telephone= $userQuerySTMTResult['telephone'];
  			$email= $userQuerySTMTResult['email'];
  			$full_name= $last_name.' '.$first_name;
		}	

		$baseQuerySQL = "SELECT * FROM borrow_svds INNER JOIN equip_svds on borrow_svds.id_equip_borrow = equip_svds.id_equip WHERE id_user_borrow= :idToFindUser ORDER BY notify10 ASC"; 
		$baseQuerySTMT = $db->prepare($baseQuerySQL);
		$baseQuerySTMT->bindParam(':idToFindUser', $idToFindUser);
		$baseQuerySTMT->execute();
		if ($baseQuerySTMT->rowCount() > 0){
			echo $tableTop;
			while($baseQuerySTMTResult=$baseQuerySTMT->fetch(PDO::FETCH_ASSOC)){
				if ($baseQuerySTMTResult['history_flag'] != 0 AND $baseQuerySTMTResult['confirmation_borrow'] == 1){	
					$now = new DateTime();
					$dateToCheck = new DateTime($baseQuerySTMTResult['expire_date']);
					if($dateToCheck >= $now) {
						$borrowState = '<tr id="borrowStateActive">';
					}else {
						$borrowState ='<tr id="borrowStateNotActive">';
					}
					$nowDate =  strtotime(date("d-m-Y"));
					$dateToCheck = strtotime(date('d-m-Y',strtotime($baseQuerySTMTResult['expire_date'])));
					$dateDiff = round(($dateToCheck - $nowDate) / 86400);

		      		$popoverMessage = "User : $last_name $first_name <br> AEM: $aem <br> Phone number: $telephone <br> Email: $email";
					echo '
						'.$borrowState.'
				    	<td class="showBorrowerInfo" data-toggle="popover" title="Πληροφορίες" data-html="true" data-placement="top" data-content="'.$popoverMessage.'"><a href=borrow_user.php?id_user='.$aem.'>'.$last_name.' '.$first_name.'</a></td>
			    		<td><a href=borrow_user.php?id_user='.$aem.'>'.$aem.'</a></td>
				    	<td><a href=equipment_details.php?id_equip='.$baseQuerySTMTResult['id_equip'].'>'.$baseQuerySTMTResult['name_e'] .'</a></td>
				    	<td>'.date('d/m/Y',strtotime($baseQuerySTMTResult['start_date'])).' εώς '.date('d/m/Y',strtotime($baseQuerySTMTResult['expire_date'])).'</td>
				    	<td>'.$dateDiff.' ημέρες</td>
				    ';
				    if ($baseQuerySTMTResult['confirmation_borrow'] == 1){
				    	echo '<td>Επιβεβαιώθηκε</td>';
				  	}else{
				  		echo '<td id="notAprrovedBorrow"><a href=confirmation.php>Εκκρεμεί</a></td>';
				  	}
				  	if ($baseQuerySTMTResult['extend_reason'] != NULL){
				  		echo '	 
				  			<td>'.$baseQuerySTMTResult['extend_reason'] .'</td>
				  	 	';
				  	}elseif ($baseQuerySTMTResult['borrow_reason'] != NULL){
				  		echo '	 
				  			<td>'.$baseQuerySTMTResult['borrow_reason'] .'</td>
				  	 	';	
				  	}else{
				  		echo '	 
				  			<td>Δεν υπάρχει λόγος δανειμού.</td>
				  	 	';
				  	}
				  	echo '
				  		<td><form method="POST"><input type="hidden" name="full_name" value="'.$full_name.'"><input type="hidden" name="telephone" value="'.$telephone.'"><input type="hidden" name="aem_borrow" value="'.$aem.'"><input type="hidden" name="dateDiff" value='.$dateDiff.'><input type="hidden" name="equipmentName" value="'.$baseQuerySTMTResult['name_e'].'"><input type="hidden" name="email" value="'.$email.'"><button class="btn btn-dark" name="reminder">Αποστολή</button></form></td>
				  	 	<td id="buttonToReturnEquipment"><a href=borrow_delete.php?id_borrow='.$baseQuerySTMTResult['id_borrow'].'&id_equip='.$baseQuerySTMTResult['id_equip'].'&id_user_borrow='.$idToFindUser.' class="btn btn-dark returnEquipment" id_equip='.$baseQuerySTMTResult['id_equip'].'>Επιστροφή</a><a href=borrow_change.php?id_borrow='.$baseQuerySTMTResult['id_borrow'].' class="btn btn-dark">Επεξεργασία</a><a href=borrow_transfer.php?id_borrow='.$baseQuerySTMTResult['id_borrow'].' class="btn btn-dark" >Μεταφορά</a></td>
				  		</tr>
					';	
				}		
			}
		}else{
			echo '<br><span class="alert alert-warning">Το ΑΕΜ δεν αντιστοιχεί σε κάποιο χρήστη.</span><br>';
		}	
	}elseif (isset($_POST['borrowerNameQuery'])){
					
		$borrowerName = filter_var($_POST['borrowerNameQuery'],FILTER_SANITIZE_STRING);
		$fullName = (explode(" ", $borrowerName));
		$lastName = $fullName[0];
		$firstName = $fullName[1];

		$searchEquipmentQuerySQL = "SELECT * FROM users_svds WHERE last_name LIKE :lastName AND first_name LIKE :firstName";
		$searchEquipmentQuerySTMT = $db->prepare($searchEquipmentQuerySQL);	
		$searchEquipmentQuerySTMT->bindParam(':lastName', $lastName);
		$searchEquipmentQuerySTMT->bindParam(':firstName', $firstName); 
		$searchEquipmentQuerySTMT->execute();
		
		while($searchEquipmentQuerySTMTResult=$searchEquipmentQuerySTMT->fetch(PDO::FETCH_ASSOC)){
			
    		$baseQuerySQL = "SELECT * FROM borrow_svds INNER JOIN equip_svds on borrow_svds.id_equip_borrow = equip_svds.id_equip WHERE id_user_borrow= :idUser ORDER BY notify10 ASC"; 
			$baseQuerySTMT = $db->prepare($baseQuerySQL);
			$baseQuerySTMT->bindParam(':idUser', $searchEquipmentQuerySTMTResult['id']);
			$baseQuerySTMT->execute();
    		if ($baseQuerySTMT->rowCount() > 0){
    			echo $tableTop;
    			while($baseQuerySTMTResult=$baseQuerySTMT->fetch(PDO::FETCH_ASSOC)){
    				if ($baseQuerySTMTResult['history_flag'] != 0 AND $baseQuerySTMTResult['confirmation_borrow'] == 1){
		    			$now = new DateTime();
						$dateToCheck = new DateTime($baseQuerySTMTResult['expire_date']);
						if($dateToCheck >= $now) {
							$borrowState = '<tr id="borrowStateActive">';
						}else {
								$borrowState ='<tr id="borrowStateNotActive">';
						}
						$nowDate =  strtotime(date("d-m-Y"));
						$dateToCheck = strtotime(date('d-m-Y',strtotime($baseQuerySTMTResult['expire_date'])));
						$dateDiff = round(($dateToCheck - $nowDate) / 86400);
						$nameQuerySQL = "SELECT * FROM users_svds WHERE id= :borrowUser"; 
			      		$nameQuerySTMT = $db->prepare($nameQuerySQL);
			      		$nameQuerySTMT->bindParam(':borrowUser', $baseQuerySTMTResult['id_user_borrow']);
			      		$nameQuerySTMT->execute();
			      		while($nameQuerySTMTResult=$nameQuerySTMT->fetch(PDO::FETCH_ASSOC)){
			      			$last_name= $nameQuerySTMTResult['last_name'];
			      			$first_name= $nameQuerySTMTResult['first_name'];
			      			$aem = $nameQuerySTMTResult['aem'];
			      			$telephone= $nameQuerySTMTResult['telephone'];
			      			$email= $nameQuerySTMTResult['email'];
			      			$full_name= $last_name.' '.$first_name;
			      		}
			      		$popoverMessage = "User : $last_name $first_name <br> AEM: $aem <br> Phone number: $telephone <br> Email: $email";
						echo '
							'.$borrowState.'
				    	<td class="showBorrowerInfo" data-toggle="popover" title="Πληροφορίες" data-html="true" data-placement="top" data-content="'.$popoverMessage.'"><a href=borrow_user.php?id_user='.$aem.'>'.$last_name.' '.$first_name.'</a></td>
			    		<td><a href=borrow_user.php?id_user='.$aem.'>'.$aem.'</a></td>
				    	<td><a href=equipment_details.php?id_equip='.$baseQuerySTMTResult['id_equip'].'>'.$baseQuerySTMTResult['name_e'] .'</a></td>
				    	<td>'.date('d/m/Y',strtotime($baseQuerySTMTResult['start_date'])).' εώς '.date('d/m/Y',strtotime($baseQuerySTMTResult['expire_date'])).'</td>
				    	<td>'.$dateDiff.' ημέρες</td>
					    ';
					    if ($baseQuerySTMTResult['confirmation_borrow'] == 1){
					    	echo '<td>Επιβεβαιώθηκε</td>';
					  	}else{
					  		echo '<td id="notAprrovedBorrow"><a href=confirmation.php>Εκκρεμεί</a></td>';
					  	}
					  	if ($baseQuerySTMTResult['extend_reason'] != NULL){
					  		echo '	 
					  			<td>'.$baseQuerySTMTResult['extend_reason'] .'</td>
					  	 	';
					  	}elseif ($baseQuerySTMTResult['borrow_reason'] != NULL){
					  		echo '	 
					  			<td>'.$baseQuerySTMTResult['borrow_reason'] .'</td>
					  	 	';	
					  	}else{
					  		echo '	 
					  			<td>Δεν υπάρχει λόγος δανειμού.</td>
					  	 	';
					  	}
					  	echo '
					  		<td><form method="POST"><input type="hidden" name="full_name" value="'.$full_name.'"><input type="hidden" name="telephone" value="'.$telephone.'"><input type="hidden" name="aem_borrow" value="'.$aem.'"><input type="hidden" name="dateDiff" value='.$dateDiff.'><input type="hidden" name="equipmentName" value="'.$baseQuerySTMTResult['name_e'].'"><input type="hidden" name="email" value="'.$email.'"><button class="btn btn-dark" name="reminder">Αποστολή</button></form></td>
					  	 	<td id="buttonToReturnEquipment"><a href=borrow_delete.php?id_borrow='.$baseQuerySTMTResult['id_borrow'].'&id_equip='.$baseQuerySTMTResult['id_equip'].'&id_user_borrow='.$baseQuerySTMTResult['id_user_borrow'].' class="btn btn-dark returnEquipment" id_equip='.$baseQuerySTMTResult['id_equip'].'>Επιστροφή</a><a href=borrow_change.php?id_borrow='.$baseQuerySTMTResult['id_borrow'].' class="btn btn-dark">Επεξεργασία</a><a href=borrow_transfer.php?id_borrow='.$baseQuerySTMTResult['id_borrow'].' class="btn btn-dark" >Μεταφορά</a></td>
					  		</tr>
						';
					}
				}
    		}else {
    				echo '<br><span class="alert alert-warning">Το όνομα που εισάγατε δεν αντιστοιχεί σε κάποιο ενεργό δανεισμό.</span><br>';
    		}
		}
	}elseif(isset($_POST['startDate']) AND isset($_POST['endDate'])){
		
		$baseQuerySQL = "SELECT * FROM borrow_svds INNER JOIN equip_svds on borrow_svds.id_equip_borrow = equip_svds.id_equip WHERE start_date >= :startDate AND expire_date <= :endDate ORDER BY expire_date DESC"; 
      		$baseQuerySTMT = $db->prepare($baseQuerySQL);
      		$baseQuerySTMT->bindParam(':startDate', $_POST['startDate']);
      		$baseQuerySTMT->bindParam(':endDate', $_POST['endDate']);
      		$baseQuerySTMT->execute();
        	if ($baseQuerySTMT->rowCount() > 0){
          		echo $tableTop;
          		while($baseQuerySTMTResult=$baseQuerySTMT->fetch(PDO::FETCH_ASSOC)){
          			if ($baseQuerySTMTResult['history_flag'] != 0 AND $baseQuerySTMTResult['confirmation_borrow'] == 1){	
          				$now = new DateTime();
						$dateToCheck = new DateTime($baseQuerySTMTResult['expire_date']);
						if($dateToCheck >= $now) {
    						$borrowState = '<tr id="borrowStateActive">';
						}else {
  							$borrowState ='<tr id="borrowStateNotActive">';
						}
						$nowDate =  strtotime(date("d-m-Y"));
						$dateToCheck = strtotime(date('d-m-Y',strtotime($baseQuerySTMTResult['expire_date'])));
						$dateDiff = round(($dateToCheck - $nowDate) / 86400);
						$nameQuerySQL = "SELECT * FROM users_svds WHERE id= :borrowUser"; 
			      		$nameQuerySTMT = $db->prepare($nameQuerySQL);
			      		$nameQuerySTMT->bindParam(':borrowUser', $baseQuerySTMTResult['id_user_borrow']);
			      		$nameQuerySTMT->execute();
			      		while($nameQuerySTMTResult=$nameQuerySTMT->fetch(PDO::FETCH_ASSOC)){
			      			$last_name= $nameQuerySTMTResult['last_name'];
			      			$first_name= $nameQuerySTMTResult['first_name'];
			      			$aem = $nameQuerySTMTResult['aem'];
			      			$telephone= $nameQuerySTMTResult['telephone'];
			      			$email= $nameQuerySTMTResult['email'];
			      			$full_name= $last_name.' '.$first_name;
			      		}
			      		$popoverMessage = "User : $last_name $first_name <br> AEM: $aem <br> Phone number: $telephone <br> Email: $email";
						echo '
							'.$borrowState.'
              			<td class="showBorrowerInfo" data-toggle="popover" title="Πληροφορίες" data-html="true" data-placement="top" data-content="'.$popoverMessage.'"><a href=borrow_user.php?id_user='.$aem.'>'.$last_name.' '.$first_name.'</a></td>
			    		<td><a href=borrow_user.php?id_user='.$aem.'>'.$aem.'</a></td>
				    	<td><a href=equipment_details.php?id_equip='.$baseQuerySTMTResult['id_equip'].'>'.$baseQuerySTMTResult['name_e'] .'</a></td>
              			<td>'.date('d/m/Y',strtotime($baseQuerySTMTResult['start_date'])).' εώς '.date('d/m/Y',strtotime($baseQuerySTMTResult['expire_date'])).'</td>
              			<td>'.$dateDiff.' ημέρες</td>
            			';
	                	if ($baseQuerySTMTResult['confirmation_borrow'] == 1){
	                  		echo '<td>Επιβεβαιώθηκε</td>';
	                	}else{
	                		echo '<td id="notAprrovedBorrow"><a href=confirmation.php>Εκκρεμεί</a></td>';
	                	}
		                if ($baseQuerySTMTResult['extend_reason'] != NULL){
					  		echo '	 
					  			<td>'.$baseQuerySTMTResult['extend_reason'] .'</td>
					  	 	';
					  	}elseif ($baseQuerySTMTResult['borrow_reason'] != NULL){
					  		echo '	 
					  			<td>'.$baseQuerySTMTResult['borrow_reason'] .'</td>
					  	 	';	
					  	}else{
					  		echo '	 
					  			<td>Δεν υπάρχει λόγος δανειμού.</td>
					  	 	';
					  	}
			                echo '
			                	<td><form method="POST"><input type="hidden" name="full_name" value="'.$full_name.'"><input type="hidden" name="telephone" value="'.$telephone.'"><input type="hidden" name="aem_borrow" value="'.$aem.'"><input type="hidden" name="dateDiff" value='.$dateDiff.'><input type="hidden" name="equipmentName" value="'.$baseQuerySTMTResult['name_e'].'"><input type="hidden" name="email" value="'.$email.'"><button class="btn btn-dark" name="reminder">Αποστολή</button></form></td>
				                <td id="buttonToReturnEquipment"><a href=borrow_delete.php?id_borrow='.$baseQuerySTMTResult['id_borrow'].'&id_equip='.$baseQuerySTMTResult['id_equip'].'&id_user_borrow='.$baseQuerySTMTResult['id_user_borrow'].' class="btn btn-dark returnEquipment" id_equip='.$baseQuerySTMTResult['id_equip'].'>Επιστροφή</a><a href=borrow_change.php?id_borrow='.$baseQuerySTMTResult['id_borrow'].' class="btn btn-dark">Επεξεργασία</a><a href=borrow_transfer.php?id_borrow='.$baseQuerySTMTResult['id_borrow'].' class="btn btn-dark" >Μεταφορά</a></td>
			                  </tr>
			              ';
			        }      
        		}
        	}else {
    				echo '<br><span class="alert alert-warning">Δεν βρέθηκαν δανεισμοί στο χρονικό διάστημα που ζητήσατε.</span><br>';
    		}
	}else {
		echo $tableTop;
		while($baseQuerySTMTResult=$baseQuerySTMT->fetch(PDO::FETCH_ASSOC)){
			if ($baseQuerySTMTResult['history_flag'] != 0 AND $baseQuerySTMTResult['confirmation_borrow'] == 1){
				$now = new DateTime();
				$dateToCheck = new DateTime($baseQuerySTMTResult['expire_date']);
				if($dateToCheck >= $now) {
					$borrowState = '<tr id="borrowStateActive">';
				}else {
					$borrowState ='<tr id="borrowStateNotActive">';
				}
				$nowDate =  strtotime(date("d-m-Y"));
				$dateToCheck = strtotime(date('d-m-Y',strtotime($baseQuerySTMTResult['expire_date'])));
				$dateDiff = round(($dateToCheck - $nowDate) / 86400);
				
				$nameQuerySQL = "SELECT * FROM users_svds WHERE id= :borrowUser"; 
	      		$nameQuerySTMT = $db->prepare($nameQuerySQL);
	      		$nameQuerySTMT->bindParam(':borrowUser', $baseQuerySTMTResult['id_user_borrow']);
	      		$nameQuerySTMT->execute();
	      		while($nameQuerySTMTResult=$nameQuerySTMT->fetch(PDO::FETCH_ASSOC)){
	      			$last_name= $nameQuerySTMTResult['last_name'];
	      			$first_name= $nameQuerySTMTResult['first_name'];
	      			$aem = $nameQuerySTMTResult['aem'];
	      			$telephone= $nameQuerySTMTResult['telephone'];
	      			$email= $nameQuerySTMTResult['email'];
	      			$full_name= $last_name.' '.$first_name;
	      		}	
	      		$popoverMessage = "User : $last_name $first_name <br> AEM: $aem <br> Phone number: $telephone <br> Email: $email";
				echo '
					'.$borrowState.'
			    	<td class="showBorrowerInfo" data-toggle="popover" title="Πληροφορίες" data-html="true" data-placement="top" data-content="'.$popoverMessage.'"><a href=borrow_user.php?id_user='.$aem.'>'.$last_name.' '.$first_name.'</a></td>
			    	<td><a href=borrow_user.php?id_user='.$aem.'>'.$aem.'</a></td>
			    	<td><a href=equipment_details.php?id_equip='.$baseQuerySTMTResult['id_equip'].'>'.$baseQuerySTMTResult['name_e'].'</a></td>
			    	<td>'.date('d/m/Y',strtotime($baseQuerySTMTResult['start_date'])).' εώς'.date('d/m/Y',strtotime($baseQuerySTMTResult['expire_date'])).'</td>
			    	<td>'.$dateDiff.' ημέρες</td>
			    ';
			    if ($baseQuerySTMTResult['confirmation_borrow'] == 1){
			    	echo '<td>Επιβεβαιώθηκε</td>';
			  	}else{
			  		echo '<td id="notAprrovedBorrow"><a href=confirmation.php>Εκκρεμεί</a></td>';
			  	}
			  	if ($baseQuerySTMTResult['extend_reason'] != NULL){
			  		echo '	 
			  			<td>'.$baseQuerySTMTResult['extend_reason'] .'</td>
			  	 	';
			  	}elseif ($baseQuerySTMTResult['borrow_reason'] != NULL){
			  		echo '	 
			  			<td>'.$baseQuerySTMTResult['borrow_reason'] .'</td>
			  	 	';	
			  	}else{
			  		echo '	 
			  			<td>Δεν υπάρχει λόγος δανειμού.</td>
			  	 	';
			  	}
			  	echo '
			  		<td><form method="POST"><input type="hidden" name="full_name" value="'.$full_name.'"><input type="hidden" name="telephone" value="'.$telephone.'"><input type="hidden" name="aem_borrow" value="'.$aem.'"><input type="hidden" name="dateDiff" value='.$dateDiff.'><input type="hidden" name="equipmentName" value="'.$baseQuerySTMTResult['name_e'].'"><input type="hidden" name="email" value="'.$email.'"><button class="btn btn-dark" name="reminder">Αποστολή</button></form></td>
			  	 	<td id="buttonToReturnEquipment"><a href=borrow_delete.php?id_borrow='.$baseQuerySTMTResult['id_borrow'].'&id_equip='.$baseQuerySTMTResult['id_equip'].'&id_user_borrow='.$baseQuerySTMTResult['id_user_borrow'].' class="btn btn-dark returnEquipment" id_equip='.$baseQuerySTMTResult['id_equip'].'>Επιστροφή</a><a href=borrow_change.php?id_borrow='.$baseQuerySTMTResult['id_borrow'].' class="btn btn-dark" >Επεξεργασία</a><a href=borrow_transfer.php?id_borrow='.$baseQuerySTMTResult['id_borrow'].' class="btn btn-dark" >Μεταφορά</a></td>
			  		</tr>
			  		</body>
				';
			}	
		}
	}

	$rowsQuerySQL = "SELECT * FROM borrow_svds WHERE history_flag= :history_flag";
	$rowsQuerySTMT = $db->prepare($rowsQuerySQL);
	$rowsQuerySTMT->bindParam(':history_flag', $one, PDO::PARAM_INT);
 	$rowsQuerySTMT->execute();		
	$rowsNumberPagination = $rowsQuerySTMT->rowCount();
    $totalCellsPagination = ceil($rowsNumberPagination/$limitPagination);

    echo '
    	<br>
			<ul class="pagination pagination-sm justify-content-center">
    ';
    if ($pageOfPagination > 1){    	 
    	echo'
    		<li class="page-item">
    		<a href=equipment_return.php?p='.($pageOfPagination-1).' class="page-link"><<</a></li>
    	';
    }

    for ($i=1; $i <= $totalCellsPagination; $i++) { 
        if ($pageOfPagination == $i){
            echo "<li class='page-item  active'><a class='page-link' href=equipment_return.php?p=".$i.">".$i."</a></li>";                    
        }else {

    	    echo "<li class='page-item'><a class='page-link' href=equipment_return.php?p=".$i.">".$i."</a></li>";
        }
    }


    if ($pageOfPagination < $totalCellsPagination){    	 
    	echo'
           	<li class="page-item">
           	<a href=equipment_return.php?p='.($pageOfPagination+1).' class="page-link">>></a></li>
        ';
    }

	echo '
		</ul>
		</table>
		</div>
	';
	include("views/footer.php");
	echo '
		</body>
		</html>
		</div>
	';
}			
if (isset($_POST['reminder'])){
	reminder($_POST['full_name'], $_POST['telephone'], $_POST['aem_borrow'], $_POST['dateDiff'], $_POST['equipmentName']);	
	sendEmail($_POST['email'], $_POST['full_name'], $_POST['aem_borrow'], $_POST['dateDiff'], $_POST['equipmentName']);
}
}else {
	header("Location: index.php");
	die("Δεν έχετε συνδεθεί");
}


?>