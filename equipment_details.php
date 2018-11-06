<?php
	session_start();
	include("views/connection.php");
	include("views/header.php");
	include("views/navbar.php");
	if ($_SESSION['email']){
		 if (isset($_GET['id_equip'])){
		 	$idToShow= $_GET['id_equip'];
		 	$condition = "Ενεργό";	
		 	$equipQuery = $db->prepare("SELECT * FROM equip_svds WHERE id_equip= $idToShow"); 
		 	$equipQuery->execute();
		 	while($equipQueryResult=$equipQuery->fetch(PDO::FETCH_ASSOC)){
		 		if ($equipQueryResult['retired'] == 1){
		 			$condition = "Ανενεργό";
		 		}
		 		$idDepartment = $equipQueryResult['department'];
		 		$idProvider = $equipQueryResult['provider_e'];
		 		$idDescription = $equipQueryResult['short_desc_e'];
		 		$departmentQuery = $db->prepare("SELECT * FROM department_svds WHERE id_dep= $idDepartment"); 
		 		$departmentQuery->execute();
		 		while($departmentQueryResult=$departmentQuery->fetch(PDO::FETCH_ASSOC)){
		 		$providerQuery = $db->prepare("SELECT * FROM provider_svds WHERE id_p= $idProvider"); 
		 		$providerQuery->execute();
		 		while($providerQueryResult=$providerQuery->fetch(PDO::FETCH_ASSOC)){
		 		$descriptionQuery = $db->prepare("SELECT * FROM description_svds WHERE id_desc= $idDescription"); 
		 		$descriptionQuery->execute();
		 		while($descriptionQueryResult=$descriptionQuery->fetch(PDO::FETCH_ASSOC)){	
		 			echo '
			 			<div class="container">
		 				  <h1> Πληροφορίες Εξαρτήματος </h1>   
						    <img class="card-img-top" src="uploadedImages/'.$equipQueryResult['real_filename'].'" alt="Card image cap">
						    <div class="card-body">
							    <h3 class="card-title">'.$equipQueryResult['name_e'].'</h3>
							    <p class="card-text">Τοποθεσία: '.$equipQueryResult['location_e'].'</p>
							    <p class="card-text">Τμήμα: '.$departmentQueryResult['name_dep'].' Πάροχος: '.$providerQueryResult['name_p'].'</p>
							    <p class="card-text">Ποσότητα: '.$equipQueryResult['location_e'].' Κατάσταση '.$condition.'</p>
							    <p class="card-text"><small class="text-muted">Σύντομη Περιγραφή: '.$descriptionQueryResult['short_desc'].'</small></p>
							    <p class="card-text"><small class="text-muted">Εκτενείς Περιγραφή: '.$descriptionQueryResult['long_desc'].'</small></p>
						    </div>
							
						</div>
		 			';
		 		}	
		 		}
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

	include("views/footer.php")
?>