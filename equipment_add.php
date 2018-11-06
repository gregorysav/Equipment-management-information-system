<?php
session_start();
	include("views/connection.php");
	include("views/header.php");
	include("views/navbar.php");


	if (array_key_exists("logout", $_GET)){
	unset($_SESSION);
	}
	if (!isset($_SESSION['email'])){

		header("Location: index.php");
	}	
		

	if(isset($_POST['add'])){

				try{
				 
				
				$add_department = $db->prepare("INSERT INTO department_svds (name_dep, telephone_dep) 
    VALUES (:name_dep, :telephone_dep)");

		    $add_department->bindParam(':name_dep', $_POST['name_dep']);
		    $add_department->bindParam(':telephone_dep', $_POST['telephone_dep']);
		    $add_department->execute();	
		    $departmentID = $db->lastInsertId();



				$add_description = $db->prepare("INSERT INTO description_svds (short_desc, long_desc) 
    VALUES (:short_desc, :long_desc)");

		    $add_description->bindParam(':short_desc', $_POST['short_desc']);
		    $add_description->bindParam(':long_desc', $_POST['long_desc']);
		    $add_description->execute();	
		    $descriptionID = $db->lastInsertId();



				$add_comment = $db->prepare("INSERT INTO comments_svds (id_equip_com, id_user_com, comments, date_com) 
    VALUES (:id_equip_com, :id_user_com, :comments, NOW())");

		    $add_comment->bindParam(':comments', $_POST['comments']);
		    $add_comment->bindParam(':id_equip_com', $_SESSION['password']);
		    $add_comment->bindParam(':id_user_com', $_SESSION['password']);
		    $add_comment->execute();
		    $commentID = $db->lastInsertId();



				$add_provider = $db->prepare("INSERT INTO provider_svds (name_p, telephone_p, website_p, email_p, support_p, comments_p) 
    VALUES (:name_p, :telephone_p, :website_p, :email_p, :support_p, :comments_p)");

		    $add_provider->bindParam(':name_p', $_POST['name_p']);
		    $add_provider->bindParam(':telephone_p', $_POST['telephone_p']);
		    $add_provider->bindParam(':website_p', $_POST['website_p']);
		    $add_provider->bindParam(':email_p', $_POST['email_p']);
		    $add_provider->bindParam(':support_p', $_POST['support_p']);
		    $add_provider->bindParam(':comments_p', $_POST['comments_p']);
		    $add_provider->execute();	
		    $providerID = $db->lastInsertId();

		   
				$add_equipment = $db->prepare("INSERT INTO equip_svds (name_e, buy_method_e, buy_year_e, owner_name, department, provider_e, isborrowed, comment_e, quantity, retired, short_desc_e, location_e, serial_number) 
    VALUES (:name_e, :buy_method_e, :buy_year_e, :owner_name, :department, :provider_e, :isborrowed, :comment_e, :quantity, :retired, :short_desc_e, :location_e, :serial_number)");

		    $add_equipment->bindParam(':name_e', $_POST['name_e']);
		    $add_equipment->bindParam(':buy_method_e', $_POST['buy_method_e']);
		    $add_equipment->bindParam(':buy_year_e', $_POST['buy_year_e']);
		    $add_equipment->bindParam(':owner_name', $_POST['owner_name']);
		    $add_equipment->bindParam(':department', $departmentID);
		    $add_equipment->bindParam(':provider_e', $providerID);
		    $add_equipment->bindParam(':isborrowed', $_POST['isborrowed']);
		    $add_equipment->bindParam(':comment_e', $commentID);
		    $add_equipment->bindParam(':quantity', $_POST['quantity']);
		    $add_equipment->bindParam(':retired', $_POST['retired']);
		    $add_equipment->bindParam(':short_desc_e', $descriptionID);
		    $add_equipment->bindParam(':location_e', $_POST['location_e']);
		    $add_equipment->bindParam(':serial_number', $_POST['serial_number']);
		    $add_equipment->execute();

		    $equipQuery = "SELECT `id_equip` FROM equip_svds LIMIT 1"; 
			$equipQuery_stmt = $db->prepare($equipQuery);
			$equipQuery_stmt->execute();
			$result=$equipQuery_stmt->fetch(PDO::FETCH_ASSOC);

		   	echo '<a class="p-3 mb-2 bg-success text-white">Επιτυχείς καταχώρηση αποτελεσμάτων</a>';
		    
		    
		}
		    

		catch(PDOException $e)
		    {
		    echo "Error: " . $e->getMessage();
		    } 

	}

?>

<!DOCTYPE html>
			<html>
			<head>
				<title></title>
			</head>
			<body>


				<div class="container">
					<form method="post">
						<div class="row">
						    <div class="col-md-4">
						        <h2>Προσθήκη Πληροφοριών</h2>
					       		<div class="form-group">
								    <label for="name_e">Όνομα Εξαρτήματος:</label>
								    <input type="text" class="form-control" id="name_e" name="name_e" placeholder="Όνομα Εξαρτήματος" required>

								    <label for="buy_method_e">Τρόπος Απόκτησης:</label>
								    <input type="text" class="form-control" id="buy_method_e" name="buy_method_e" placeholder="Τρόπος Απόκτησης" required>

								    <label for="buy_year_e">Έτος Απόκτησης:</label>
								    <input type="text" class="form-control" id="buy_year_e" name="buy_year_e" placeholder="Έτος Απόκτησης" required>

								    <label for="owner_name">Όνομα Κατόχου:</label>
								    <input type="text" class="form-control" id="owner_name" name="owner_name" placeholder="Όνομα Κατόχου" required>

								    <label for="isborrowed">Διαθεσιμότητα Δανεισμού:</label>
								    <input type="text" class="form-control" id="isborrowed" name="isborrowed" placeholder="Εισάγετε 1 εάν είναι δανεισμένο" required>

								    <label for="quantity">Ποσότητα:</label>
								    <input type="text" class="form-control" id="quantity" name="quantity" placeholder="Ποσότητα" required>

								    <label for="retired">Κατάσταση Απόσυρσης:</label>
								    <input type="text" class="form-control" id="retired" name="retired" placeholder="Εισάγετε 1 εάν έχει αποσυρθεί" required>

								    <label for="location_e">Τοποθεσία:</label>
								    <input type="text" class="form-control" id="location_e" name="location_e" placeholder="Τοποθεσία" required>

								    <label for="serial_number">Σειριακός Αριθμός:</label>
								    <input type="text" class="form-control" id="serial_number" name="serial_number" placeholder="Σειριακός Αριθμός" required>
								   
								</div>
						    </div>
						    <div class="col-md-4">
						        <h2>Πληροφορίες Παρόχου</h2>

								<div class="form-group">	
								<label for="name_p">Όνομα Παρόχου:</label>
								<input type="text" class="form-control" id="name_p" name="name_p" required>


								<label for="telephone_p">Τηλέφωνο Παρόχου:</label>
								<input type="text" class="form-control" id="telephone_p" name="telephone_p" required>

								<label for="website_p">Ιστοσελίδα Παρόχου:</label>
								<input type="text" class="form-control" id="website_p" name="website_p" required>


								<label for="email_p">Email Παρόχου:</label>
								<input type="email" class="form-control" id="email_p" name="email_p" required>


								<label for="support_p">Υποστήριξη Παρόχου:</label>
								<input type="text" class="form-control" id="support_p" name="support_p" required>


								<label for="comments_p">Σχόλια Παρόχου:</label>
								<input type="text" class="form-control" id="comments_p" name="comments_p" required>								

								</div> 
						    </div>

			                <div class="col-md-4">
						  		<h2>Πληροφορίες Τμήματος</h2>
			                    <div class="form-group">
								<label for="name_dep">Όνομα Τμήματος:</label>
								<input type="text" class="form-control" id="name_dep" name="name_dep" placeholder="Όνομα Τμήματος" required>


								<label for="telephone_dep">Τηλέφωνο Τμήματος:</label>
								<input type="text" class="form-control" id="telephone_dep" name="telephone_dep" placeholder="Τηλέφωνο Τμήματος" required>

								<h2>Περιγραφή</h2>

								<label for="short_desc">Σύντομη Περιγραφή:</label>
								<input type="text" class="form-control" id="short_desc" name="short_desc" placeholder="Δώστε σύντομη περιγραφή" required>

								<label for="long_desc">Εκτενείς Περιγραφή:</label>
								<input type="text" class="form-control" id="long_desc" name="long_desc" placeholder="Δώστε περισσότερες λεπτομέριες" >

								<h2>Επιπλέον Σχόλια</h2>

								<label for="comments">Σχόλια:</label>
								<input type="text" class="form-control" id="comments" name="comments" placeholder="Αφήστε το σχόλιο σας" required>
								</div>

						    </div>
						</div>

						<button name ="add" type="submit" class="btn btn-primary">Προσθήκη Εξαρτήματος</button>
					</form>
					</div>

</body>
</html>

<?php
	include("views/footer.php");
?>