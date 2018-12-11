<?php
include("variables_file.php");
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");

	if (!isset($_SESSION['email'])){

		header("Location: index.php");
		die("Δεν έχετε συνδεθεί");
	}	
		
	$departmentQuerySQL = "SELECT * FROM department_svds";
	$departmentQuerySTMT = $db->prepare($departmentQuerySQL);
 	$departmentQuerySTMT->execute();
 	while($departmentQuerySTMTResult=$departmentQuerySTMT->fetch(PDO::FETCH_ASSOC)){
 		$departmentsNameArray[] = $departmentQuerySTMTResult['name_dep'];
 	}

 	$providerQuerySQL = "SELECT * FROM provider_svds";
 	$providerQuerySTMT = $db->prepare($providerQuerySQL);
 	$providerQuerySTMT->execute();
 	while($providerQuerySTMTResult=$providerQuerySTMT->fetch(PDO::FETCH_ASSOC)){
 		$providersNameArray[] = $providerQuerySTMTResult['name_p'];
 	}	


	if(isset($_POST['add'])){

	try{
		
		$departmentID = array_search($_POST['name_dep'], $departmentsNameArray);
		$departmentID++;
		$providerID = array_search($_POST['name_p'], $providersNameArray);
		$providerID++;

		$add_descriptionSQL = "INSERT INTO description_svds (short_desc, long_desc) 
    VALUES (:short_desc, :long_desc)";
		$add_descriptionSTMT = $db->prepare($add_descriptionSQL);
		$add_descriptionSTMT->bindParam(':short_desc', $_POST['short_desc']);
		$add_descriptionSTMT->bindParam(':long_desc', $_POST['long_desc']);
		$add_descriptionSTMT->execute();	
		$descriptionID = $db->lastInsertId();

		$add_commentSQL = "INSERT INTO comments_svds (id_equip_com, id_user_com, comments, date_com) 
    VALUES (:id_equip_com, :id_user_com, :comments, NOW())";
		$add_commentSTMT = $db->prepare($add_commentSQL);
		$add_commentSTMT->bindParam(':comments', $_POST['comments']);
		$add_commentSTMT->bindParam(':id_equip_com', $equipID);
		$add_commentSTMT->bindParam(':id_user_com', $_SESSION['aem']);
		$add_commentSTMT->execute();
		$commentID = $db->lastInsertId();

		if ($_POST['retired'] == "Δεν έχει αποσυρθεί"){
			$retiredState = 0;
		}else{
			$retiredState = 1;
		}

		if ($_POST['isborrowed'] == "Δεν είναι διαθέσιμο"){
			$conditionState = 0;
		}else{
			$conditionState = 1;
		}

		if ($_POST['quantity']){
			$quantity = $_POST['quantity'];
		}else{
			$quantity = 1;
		}		

		$add_equipmentSQL = "INSERT INTO equip_svds (name_e, owner_name, department, provider_e, isborrowed, comment_e, quantity, retired, short_desc_e, location_e, serial_number) 
    VALUES (:name_e, :owner_name, :department, :provider_e, :isborrowed, :comment_e, :quantity, :retired, :short_desc_e, :location_e, :serial_number)";
		$add_equipmentSTMT = $db->prepare($add_equipmentSQL);
		$add_equipmentSTMT->bindParam(':name_e', $_POST['name_e']);
		$add_equipmentSTMT->bindParam(':owner_name', $_POST['owner_name']);
		$add_equipmentSTMT->bindParam(':department', $departmentID);
		$add_equipmentSTMT->bindParam(':provider_e', $providerID);
		$add_equipmentSTMT->bindParam(':isborrowed', $conditionState);
		$add_equipmentSTMT->bindParam(':comment_e', $commentID);
		$add_equipmentSTMT->bindParam(':quantity', $quantity);
		$add_equipmentSTMT->bindParam(':retired', $retiredState);
		$add_equipmentSTMT->bindParam(':short_desc_e', $descriptionID);
		$add_equipmentSTMT->bindParam(':location_e', $_POST['location_e']);
		$add_equipmentSTMT->bindParam(':serial_number', $_POST['serial_number']);
		$add_equipmentSTMT->execute();
		$equipID = $db->lastInsertId();

	    $add_commentSQL = "UPDATE comments_svds SET id_equip_com= :id_equip_com";
		$add_commentSTMT = $db->prepare($add_commentSQL);
		$add_commentSTMT->bindParam(':id_equip_com', $equipID);
		$add_commentSTMT->execute();

		header("Refresh:0; url=equipment.php"); 
        die("Τα αποτελέσματα καταχωρύθηκαν με επιτυχία");    
		    
	}
		    
	catch(PDOException $e)
    {
	    echo "Error: " . $e->getMessage();
    } 

	}

echo '
	
	<div class="container">
		<h3>Απαιτούμενες Πληροφορίες</h3>
		<form method="post">
		<div class="row">
	    <div class="col-md-4">
	   		<div class="form-group">
			    <label for="name_e">Όνομα Εξαρτήματος*:</label>
			    <input type="text" class="form-control" id="name_e" name="name_e" placeholder="Όνομα Εξαρτήματος" required>

			    <label for="owner_name">Όνομα Κατόχου:</label>
			    <input type="text" class="form-control" id="owner_name" name="owner_name" placeholder="Όνομα Κατόχου">


			    <label for="location_e">Τοποθεσία*:</label>
			    <input type="text" class="form-control" id="location_e" name="location_e" placeholder="Τοποθεσία">

			    <label for="quantity">Ποσότητα*:</label>
			    <input type="text" class="form-control" id="quantity" name="quantity" placeholder="Ποσότητα">

			</div>
		</div>
		<div class="col-md-4">
			<label for="serial_number">Σειριακός Αριθμός*:</label>
			<input type="text" class="form-control" id="serial_number" name="serial_number" placeholder="Σειριακός Αριθμός" required>
			<br>
			<div class="form-group">	
				<label for="name_p">Όνομα Παρόχου*:</label>
				<select class="form-control" name="name_p" id="name_p" required>
';
			foreach ($providersNameArray as $providerName) {
  				echo' <option>'.$providerName.'</option> ';	
			}	
echo'
			</select>
			<br>
			<label for="name_dep">Όνομα Τμήματος*:</label>
				<select class="form-control" name="name_dep" id="name_dep" required>				
';				
			foreach ($departmentsNameArray as $departmentName) {
  				echo' <option>'.$departmentName.'</option> ';	
			}	
echo'		
				</select>							
			</div> 
	    </div>
        <div class="col-md-4">
            <div class="form-group">
				<label for="isborrowed">Διαθεσιμότητα :</label>
			    <select class="form-control" name="isborrowed" id="isborrowed">
  				<option>Δεν είναι διαθέσιμο</option>
  				<option>Είναι διαθέσιμο</option>
				</select>

			    <label for="retired">Κατάσταση Απόσυρσης:</label>
			    <select class="form-control" name="retired" id="retired">
  				<option>Δεν έχει αποσυρθεί</option>
  				<option>Έχει αποσυρθεί</option>
				</select>		

				<label for="short_desc">Σύντομη Περιγραφή:</label>
				<input type="text" class="form-control" id="short_desc" name="short_desc" placeholder="Δώστε σύντομη περιγραφή">
				
				<label for="comments">Σχόλια:</label>
				<input type="text" class="form-control" id="comments" name="comments" placeholder="Αφήστε το σχόλιο σας">
			</div>
		</div>
		</div>
			<button name ="add" type="submit" class="btn btn-primary">Προσθήκη Εξαρτήματος</button>
		</form>
	</div>

';

include("views/footer.php");
?>