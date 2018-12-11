<?php
include("variables_file.php");
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");
	if ($_SESSION['email']){	
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
						 		$imageHashedName = "noimage.jpg";	
						}else {
						 		$imageHashedName = $equipQuerySTMTResult['hash_filename'];
						}
				 			echo '
					 			<div class="container">
					 				<div class="row">
					 					<div class="col-md-8">
						 				  <h4>Πληροφορίες Εξαρτήματος </h4>   
										    <img class="card-img-top equipmentDetailsImage" src="uploadedImages/'.$imageHashedName.'" alt="Card image cap">
										    <div class="card-body">
											    <h3 class="card-title">'.$equipQuerySTMTResult['name_e'].'</h3>
											    <p class="card-text">Τοποθεσία: '.$equipQuerySTMTResult['location_e'].'</p>
											    <p class="card-text">Τμήμα: '.$departmentQuerySTMTResult['name_dep'].' Πάροχος: '.$providerQuerySTMTResult['name_p'].'</p>
											    <p class="card-text">Σειριακός Αρ.: '.$equipQuerySTMTResult['serial_number'].' Κατάσταση: '.$condition.'</p>
											    <p class="card-text"><small class="text-muted">Σύντομη Περιγραφή: '.$descriptionQuerySTMTResult['short_desc'].' Εκτενείς Περιγραφή: '.$descriptionQuerySTMTResult['long_desc'].'</small></p>
											    <p class="card-text extraComment"><button type="submit">Προσθέστε Σχόλιο</button></p>
									    	</div>
									    	<form class="form-inline" method="POST" id="newComment">
												<textarea name="newComment" id="commentArea">
												</textarea><br>
												<button type="submit">Καταχώρηση Σχολίου</button> 
											</form>
										</div>
										<div class="col-md-4">
											<h4>Πρόσφατα Σχόλια</h4>
							';
											$commentsDisplayQuerySQL = "SELECT * FROM comments_svds WHERE id_equip_com= :idToShow ORDER BY date_com DESC LIMIT 3";
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
					 									echo 'Ο χρήστης '.$userCommentQuerySTMTResult['last_name'].' '.$userCommentQuerySTMTResult['first_name'].'  έγραψε :
					 										<ul>
																<li>'.$commentsDisplayQuerySTMTResult['comments'].'</li>
															</ul>
															στις '.date('d/m/Y',strtotime($commentsDisplayQuerySTMTResult['date_com'])).'<br>
					 									';
					 								}	
				 								}
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
			       		<div class="container">
			       		Επιτυχείς εισαγωγή σχολίου.
			       		</div>
			       		<meta http-equiv="refresh" content="0.5">
			       	';							       
			    }
			}
		 }else {
		 	echo '
		 		<div class="container">
		 		<h3>Εμφανίστηκε πρόβλημα προσπαθήστε ξανά.</h3>
		 		</div>
		 	';
		 }
	}	 

include("views/footer.php");
?>