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

if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "add") {
	if ($type == 1 OR $type == 2 OR $type == 3){
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


		if(isset($_POST['addProduct'])){
			$short_desc = filter_var($_POST['short_desc'],FILTER_SANITIZE_STRING);	
			$comments = filter_var($_POST['comments'],FILTER_SANITIZE_STRING);
			$owner_name = filter_var($_POST['owner_name'],FILTER_SANITIZE_STRING);
			$name_e = filter_var($_POST['name_e'],FILTER_SANITIZE_STRING);
			$location_e = filter_var($_POST['location_e'],FILTER_SANITIZE_STRING);
			$serial_number = filter_var($_POST['serial_number'],FILTER_SANITIZE_NUMBER_FLOAT);
			$_SESSION['short_desc'] = $short_desc;
			$_SESSION['comments'] = $comments;
			$_SESSION['owner_name'] = $owner_name;
			$_SESSION['name_e'] = $name_e;
			$_SESSION['location_e'] = $location_e;
			$_SESSION['serial_number'] = $serial_number;

		try{
			
			$departmentName = $_POST['name_dep'];
			$departmentQuerySQL = "SELECT * FROM department_svds WHERE name_dep= :idDepartment";
		 	$departmentQuerySTMT = $db->prepare($departmentQuerySQL);
		 	$departmentQuerySTMT->bindParam(':idDepartment', $departmentName, PDO::PARAM_INT);
		 	$departmentQuerySTMT->execute();
		 	while($departmentQuerySTMTResult=$departmentQuerySTMT->fetch(PDO::FETCH_ASSOC)){
	 			$departmentID = $departmentQuerySTMTResult['id_dep'];
	 		}

			$providerName = $_POST['name_p'];
			$providerQuerySQL = "SELECT * FROM provider_svds WHERE name_p= :idProvider";
		 	$providerQuerySTMT = $db->prepare($providerQuerySQL);
		 	$providerQuerySTMT->bindParam(':idProvider', $providerName, PDO::PARAM_INT);
		 	$providerQuerySTMT->execute();
		 	while($providerQuerySTMTResult=$providerQuerySTMT->fetch(PDO::FETCH_ASSOC)){
	 			$providerID = $providerQuerySTMTResult['id_p'];
	 		}

			$add_descriptionSQL = "INSERT INTO description_svds (short_desc) VALUES (:short_desc)";
			$add_descriptionSTMT = $db->prepare($add_descriptionSQL);
			$add_descriptionSTMT->bindParam(':short_desc', $short_desc);
			$add_descriptionSTMT->execute();	
			$descriptionID = $db->lastInsertId();

			$add_commentSQL = "INSERT INTO comments_svds (id_equip_com, id_user_com, comments, date_com) VALUES (:id_equip_com, :id_user_com, :comments, NOW())";
			$add_commentSTMT = $db->prepare($add_commentSQL);
			$add_commentSTMT->bindParam(':comments', $comments);
			$add_commentSTMT->bindParam(':id_equip_com', $equipID);
			$add_commentSTMT->bindParam(':id_user_com', $_SESSION['id']);
			$add_commentSTMT->execute();
			$commentID = $db->lastInsertId();

			

			if ($_POST['isborrowed'] == 0){
				$conditionState = 0;
				if ($_POST['retired'] == 0){
					$retiredState = 0;
				}else{
					$retiredState = 1;
				}
			}else{
				$conditionState = 1;
				$retiredState = 0;
			}

			if ($_POST['quantity']){
				$quantity = $_POST['quantity'];
			}else{
				$quantity = 1;
			}		

			$add_equipmentSQL = "INSERT INTO equip_svds (name_e, owner_name, department, provider_e, isborrowed, comment_e, quantity, retired, short_desc_e, location_e, serial_number) VALUES (:name_e, :owner_name, :department, :provider_e, :isborrowed, :comment_e, :quantity, :retired, :short_desc_e, :location_e, :serial_number)";
			$add_equipmentSTMT = $db->prepare($add_equipmentSQL);
			$add_equipmentSTMT->bindParam(':name_e', $name_e);
			$add_equipmentSTMT->bindParam(':owner_name', $owner_name);
			$add_equipmentSTMT->bindParam(':department', $departmentID);
			$add_equipmentSTMT->bindParam(':provider_e', $providerID);
			$add_equipmentSTMT->bindParam(':isborrowed', $conditionState);
			$add_equipmentSTMT->bindParam(':comment_e', $commentID);
			$add_equipmentSTMT->bindParam(':quantity', $quantity);
			$add_equipmentSTMT->bindParam(':retired', $retiredState);
			$add_equipmentSTMT->bindParam(':short_desc_e', $descriptionID);
			$add_equipmentSTMT->bindParam(':location_e', $location_e);
			$add_equipmentSTMT->bindParam(':serial_number', $serial_number);
			$add_equipmentSTMT->execute();
			$equipID = $db->lastInsertId();

		    $add_commentSQL = "UPDATE comments_svds SET id_equip_com= :id_equip_com WHERE id_comment= :idComment";
			$add_commentSTMT = $db->prepare($add_commentSQL);
			$add_commentSTMT->bindParam(':idComment', $commentID);
			$add_commentSTMT->bindParam(':id_equip_com', $equipID);
			$add_commentSTMT->execute();

			$_SESSION['short_desc'] = "";
			$_SESSION['comments'] = "";
			$_SESSION['owner_name'] = "";
			$_SESSION['name_e'] = "";
			$_SESSION['location_e'] = "";
			$_SESSION['serial_number'] = "";

			echo '<p class="alert alert-info container">Επιτυχής καταχώρηση αποτελεσμάτων<br></p>';
			header("Refresh:0; url=equipmentViewForTeacher.php"); 
	            
		}    
		catch(PDOException $e)
	    {
		    echo "Error: " . $e->getMessage();
	    } 

		}

		if (isset($_SESSION['name_e'])) {
			$name_e = $_SESSION['name_e'];
		}else {
			$name_e = "";
		}

		if (isset($_SESSION['owner_name'])) {
			$owner_name = $_SESSION['owner_name'];
		}else {
			$owner_name = "";
		}

		if (isset($_SESSION['location_e'])) {
			$location_e = $_SESSION['location_e'];
		}else {
			$location_e = "";
		}

		if (isset($_SESSION['quantity'])) {
			$quantity = $_SESSION['quantity'];
		}else {
			$quantity = "";
		}

		if (isset($_SESSION['serial_number'])) {
			$serial_number = $_SESSION['serial_number'];
		}else {
			$serial_number = "";
		}

		if (isset($_SESSION['name_p'])) {
			$name_p = $_SESSION['name_p'];
		}else {
			$name_p = "";
		}

		if (isset($_SESSION['name_dep'])) {
			$name_dep = $_SESSION['name_dep'];
		}else {
			$name_dep = "";
		}

		if (isset($_SESSION['isborrowed'])) {
			$isborrowed = $_SESSION['isborrowed'];
		}else {
			$isborrowed = 0;
		}

		if (isset($_SESSION['retired'])) {
			$retired = $_SESSION['retired'];
		}else {
			$retired = 0;
		}

		if (isset($_SESSION['short_desc'])) {
			$short_desc = $_SESSION['short_desc'];
		}else {
			$short_desc = "";
		}

		if (isset($_SESSION['comments'])) {
			$comments = $_SESSION['comments'];
		}else {
			$comments = "";
		}


		echo '
			<div class="container">
				<h3>Απαιτούμενες Πληροφορίες</h3>
				<form method="post">
				<div class="row">
		    		<div class="col-md-4">
		   				<div class="form-group">
				    		<label for="name_e">Όνομα Εξαρτήματος*:</label>
						    <input type="text" class="form-control" id="name_e" name="name_e" placeholder="Όνομα Εξαρτήματος" value="'.$name_e.'" required>

						    <label for="owner_name">Ονοματεπώνυμο Κατόχου:</label>
						    <input type="text" class="form-control" id="owner_name" name="owner_name" value="'.$owner_name.'" placeholder="Όνομα Κατόχου">


						    <label for="location_e">Τοποθεσία*:</label>
						    <input type="text" class="form-control" id="location_e" name="location_e" value="'.$location_e.'" placeholder="Τοποθεσία">

						    <label for="quantity">Ποσότητα:</label>
						    <input type="text" class="form-control" id="quantity" name="quantity" value="'.$quantity.'" placeholder="Ποσότητα">
						</div>
					</div>
					<div class="col-md-4">
						<label for="serial_number">Σειριακός Αριθμός*:</label>
						<input type="text" class="form-control" id="serial_number" name="serial_number" value="'.$serial_number.'" placeholder="Σειριακός Αριθμός" required>
						<br>
						<div class="form-group">	
							<label for="name_p">Όνομα Παρόχου*:</label>
							<select class="form-control" name="name_p" id="name_p" value="'.$name_p.'" required>
		';
		foreach ($providersNameArray as $providerName) {
				echo' <option>'.$providerName.'</option> ';	
		}	
		echo'
							</select>
							<a id="newProviderAdd" href="provider.php">Διαχείριση Προμηθευτών</a>
							<br>
							<label for="name_dep">Όνομα Τμήματος*:</label>
							<select class="form-control" name="name_dep" id="name_dep" value="'.$name_dep.'" required>				
		';				
		foreach ($departmentsNameArray as $departmentName) {
	  		echo' <option>'.$departmentName.'</option> ';	
		}	
		echo'		
							</select>
							<a id="newDepartmentAdd" href="departments.php">Διαχείριση Τμημάτων</a>							
							</div> 
		    			</div>
	       				<div class="col-md-4">
	            			<div class="form-group">
								<label for="isborrowed">Διαθεσιμότητα :</label>
							    <select class="form-control" name="isborrowed" id="isborrowed" value="'.$isborrowed.'">
				  				<option value="0">Δεν είναι διαθέσιμο</option>
				  				<option value="1" selected>Είναι διαθέσιμο</option>
								</select>

							    <label for="retired">Κατάσταση Απόσυρσης:</label>
							    <select class="form-control" name="retired" id="retired" value="'.$retired.'" disabled>
				  				<option value="0">Δεν έχει αποσυρθεί</option>
				  				<option value="1">Έχει αποσυρθεί</option>
								</select>		

								<label for="short_desc">Σύντομη Περιγραφή:</label>
								<input type="text" class="form-control" id="short_desc" name="short_desc" placeholder="Δώστε σύντομη περιγραφή" value="'.$short_desc.'">
								
								<label for="comments">Σχόλια:</label>
								<input type="text" class="form-control" id="comments" name="comments" placeholder="Αφήστε το σχόλιο σας" value="'.$comments.'">
							</div>
						</div>
					</div>
					<button name="addProduct" id="addProduct" type="submit" class="btn btn-primary">Προσθήκη Εξαρτήματος</button>
				</form>
			</div>
		';	
	}else{
		header("Location: equipment.php");
		die("Δεν έχετε διακαιώματα πρόσβασης στη σελίδα.");
	}
 
}

