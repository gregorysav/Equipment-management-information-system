<?php
	session_start();
	include("views/connection.php");
	include("views/header.php");
	include("views/navbar.php");
	if ($_SESSION['email']){
		if ($_SESSION['type'] == 1){
			echo '
				<div class="container">
				<h2>Σελίδα Διαχείρισης Εξαρτημάτων</h2>
			    <button type="submit" id="add_equipment" class="btn btn-success btn-info">Προσθήκη Νέου Εξαρτήματος</button>
		        <button type="submit" id="modify_equipment"class="btn btn-primary btn-danger">Διαγραφή / Τροποποίηση Εξαρτήματος</button>
				</div>
			';
		}

		$equipQuery = $db->prepare("SELECT * FROM equip_svds"); 
	 	$equipQuery->execute();
	 	echo '
 					  <div class="container">
 						<table class="table table-bordered">
						  <thead class="thead-dark">
						    <tr>
						      <th scope="col">Εικόνα</th>
						      <th scope="col">Όνομα</th>
						      <th scope="col">Έτος απόκτησης</th>
						      <th scope="col">Ιδιοκτήτης</th>
						      <th scope="col">Ποσότητα</th>
						      <th scope="col">Τοποθεσία</th>
						    </tr>
						  </thead>
						  ';
	 	while($equipQueryResult=$equipQuery->fetch(PDO::FETCH_ASSOC)){
	 		if (($equipQueryResult['quantity']) > 0 ){
	 		echo '
 						  <tbody>
					      <td><img src="uploadedImages/'.$equipQueryResult['real_filename'].'"/></td>
					      <td>'.$equipQueryResult['name_e'].'</td>
					      <td>'.$equipQueryResult['buy_year_e'].'</td>
					      <td>'.$equipQueryResult['owner_name'].'</td>
					      <td>'.$equipQueryResult['quantity'].'</td>
					      <td>'.$equipQueryResult['location_e'].'</td>
					      </div> ';
			}
					    
					}
	} else {
		header("Location: index.php");
	}
	include("views/footer.php");
?>