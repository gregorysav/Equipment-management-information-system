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
		

				try{
				 
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
						      <th scope="col">Ενέργειες</th>
						    </tr>
						  </thead>
						  ';
 					while($result=$equipQuery->fetch(PDO::FETCH_ASSOC)){

 					echo '
 						  <tbody>
					      <td><img src="uploadedImages/'.$result['real_filename'].'"/></td>
					      <td>'.$result['name_e'].'</td>
					      <td>'.$result['buy_year_e'].'</td>
					      <td>'.$result['owner_name'].'</td>
					      <td>'.$result['quantity'].'</td>
					      <td>'.$result['location_e'].'</td>
					      <td><button id="delete" name ="delete" class="btn btn-dark" type="submit"><a href=equipment_delete.php?id_equip='.$result['id_equip'].'>Διαγραφή</a></button><br><br><button id="modify" name ="modify" class="btn btn-dark" type="submit"><a href=equipment_modify.php?id_equip='.$result['id_equip'].'>Τροποποίηση</a></button><br><br><button id="addImage" name ="addImage" class="btn btn-dark" type="submit"><a href=addImage.php?id_equip='.$result['id_equip'].'>Εισαγωγή Εικόνας</a></button></td>
					      </div> ';
					    
					}
					}		    

		catch(PDOException $e)
		    {
		    echo "Error: " . $e->getMessage();
		    } 
	
//base64_encode(paseid.$result['id_equip'])
 // $get = base64_decode($_GET['id_equip'])
 // $array= explode($get, paseid )
 // $id= $array[1]

include("views/footer.php");

?>