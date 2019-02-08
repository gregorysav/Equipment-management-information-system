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
		if (isset($_GET['id_equip'])){
		 	$idToShow= filter_var($_GET['id_equip'],FILTER_SANITIZE_NUMBER_FLOAT);
		 	$equipQuerySQL = "SELECT * FROM equip_svds WHERE id_equip= :idToShow";
		 	$equipQuerySTMT = $db->prepare($equipQuerySQL);
		 	$equipQuerySTMT->bindParam(':idToShow', $idToShow, PDO::PARAM_INT); 
		 	$equipQuerySTMT->execute();
		 	while($equipQuerySTMTResult=$equipQuerySTMT->fetch(PDO::FETCH_ASSOC)){
		 		if ($equipQuerySTMTResult['retired'] == 1){
		 			$condition = "Ανενεργό";
		 		}
		 		$idDepartment = $equipQuerySTMTResult['department'];
		 		$idProvider = $equipQuerySTMTResult['provider_e'];
		 		$idDescription = $equipQuerySTMTResult['short_desc_e'];
		 		$departmentQuerySQL = "SELECT * FROM department_svds WHERE id_dep= :idDepartment";
		 		$departmentQuerySTMT = $db->prepare($departmentQuerySQL);
		 		$departmentQuerySTMT->bindParam(':idDepartment', $idDepartment, PDO::PARAM_INT);
		 		$departmentQuerySTMT->execute();
		 		while($departmentQuerySTMTResult=$departmentQuerySTMT->fetch(PDO::FETCH_ASSOC)){
			 		$providerQuerySQL = "SELECT * FROM provider_svds WHERE id_p= :idProvider";
			 		$providerQuerySTMT = $db->prepare($providerQuerySQL);	
			 		$providerQuerySTMT->bindParam(':idProvider', $idProvider, PDO::PARAM_INT); 
			 		$providerQuerySTMT->execute();
			 		while($providerQuerySTMTResult=$providerQuerySTMT->fetch(PDO::FETCH_ASSOC)){
			 		$descriptionQuerySQL = "SELECT * FROM description_svds WHERE id_desc= :idDescription";
			 		$descriptionQuerySTMT = $db->prepare($descriptionQuerySQL);
			 		$descriptionQuerySTMT->bindParam(':idDescription', $idDescription, PDO::PARAM_INT); 
			 		$descriptionQuerySTMT->execute();
			 		while($descriptionQuerySTMTResult=$descriptionQuerySTMT->fetch(PDO::FETCH_ASSOC)){	
			 			if (!$equipQuerySTMTResult['hash_filename']){
						 		$imageHashedName = "noimage.png";	
						}else {
						 		$imageHashedName = $equipQuerySTMTResult['hash_filename'];
						}
				 			echo '
					 			<div class="container">
					 			<br>
					 				<div class="row">
					 					<div class="col-md-8 border rounded">
						 				  <h4 id="title">Πληροφορίες Εξαρτήματος </h4>   
										    <img id="imageToOpen" class="card-img-top equipmentDetailsImage" src="uploadedImages/'.$imageHashedName.'" alt="">
										    <div id="myModal" class="modal">
										    	<span class="close">&times;</span>
												<img class="modal-content" id="openedImage">
											</div>
										    <div class="card-body">
											    <h4 class="card-title">'.$equipQuerySTMTResult['name_e'].'<button type="submit" class="btn btn-secondary add_to_basket" name_basket="'.$equipQuerySTMTResult['name_e'].'" id_user_basket='.$_SESSION['id'].' id_equip_basket='.$equipQuerySTMTResult['id_equip'].'>Καλάθι</button></h4>
											    <p class="card-text">Τοποθεσία: '.$equipQuerySTMTResult['location_e'].'</p>
											    <p class="card-text">Τμήμα: '.$departmentQuerySTMTResult['name_dep'].' Πάροχος: '.$providerQuerySTMTResult['name_p'].'</p>
											    <p class="card-text">Σειριακός Αρ.: '.$equipQuerySTMTResult['serial_number'].' Κατάσταση: '.$condition.'</p>
											    <p class="card-text"><small class="text-muted">Σύντομη Περιγραφή: '.$descriptionQuerySTMTResult['short_desc'].'</small></p>
											    <p class="card-text"><small class="text-muted">Εκτενής Περιγραφή: '.$descriptionQuerySTMTResult['long_desc'].'</small></p>
									    	</div><br><hr>
									    	<div id="scrollableRecentBorrowers">
									    	<h4 id="title">Τρέχων Δανεισμός</h4>
							';
							$borrowQuerySQL = "SELECT * FROM borrow_svds WHERE id_equip_borrow= :idToShow AND history_flag= :history_flag ORDER BY start_date DESC";
							$borrowQuerySTMT = $db->prepare($borrowQuerySQL);
					 		$borrowQuerySTMT->bindParam(':idToShow', $idToShow, PDO::PARAM_INT); 
					 		$borrowQuerySTMT->bindParam(':history_flag', $one, PDO::PARAM_INT); 
					 		$borrowQuerySTMT->execute();
					 		if ($borrowQuerySTMT->rowCount() == 0 ){
					 			echo '<p class="alert alert-warning">Το εξάρτημα δεν είναι δανεισμένο αυτή την περίοδο.</p>
					 				</div>
					 			';
					 		}else {
					 			while($borrowQuerySTMTResult=$borrowQuerySTMT->fetch(PDO::FETCH_ASSOC)){
					 				$recentBorrowerQuerySQL = "SELECT * FROM users_svds WHERE id= :idToShow";
									$recentBorrowerQuerySTMT = $db->prepare($recentBorrowerQuerySQL);
							 		$recentBorrowerQuerySTMT->bindParam(':idToShow', $borrowQuerySTMTResult['id_user_borrow'], PDO::PARAM_INT); 
							 		$recentBorrowerQuerySTMT->execute();
					 				if ($borrowQuerySTMTResult['extend_reason'] != NULL){
					 					$borrowReason = $borrowQuerySTMTResult['extend_reason'];
					 				}elseif ($borrowQuerySTMTResult['borrow_reason'] != NULL){
					 					$borrowReason = $borrowQuerySTMTResult['borrow_reason'];
					 				}else {
					 					$borrowReason = "Δεν δόθηκε λόγος δανεισμού.";
					 				}
					 				$startDate = date("d-m-Y", strtotime($borrowQuerySTMTResult['start_date']));
					 				$expireDate = date("d-m-Y", strtotime($borrowQuerySTMTResult['expire_date']));
					 				while($recentBorrowerQuerySTMTResult=$recentBorrowerQuerySTMT->fetch(PDO::FETCH_ASSOC)){
						 				echo '-Ο χρήστης ['.$recentBorrowerQuerySTMTResult['last_name'].' '.$recentBorrowerQuerySTMTResult['first_name'].' ] με ΑΕΜ=['.$recentBorrowerQuerySTMTResult['aem'].'] δανείσθηκε το εξάρτημα από '.$startDate.' μέχρι '.$expireDate.' με σκοπό '.$borrowReason.'.<br>';
						 			}	
						 		}	
						 		echo '
						 			</div>
						 		';
					 		}
					 		echo '	
					 				<hr>
					 				<div id="scrollableRecentBorrowers">
						 			<h4 id="title">Παλαιότεροι Δανεισμοί</h4>
						 		';
							$borrowQuerySQL = "SELECT * FROM borrow_svds WHERE id_equip_borrow= :idToShow AND history_flag= :history_flag ORDER BY start_date DESC";
							$borrowQuerySTMT = $db->prepare($borrowQuerySQL);
					 		$borrowQuerySTMT->bindParam(':idToShow', $idToShow, PDO::PARAM_INT); 
					 		$borrowQuerySTMT->bindParam(':history_flag', $zero, PDO::PARAM_INT); 
					 		$borrowQuerySTMT->execute();
					 		if ($borrowQuerySTMT->rowCount() == 0 ){
					 			echo '<p class="alert alert-warning">Δεν υπάρχουν δανεισμοί για αυτό το εξάρτημα.</p>
					 				</div>
					 			';
					 		}else {
					 			while($borrowQuerySTMTResult=$borrowQuerySTMT->fetch(PDO::FETCH_ASSOC)){
					 				$recentBorrowerQuerySQL = "SELECT * FROM users_svds WHERE id= :idToShow";
									$recentBorrowerQuerySTMT = $db->prepare($recentBorrowerQuerySQL);
							 		$recentBorrowerQuerySTMT->bindParam(':idToShow', $borrowQuerySTMTResult['id_user_borrow'], PDO::PARAM_INT); 
							 		$recentBorrowerQuerySTMT->execute();
					 				if ($borrowQuerySTMTResult['extend_reason'] != NULL){
					 					$borrowReason = $borrowQuerySTMTResult['extend_reason'];
					 				}elseif ($borrowQuerySTMTResult['borrow_reason'] != NULL){
					 					$borrowReason = $borrowQuerySTMTResult['borrow_reason'];
					 				}else {
					 					$borrowReason = "Δεν δόθηκε λόγος δανεισμού.";
					 				}
					 				$startDate = date("d-m-Y", strtotime($borrowQuerySTMTResult['start_date']));
					 				$expireDate = date("d-m-Y", strtotime($borrowQuerySTMTResult['expire_date']));
					 				while($recentBorrowerQuerySTMTResult=$recentBorrowerQuerySTMT->fetch(PDO::FETCH_ASSOC)){
						 				echo '-Ο χρήστης ['.$recentBorrowerQuerySTMTResult['last_name'].' '.$recentBorrowerQuerySTMTResult['first_name'].' ] με ΑΕΜ=['.$recentBorrowerQuerySTMTResult['aem'].'] δανείσθηκε το εξάρτημα από '.$startDate.' μέχρι '.$expireDate.' με σκοπό '.$borrowReason.'.<br>';
						 			}	
						 		}	
						 		echo '
						 			</div>
						 		';
					 		}	
							echo '		 		
										</div>
										<div class="col-md-4 border rounded" id="scrollableComments">
											<h4 id="title">Πρόσφατα Σχόλια<a href="#bottom" class="card-text extraComment"><button class="btn btn-secondary" type="submit">Προσθέστε Σχόλιο</button></a></h4>
							';
											$commentsDisplayQuerySQL = "SELECT * FROM comments_svds WHERE id_equip_com= :idToShow ORDER BY date_com DESC";
											$commentsDisplayQuerySTMT = $db->prepare($commentsDisplayQuerySQL);
									 		$commentsDisplayQuerySTMT->bindParam(':idToShow', $idToShow, PDO::PARAM_INT); 
									 		$commentsDisplayQuerySTMT->execute();
									 		if ($commentsDisplayQuerySTMT->rowCount() == 0 ){
									 			echo 'Δεν υπάρχουν σχόλια για αυτό το εξάρτημα.';
									 		}else {
				 								while($commentsDisplayQuerySTMTResult=$commentsDisplayQuerySTMT->fetch(PDO::FETCH_ASSOC)){
				 									$userCommentQuerySQL = "SELECT first_name, last_name FROM users_svds WHERE id= :idUser";
				 									$userCommentQuerySTMT = $db->prepare($userCommentQuerySQL); 
			 										$userCommentQuerySTMT->bindParam(':idUser', $commentsDisplayQuerySTMTResult['id_user_com'], PDO::PARAM_INT);
			 										$userCommentQuerySTMT->execute();
			 										while($userCommentQuerySTMTResult=$userCommentQuerySTMT->fetch(PDO::FETCH_ASSOC)){
					 									echo '<div class="row">
					 										  	<div class="col-md-10">		
					 												Ο χρήστης '.$userCommentQuerySTMTResult['last_name'].' '.$userCommentQuerySTMTResult['first_name'].'  έγραψε :
					 												<ul>
																		<li>'.$commentsDisplayQuerySTMTResult['comments'].'</li> 
																		στις '.date('d/m/Y',strtotime($commentsDisplayQuerySTMTResult['date_com'])).'
					 												</ul>	
					 											</div>
					 									';
					 									if ($commentsDisplayQuerySTMTResult['id_user_com'] == $id OR $type == 1 OR $type == 2 OR $type ==3){
					 										echo '
					 											<div class="col-md-2">
					 												<div class="detailsButtons">
					 													<a href=functions_equipment.php?function=deleteComment&id_comment='.$commentsDisplayQuerySTMTResult['id_comment'].'&idToShow='.$idToShow.' id="deleteComment" name="deleteComment"><p class="fas fa-trash-alt" title=Διαγραφή></p></a>
					 												</div>	
					 											</div>
					 										  </div>
					 										  <hr id="commentsHr">
					 										';
					 									}else {
					 										echo '
					 											</div>
					 										  <hr id="commentsHr">
					 										';
					 									}					 										
					 								}	
				 								}
				 								echo '
				 									<div id="bottom"></div>
				 									<form class="form-inline" method="POST" id="newComment">
														<textarea name="newComment" id="equipmentDetailsCommentArea"></textarea><br>
														<button id="commentAreaButton" type="submit">Καταχώρηση Σχολίου</button> 
													</form>
				 								';
				 							}		
							echo '
										</div>	
									</div>	
								</div>
				 			';
				 		}	
			 		}
		 		}
			}
		 	if (isset($_POST['newComment'])){
 				$id_equip_com = filter_var($_GET['id_equip'],FILTER_SANITIZE_NUMBER_FLOAT);
 				$comments = filter_var($_POST['newComment'],FILTER_SANITIZE_STRING);
				$commentQuerySQL = "INSERT INTO comments_svds (id_equip_com, id_user_com, comments, date_com) VALUES (:id_equip_com, :id_user_com, :comments, NOW())";
				$commentQuerySTMT = $db->prepare($commentQuerySQL);
			    $commentQuerySTMT->bindParam(':id_equip_com', $id_equip_com);
			    $commentQuerySTMT->bindParam(':id_user_com', $_SESSION['id']);
			    $commentQuerySTMT->bindParam(':comments', $comments);
			    if ($commentQuerySTMT->execute()){
			      	echo '
			       		<p class="alert alert-success">Επιτυχής εισαγωγή σχολίου.</p>
			       		<meta http-equiv="refresh" content="0.5">
			       	';							       
			    }else{
			    	echo '<p class="alert alert-warning">πρόβλημα κατά την εισαγωγή σχολίου.</p>';
			    }
			}
		}else {
		 	echo '
		 		<div class="container">
		 		<h3>Εμφανίστηκε πρόβλημα προσπαθήστε ξανά.</h3>
		 		</div>
		 	';
		}	 

include("views/footer.php");
echo '
	</body>
	</html>
';
?>