if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "delete") {
	if (isset($_GET['id_equip'])){
		$idToDelete= filter_var($_GET['id_equip'],FILTER_SANITIZE_NUMBER_FLOAT);
		$deleteQueryBorrowSQL = "DELETE  FROM borrow_svds WHERE id_equip_borrow=  :idToDelete";
		$deleteQueryBorrowSTMT = $db->prepare($deleteQueryBorrowSQL);
		$deleteQueryBorrowSTMT->bindParam(':idToDelete', $idToDelete, PDO::PARAM_INT);
		$deleteQueryBorrowSTMT->execute();
		
		$deleteQueryEquipSQL = "DELETE  FROM equip_svds WHERE id_equip=  :idToDelete";
		$deleteQueryEquipSTMT = $db->prepare($deleteQueryEquipSQL);
		$deleteQueryEquipSTMT->bindParam(':idToDelete', $idToDelete, PDO::PARAM_INT);
		$deleteQueryEquipSTMT->execute();

		$deleteQueryCommentsSQL = "DELETE  FROM comments_svds WHERE id_equip_com=  :idToDelete";
		$deleteQueryCommentsSTMT = $db->prepare($deleteQueryCommentsSQL);
		$deleteQueryCommentsSTMT->bindParam(':idToDelete', $idToDelete, PDO::PARAM_INT);
		$deleteQueryCommentsSTMT->execute();
		
		header("Location: equipmentViewForTeacher.php");
		die();	
	}else{
		header("Location: equipmentViewForTeacher.php");
		die("Δεν δόθηκε σωστό ID εξαρτήματος.");
	}
}


