<?php
	session_start();
	include("connection.php");
	include("header.php");
	include("navbar.php");


	if (array_key_exists("logout", $_GET)){
	unset($_SESSION);
	}
	if (!isset($_SESSION['email'])){

		header("Location: index.php");
	}	
		
					 
					$equipQuery = "SELECT * FROM equip_svds WHERE id_equip=  :id_equip"; 
					$equipQuery_stmt = $db->prepare($equipQuery);
					$equipQuery_stmt->bindParam(':id_equip', $_GET['id_equip'], PDO::PARAM_INT);   
					$equipQuery_stmt->execute();

 					$depQuery = "SELECT * FROM department_svds";
 					$depQuery_stmt = $db->prepare($depQuery); 
 					$depQuery_stmt->execute();
 					$result2=$depQuery_stmt->fetch(PDO::FETCH_ASSOC);

 					$providerQuery = "SELECT * FROM provider_svds"; 
 					$providerQuery_stmt = $db->prepare($providerQuery);
 					$providerQuery_stmt->execute();
 					$result3=$providerQuery_stmt->fetch(PDO::FETCH_ASSOC);


 					$commentsQuery = "SELECT * FROM comments_svds";
 					$commentsQuery_stmt = $db->prepare($commentsQuery); 
 					$commentsQuery_stmt->execute();
 					$result4=$commentsQuery_stmt->fetch(PDO::FETCH_ASSOC);

     				
				    while($result=$equipQuery_stmt->fetch(PDO::FETCH_ASSOC)){

					echo '
						<div class="container">
					<form method="post">
								     <div class="row">
								     	<h2>Add Equipment Fields</h2>
						  <div class="col-md-6">
						       
					        <div class="form-group">
								    <label for="name_e">Equipment Name label:</label>
								    <input type="text" class="form-control" id="name_e" name="name_e" value=" '.$result['name_e'].'" required>


								    <label for="buy_year_e">Buy year label:</label>
								    <input type="text" class="form-control" id="buy_year_e" name="buy_year_e" value=" '.$result['buy_year_e'].'" required>


								    <label for="owner_name">Owner Name label:</label>
								    <input type="text" class="form-control" id="owner_name" name="owner_name" value=" '.$result['owner_name'].'" required>


								    <label for="department">Department label:</label>
								    <input type="text" class="form-control" id="department" name="department" value=" '.$result2['name_dep'].'" required>


								    <label for="provider_e">Provider Name label:</label>
								    <input type="text" class="form-control" id="provider_e" name="provider_e" value=" '.$result3['name_p'].'" required>


								    <label for="isborrowed">Available label:</label>
								    <input type="text" class="form-control" id="isborrowed" name="isborrowed" value=" '.$result['isborrowed'].'" required>

							</div>
						        </div>
						  <div class="col-md-6">
						        

								        <div class="form-group">	
								        <label for="comment_e">Comment label:</label>
								    <input type="text" class="form-control" id="comment_e" name="comment_e" value=" '.$result4['comments'].'" required>


								    <label for="quantity">Quantity label:</label>
								    <input type="text" class="form-control" id="quantity" name="quantity" value=" '.$result['quantity'].'" required>


								    <label for="retired">Condition label:</label>
								    <input type="text" class="form-control" id="retired" name="retired" value=" '.$result['retired'].'" required>


								    <label for="short_desc_e">Description label:</label>
								    <input type="text" class="form-control" id="short_desc_e" name="short_desc_e" value=" '.$result['short_desc_e'].'" required>


								    <label for="location_e">Location label:</label>
								    <input type="text" class="form-control" id="location_e" name="location_e" value=" '.$result['location_e'].'" required>


								    <label for="serial_number">Serial Number label:</label>
								    <input type="text" class="form-control" id="serial_number" name="serial_number" value=" '.$result['serial_number'].'" required>


					      </div> 
						       </div>
						</div>
						<button name ="add" type="submit" class="btn btn-primary">Add Equipment</button>
					</form>
					</div> ';
		    



    }
				   
	if(isset($_POST['add'])){


				try{
				 		

				$add_equipment = $db->prepare("INSERT INTO equip_svds (name_e, buy_year_e, owner_name, department, provider_e, isborrowed, comment_e, quantity, retired, short_desc_e, location_e, serial_number) 
    VALUES (:name_e, :buy_year_e, :owner_name, :department, :provider_e, :isborrowed, :comment_e, :quantity, :retired, :short_desc_e, :location_e, :serial_number)");

		    $add_equipment->bindParam(':name_e', $_POST['name_e']);
		    $add_equipment->bindParam(':buy_year_e', $_POST['buy_year_e']);
		    $add_equipment->bindParam(':owner_name', $_POST['owner_name']);
		    $add_equipment->bindParam(':department', $_POST['department']);
		    $add_equipment->bindParam(':provider_e', $_POST['provider_e']);
		    $add_equipment->bindParam(':isborrowed', $_POST['isborrowed']);
		    $add_equipment->bindParam(':comment_e', $_POST['comment_e']);
		    $add_equipment->bindParam(':quantity', $_POST['quantity']);
		    $add_equipment->bindParam(':retired', $_POST['retired']);
		    $add_equipment->bindParam(':short_desc_e', $_POST['short_desc_e']);
		    $add_equipment->bindParam(':location_e', $_POST['location_e']);
		    $add_equipment->bindParam(':serial_number', $_POST['serial_number']);

		    $add_equipment->execute();

		   	echo "New records created successfully";
		    
		    
		}
		    

		catch(PDOException $e)
		    {
		    echo "Error: " . $e->getMessage();
		    } 

	}


include("footer.php");

?>