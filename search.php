<?php
	session_start();
	include("views/connection.php"); 
	include("views/header.php");
	include("views/navbar.php");

	if(isset($_GET['q'])){

	 
	 $searchTerm = $_GET['q'];
            
	 $Query = "SELECT * FROM equip_svds WHERE name_e LIKE '%$searchTerm%'";  
	 $Query_stmt = $db->prepare($Query);
	 $Query_stmt->execute();
	 $result=$Query_stmt->fetch(PDO::FETCH_ASSOC);

	 if ($result > 0){
	 echo $result['id_equip'].' '.$result['name_e'].' '.$result['buy_method_e'].'  '.$result['buy_year_e'].' '.$result['owner_name'].' '.$result['department'].' '.$result['provider_e'].' '.$result['isborrowed'].' '.$result['comment_e'].' '.$result['quantity'].' '.$result['retired'].' '.$result['short_desc_e'].' '.$result['location_e'].' '.$result['serial_number'];
	} else echo "No matches for that query";
	}

	include("views/footer.php");
?>

<!DOCTYPE html>
<html>
<head>
	<title>Search Results</title>
</head>
<body>



	        
	        <h2>Αναζήτηση Εξαρτημάτων</h2>
	        
	        
	<form class="form-inline">
	  <div class="form-group">
	    <input type="text" name="q" class="form-control" id="search" placeholder="Όνομα Εξαρτήματος">
	  </div>
	  <button type="submit" class="btn btn-primary">Αναζήτηση</button>
	</form>


</body>
</html>

