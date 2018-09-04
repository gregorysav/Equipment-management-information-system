<?php
	session_start();
	include("views/connection.php");
	include("views/header.php");
	include("views/navbar.php");
	if ($_SESSION['email']){

	
			$equipQuery = $db->prepare("SELECT * FROM equip_svds"); 
	 		$equipQuery->execute();

	 		$borrowQuery = $db->prepare("SELECT * FROM borrow_svds"); 
	 		$borrowQuery->execute();

} else {
		header("Location: index.php");
	}
	
?>		

<!DOCTYPE html>
<html>
<head>
	<title></title>
	

</head>
<body>

	<div class="container">
		<div class="row">
		  <div class="col-md-8"> <?php 
		  			while($equip_result=$equipQuery->fetch(PDO::FETCH_ASSOC)){
		  				if ($equip_result['isborrowed'] == 0 && $equip_result['retired'] == 0 ) {

								 echo '<div class="row" style="padding:2px"><div class="col-md-2">'.$equip_result['id_equip'].'</div><div class="col-md-2"><img src="uploadedImages/'.$equip_result['real_filename'].'"/></div><div class="col-md-2">'.$equip_result['owner_name'].'</div><div class="col-md-2">'.$equip_result['name_e'].'</div><div class="col-md-2"><button type="submit" class="btn btn-primary add_to_basket"  id_equip_borrow='.$equip_result['id_equip'].'
								  aem_borrow='.$_SESSION['aem'].'>Καλάθι</button></div></div>';
							}
						}

		   ?> </div>
		  
		  <div class="col-md-4"> <?php echo "To kalathi moy<br>";
		  			while($borrow_result=$borrowQuery->fetch(PDO::FETCH_ASSOC)){
		  				echo $borrow_result['aem_borrow'].'<br>';
		  				echo $borrow_result['id_equip_borrow'].'<br>';
		  				echo $borrow_result['isborrowed'].'<br>';
		  			}
		   ?> </div>
		  <br><br>
		  <button class="btn btn-primary">Καθαρισμός</button>
		  <button class="btn btn-primary">Ολοκλήρωση</button>
		</div>
	</div>	

</body>
</html>


<?php
include("views/footer.php");
?>		