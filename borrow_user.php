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
$tableHead = '
	<div class="table-responsive">
		<table class="table table-bordered">	
		<thead class="thead-dark">
		<tr>
			<th>Εικόνα</th>
    		<th>Εξάρτημα</th>
    		<th>Από - Εώς</th>
    		<th>Αιτιολόγηση</th>
    		<th>Κατάσταση</th>
  		</tr>
  		</thead>
	';
		if (isset($_GET['id_user'])){
			$aemUser = filter_var(($_GET['id_user']),FILTER_SANITIZE_NUMBER_FLOAT);
			$nameQuerySQL = "SELECT * FROM users_svds WHERE aem= :borrowUser"; 
      		$nameQuerySTMT = $db->prepare($nameQuerySQL);
      		$nameQuerySTMT->bindParam(':borrowUser', $aemUser);
      		$nameQuerySTMT->execute();
      		while($nameQuerySTMTResult=$nameQuerySTMT->fetch(PDO::FETCH_ASSOC)){
      			$last_name= $nameQuerySTMTResult['last_name'];
      			$first_name= $nameQuerySTMTResult['first_name'];
      			$id= $nameQuerySTMTResult['id'];
      		}

		 	echo '
				<div class="container" id="tableEquipment">
				<div class="form-inline" id="searchHolder">
				<h2>Σελίδα δανεισμών του χρήστη '.$last_name.' '.$first_name.'</h2>
		        </div><br>
				</div>
				<div class="form-inline" id="searchHolder">
				  	<form name="form" method="POST">
	  					<input type="text" name="userName"  class="form-control input-lg" id="userName" autocomplete="off" placeholder="Όνομα χρήστη"/>
						<input type="text" name="userAEM"  class="form-control input-lg" id="userAEM" autocomplete="off" value='.$aemUser.' placeholder="ΑΕΜ"/>
						<button type="submit" name="search" class="btn btn-dark">Αναζήτηση</button>
				    </form><br>
				</div>
				<br>
		 	';

		 	if (isset($_POST['search'])){
		 		$borrowQuerySQL = "SELECT * FROM borrow_svds WHERE id_user_borrow= :idToFindUser ORDER BY id_borrow DESC"; 
				$borrowQuerySTMT = $db->prepare($borrowQuerySQL);
				$borrowQuerySTMT->bindParam(':idToFindUser', $id);
				$borrowQuerySTMT->execute();
				if ($borrowQuerySTMT->rowCount() > 0){	
				  	echo $tableHead;
					while($borrowQuerySTMTResult=$borrowQuerySTMT->fetch(PDO::FETCH_ASSOC)){
		 				$equipmentQuerySQL = "SELECT * FROM equip_svds WHERE id_equip= :idEquipToShow";
						$equipmentQuerySTMT = $db->prepare($equipmentQuerySQL);
				 		$equipmentQuerySTMT->bindParam(':idEquipToShow', $borrowQuerySTMTResult['id_equip_borrow'], PDO::PARAM_INT); 
				 		$equipmentQuerySTMT->execute();
				 		while($equipmentQuerySTMTResult=$equipmentQuerySTMT->fetch(PDO::FETCH_ASSOC)){
				 			$equipmentToBorrow = $equipmentQuerySTMTResult['name_e'];
				 			if (!$equipmentQuerySTMTResult['hash_filename']){
							 	$imageHashedName = "noimage.png";	
							}else {
							 	$imageHashedName = $equipmentQuerySTMTResult['hash_filename'];
							}
							
							if (!file_exists('uploadedImages/'.$imageHashedName)){ 
								$imageHashedName = "noimage.png";
							}	
				 		}	
		 				if ($borrowQuerySTMTResult['extend_reason'] != NULL){
		 					$borrowReason = $borrowQuerySTMTResult['extend_reason'];
		 				}elseif ($borrowQuerySTMTResult['borrow_reason'] != NULL){
		 					$borrowReason = $borrowQuerySTMTResult['borrow_reason'];
		 				}else {
		 					$borrowReason = "Δεν δόθηκε λόγος δανεισμού.";
		 				}

		 				if ($borrowQuerySTMTResult['history_flag'] == 0){
		 					$borrowState = "Έχει ολοκληρωθεί";
		 				}else {
		 					$borrowState = "Σε εξέλιξη";
		 				}

		 				$startDate = date("d-m-Y", strtotime($borrowQuerySTMTResult['start_date']));
		 				$expireDate = date("d-m-Y", strtotime($borrowQuerySTMTResult['expire_date']));
		 				echo '
		 					<tr>
		 					<td><img id="imageToShowUserBorrow" class="card-img-top equipmentDetailsImage" src="uploadedImages/'.$imageHashedName.'" alt=""></td>
		 					<td>'.$equipmentToBorrow.'</td>
		 					<td>'.$startDate.' εώς '.$expireDate.'</td>
		 					<td>'.$borrowReason.'</td>
		 					<td>'.$borrowState.'</td>
		 					</tr>
		 					</body>
		 				';
					}	
					echo '
						</table>
						</div>
					';
				}else {
					echo '<p class="alert alert-warning>Δεν βρέθηκαν δανεισμοί για το συγκεκριμένο χρήστη</p>';
				}
			}		
		}else {
			echo '
				<div class="container" id="tableEquipment">
					<div class="form-inline" id="searchHolder">
					<h2>Αναζήτηση δανεισμών χρήστη</h2>
		        	</div><br>
				</div>
				<div class="form-inline" id="searchHolder">
				  	<form name="form" method="POST">
	  					<input type="text" name="userName"  class="form-control input-lg" id="userName" autocomplete="off" placeholder="Όνομα χρήστη"/>
						<input type="text" name="userAEM"  class="form-control input-lg" id="userAEM" autocomplete="off" placeholder="ΑΕΜ"/>
						<button type="submit" name="search" class="btn btn-dark">Αναζήτηση</button>
				    </form><br>
				</div>
				<br>
		 	';

		 	if (isset($_POST['search'])){
		 		if (isset($_POST['userAEM']) AND !empty($_POST['userAEM'])){
		 			$aemUser = filter_var(($_POST['userAEM']),FILTER_SANITIZE_NUMBER_FLOAT);
		 			$nameQuerySQL = "SELECT * FROM users_svds WHERE aem= :borrowUser"; 
		      		$nameQuerySTMT = $db->prepare($nameQuerySQL);
		      		$nameQuerySTMT->bindParam(':borrowUser', $aemUser);
		      		$nameQuerySTMT->execute();
		      		while($nameQuerySTMTResult=$nameQuerySTMT->fetch(PDO::FETCH_ASSOC)){
		      			$last_name= $nameQuerySTMTResult['last_name'];
		      			$first_name= $nameQuerySTMTResult['first_name'];
		      			$id= $nameQuerySTMTResult['id'];
		      		}
		 			$borrowQuerySQL = "SELECT * FROM borrow_svds WHERE id_user_borrow= :idToFindUser ORDER BY id_borrow DESC"; 
					$borrowQuerySTMT = $db->prepare($borrowQuerySQL);
					$borrowQuerySTMT->bindParam(':idToFindUser', $id);
					$borrowQuerySTMT->execute();
					if ($borrowQuerySTMT->rowCount() > 0){
						echo '
							<div class="container"><h3>Οι δανεισμοί του χρήστη: '.$last_name.' '.$first_name.' </h3></div>
						';
						echo $tableHead;
						while($borrowQuerySTMTResult=$borrowQuerySTMT->fetch(PDO::FETCH_ASSOC)){
			 				$equipmentQuerySQL = "SELECT * FROM equip_svds WHERE id_equip= :idEquipToShow";
							$equipmentQuerySTMT = $db->prepare($equipmentQuerySQL);
					 		$equipmentQuerySTMT->bindParam(':idEquipToShow', $borrowQuerySTMTResult['id_equip_borrow'], PDO::PARAM_INT); 
					 		$equipmentQuerySTMT->execute();
					 		while($equipmentQuerySTMTResult=$equipmentQuerySTMT->fetch(PDO::FETCH_ASSOC)){
					 			$equipmentToBorrow = $equipmentQuerySTMTResult['name_e'];	
					 			if (!$equipmentQuerySTMTResult['hash_filename']){
								 	$imageHashedName = "noimage.png";	
								}else {
								 	$imageHashedName = $equipmentQuerySTMTResult['hash_filename'];
								}
								
								if (!file_exists('uploadedImages/'.$imageHashedName)){ 
									$imageHashedName = "noimage.png";
								}
					 		}	
			 				if ($borrowQuerySTMTResult['extend_reason'] != NULL){
			 					$borrowReason = $borrowQuerySTMTResult['extend_reason'];
			 				}elseif ($borrowQuerySTMTResult['borrow_reason'] != NULL){
			 					$borrowReason = $borrowQuerySTMTResult['borrow_reason'];
			 				}else {
			 					$borrowReason = "Δεν δόθηκε λόγος δανεισμού.";
			 				}

			 				if ($borrowQuerySTMTResult['history_flag'] == 0){
			 					$borrowState = "Έχει ολοκληρωθεί";
			 				}else {
			 					$borrowState = "Σε εξέλιξη";
			 				}

			 				$startDate = date("d-m-Y", strtotime($borrowQuerySTMTResult['start_date']));
			 				$expireDate = date("d-m-Y", strtotime($borrowQuerySTMTResult['expire_date']));
			 				echo '
			 					<tr>
			 					<td><img id="imageToShowUserBorrow" class="card-img-top equipmentDetailsImage" src="uploadedImages/'.$imageHashedName.'" alt=""></td>
			 					<td>'.$equipmentToBorrow.'</td>
			 					<td>'.$startDate.' εώς '.$expireDate.'</td>
			 					<td>'.$borrowReason.'</td>
			 					<td>'.$borrowState.'</td>
			 					</tr>
			 					</body>
			 				';	
						}	
					}else {
						echo '<p class="alert alert-warning">Δεν βρέθηκαν δανεισμοί για το χρήστη με αυτό το ΑΕΜ</p>';
					}	
		 		}elseif (isset($_POST['userName']) AND !empty($_POST['userName'])){
		 			$borrowerName = filter_var($_POST['userName'],FILTER_SANITIZE_STRING);
					$fullName = (explode(" ", $borrowerName));
					$lastName = $fullName[0];
					$firstName = $fullName[1];

					$searchNameQuerySQL = "SELECT * FROM users_svds WHERE last_name LIKE :lastName AND first_name LIKE :firstName";
					$searchNameQuerySTMT = $db->prepare($searchNameQuerySQL);	
					$searchNameQuerySTMT->bindParam(':lastName', $lastName);
					$searchNameQuerySTMT->bindParam(':firstName', $firstName); 
					$searchNameQuerySTMT->execute();
					
					while($searchNameQuerySTMTResult=$searchNameQuerySTMT->fetch(PDO::FETCH_ASSOC)){
		      			$id= $searchNameQuerySTMTResult['id'];
		      		}
		 			$borrowQuerySQL = "SELECT * FROM borrow_svds WHERE id_user_borrow= :idToFindUser ORDER BY id_borrow DESC"; 
					$borrowQuerySTMT = $db->prepare($borrowQuerySQL);
					$borrowQuerySTMT->bindParam(':idToFindUser', $id);
					$borrowQuerySTMT->execute();
					if ($borrowQuerySTMT->rowCount() > 0){
						echo '
							<div class="container"><h3>Οι δανεισμοί του χρήστη: '.$lastName.' '.$firstName.' </h3></div>
						';
						echo $tableHead;
						while($borrowQuerySTMTResult=$borrowQuerySTMT->fetch(PDO::FETCH_ASSOC)){
			 				$equipmentQuerySQL = "SELECT * FROM equip_svds WHERE id_equip= :idEquipToShow";
							$equipmentQuerySTMT = $db->prepare($equipmentQuerySQL);
					 		$equipmentQuerySTMT->bindParam(':idEquipToShow', $borrowQuerySTMTResult['id_equip_borrow'], PDO::PARAM_INT); 
					 		$equipmentQuerySTMT->execute();
					 		while($equipmentQuerySTMTResult=$equipmentQuerySTMT->fetch(PDO::FETCH_ASSOC)){
					 			$equipmentToBorrow = $equipmentQuerySTMTResult['name_e'];	
					 			if (!$equipmentQuerySTMTResult['hash_filename']){
								 	$imageHashedName = "noimage.png";	
								}else {
								 	$imageHashedName = $equipmentQuerySTMTResult['hash_filename'];
								}
								
								if (!file_exists('uploadedImages/'.$imageHashedName)){ 
									$imageHashedName = "noimage.png";
								}
					 		}	
			 				if ($borrowQuerySTMTResult['extend_reason'] != NULL){
			 					$borrowReason = $borrowQuerySTMTResult['extend_reason'];
			 				}elseif ($borrowQuerySTMTResult['borrow_reason'] != NULL){
			 					$borrowReason = $borrowQuerySTMTResult['borrow_reason'];
			 				}else {
			 					$borrowReason = "Δεν δόθηκε λόγος δανεισμού.";
			 				}

			 				if ($borrowQuerySTMTResult['history_flag'] == 0){
			 					$borrowState = "Έχει ολοκληρωθεί";
			 				}else {
			 					$borrowState = "Σε εξέλιξη";
			 				}
			 				$startDate = date("d-m-Y", strtotime($borrowQuerySTMTResult['start_date']));
			 				$expireDate = date("d-m-Y", strtotime($borrowQuerySTMTResult['expire_date']));
			 				echo '
			 					<tr>
			 					<td><img id="imageToShowUserBorrow" class="card-img-top equipmentDetailsImage" src="uploadedImages/'.$imageHashedName.'" alt=""></td>
			 					<td>'.$equipmentToBorrow.'</td>
			 					<td>'.$startDate.' εώς '.$expireDate.'</td>
			 					<td>'.$borrowReason.'</td>
			 					<td>'.$borrowState.'</td>
			 					</tr>
			 					</body>
			 				';	
						}	
					}else {
						echo '<p class="alert alert-warning">Δεν βρέθηκαν δανεισμοί για το συγκεκριμένο χρήστη</p>';
					}	
		 		}
	 			echo '
					</table>
					</div>
				';
			}
		} 		 

include("views/footer.php");
echo '
	</body>
	</html>
';
?>