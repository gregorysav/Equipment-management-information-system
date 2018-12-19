<?php
include("variables_file.php");
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");

	echo '<div class="container">	        
		<form name="form" method="get" class="form-inline" id="searchHolder">
		<h3>Αναζήτηση Εξαρτημάτων:  </h3>
  		<input type="text" name="equipmentName"  class="form-control input-lg" id="equipmentName" autocomplete="off" placeholder="Όνομα εξαρτήματος"/>
		<button type="submit" class="btn btn-primary">Αναζήτηση</button>
		</form><br>
	';
	if (isset($_GET['equipmentName'])){
		$equipmentName = filter_var($_GET['equipmentName'],FILTER_SANITIZE_STRING);
		$searchQuerySQL = "SELECT * FROM equip_svds WHERE name_e LIKE :keyword";
		$searchQuerySTMT = $db->prepare($searchQuerySQL);
		$searchQuerySTMT->bindParam(':keyword', $equipmentName); 
		$searchQuerySTMT->execute();
		$searchQuerySTMTResult=$searchQuerySTMT->fetch(PDO::FETCH_ASSOC);
		if (!$searchQuerySTMTResult['hash_filename']){
		 		$imageHashedName = "noimage.jpg";	
		}else {
		 		$imageHashedName = $searchQuerySTMTResult['hash_filename'];
		}
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
				<tbody>
					<tr>
					<td><img src="uploadedImages/'.$imageHashedName.'"/></td>
				    <td>'.$searchQuerySTMTResult['name_e'].'</td>
				    <td>'.$searchQuerySTMTResult['owner_name'].'</td>
				    <td>'.$searchQuerySTMTResult['location_e'].'</td>
				    <td><button type="submit" class="fa fa-shopping-cart add_to_basket" name_basket="'.$searchQuerySTMTResult['name_e'].'" id_user_basket='.$_SESSION['aem'].' id_equip_basket='.$searchQuerySTMTResult['id_equip'].'></button></td>
				    </tr>
			    </tbody>
	        ';
			    
		}else{
			echo "Δεν βρέθηκε εξάρτημα να ταιρίαζει στην αναζήτηση.";
		}
	}else{
			if (isset($_GET['p'])){
	        	$pageOfPagination = filter_var($_GET['p'],FILTER_SANITIZE_NUMBER_FLOAT);
	            $startPagination = ($pageOfPagination- 1) * $limitPagination;
	        }

			$equipQuerySQL = "SELECT * FROM equip_svds LIMIT :startPagination, :limitPagination";
			$equipQuerySTMT = $db->prepare($equipQuerySQL);
			$equipQuerySTMT->bindParam(':startPagination', $startPagination, PDO::PARAM_INT);
			$equipQuerySTMT->bindParam(':limitPagination', $limitPagination, PDO::PARAM_INT); 
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
				if (!$equipQuerySTMTResult['hash_filename']){
				 		$imageHashedName = "noimage.jpg";	
				}else {
				 		$imageHashedName = $equipQuerySTMTResult['hash_filename'];
				}
			 	if (($equipQuerySTMTResult['quantity']) > 0 ){
			 		echo '
						<tbody>
						<tr>
						<td><img src="uploadedImages/'.$imageHashedName.'"/></td>
						<td><a href=equipment_details.php?id_equip='.$equipQuerySTMTResult['id_equip'].'>'.$equipQuerySTMTResult['name_e'].'</a></td>
						<td>'.$equipQuerySTMTResult['owner_name'].'</td>
			    		<td>'.$equipQuerySTMTResult['location_e'].'</td>
						<td><button type="submit" class="fa fa-shopping-cart add_to_basket" name_basket="'.$equipQuerySTMTResult['name_e'].'" id_user_basket='.$_SESSION['aem'].' id_equip_basket='.$equipQuerySTMTResult['id_equip'].'></button></td>	
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
			  	<br>
					<ul class="pagination pagination-sm justify-content-center">
			';		
			if ($pageOfPagination > 1){    	 
		    	echo'
		    		<li class="page-item">
		    		<a class="page-link" href=new_borrow.php?p='.($pageOfPagination-1).'><<</a></li>
		    	';
		    }

			for ($i=1; $i <= $totalCellsPagination; $i++) { 
			    if ($pageOfPagination == $i){
			        echo "<li class='page-item  active'>
			        <a class='page-link' href=new_borrow.php?p=".$i.">".$i."</a></li>";                    
			    }else {
			        echo "<li class='page-item'>
			        <a class='page-link' href=new_borrow.php?p=".$i.">".$i."</a></li>";
			    }
	    	}
	    	if ($pageOfPagination < $totalCellsPagination){    	 
		    	echo'
		           	<li class="page-item">
		           	<a class="page-link" href=new_borrow.php?p='.($pageOfPagination+1).'>>></a></li>
		        ';
		    } 
	    	echo '
	       		</ul>
	       		</table>
	       		</div>
	    	';
	 	} 
	 	echo '
	 		</table>
	 		</div>
	 	';
		
include("views/footer.php");
?>