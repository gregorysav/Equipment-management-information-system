<?php
	session_start();
	include("views/connection.php"); 
	include("views/header.php");
	include("views/navbar.php");

	$userId= $_SESSION['id'];
	
	$borrowQuery = $db->prepare("SELECT * FROM borrow_svds WHERE aem_borrow= $userId"); 
	$borrowQuery->execute();
	 

?>

<!DOCTYPE html>
<html>
<head>
	<title>Search Results</title>
</head>
<body>

	<div class="container">
		<div class="row">
			<div class="col-md-4"> 
		  	<h2>Ενεργοί δανεισμοί: </h2>
					  	<?php 
					  				while($borrow_result=$borrowQuery->fetch(PDO::FETCH_ASSOC)){
					  				$idEquipBorrow = $borrow_result['id_equip_borrow'];
					  				$equipBasketQuery = $db->prepare("SELECT * FROM equip_svds WHERE id_equip= $idEquipBorrow"); 
							 		$equipBasketQuery->execute();
							 		$equipBasketQueryResult= $equipBasketQuery->fetch(PDO::FETCH_ASSOC);

					  				echo '<p style="padding-left: 50px;">'.$equipBasketQueryResult['name_e'].'</p>';
					  			}
					   ?> 
					  <br><br>
					  <button class="btn btn-dark" id="clear" aem_borrow="<?php echo $_SESSION['id']; ?>">Καθαρισμός</button>
					  <button class="btn btn-dark" id="finish"><a href=finish.php style="color: black">Ολοκλήρωση</a></button>

					    </div>
		    <div class="col-md-8">
		  		<h2>Αναζήτηση Εξαρτημάτων</h2>	        
				<form class="form-inline">
				  <div class="form-group">
				    <input type="text" name="q" class="form-control" id="search" placeholder="Όνομα Εξαρτήματος">
				  </div>
				  <button type="submit" class="btn btn-primary">Αναζήτηση</button>
				</form>
		    
		  	
		    <?php
		    	if(isset($_GET['q'])){
 
				 $searchTerm = $_GET['q'];            
				 $searchQuery = "SELECT * FROM equip_svds WHERE name_e LIKE '%$searchTerm%'";  
				 $searchQuery_stmt = $db->prepare($searchQuery);
				 $searchQuery_stmt->execute();
				 $searchQueryResult=$searchQuery_stmt->fetch(PDO::FETCH_ASSOC);

		    	if ($searchQueryResult){

			 
		  		echo '
 						<table class="table table-bordered">
						  <thead class="thead-dark">
						    <tr>
						      <th scope="col">Εικόνα</th> 	
						      <th scope="col">ID Καταχώρησης</th>
						      <th scope="col">Ιδιοκτήτης</th>
						      <th scope="col">Ποσότητα</th>
						      <th scope="col">Ενέργειες</th>
						    </tr>
						  </thead>
						  ';

		  			
		  					echo '
 						  <tbody>
 						  <td><img src="uploadedImages/'.$searchQueryResult['real_filename'].'"/></td>
					      <td>'.$searchQueryResult['id_equip'].'</td>
					      <td>'.$searchQueryResult['owner_name'].'</td>
					      <td>'.$searchQueryResult['quantity'].'</td>
					      <td><button type="submit" class="btn btn-primary add_to_basket" id_equip_borrow='.$searchQueryResult['id_equip'].' aem_borrow='.$_SESSION['id'].'>Καλάθι</button></td></div>
					       ';
		    
				} else echo "Προέκυψε λάθος. Δοκιμάστε ξανά.";
				} ?>
        	</div>
		</div>
	</div>	


</body>
</html>

<?php
include("views/footer.php");
?>