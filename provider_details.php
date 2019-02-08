<?php
//Access: Administrator
include("variables_file.php");
echo '
	<!DOCTYPE html>
	<html lang="en">
';
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");
	if ($type == 1 OR $type == 2 OR $type == 3 ) {
		if (isset($_GET['id_p'])){
		 	$idProviderToShow= filter_var($_GET['id_p'],FILTER_SANITIZE_NUMBER_FLOAT);
		 	$providerQuerySQL = "SELECT * FROM provider_svds WHERE id_p= :idProviderToShow";
		 	$providerQuerySTMT = $db->prepare($providerQuerySQL);
		 	$providerQuerySTMT->bindParam(':idProviderToShow', $idProviderToShow, PDO::PARAM_INT); 
		 	$providerQuerySTMT->execute();
		 	while($providerQuerySTMTResult=$providerQuerySTMT->fetch(PDO::FETCH_ASSOC)){
		 		echo '
		 			<br>
		 			<div class="container">
		 				<div class="row">
				 			<div class="col-md-4 border rounded">
					 			<h3 id="title">Πληροφορίες Προμηθευτή</h3>
					 			<div class="card-body">
								    <h4 class="card-title">Ονομασία: '.$providerQuerySTMTResult['name_p'].'</h4>
								    <p class="card-text">Τηλέφωνο: '.$providerQuerySTMTResult['telephone_p'].'</p>
								    <p class="card-text">Ιστοσελίδα: '.$providerQuerySTMTResult['website_p'].'</p>
								    <p class="card-text">Email: '.$providerQuerySTMTResult['email_p'].'</p>
								    <p class="card-text"><small class="text-muted">Σχόλια: '.$providerQuerySTMTResult['comments_p'].'</small></p>
								</div>
							</div>	
				';
				$equipmentQuerySQL = "SELECT * FROM equip_svds WHERE provider_e= :idEquipmentToShow";
			 	$equipmentQuerySTMT = $db->prepare($equipmentQuerySQL);
			 	$equipmentQuerySTMT->bindParam(':idEquipmentToShow', $providerQuerySTMTResult['id_p'], PDO::PARAM_INT); 
			 	$equipmentQuerySTMT->execute();
			 	if ($equipmentQuerySTMT-> rowCount() > 0){
			 		echo'			
							<div class="col-md-8 border rounded">
								<h3 id="title">Από τον '.$providerQuerySTMTResult['name_p'].' έχουμε πάρει επιπλέον: </h3>
								<ul>
					';
					while($equipmentQuerySTMTResult=$equipmentQuerySTMT->fetch(PDO::FETCH_ASSOC)){
						echo '<li>'.$equipmentQuerySTMTResult['name_e'].'</li>';
					}	
					echo '			
								</ul>	
							</div>

		 			';
			 	}else {
			 		echo '<div class="col-md-8 border rounded"><p class="alert alert-warning">Δεν υπάρχουν εξαρτήματα από αυτό τον προμηθευτή.</p></div>';
			 	}
				
		 	}
		 	echo '</div>';
		} else {
			echo "Δεν βρέθηκε προμηθευτής με αυτό το ID";
			die();
		}		
	}else{
		header("Refresh:0; url=index.php"); 
        die("Δεν έχετε δικαιώματα πρόσβασης σε αυτή τη σελίδα.");
	}

include("views/footer.php");
echo '
	</body>
	</html>
';
?>