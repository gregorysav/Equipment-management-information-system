<?php
include("variables_file.php");
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");

	if (!isset($_SESSION['email'])){

		header("Location: index.php");
		die("Δεν έχετε συνδεθεί");
	}	
	
	$idToChange= filter_var($_GET['id_equip'],FILTER_SANITIZE_NUMBER_FLOAT);
	$equipQuerySQL = "SELECT * FROM equip_svds INNER JOIN department_svds on equip_svds.department = department_svds.id_dep INNER JOIN provider_svds on equip_svds.provider_e = provider_svds.id_p INNER JOIN comments_svds on equip_svds.comment_e = comments_svds.id_comment INNER JOIN description_svds on equip_svds.short_desc_e = description_svds.id_desc WHERE equip_svds.id_equip = $idToChange"; 
	$equipQuerySTMT = $db->prepare($equipQuerySQL);
	$equipQuerySTMT->execute();
	$result=$equipQuerySTMT->fetch(PDO::FETCH_ASSOC);

	$depID=$result['id_dep'];
	$descID=$result['id_desc'];
	$comID=$result['id_comment'];
	$provID=$result['id_p'];


	if(isset($_POST['add'])){
		try{
			$add_departmentSQL = "UPDATE department_svds SET  name_dep= :name_dep, telephone_dep= :telephone_dep  WHERE id_dep= :depID ";
			$add_departmentSTMT = $db->prepare($add_departmentSQL);
			$add_departmentSTMT->bindParam(':depID', $depID, PDO::PARAM_INT); 			
 			$add_departmentSTMT->bindParam(':name_dep', $_POST['name_dep']);
 			$add_departmentSTMT->bindParam(':telephone_dep', $_POST['telephone_dep']);
 			$add_departmentSTMT->execute();

			$add_descriptionSQL = "UPDATE description_svds SET short_desc= :short_desc, long_desc= :long_desc WHERE id_desc= :descID";
			$add_descriptionSTMT = $db->prepare($add_descriptionSQL);
			$add_descriptionSTMT->bindParam(':descID', $descID, PDO::PARAM_INT);
		    $add_descriptionSTMT->bindParam(':short_desc', $_POST['short_desc']);
		    $add_descriptionSTMT->bindParam(':long_desc', $_POST['long_desc']);
		    $add_descriptionSTMT->execute();	

			$add_commentSQL = "UPDATE comments_svds SET id_equip_com= :id_equip_com, id_user_com= :id_user_com, comments= :comments, date_com= NOW() WHERE id_comment= :comID";
			$add_commentSTMT = $db->prepare($add_commentSQL);
			$add_commentSTMT->bindParam(':comID', $comID, PDO::PARAM_INT);
			$add_commentSTMT->bindParam(':comments', $_POST['comments']);
			$add_commentSTMT->bindParam(':id_equip_com', $idToChange);
			$add_commentSTMT->bindParam(':id_user_com', $_SESSION['aem']);
			$add_commentSTMT->execute();

			$add_providerSQL = "UPDATE provider_svds SET name_p= :name_p, telephone_p= :telephone_p, website_p= :website_p, email_p= :email_p, support_p= :support_p, comments_p= :comments_p WHERE id_p= :provID";
			$add_providerSTMT = $db->prepare($add_providerSQL);
			$add_providerSTMT->bindParam(':provID', $provID, PDO::PARAM_INT);
			$add_providerSTMT->bindParam(':name_p', $_POST['name_p']);
			$add_providerSTMT->bindParam(':telephone_p', $_POST['telephone_p']);
			$add_providerSTMT->bindParam(':website_p', $_POST['website_p']);
			$add_providerSTMT->bindParam(':email_p', $_POST['email_p']);
			$add_providerSTMT->bindParam(':support_p', $_POST['support_p']);
			$add_providerSTMT->bindParam(':comments_p', $_POST['comments_p']);
			$add_providerSTMT->execute();		
			
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

			$add_equipmentSQL = "UPDATE equip_svds SET name_e= :name_e, buy_method_e= :buy_method_e, buy_year_e= :buy_year_e, owner_name= :owner_name, department= :department, provider_e= :provider_e, isborrowed= :isborrowed, comment_e= :comment_e, retired= :retired, short_desc_e= :short_desc_e, location_e= :location_e, serial_number= :serial_number WHERE id_equip= :idToChange";
			$add_equipmentSTMT = $db->prepare($add_equipmentSQL);
			$add_equipmentSTMT->bindParam(':idToChange', $idToChange, PDO::PARAM_INT);
		    $add_equipmentSTMT->bindParam(':name_e', $_POST['name_e']);
		    $add_equipmentSTMT->bindParam(':buy_method_e', $_POST['buy_method_e']);
		    $add_equipmentSTMT->bindParam(':buy_year_e', $_POST['buy_year_e']);
		    $add_equipmentSTMT->bindParam(':owner_name', $_POST['owner_name']);
		    $add_equipmentSTMT->bindParam(':department', $depID);
		    $add_equipmentSTMT->bindParam(':provider_e', $provID);
		    $add_equipmentSTMT->bindParam(':isborrowed', $conditionState);
		    $add_equipmentSTMT->bindParam(':comment_e', $comID);
		    $add_equipmentSTMT->bindParam(':retired', $retiredState);
		    $add_equipmentSTMT->bindParam(':short_desc_e', $descID);
		    $add_equipmentSTMT->bindParam(':location_e', $_POST['location_e']);
		    $add_equipmentSTMT->bindParam(':serial_number', $_POST['serial_number']);
		    $add_equipmentSTMT->execute();

		   

		   	echo '<div clas="container"><br><a class="p-3 mb-2 bg-success text-white">Επιτυχείς καταχώρηση αποτελεσμάτων</a><br><br></div>';
		   	echo '<meta http-equiv="refresh" content="0; URL=equipment_manage.php">';		    
		}
		catch(PDOException $e)
		{
		    echo "Error: " . $e->getMessage();
		} 

	}	    

	echo '
		<div class="container">
			<form method="post">
		        <div class="row">
			     	<div class="col-md-4">
				     	<h4>Προσθήκη Πληροφοριών</h4>
				        <div class="col-md-6">
					        <div class="form-group">
							    <label for="name_e">Όνομα Εξαρτήματος:</label>
							    <input type="text" class="form-control" id="name_e" name="name_e" value="'.$result['name_e'].'" required>

							    <label for="buy_method_e">Τρόπος Απόκτησης:</label>
				    			<input type="text" class="form-control" id="buy_method_e" name="buy_method_e" value="'.$result['buy_method_e'].'"  required>

							    <label for="buy_year_e">Έτος Απόκτησης:</label>
							    <input type="text" class="form-control" id="buy_year_e" name="buy_year_e" value="'.$result['buy_year_e'].'" required>

							    <label for="owner_name">Όνομα Κατόχου:</label>
							    <input type="text" class="form-control" id="owner_name" name="owner_name" value="'.$result['owner_name'].'" required>

							    <label for="isborrowed">Διαθεσιμότητα :</label>
			    				<select class="form-control" name="isborrowed" id="isborrowed" value"'.$result['isborrowed'].'">
				  				<option>Δεν είναι διαθέσιμο</option>
  								<option>Είναι διαθέσιμο</option>
								</select>

							    <label for="retired">Κατάσταση Απόσυρσης:</label>
							    <select class="form-control" name="retired" id="retired" value="'.$result['retired'].'">
  								<option>Δεν έχει αποσυρθεί</option>
  								<option>Έχει αποσυρθεί</option>
								</select>

							    <label for="location_e">Τοποθεσία label:</label>
							    <input type="text" class="form-control" id="location_e" name="location_e" value="'.$result['location_e'].'" required>

							    <label for="serial_number">Σειριακός Αριθμός:</label>
							    <input type="text" class="form-control" id="serial_number" name="serial_number" value="'.$result['serial_number'].'" required>
							</div> 
						</div>
				    </div>
				    <div class="col-md-4">
				        <h4>Πληροφορίες Παρόχου</h4>
				        <div class="form-group">	
								<label for="name_p">Όνομα Παρόχου:</label>
								<input type="text" class="form-control" id="name_p" name="name_p" value="'.$result['name_p'].'" required>
			
								<label for="telephone_p">Τηλέφωνο Παρόχου:</label>
								<input type="text" class="form-control" id="telephone_p" name="telephone_p" value="'.$result['telephone_p'].'" required>

								<label for="website_p">Ιστοσελίδα Παρόχου:</label>
								<input type="text" class="form-control" id="website_p" name="website_p" value="'.$result['website_p'].'" required>


								<label for="email_p">Email Παρόχου:</label>
								<input type="email" class="form-control" id="email_p" name="email_p" value="'.$result['email_p'].'" required>


								<label for="support_p">Υποστήριξη Παρόχου:</label>
								<input type="text" class="form-control" id="support_p" name="support_p" value="'.$result['support_p'].'" required>


								<label for="comments_p">Σχόλια Παρόχου:</label>
								<input type="text" class="form-control" id="comments_p" name="comments_p" value="'.$result['comments_p'].'" required>
						</div>
					</div>	
					<div class="col-md-4">
	  					<h4>Πληροφορίες Τμήματος</h4>
				  				<label for="name_dep">Όνομα Τμήματος:</label>
						 		<input type="text" class="form-control" id="name_dep" name="name_dep" value="'.$result['name_dep'].'" required>

						    	<label for="telephone_dep">Τηλέφωνο Τμήματος:</label>
								<input type="text" class="form-control" id="telephone_dep" name="telephone_dep" value="'.$result['telephone_dep'].'" required>

						<h4>Περιγραφή</h4>			    
								<label for="short_desc">Σύντομη Περιγραφή:</label>
						    	<input type="text" class="form-control" id="short_desc" name="short_desc" value="'.$result['short_desc'].'" required>
						        
								<label for="long_desc">Εκτενείς Περιγραφή:</label>
						    	<input type="text" class="form-control" id="long_desc" name="long_desc" value="'.$result['long_desc'].'" required>	        

						<h4>Επιπλέον Σχόλια</h4>
						<div class="form-group">	
						        <label for="comments">Σχόλια:</label>
							    <input type="text" class="form-control" id="comments" name="comments" value="'.$result['comments'].'" required>	   			
						</div> 
			        </div>
				</div>
			<button id="add" name ="add" type="submit" class="btn btn-primary">Προσθήκη Εξαρτήματος</button>
			</form>
		</div> <br><br><br>

	';

include("views/footer.php");
?>

