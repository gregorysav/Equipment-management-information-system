<?php
	session_start();
	include("views/connection.php");
	include("views/header.php");
	include("views/navbar.php");
	if ($_SESSION['email']){

			$userId= $_SESSION['id'];
			$equipQuery = $db->prepare("SELECT * FROM equip_svds"); 
	 		$equipQuery->execute();

	 		$borrowQuery = $db->prepare("SELECT * FROM borrow_svds WHERE aem_borrow= $userId"); 
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
			<div class="col-md-4"> 
		  	<h2>Καλάθι: </h2>
		  	<?php 
		  		$basketQuery = $db->prepare("SELECT * FROM basket_svds"); 
	 			$basketQuery->execute();
	 			while($basketQueryResult=$basketQuery->fetch(PDO::FETCH_ASSOC)){
	 				echo $basketQueryResult['name_basket'].'<br>';
	 				}
		   ?> 
		  <br><br>
		  <button class="btn btn-dark" id="clear" aem_borrow="<?php echo $_SESSION['id']; ?>">Καθαρισμός</button>
		  <button class="btn btn-dark" id="finish"><a href=finish.php style="color: black">Ολοκλήρωση</a></button>

		    </div>
		  	<div class="col-md-8">
		  		<h2>Επιλέξτε από τα παρακάτω εξαρτήματα: </h2>
		
		  			<?php 
		  		echo '
 						<table class="table table-bordered">
						  <thead class="thead-dark">
						    <tr>
						      <th scope="col">Εικόνα</th>
						      <th scope="col">Όνομα</th>
						      <th scope="col">Ιδιοκτήτης</th>
						      <th scope="col">Ποσότητα</th>
						      <th scope="col">Ενέργειες</th>
						    </tr>
						  </thead>
						  ';

		  			while($equipQueryResult=$equipQuery->fetch(PDO::FETCH_ASSOC)){
		  					if ($equipQueryResult['isborrowed'] == 0 && $equipQueryResult['retired'] == 0 && $equipQueryResult['quantity'] > 0) {
		  					echo '
 						  <tbody>
					      <td><img src="uploadedImages/'.$equipQueryResult['real_filename'].'"/></td>
					      <td>'.$equipQueryResult['name_e'].'</td>
					      <td>'.$equipQueryResult['owner_name'].'</td>
					      <td>'.$equipQueryResult['quantity'].'</td>
					      <td><button type="submit" class="btn btn-primary add_to_basket"  name_basket='.$equipQueryResult['name_e'].' id_equip_basket='.$equipQueryResult['id_equip'].' >Καλάθι</button></td></div>
					       ';

								
							}
						}

		    ?>
		    </div>
        
		</div>
	</div>	

</body>
</html>

<?php
	include("views/footer.php");
?>		