<?php
	session_start();
	include("views/connection.php"); 
	include("views/header.php");
	include("views/navbar.php");

	$userId= $_SESSION['id'];
	
	$confirmQuery = $db->prepare("SELECT * FROM borrow_svds WHERE confirmation_borrow = 0");
    $confirmQuery->execute();
      
?>

<!DOCTYPE html>
<html>
<head>
	<title>Pending Borrows</title>
</head>
<body>

	<div class="container">
		<h2>Επιβεβαίωση Δανεισμών: </h2>

		<table class="table table-bordered">
		  <thead class="thead-dark">
		  <tr>
		  	<th scope="col">Ονοματεπώνυμο</th>
		    <th scope="col">ΑΕΜ Δανειστή</th>
		    <th scope="col">Ημερ. Εναρξης</th>
		    <th scope="col">Ημερ. Λήξης</th>
		    <th scope="col">Επιλογές</th>
		  </tr>
		  </thead>
		  <?php 
		  	while($confirmQueryResult=$confirmQuery->fetch(PDO::FETCH_ASSOC)){
		  		$userToBorrow = $confirmQueryResult['aem_borrow'];
		  		$borrowerQuery = $db->prepare("SELECT * FROM users_svds WHERE id = $userToBorrow"); 
			 	$borrowerQuery->execute();
			 	while($borrowerQueryResult=$borrowerQuery->fetch(PDO::FETCH_ASSOC)){
				  		echo '
				  				<tbody>
				  				  <td>'.$borrowerQueryResult['last_name'].' '.$borrowerQueryResult['first_name'].' </td>	
							      <td>'.$confirmQueryResult['aem_borrow'].'</td>
							      <td>'.$confirmQueryResult['start_date'].'</td>
							      <td>'.$confirmQueryResult['expire_date'].'</td>
							      <td><button type="submit" class="btn btn-primary confirm" id_to_confirm='.$confirmQueryResult['id_borrow'].' >Επιβεβαίωση</button>

							      </div> ';	
				}			      
		  	}

      	  ?>	
			
					  	
        
	</div>	


</body>
</html>

<?php
include("views/footer.php");
?>