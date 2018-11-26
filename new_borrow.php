<?php
include("variables_file.php");
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");

	if ($_SESSION['email']){ 
		echo '<div class="container">	        
			<form class="form-inline" id="searchHolder">
			<h3>Αναζήτηση Εξαρτημάτων:  </h3>
			<div class="form-group">
			<input type="text" name="equipmentName" class="form-control equipmentName" placeholder="Όνομα Εξαρτήματος">
			</div>
			<button type="submit" class="btn btn-primary">Αναζήτηση</button>
			</form><br>
		';
		if (isset($_GET['equipmentName'])){
    		$searchQuerySQL = "SELECT * FROM equip_svds WHERE name_e LIKE :keyword";
    		$searchQuerySTMT = $db->prepare($searchQuerySQL);
    		$searchQuerySTMT->bindParam(':keyword', $_GET['equipmentName']); 
    		$searchQuerySTMT->execute();
    		$searchQuerySTMTResult=$searchQuerySTMT->fetch(PDO::FETCH_ASSOC);
			if ($searchQuerySTMTResult){

				echo '
		 			<table class="table table-bordered table-hover">
				    <thead class="thead-dark">
					    <tr>
					      <th scope="col">Εικόνα</th> 	
					      <th scope="col">Ονομασία</th>
					      <th scope="col">Ιδιοκτήτης</th>
					      <th scope="col">Τοποθεσία</th>
					      <th scope="col"></th>
					    </tr>
		 		    </thead>
				';

				  			
				echo '
					<tbody>
					<tr>
					<td><img src="uploadedImages/'.$searchQuerySTMTResult['real_filename'].'"/></td>
				    <td>'.$searchQuerySTMTResult['name_e'].'</td>
				    <td>'.$searchQuerySTMTResult['owner_name'].'</td>
				    <td>'.$searchQuerySTMTResult['location_e'].'</td>
				    <td><button type="submit" class="fa fa-shopping-cart add_to_basket" name_basket='.$searchQuerySTMTResult['name_e'].' id_user_basket='.$_SESSION['aem'].' id_equip_basket='.$searchQuerySTMTResult['id_equip'].'></button></td>
				    </tr>
				    </tbody>
		        ';
				    
			}else{
				echo "Δεν βρέθηκε εξάρτημα να ταιρίαζει στην αναζήτηση.";
			}
		}else{
				if (isset($_GET['p'])){
		            $pageOfPagination = $_GET['p'];
		            $startPagination = ($pageOfPagination- 1) * $limitPagination;
		        }

				$equipQuerySQL = "SELECT * FROM equip_svds LIMIT $startPagination, $limitPagination"; 
				$equipQuerySTMT = $db->prepare($equipQuerySQL);
				$equipQuerySTMT->execute();
				echo '
				    <div class="container">
					<table class="table table-bordered table-hover">
							<thead class="thead-dark">
					<tr>
					<th scope="col">Εικόνα</th> 	
					<th scope="col">Ονομασία</th>
					<th scope="col">Ιδιοκτήτης</th>
					<th scope="col">Τοποθεσία</th>
					<th scope="col"></th>
					</tr>
					</thead>
				';
				while($equipQuerySTMTResult=$equipQuerySTMT->fetch(PDO::FETCH_ASSOC)){
				 	if (($equipQuerySTMTResult['quantity']) > 0 ){
				 		echo '
							<tbody>
							<tr>
							<td><img src="uploadedImages/'.$equipQuerySTMTResult['real_filename'].'"/></td>
							<td><a href=equipment_details.php?id_equip='.$equipQuerySTMTResult['id_equip'].'>'.$equipQuerySTMTResult['name_e'].'</a></td>
							<td>'.$equipQuerySTMTResult['owner_name'].'</td>
				    		<td>'.$equipQuerySTMTResult['location_e'].'</td>
							<td><button type="submit" class="fa fa-shopping-cart add_to_basket" name_basket='.$equipQuerySTMTResult['name_e'].' id_user_basket='.$_SESSION['aem'].' id_equip_basket='.$equipQuerySTMTResult['id_equip'].'></button></td>	
							</tr>
							</tbody>       
						';
					}
				}

				$rowsQuerySQL = "SELECT * FROM equip_svds";
		 		$rowsQuerySTMT = $db->prepare($rowsQuerySQL);
		 		$rowsQuerySTMT->execute();		
				$rowsNumberPagination = $rowsQuerySTMT->rowCount();
		        $totalCellsPagination = ceil($rowsNumberPagination/$limitPagination);
				echo '
				  	<div style="display: flex; padding-left: 0px; list-style: none; justify-content: center;">
					<ul class="pagination" style="position: fixed; bottom: 10px;">
				';		
				for ($i=1; $i <= $totalCellsPagination; $i++) { 
				    if ($pageOfPagination == $i){
				        echo "<li class='active'><a href=new_borrow.php?p=".$i.">".$i."</a></li>";                    
				    }else {
				        echo "<li><a href=new_borrow.php?p=".$i.">".$i."</a></li>";
				    }
		    	}
		    	echo '
		       		</ul>
		       		</table>
		       		</div>
		    	';
		 	}
		 	
	}	
	echo '
	   	
		</div>	

	';	
		
include("views/footer.php");
?>