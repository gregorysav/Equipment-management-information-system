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
 					<div class="alert alert-light" role="alert">
						Όνομα Έτος Απόκτησης Όνομα Κατόχου Ποσότητα Τοποθεσία Σειριακός Αριθμός';

				    while($result=$equipQuery->fetch(PDO::FETCH_ASSOC)){

				    echo '
				    <div class="alert alert-info" role="alert"><img src="uploadedImages/'.$result['real_filename'].'"/>Ta stoixeia einai '.$result['name_e'].' '.$result['buy_year_e'].' '.$result['owner_name'].' '.$result['quantity'].' '.$result['location_e'].' '.$result['serial_number'].'
				     '.$result['serial_number'].' 
					</div>						    	
					';
						

				  
               	    echo '
               	    	<button id="delete" name ="delete" type="submit"><a href=equipment_delete.php?id_equip='.$result['id_equip'].'>Διαγραφή</a></button>';

               	   	echo '
               	   		<button id="modify" name ="modify" type="submit"><a href=equipment_modify.php?id_equip='.$result['id_equip'].'>Τροποποίηση</a></button> 	
               	    
               	    ';

               	    echo '
               	    	<button id="addImage" name ="addImage" type="submit"><a href=addImage.php?id_equip='.$result['id_equip'].'>Εισαγωγή Εικόνας</a></button>  <br>	
               	    
               	    ';
		    
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

