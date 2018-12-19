<?php
include("variables_file.php");
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");
	
		if ($type == 1){
			echo '
				<div class="container" id="tableEquipment">
				<div class="form-inline" id="searchHolder">
				<h2>Σελίδα Διαχείρισης Εξαρτημάτων: </h2>
			    <button type="submit" id="add_equipment" class="btn btn-success btn-info">Προσθήκη Νέου Εξαρτήματος</button><br>
		        <button type="submit" id="modify_equipment" class="btn btn-primary btn-danger">Επεξεργασία Εξαρτήματος</button>
		        </div><br>
				</div>
			';
		}

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
 					<h3 id="title">Διαθέσιμος Εξοπλισμός Εργαστηρίου</h3><br>
 					<div class="form-inline" id="searchHolder"> 					
 						Αναζήτηση:	
 					  	<form name="form" method="get">
  						<input type="text" name="equipmentName"  class="form-control input-lg" id="equipmentName" autocomplete="off" placeholder="Όνομα εξαρτήματος"/>
						</form>
						<form name="form" method="get">
  						<input type="text" name="yearOfBuy"  class="form-control input-lg" id="yearOfBuy" autocomplete="off" placeholder="Ημερομηνία απόκτησης"/>
						</form>
						<form name="form" method="get">
  						<input type="text" name="locationName"  class="form-control input-lg" id="locationName" autocomplete="off" placeholder="Μέρος"/>
					    </form>
					</div>	  
 					<table class="table table-bordered table-hover">
					<thead class="thead-dark">
					<tr>
					    <th scope="col">Εικόνα</th>
					    <th scope="col">Όνομα</th>
					    <th scope="col">Έτος απόκτησης</th>
					    <th scope="col">Τοποθεσία</th>
					    <th scope="col">Σύντομη Περιγραφή</th>
					</tr>
					</thead>
		';
		$url = $_SERVER['REQUEST_URI'];
		$value=(explode("=", $url));
		if (isset($value[1]) AND $value[1] == ""){
			while($equipQuerySTMTResult=$equipQuerySTMT->fetch(PDO::FETCH_ASSOC)){
				if (!$equipQuerySTMTResult['hash_filename']){
			 		$imageHashedName = "noimage.jpg";	
			 	}else {
			 		$imageHashedName = $equipQuerySTMTResult['hash_filename'];
			 	}
		 		if (($equipQuerySTMTResult['quantity']) > 0 ){
		 			$descriptionQuerySQL = "SELECT * FROM description_svds WHERE id_desc= :idDesc";
					$descriptionQuerySTMT = $db->prepare($descriptionQuerySQL);
					$descriptionQuerySTMT->bindParam(':idDesc', $equipQuerySTMTResult['short_desc_e'], PDO::PARAM_INT); 
				 	$descriptionQuerySTMT->execute();
				 	$descriptionQuerySTMTResult=$descriptionQuerySTMT->fetch(PDO::FETCH_ASSOC);
			 		echo '
	 						<tbody>
	 						<tr>
						    <td><img src="uploadedImages/'.$imageHashedName.'"/></td>
						    <td><a href=equipment_details.php?id_equip='.$equipQuerySTMTResult['id_equip'].'>'.$equipQuerySTMTResult['name_e'].'</a></td>
						    <td>'.$equipQuerySTMTResult['buy_year_e'].'</td>
						    <td>'.$equipQuerySTMTResult['location_e'].'</td>
						    <td>'.$equipQuerySTMTResult['location_e'].'</td>
						    <td>'.$descriptionQuerySTMTResult['short_desc'].'</td>
						    </tr>
						    </tbody>
					';
				}
			}
		}
		if (isset($_GET['equipmentName'])){
			$equipmentName = filter_var($_GET['equipmentName'],FILTER_SANITIZE_STRING);
    		$searchQuerySQL = "SELECT * FROM equip_svds WHERE name_e LIKE :keyword";
    		$searchQuerySTMT = $db->prepare($searchQuerySQL);	
    		$searchQuerySTMT->bindParam(':keyword', $equipmentName); 
    		$searchQuerySTMT->execute();
			while ($searchQuerySTMTResult=$searchQuerySTMT->fetch(PDO::FETCH_ASSOC)){
				if (!$searchQuerySTMTResult['hash_filename']){
			 		$imageHashedName = "noimage.jpg";	
			 	}else {
			 		$imageHashedName = $searchQuerySTMTResult['hash_filename'];
			 	}
			 	$descriptionQuerySQL = "SELECT * FROM description_svds WHERE id_desc= :idDesc";
				$descriptionQuerySTMT = $db->prepare($descriptionQuerySQL);
				$descriptionQuerySTMT->bindParam(':idDesc', $searchQuerySTMTResult['short_desc_e'], PDO::PARAM_INT); 
				$descriptionQuerySTMT->execute();
				$descriptionQuerySTMTResult=$descriptionQuerySTMT->fetch(PDO::FETCH_ASSOC);
				echo '
	 						<tbody>
	 						<tr>
						    <td><img src="uploadedImages/'.$imageHashedName.'"/></td>
						    <td><a href=equipment_details.php?id_equip='.$searchQuerySTMTResult['id_equip'].'>'.$searchQuerySTMTResult['name_e'].'</a></td>
						    <td>'.$searchQuerySTMTResult['buy_year_e'].'</td>
						    <td>'.$searchQuerySTMTResult['location_e'].'</td>
					    	<td>'.$descriptionQuerySTMTResult['short_desc'].'</td>
						    </tr>
						    </tbody>
				';
			}

    	}elseif (isset($_GET['yearOfBuy'])) {
    		$yearOfBuy= filter_var($_GET['yearOfBuy'],FILTER_SANITIZE_NUMBER_FLOAT);
			$searchQuerySQL = "SELECT * FROM equip_svds WHERE buy_year_e LIKE :keyword";
			$searchQuerySTMT = $db->prepare($searchQuerySQL);
    		$searchQuerySTMT->bindParam(':keyword', $yearOfBuy); 
    		$searchQuerySTMT->execute();
			while ($searchQuerySTMTResult=$searchQuerySTMT->fetch(PDO::FETCH_ASSOC)){
				if (!$searchQuerySTMTResult['hash_filename']){
			 		$imageHashedName = "noimage.jpg";	
			 	}else {
			 		$imageHashedName = $searchQuerySTMTResult['hash_filename'];
			 	}
			 	$descriptionQuerySQL = "SELECT * FROM description_svds WHERE id_desc= :idDesc";
				$descriptionQuerySTMT = $db->prepare($descriptionQuerySQL);
				$descriptionQuerySTMT->bindParam(':idDesc', $searchQuerySTMTResult['short_desc_e'], PDO::PARAM_INT); 
				$descriptionQuerySTMT->execute();
				$descriptionQuerySTMTResult=$descriptionQuerySTMT->fetch(PDO::FETCH_ASSOC);
				echo '
	 						<tbody>
	 						<tr>
						    <td><img src="uploadedImages/'.$imageHashedName.'"/></td>
						    <td><a href=equipment_details.php?id_equip='.$searchQuerySTMTResult['id_equip'].'>'.$searchQuerySTMTResult['name_e'].'</a></td>
						    <td>'.$searchQuerySTMTResult['buy_year_e'].'</td>
						    <td>'.$searchQuerySTMTResult['location_e'].'</td>
					   		<td>'.$descriptionQuerySTMTResult['short_desc'].'</td>
						    </tr>
						    </tbody>
				';
			}
    	}elseif (isset($_GET['locationName'])) {
    		$locationName = filter_var($_GET['locationName'],FILTER_SANITIZE_STRING);
    		$searchQuerySQL = "SELECT * FROM equip_svds WHERE location_e LIKE :keyword";
    		$searchQuerySTMT = $db->prepare($searchQuerySQL);
    		$searchQuerySTMT->bindParam(':keyword', $locationName); 
    		$searchQuerySTMT->execute();
			while ($searchQuerySTMTResult=$searchQuerySTMT->fetch(PDO::FETCH_ASSOC)){
				if (!$searchQuerySTMTResult['hash_filename']){
			 		$imageHashedName = "noimage.jpg";	
			 	}else {
			 		$imageHashedName = $searchQuerySTMTResult['hash_filename'];
			 	}
			 	$descriptionQuerySQL = "SELECT * FROM description_svds WHERE id_desc= :idDesc";
				$descriptionQuerySTMT = $db->prepare($descriptionQuerySQL);
				$descriptionQuerySTMT->bindParam(':idDesc', $searchQuerySTMTResult['short_desc_e'], PDO::PARAM_INT); 
				$descriptionQuerySTMT->execute();
				$descriptionQuerySTMTResult=$descriptionQuerySTMT->fetch(PDO::FETCH_ASSOC);
				echo '
	 						<tbody>
	 						<tr>
						    <td><img src="uploadedImages/'.$imageHashedName.'"/></td>
						    <td><a href=equipment_details.php?id_equip='.$searchQuerySTMTResult['id_equip'].'>'.$searchQuerySTMTResult['name_e'].'</a></td>
						    <td>'.$searchQuerySTMTResult['buy_year_e'].'</td>
						    <td>'.$searchQuerySTMTResult['location_e'].'</td>
					    	<td>'.$descriptionQuerySTMTResult['short_desc'].'</td>
						    </tr>
						    </tbody>
				';
			}
    	}else {
			while($equipQuerySTMTResult=$equipQuerySTMT->fetch(PDO::FETCH_ASSOC)){
				if (!$equipQuerySTMTResult['hash_filename']){
			 		$imageHashedName = "noimage.jpg";	
			 	}else {
			 		$imageHashedName = $equipQuerySTMTResult['hash_filename'];
			 	}
		 		if (($equipQuerySTMTResult['quantity']) > 0 ){
		 			$descriptionQuerySQL = "SELECT * FROM description_svds WHERE id_desc= :idDesc";
					$descriptionQuerySTMT = $db->prepare($descriptionQuerySQL);
					$descriptionQuerySTMT->bindParam(':idDesc', $equipQuerySTMTResult['short_desc_e'], PDO::PARAM_INT); 
					$descriptionQuerySTMT->execute();
					$descriptionQuerySTMTResult=$descriptionQuerySTMT->fetch(PDO::FETCH_ASSOC);
			 		echo '
		 						<tbody>
		 						<tr>
							    <td><img src="uploadedImages/'.$imageHashedName.'"/></td>
							    <td><a href=equipment_details.php?id_equip='.$equipQuerySTMTResult['id_equip'].'>'.$equipQuerySTMTResult['name_e'].'</a></td>
							    <td>'.$equipQuerySTMTResult['buy_year_e'].'</td>
							    <td>'.$equipQuerySTMTResult['location_e'].'</td>
						    	<td>'.$descriptionQuerySTMTResult['short_desc'].'</td>
							    </tr>
					            </tbody>
					';				
				}
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
	    		<a href=equipment.php?p='.($pageOfPagination-1).' class="page-link"><<</a></li>
	    	';
	    }

	    for ($i=1; $i <= $totalCellsPagination; $i++) { 
            if ($pageOfPagination == $i){
                echo "<li class='page-item  active'><a class='page-link' href=equipment.php?p=".$i.">".$i."</a></li>";                    
            }else {

        	    echo "<li class='page-item'><a class='page-link' href=equipment.php?p=".$i.">".$i."</a></li>";
            }
        }


        if ($pageOfPagination < $totalCellsPagination){    	 
	    	echo'
	           	<li class="page-item">
	           	<a href=equipment.php?p='.($pageOfPagination+1).' class="page-link">>></a></li>
	        ';
	    }       	
        echo '
          	</ul>
          	</table>
            </div>
        ';

include("views/footer.php");
?>