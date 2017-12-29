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
		

				try{
				 
					$equipQuery = $db->prepare("SELECT * FROM equip_svds"); 
 					$equipQuery->execute();

     				echo "ID / Onoma / Etos Agoras / Idioktitis / Tmima / Paroxos / Diathesimotita / Sxolia / Posotita / Aposirthike / Perigrafi / Topothesia / Seiriakos Arithmos <br>";
				    while($result=$equipQuery->fetch(PDO::FETCH_ASSOC)){

				   echo $result['id_equip'].' '.$result['name_e'].' '.$result['buy_method_e'].'  '.$result['buy_year_e'].' '.$result['owner_name'].' '.$result['department'].' '.$result['provider_e'].' '.$result['isborrowed'].' '.$result['comment_e'].' '.$result['quantity'].' '.$result['retired'].' '.$result['short_desc_e'].' '.$result['location_e'].' '.$result['serial_number'];
               	    echo '
               	    	<button id="delete" name ="delete" type="submit"><a href=delete_page.php?id_equip='.$result['id_equip'].'>Delete</a></button>';

               	    	echo '
               	    	<button id="modify" name ="modify" type="submit"><a href=equipment_modify.php?id_equip='.$result['id_equip'].'>Modify</a></button>  <br>	
               	    
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

include("footer.php");

?>