if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "update") {
	if (isset($_GET['id_equip'])){
		$idToChange= filter_var($_GET['id_equip'],FILTER_SANITIZE_NUMBER_FLOAT);
		$equipQuerySQL = "SELECT * FROM equip_svds WHERE id_equip= :idToChange"; 
		$equipQuerySTMT = $db->prepare($equipQuerySQL);
		$equipQuerySTMT->bindParam(':idToChange', $idToChange, PDO::PARAM_INT);
		$equipQuerySTMT->execute();
		while($equipQuerySTMTResult=$equipQuerySTMT->fetch(PDO::FETCH_ASSOC)){
	 		$depID= $equipQuerySTMTResult['department'];
			$descID= $equipQuerySTMTResult['short_desc_e'];
			$comID= $equipQuerySTMTResult['comment_e'];
			$provID= $equipQuerySTMTResult['provider_e'];
			$equipmentName = $equipQuerySTMTResult['name_e'];
			$equipmentBuyYear = $equipQuerySTMTResult['buy_year_e'];
			$equipmentBuyMethod = $equipQuerySTMTResult['buy_method_e'];
			$equipmentOwnerName = $equipQuerySTMTResult['owner_name'];
			$equipmentIsBorrowed = $equipQuerySTMTResult['isborrowed'];
			$equipmentRetired = $equipQuerySTMTResult['retired'];
			$equipmentLocation = $equipQuerySTMTResult['location_e'];
			$equipmentQuantity = $equipQuerySTMTResult['quantity'];
			$equipmentSerialNumber = $equipQuerySTMTResult['serial_number'];
	 	}
		

		$departmentQuerySQL = "SELECT * FROM department_svds WHERE id_dep= :idDepartment"; 
		$departmentQuerySTMT = $db->prepare($departmentQuerySQL);
		$departmentQuerySTMT->bindParam(':idDepartment', $depID, PDO::PARAM_INT);
		$departmentQuerySTMT->execute();
		while($departmentQuerySTMTResult=$departmentQuerySTMT->fetch(PDO::FETCH_ASSOC)){
	 		$departmentName = $departmentQuerySTMTResult['name_dep'];
	 		$departmentTelephone = $departmentQuerySTMTResult['telephone_dep'];
	 	}

	 	$descriptionQuerySQL = "SELECT * FROM description_svds WHERE id_desc= :idDescription"; 
		$descriptionQuerySTMT = $db->prepare($descriptionQuerySQL);
		$descriptionQuerySTMT->bindParam(':idDescription', $descID, PDO::PARAM_INT);
		$descriptionQuerySTMT->execute();
		while($descriptionQuerySTMTResult=$descriptionQuerySTMT->fetch(PDO::FETCH_ASSOC)){
	 		$shortDescription = $descriptionQuerySTMTResult['short_desc'];
	 		$longDescription = $descriptionQuerySTMTResult['long_desc'];
	 	}

	 	$providerQuerySQL = "SELECT * FROM provider_svds WHERE id_p= :idProvider"; 
		$providerQuerySTMT = $db->prepare($providerQuerySQL);
		$providerQuerySTMT->bindParam(':idProvider', $provID, PDO::PARAM_INT);
		$providerQuerySTMT->execute();
		while($providerQuerySTMTResult=$providerQuerySTMT->fetch(PDO::FETCH_ASSOC)){
	 		$providerName = $providerQuerySTMTResult['name_p'];
	 		$providerTelephone = $providerQuerySTMTResult['telephone_p'];
	 		$providerWebsite = $providerQuerySTMTResult['website_p'];
	 		$providerEmail = $providerQuerySTMTResult['email_p'];
	 		$providerSupport = $providerQuerySTMTResult['support_p'];
	 		$providerComments = $providerQuerySTMTResult['comments_p'];
	 	}

		$commentsQuerySQL = "SELECT * FROM comments_svds WHERE id_comment= :idComment"; 
		$commentsQuerySTMT = $db->prepare($commentsQuerySQL);
		$commentsQuerySTMT->bindParam(':idComment', $comID, PDO::PARAM_INT);
		$commentsQuerySTMT->execute();
		if ($commentsQuerySTMT->rowCount() > 0){
			while($commentsQuerySTMTResult=$commentsQuerySTMT->fetch(PDO::FETCH_ASSOC)){
	 			$comments = $commentsQuerySTMTResult['comments'];
	 		}			
		}else {
			$comments = "Δεν υπάρχει καταχωρυμένο σχόλιο.";
		}
	 	



		if(isset($_POST['addProduct'])){
		$name_dep = filter_var($_POST['name_dep'],FILTER_SANITIZE_STRING);
		$telephone_dep = filter_var($_POST['telephone_dep'],FILTER_SANITIZE_NUMBER_FLOAT);
		$short_desc = filter_var($_POST['short_desc'],FILTER_SANITIZE_STRING);	
		$long_desc = filter_var($_POST['long_desc'],FILTER_SANITIZE_STRING);
		$comments = filter_var($_POST['comments'],FILTER_SANITIZE_STRING);
		$owner_name = filter_var($_POST['owner_name'],FILTER_SANITIZE_STRING);
		$name_e = filter_var($_POST['name_e'],FILTER_SANITIZE_STRING);
		$name_p = filter_var($_POST['name_p'],FILTER_SANITIZE_STRING);
		$telephone_p = filter_var($_POST['telephone_p'],FILTER_SANITIZE_NUMBER_FLOAT);
		$comments_p = filter_var($_POST['comments_p'],FILTER_SANITIZE_STRING);
		$website_p = filter_var($_POST['website_p'],FILTER_SANITIZE_STRING);
		$support_p = filter_var($_POST['support_p'],FILTER_SANITIZE_STRING);
		$location_e = filter_var($_POST['location_e'],FILTER_SANITIZE_STRING);
		$serial_number = filter_var($_POST['serial_number'],FILTER_SANITIZE_NUMBER_FLOAT);
		$owner_name = filter_var($_POST['owner_name'],FILTER_SANITIZE_STRING);

		if (isset($_POST['buy_method_e']) AND (!empty($_POST['buy_method_e']))){
			$buy_method_e = filter_var($_POST['buy_method_e'],FILTER_SANITIZE_STRING);
		}else {
			$buy_method_e = "";
		}

		if (isset($_POST['buy_year_e']) AND (!empty($_POST['buy_year_e']))){
			$buy_year_e = filter_var($_POST['buy_year_e'],FILTER_SANITIZE_NUMBER_FLOAT);
		}else {
			$buy_year_e = 0;
		}

		
		try{
			$add_departmentSQL = "UPDATE department_svds SET  name_dep= :name_dep, telephone_dep= :telephone_dep  WHERE id_dep= :depID ";
			$add_departmentSTMT = $db->prepare($add_departmentSQL);
			$add_departmentSTMT->bindParam(':depID', $depID, PDO::PARAM_INT); 			
 			$add_departmentSTMT->bindParam(':name_dep', $name_dep);
 			$add_departmentSTMT->bindParam(':telephone_dep', $telephone_dep);
 			$add_departmentSTMT->execute();

			$add_descriptionSQL = "UPDATE description_svds SET short_desc= :short_desc, long_desc= :long_desc WHERE id_desc= :descID";
			$add_descriptionSTMT = $db->prepare($add_descriptionSQL);
			$add_descriptionSTMT->bindParam(':descID', $descID, PDO::PARAM_INT);
		    $add_descriptionSTMT->bindParam(':short_desc', $short_desc);
		    $add_descriptionSTMT->bindParam(':long_desc', $long_desc);
		    $add_descriptionSTMT->execute();	

			$add_commentSQL = "UPDATE comments_svds SET id_equip_com= :id_equip_com, id_user_com= :id_user_com, comments= :comments, date_com= NOW() WHERE id_comment= :comID";
			$add_commentSTMT = $db->prepare($add_commentSQL);
			$add_commentSTMT->bindParam(':comID', $comID, PDO::PARAM_INT);
			$add_commentSTMT->bindParam(':comments', $comments);
			$add_commentSTMT->bindParam(':id_equip_com', $idToChange);
			$add_commentSTMT->bindParam(':id_user_com', $_SESSION['id']);
			$add_commentSTMT->execute();

			$add_providerSQL = "UPDATE provider_svds SET name_p= :name_p, telephone_p= :telephone_p, website_p= :website_p, email_p= :email_p, support_p= :support_p, comments_p= :comments_p WHERE id_p= :provID";
			$add_providerSTMT = $db->prepare($add_providerSQL);
			$add_providerSTMT->bindParam(':provID', $provID, PDO::PARAM_INT);
			$add_providerSTMT->bindParam(':name_p', $name_p);
			$add_providerSTMT->bindParam(':telephone_p', $telephone_p);
			$add_providerSTMT->bindParam(':website_p', $website_p);
			$add_providerSTMT->bindParam(':email_p', $_POST['email_p']);
			$add_providerSTMT->bindParam(':support_p', $support_p);
			$add_providerSTMT->bindParam(':comments_p', $comments_p);
			$add_providerSTMT->execute();		
			
			if ($_POST['isborrowed'] == 0){
				$conditionState = 0;
				if ($_POST['retired'] == 0){
					$retiredState = 0;
				}else{
					$retiredState = 1;
				}
			}else{
				$conditionState = 1;
				$retiredState = 0;
			}

			if (isset($_POST['quantity'])) {
				$quantity = $_POST['quantity'];
			}else{
				$quantity = 1;
			}		 		

			$add_equipmentSQL = "UPDATE equip_svds SET name_e= :name_e, buy_method_e= :buy_method_e, buy_year_e= :buy_year_e, owner_name= :owner_name, department= :department, provider_e= :provider_e, isborrowed= :isborrowed, comment_e= :comment_e, retired= :retired, short_desc_e= :short_desc_e, location_e= :location_e, quantity= :quantity, serial_number= :serial_number WHERE id_equip= :idToChange";
			$add_equipmentSTMT = $db->prepare($add_equipmentSQL);
			$add_equipmentSTMT->bindParam(':idToChange', $idToChange, PDO::PARAM_INT);
		    $add_equipmentSTMT->bindParam(':name_e', $name_e);
		    $add_equipmentSTMT->bindParam(':buy_method_e', $buy_method_e);
		    $add_equipmentSTMT->bindParam(':buy_year_e', $buy_year_e);
		    $add_equipmentSTMT->bindParam(':owner_name', $owner_name);
		    $add_equipmentSTMT->bindParam(':department', $depID);
		    $add_equipmentSTMT->bindParam(':provider_e', $provID);
		    $add_equipmentSTMT->bindParam(':isborrowed', $conditionState);
		    $add_equipmentSTMT->bindParam(':comment_e', $comID);
		    $add_equipmentSTMT->bindParam(':retired', $retiredState);
		    $add_equipmentSTMT->bindParam(':short_desc_e', $descID);
		    $add_equipmentSTMT->bindParam(':location_e', $location_e);
		    $add_equipmentSTMT->bindParam(':quantity', $quantity);
		    $add_equipmentSTMT->bindParam(':serial_number', $serial_number);
		    $add_equipmentSTMT->execute();

		   

		   	echo '<p class="alert alert-info container">Επιτυχής καταχώρηση αποτελεσμάτων<br></p>';
		   	echo '<meta http-equiv="refresh" content="0; URL=equipmentViewForTeacher.php">';		    
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
				        <div class="form-group">
						    <label for="name_e">Όνομα Εξαρτήματος:</label>
						    <input type="text" class="form-control" id="name_e" name="name_e" value="'.$equipmentName.'">

						    <label for="buy_method_e">Τρόπος Απόκτησης:</label>
			    			<input type="text" class="form-control" id="buy_method_e" name="buy_method_e" value="'.$equipmentBuyMethod.'" >

						    <label for="buy_year_e">Έτος Απόκτησης:</label>
						    <input type="text" class="form-control" id="buy_year_e" name="buy_year_e" value="'.$equipmentBuyYear.'">

						    <label for="owner_name">Όνομα Κατόχου:</label>
						    <input type="text" class="form-control" id="owner_name" name="owner_name" value="'.$equipmentOwnerName.'">

						    <label for="isborrowed">Διαθεσιμότητα :</label>
		    				<select class="form-control" name="isborrowed" id="isborrowed" value"'.$equipmentIsBorrowed.'">
			  				<option value="0">Δεν είναι διαθέσιμο</option>
							<option value="1">Είναι διαθέσιμο</option>
							</select>

						    <label for="retired">Κατάσταση Απόσυρσης:</label>
						    <select class="form-control" name="retired" id="retired" value="'.$equipmentRetired.'">
								<option value="0">Δεν έχει αποσυρθεί</option>
								<option value="1">Έχει αποσυρθεί</option>
							</select>

						    <label for="location_e">Τοποθεσία label:</label>
						    <input type="text" class="form-control" id="location_e" name="location_e" value="'.$equipmentLocation.'">

						    <label for="quantity">Ποσότητα:</label>
						    <input type="text" class="form-control" id="quantity" name="quantity" value="'.$equipmentQuantity.'">

						    <label for="serial_number">Σειριακός Αριθμός:</label>
						    <input type="text" class="form-control" id="serial_number" name="serial_number" value="'.$equipmentSerialNumber.'">
						</div> 
				    </div>
				    <div class="col-md-4">
				        <h4>Πληροφορίες Παρόχου</h4>
				        <div class="form-group">	
								<label for="name_p">Όνομα Παρόχου:</label>
								<input type="text" class="form-control" id="name_p" name="name_p" value="'.$providerName.'">
			
								<label for="telephone_p">Τηλέφωνο Παρόχου:</label>
								<input type="text" class="form-control" id="telephone_p" name="telephone_p" value="'.$providerTelephone.'">

								<label for="website_p">Ιστοσελίδα Παρόχου:</label>
								<input type="text" class="form-control" id="website_p" name="website_p" value="'.$providerWebsite.'">


								<label for="email_p">Email Παρόχου:</label>
								<input type="email" class="form-control" id="email_p" name="email_p" value="'.$providerEmail.'">


								<label for="support_p">Υποστήριξη Παρόχου:</label>
								<input type="text" class="form-control" id="support_p" name="support_p" value="'.$providerSupport.'">


								<label for="comments_p">Σχόλια Παρόχου:</label>
								<input type="text" class="form-control" id="comments_p" name="comments_p" value="'.$providerComments.'">
						</div>
					</div>	
					<div class="col-md-4">
	  					<h4>Πληροφορίες Τμήματος</h4>
				  				<label for="name_dep">Όνομα Τμήματος:</label>
						 		<input type="text" class="form-control" id="name_dep" name="name_dep" value="'.$departmentName.'">

						    	<label for="telephone_dep">Τηλέφωνο Τμήματος:</label>
								<input type="text" class="form-control" id="telephone_dep" name="telephone_dep" value="'.$departmentTelephone.'">

						<h4>Περιγραφή</h4>			    
								<label for="short_desc">Σύντομη Περιγραφή:</label>
						    	<input type="text" class="form-control" id="short_desc" name="short_desc" value="'.$shortDescription.'">
						        
								<label for="long_desc">Εκτενής Περιγραφή:</label>
						    	<input type="text" class="form-control" id="long_desc" name="long_desc" value="'.$longDescription.'">	        

						<h4>Επιπλέον Σχόλια</h4>
						<div class="form-group">	
						        <label for="comments">Σχόλια:</label>
							    <input type="text" class="form-control" id="comments" name="comments" value="'.$comments.'">	   			
						</div> 
			        </div>
				</div>
			<button id="addProduct" name ="addProduct" type="submit" class="btn btn-primary">Ανανέωση Εξαρτήματος</button>
			</form>
		</div> <br><br><br>

	';
	}else{
		header("Location: equipmentViewForTeacher.php");
		die("Δεν δόθηκε σωστό ID τμήματος.");
	}
}	

if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "saveComment") {
        
    $idEquipToSave = filter_var($_POST['id_equip'],FILTER_SANITIZE_NUMBER_FLOAT);
    $answerComment = filter_var($_POST['answerComment'],FILTER_SANITIZE_STRING);
    $commentSaveQuerySQL = "INSERT INTO comments_svds (id_equip_com, id_user_com, comments, date_com) 
    VALUES (:id_equip_com, :id_user_com, :comments, NOW())";
    $commentSaveQuerySTMT = $db->prepare($commentSaveQuerySQL);
    $commentSaveQuerySTMT->bindParam(':id_equip_com', $idEquipToSave, PDO::PARAM_INT);
    $commentSaveQuerySTMT->bindParam(':id_user_com', $id, PDO::PARAM_INT);
    $commentSaveQuerySTMT->bindParam(':comments', $answerComment, PDO::PARAM_INT); 
    $commentSaveQuerySTMT->execute(); 
    header("Refresh:0; url=equipment_return.php"); 
    die("Δεν έχετε συνδεθεί");       
         
}

if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "deleteComment") {
	if (isset($_GET['idToShow']) AND isset($_GET['id_comment']) ){
		$idToDelete= filter_var($_GET['id_comment'],FILTER_SANITIZE_NUMBER_FLOAT);
		$idToShow = filter_var($_GET['idToShow'],FILTER_SANITIZE_NUMBER_FLOAT);

		$updateEquipmentSQL = "UPDATE equip_svds SET comment_e= :nothing WHERE id_equip= :idToShow";
		$updateEquipmentSTMT = $db->prepare($updateEquipmentSQL);
		$updateEquipmentSTMT->bindParam(':nothing', $nothing);
		$updateEquipmentSTMT->bindParam(':idToShow', $idToShow);
		$updateEquipmentSTMT->execute();

		$deleteQueryCommnetSQL = "DELETE  FROM comments_svds WHERE id_comment= :idToDelete";
		$deleteQueryCommnetSTMT = $db->prepare($deleteQueryCommnetSQL);
		$deleteQueryCommnetSTMT->bindParam(':idToDelete', $idToDelete, PDO::PARAM_INT);
		$deleteQueryCommnetSTMT->execute();
		
		header("Location: equipment_details.php?id_equip=$idToShow");
		die();	
	}else{
		header("Location: equipment_details.php?id_equip=$idToShow");
		die("Δεν δόθηκε σωστό ID εξαρτήματος.");
	}
}


include("views/footer.php");
echo '
	</body>
	</html>
';
?>  