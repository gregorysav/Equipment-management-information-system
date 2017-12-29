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
		

	if(isset($_POST['add'])){


				try{
				 
				
				$add_department = $db->prepare("INSERT INTO department_svds (name_dep, telephone_dep) 
    VALUES (:name_dep, :telephone_dep)");

		    $add_department->bindParam(':name_dep', $_POST['name_dep']);
		    $add_department->bindParam(':telephone_dep', $_POST['telephone_dep']);
		    $add_department->execute();	



				$add_description = $db->prepare("INSERT INTO description_svds (short_desc, long_desc) 
    VALUES (:short_desc, :long_desc)");

		    $add_description->bindParam(':short_desc', $_POST['short_desc']);
		    $add_description->bindParam(':long_desc', $_POST['long_desc']);
		    $add_description->execute();	



				$add_comment = $db->prepare("INSERT INTO comments_svds (id_equip_com, id_user_com, comments, date_com) 
    VALUES (:id_equip_com, :id_user_com, :comments, NOW())");

		    $add_comment->bindParam(':comments', $_POST['comments']);
		    $add_comment->bindParam(':id_equip_com', $_SESSION['password']);
		    $add_comment->bindParam(':id_user_com', $_SESSION['password']);
		    $add_comment->execute();



				$add_provider = $db->prepare("INSERT INTO provider_svds (name_p, telephone_p, website_p, email_p, support_p, comments_p) 
    VALUES (:name_p, :telephone_p, :website_p, :email_p, :support_p, :comments_p)");

		    $add_provider->bindParam(':name_p', $_POST['name_p']);
		    $add_provider->bindParam(':telephone_p', $_POST['telephone_p']);
		    $add_provider->bindParam(':website_p', $_POST['website_p']);
		    $add_provider->bindParam(':email_p', $_POST['email_p']);
		    $add_provider->bindParam(':support_p', $_POST['support_p']);
		    $add_provider->bindParam(':comments_p', $_POST['comments_p']);
		    $add_provider->execute();	



				$add_equipment = $db->prepare("INSERT INTO equip_svds (name_e, buy_method_e, buy_year_e, owner_name, isborrowed, quantity, retired, location_e, serial_number) 
    VALUES (:name_e, :buy_method_e, :buy_year_e, :owner_name, :isborrowed, :quantity, :retired, :location_e, :serial_number)");

		    $add_equipment->bindParam(':name_e', $_POST['name_e']);
		    $add_equipment->bindParam(':buy_method_e', $_POST['buy_method_e']);
		    $add_equipment->bindParam(':buy_year_e', $_POST['buy_year_e']);
		    $add_equipment->bindParam(':owner_name', $_POST['owner_name']);
		    $add_equipment->bindParam(':isborrowed', $_POST['isborrowed']);
		    $add_equipment->bindParam(':quantity', $_POST['quantity']);
		    $add_equipment->bindParam(':retired', $_POST['retired']);
		    $add_equipment->bindParam(':location_e', $_POST['location_e']);
		    $add_equipment->bindParam(':serial_number', $_POST['serial_number']);

		    $add_equipment->execute();

		    $equipQuery = "SELECT `id_equip` FROM equip_svds LIMIT 1"; 
			$equipQuery_stmt = $db->prepare($equipQuery);
			$equipQuery_stmt->execute();
			$result=$equipQuery_stmt->fetch(PDO::FETCH_ASSOC);

		   	echo "New records created successfully";
		    
		    
		}
		    

		catch(PDOException $e)
		    {
		    echo "Error: " . $e->getMessage();
		    } 

	}





		
		
	

	


include("footer.php");

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
			        <h2>Equipment Information</h2>
		       		<div class="form-group">
					    <label for="name_e">Equipment Name:</label>
					    <input type="text" class="form-control" id="name_e" name="name_e" placeholder="Equipment Name" required>

					    <label for="buy_method_e">Buy Method:</label>
					    <input type="text" class="form-control" id="buy_method_e" name="buy_method_e" placeholder="Buy method" required>

					    <label for="buy_year_e">Buy Year:</label>
					    <input type="text" class="form-control" id="buy_year_e" name="buy_year_e" placeholder="Buy year" required>

					    <label for="owner_name">Owner Name:</label>
					    <input type="text" class="form-control" id="owner_name" name="owner_name" placeholder="Owner Name" required>

					    <label for="isborrowed">Is Borrowed:</label>
					    <input type="text" class="form-control" id="isborrowed" name="isborrowed" placeholder="Insert 1 if is borrowed" required>

					    <label for="quantity">Quantity:</label>
					    <input type="text" class="form-control" id="quantity" name="quantity" placeholder="Quantity" required>

					    <label for="retired">Condition:</label>
					    <input type="text" class="form-control" id="retired" name="retired" placeholder="Insert 1 if is retired" required>

					    <label for="location_e">Location:</label>
					    <input type="text" class="form-control" id="location_e" name="location_e" placeholder="Location Name" required>

					    <label for="serial_number">Serial Number:</label>
					    <input type="text" class="form-control" id="serial_number" name="serial_number" placeholder="Serial Number" required>
					   
					</div>
			    </div>
			    <div class="col-md-4">
			        <h2>Provider Information</h2>

					<div class="form-group">	
					<label for="name_p">Provider Name:</label>
					<input type="text" class="form-control" id="name_p" name="name_p" required>


					<label for="telephone_p">Provider Telephone:</label>
					<input type="text" class="form-control" id="telephone_p" name="telephone_p" required>

					<label for="website_p">Provider Website:</label>
					<input type="text" class="form-control" id="website_p" name="website_p" required>


					<label for="email_p">Provider Email:</label>
					<input type="email" class="form-control" id="email_p" name="email_p" required>


					<label for="support_p">Provider Support:</label>
					<input type="text" class="form-control" id="support_p" name="support_p" required>


					<label for="comments_p">Provider Comments:</label>
					<input type="text" class="form-control" id="comments_p" name="comments_p" required>

					</div> 
			    </div>

                <div class="col-md-4">
			  		<h2>Department Information</h2>
                    <div class="form-group">
					<label for="name_dep">Department Name:</label>
					<input type="text" class="form-control" id="name_dep" name="name_dep" placeholder="Department Name" required>


					<label for="telephone_dep">Department Telephone:</label>
					<input type="text" class="form-control" id="telephone_dep" name="telephone_dep" placeholder="Department Name" required>

					<h2>Description Information</h2>

					<label for="short_desc">Short description:</label>
					<input type="text" class="form-control" id="short_desc" name="short_desc" placeholder="Give short description" required>

					<label for="long_desc">Long description:</label>
					<input type="text" class="form-control" id="long_desc" name="long_desc" placeholder="Give long description" required>

					<h2>Extra Comments</h2>

					<label for="comments">Short description:</label>
					<input type="text" class="form-control" id="comments" name="comments" placeholder="Leave a comment" required>
					</div>

			    </div>
			</div>

			<button name ="add" type="submit" class="btn btn-primary">Add Equipment</button>
		</form>
		</div>



</body>
</html>