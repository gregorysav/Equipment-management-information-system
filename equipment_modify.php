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
	
					$idToChange=$_GET['id_equip'];
					$equipQuery = "SELECT * FROM equip_svds INNER JOIN department_svds on equip_svds.department = department_svds.id_dep INNER JOIN provider_svds on equip_svds.provider_e = provider_svds.id_p INNER JOIN comments_svds on equip_svds.comment_e = comments_svds.id_comment INNER JOIN description_svds on equip_svds.short_desc_e = description_svds.id_desc WHERE equip_svds.id_equip = $idToChange"; 
					$equipQuery_stmt = $db->prepare($equipQuery);
					$equipQuery_stmt->execute();
					$result=$equipQuery_stmt->fetch(PDO::FETCH_ASSOC);

					$depID=$result['id_dep'];
					$descID=$result['id_desc'];
					$comID=$result['id_comment'];
					$provID=$result['id_p'];

					if (isset($_GET['file'])){
						$realFilename= $_GET['file'];
						$hashFilename = md5($realFilename);
					}

     				
				    
					




	if(isset($_POST['add'])){
	// 	if(isset($_POST['id_equip'])){


					try{

				$add_department = $db->prepare("UPDATE department_svds SET  name_dep= :name_dep, telephone_dep= :telephone_dep  WHERE id_dep= $depID "); 					
 					$add_department->bindParam(':name_dep', $_POST['name_dep']);
 					$add_department->bindParam(':telephone_dep', $_POST['telephone_dep']);
 					$add_department->execute();

				$add_description = $db->prepare("UPDATE description_svds SET short_desc= :short_desc, long_desc= :long_desc WHERE id_desc= $descID");
			    $add_description->bindParam(':short_desc', $_POST['short_desc']);
			    $add_description->bindParam(':long_desc', $_POST['long_desc']);
			    $add_description->execute();	

				$add_comment = $db->prepare("UPDATE comments_svds SET id_equip_com= :id_equip_com, id_user_com= :id_user_com, comments= :comments, date_com= NOW() WHERE id_comment= $comID");

			    $add_comment->bindParam(':comments', $_POST['comments']);
			    $add_comment->bindParam(':id_equip_com', $_SESSION['password']);
			    $add_comment->bindParam(':id_user_com', $_SESSION['password']);
			    $add_comment->execute();

				$add_provider = $db->prepare("UPDATE provider_svds SET name_p= :name_p, telephone_p= :telephone_p, website_p= :website_p, email_p= :email_p, support_p= :support_p, comments_p= :comments_p WHERE id_p= $provID");
			    $add_provider->bindParam(':name_p', $_POST['name_p']);
			    $add_provider->bindParam(':telephone_p', $_POST['telephone_p']);
			    $add_provider->bindParam(':website_p', $_POST['website_p']);
			    $add_provider->bindParam(':email_p', $_POST['email_p']);
			    $add_provider->bindParam(':support_p', $_POST['support_p']);
			    $add_provider->bindParam(':comments_p', $_POST['comments_p']);
			    $add_provider->execute();		
					 		

				$add_equipment = $db->prepare("UPDATE equip_svds SET name_e= :name_e, buy_method_e= :buy_method_e, buy_year_e= :buy_year_e, owner_name= :owner_name, department= :department, provider_e= :provider_e, isborrowed= :isborrowed, comment_e= :comment_e, quantity= :quantity, retired= :retired, short_desc_e= :short_desc_e, location_e= :location_e, serial_number= :serial_number, real_filename= :real_filename, hash_filename= :hash_filename WHERE id_equip= $idToChange");

		    $add_equipment->bindParam(':name_e', $_POST['name_e']);
		    $add_equipment->bindParam(':buy_method_e', $_POST['buy_method_e']);
		    $add_equipment->bindParam(':buy_year_e', $_POST['buy_year_e']);
		    $add_equipment->bindParam(':owner_name', $_POST['owner_name']);
		    $add_equipment->bindParam(':department', $depID);
		    $add_equipment->bindParam(':provider_e', $provID);
		    $add_equipment->bindParam(':isborrowed', $_POST['isborrowed']);
		    $add_equipment->bindParam(':comment_e', $comID);
		    $add_equipment->bindParam(':quantity', $_POST['quantity']);
		    $add_equipment->bindParam(':retired', $_POST['retired']);
		    $add_equipment->bindParam(':short_desc_e', $descID);
		    $add_equipment->bindParam(':location_e', $_POST['location_e']);
		    $add_equipment->bindParam(':serial_number', $_POST['serial_number']);
		    $add_equipment->bindParam(':real_filename', $realFilename);
		    $add_equipment->bindParam(':hash_filename', $hashFilename);
		    $add_equipment->execute();

		   

		   		echo '<a class="p-3 mb-2 bg-success text-white">Επιτυχείς καταχώρηση αποτελεσμάτων</a>';
		   		echo '<meta http-equiv="refresh" content="0; URL=equipment_manage.php">';


		    	
		    
		    
		}
		    

		catch(PDOException $e)
		    {
		    echo "Error: " . $e->getMessage();
		    } 

	}	    
	// }

	echo '
						<div class="container">
					<form method="post">
								     <div class="row">
								     	<div class="col-md-4">
								     	<h2>Προσθήκη Πληροφοριών</h2>
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

								    <label for="isborrowed">Διαθεσιμότητα Δανεισμού:</label>
								    <input type="text" class="form-control" id="isborrowed" name="isborrowed" value="'.$result['isborrowed'].'" required>

								    <label for="quantity">Ποσότητα label:</label>
								    <input type="text" class="form-control" id="quantity" name="quantity" value="'.$result['quantity'].'" required>

								    <label for="retired">Κατάσταση Απόσυρσης:</label>
								    <input type="text" class="form-control" id="retired" name="retired" value="'.$result['retired'].'" required>

								    <label for="location_e">Τοποθεσία label:</label>
								    <input type="text" class="form-control" id="location_e" name="location_e" value="'.$result['location_e'].'" required>


								    <label for="serial_number">Σειριακός Αριθμός:</label>
								    <input type="text" class="form-control" id="serial_number" name="serial_number" value="'.$result['serial_number'].'" required>
								   </div> 

								    

							</div>
						        </div>
						  <div class="col-md-4">
						         <h2>Πληροφορίες Παρόχου</h2>

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
			  		<h2>Πληροφορίες Τμήματος</h2>
						  			<label for="name_dep">Όνομα Τμήματος:</label>
								    <input type="text" class="form-control" id="name_dep" name="name_dep" value="'.$result['name_dep'].'" required>

								    <label for="telephone_dep">Τηλέφωνο Τμήματος:</label>
					<input type="text" class="form-control" id="telephone_dep" name="telephone_dep" placeholder="Department Name" value="'.$result['telephone_dep'].'" required>


					<h2>Περιγραφή</h2>			    
							<label for="short_desc">Σύντομη Περιγραφή:</label>
								    <input type="text" class="form-control" id="short_desc" name="short_desc" value="'.$result['short_desc'].'" required>
								        
							<label for="long_desc">Εκτενείς Περιγραφή:</label>
								    <input type="text" class="form-control" id="long_desc" name="long_desc" value="'.$result['long_desc'].'" required>	        


								    

					<h2>Επιπλέον Σχόλια</h2>
							<div class="form-group">	
								        <label for="comments">Σχόλια:</label>
								    <input type="text" class="form-control" id="comments" name="comments" value="'.$result['comments'].'" required>	   				    


					      </div> 
					      <h2>Εικόνα Εξαρτήματος</h2>	
					      <label for="image">Ονομα Εικόνας:</label>
					<input type="text" class="form-control" id="image" name="image" value="'.$_GET['file'].'" required>

					</div>
						       </div>
						</div>
						<button id="add" name ="add" type="submit" class="btn btn-primary">Προσθήκη Εξαρτήματος</button>
					</form>
					</div> ';



   	   
	


include("views/footer.php");

?>