<?php
//Access: Administrator
include("variables_file.php");
include("checkUser.php");
echo '
	<!DOCTYPE html>
	<html lang="en">
';
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");
include("functions.php");
//  Η μεταβλητή $type έχει τεθεί από το $_SESSION['type'] και ελέγχει το επίπεδο δικαιωμάτων του συνδεδεμένου χρήστη
	if ($type != 0) {
		if ($type == 1 OR $type == 2 OR $type == 3){
			echo '
				<div class="container" id="tableEquipment">
				<div class="form-inline" id="searchHolder">
				<h2>Σελίδα Διαχείρισης Εξαρτημάτων</h2>
		        </div><br>
				</div>
			';
		}
		
		if (isset($_POST['createCSV'])){
			createCSV($_SESSION['itemsToImportCSV']);
		}

//  Η μεταβλητή $_GET['p'] και ελέγχει τη σελίδα που βρισκόμαστε βάση του pagination
        if (isset($_GET['p'])){
        	$pageOfPagination = filter_var($_GET['p'],FILTER_SANITIZE_NUMBER_FLOAT);
            $startPagination = ($pageOfPagination- 1) * $limitPagination;
        }

		$equipQuerySQL = "SELECT * FROM equip_svds ORDER BY id_equip DESC LIMIT :startPagination, :limitPagination";
		$equipQuerySTMT = $db->prepare($equipQuerySQL);
		$equipQuerySTMT->bindParam(':startPagination', $startPagination, PDO::PARAM_INT);
		$equipQuerySTMT->bindParam(':limitPagination', $limitPagination, PDO::PARAM_INT); 
	 	$equipQuerySTMT->execute();
	 	
	 	echo '
 				<div class="container">
 					<h3 id="title">Συνολικός Εξοπλισμός Εργαστηρίου</h3><br>
 					<div id="searchHolder">
	 					<div class="custom-control custom-radio">
								<input type="radio" id="totalEquipmentForTeacher" name="totalEquipmentForTeacher" value="1" class="custom-control-input" checked>
								<label class="custom-control-label" for="totalEquipmentForTeacher">Συνολικός</label>
								</div>
						<div class="custom-control custom-radio">
								<input type="radio" id="availableEquipmentForTeacher" name="availableEquipmentForTeacher" value="2" class="custom-control-input">
								<label class="custom-control-label" for="availableEquipmentForTeacher">Διαθέσιμος</label>
								</div>
						</div>
					</div>
 					<div class="form-inline" id="searchHolder"> 					
 						Αναζήτηση:	
 					  	<form name="form" method="POST">
 	 						<input type="text" name="equipmentName"  class="form-control input-lg" id="equipmentName" autocomplete="off" placeholder="Όνομα εξαρτήματος"/>
							<input type="text" name="yearOfBuy"  class="form-control input-lg" id="yearOfBuy" autocomplete="off" placeholder="Ημερομηνία απόκτησης"/>
							<input type="text" name="locationName"  class="form-control input-lg" id="locationName" autocomplete="off" placeholder="Τοποθεσία"/>
							<button type="submit" name="search" class="btn btn-dark">Αναζήτηση</button>
					    </form>
					</div>	  
					<div class="table-responsive">
 					<table class="table table-bordered table-hover">
					<thead class="thead-dark">
					<tr>
					    <th scope="col">Εικόνα</th>
					    <th scope="col">Όνομα</th>
					    <th scope="col">Έτος απόκτησης</th>
					    <th scope="col">Τοποθεσία</th>
					    <th scope="col">Απόθεμα</th>
					    <th scope="col">Περιγραφή</th>
					    <th scope="col">Ενέργειες</th>
					</tr>
					</thead>
		';
//  Η μεταβλητή $url έχει τεθεί από το $_SERVER['REQUEST_URI'] και ελέγχει το ακριβές url που έχει η σελίδα που βρισκόμαστε			
		$url = $_SERVER['REQUEST_URI'];
		$value=(explode("=", $url));
		if (isset($value[1]) AND $value[1] == ""){
			while($equipQuerySTMTResult=$equipQuerySTMT->fetch(PDO::FETCH_ASSOC)){
				if (!$equipQuerySTMTResult['hash_filename']){
			 		$imageHashedName = "noimage.png";	
			 	}else {
			 		$imageHashedName = $equipQuerySTMTResult['hash_filename'];
			 	}
			 	if (!file_exists('uploadedImages/'.$imageHashedName)){ 
					$imageHashedName = "noimage.png";
				}
	 			$descriptionQuerySQL = "SELECT * FROM description_svds WHERE id_desc= :idDesc";
				$descriptionQuerySTMT = $db->prepare($descriptionQuerySQL);
				$descriptionQuerySTMT->bindParam(':idDesc', $equipQuerySTMTResult['short_desc_e'], PDO::PARAM_INT); 
			 	$descriptionQuerySTMT->execute();
			 	$descriptionQuerySTMTResult=$descriptionQuerySTMT->fetch(PDO::FETCH_ASSOC);
			 	if ($searchQuerySTMTResult['quantity'] > 0){
					$availableQuantity = $searchQuerySTMTResult['quantity'];
				}else {
					$availableQuantity = 0;
				}
		 		echo '
 						<tbody>
 						<tr>
					    <td><a href=equipment_details.php?id_equip='.$equipQuerySTMTResult['id_equip'].'><img src="uploadedImages/'.$imageHashedName.'"/></a></td>
					    <td><a href=equipment_details.php?id_equip='.$equipQuerySTMTResult['id_equip'].'>'.$equipQuerySTMTResult['name_e'].'</a></td>
					    <td>'.$equipQuerySTMTResult['buy_year_e'].'</td>
					    <td>'.$equipQuerySTMTResult['location_e'].'</td>
					    <td>'.$availableQuantity.'</td>
					    <td>'.$descriptionQuerySTMTResult['short_desc'].'</td>
					    <td id="equipmentPageButtons"><a href=actions_equipment.php?action=delete&id_equip='.$equipQuerySTMTResult['id_equip'].' id="delete" name="delete" class="btn btn-dark">Διαγραφή</a><br><a href=actions_equipment.php?action=update&id_equip='.$equipQuerySTMTResult['id_equip'].' class="btn btn-dark" id="modify" name="modify">Αλλαγή</a><br><a href=addImage.php?id_equip='.$equipQuerySTMTResult['id_equip'].' class="btn btn-dark" id="addImage" name="addImage">Εικόνα</a></td>
					    </tr>
					    </tbody>
				';
			}
		}
		
		if (isset($_POST['search'])){
			$itemsToImportCSV = array();
			$itemsToImportCSV[]= "Ονομασία, Χρονολογία, Τοποθεσία, Απόθεμα";
			if (isset($_POST['equipmentName'])){
				$equipmentName = filter_var($_POST['equipmentName'],FILTER_SANITIZE_STRING);
				$equipmentName = '%'.$equipmentName.'%';
				$searchQuerySQL = "SELECT DISTINCT * FROM equip_svds WHERE name_e LIKE :keywordName ORDER BY id_equip DESC";  
				$searchQuerySTMT = $db->prepare($searchQuerySQL);	
				$searchQuerySTMT->bindParam(':keywordName', $equipmentName, PDO::PARAM_STR); 
			}
			if (isset($_POST['yearOfBuy'])) {
    			$yearOfBuy= filter_var($_POST['yearOfBuy'],FILTER_SANITIZE_NUMBER_FLOAT);
    			$yearOfBuy= '%'.$yearOfBuy.'%';
    			$searchQuerySQL = "SELECT DISTINCT * FROM equip_svds WHERE name_e LIKE :keywordName AND buy_year_e LIKE :keywordYear ORDER BY id_equip DESC";  
	    		$searchQuerySTMT = $db->prepare($searchQuerySQL);	
	    		$searchQuerySTMT->bindParam(':keywordName', $equipmentName, PDO::PARAM_STR); 
	    		$searchQuerySTMT->bindParam(':keywordYear', $yearOfBuy, PDO::PARAM_INT);
	    	}
    		if (isset($_POST['locationName'])) {
    			$locationName = filter_var($_POST['locationName'],FILTER_SANITIZE_STRING);
   		 		$locationName= '%'.$locationName.'%';
   		 		$searchQuerySQL = "SELECT DISTINCT * FROM equip_svds WHERE (name_e LIKE :keywordName AND buy_year_e LIKE :keywordYear AND location_e LIKE :keywordLocation) ORDER BY id_equip DESC";  
	    		$searchQuerySTMT = $db->prepare($searchQuerySQL);	
	    		$searchQuerySTMT->bindParam(':keywordName', $equipmentName, PDO::PARAM_STR); 
	    		$searchQuerySTMT->bindParam(':keywordYear', $yearOfBuy, PDO::PARAM_INT);
	    		$searchQuerySTMT->bindParam(':keywordLocation', $locationName, PDO::PARAM_STR);
    		}

			
    		$searchQuerySTMT->execute();
    		if ($searchQuerySTMT->rowCount() > 0){
				while ($searchQuerySTMTResult=$searchQuerySTMT->fetch(PDO::FETCH_ASSOC)){
					if (!$searchQuerySTMTResult['hash_filename']){
				 		$imageHashedName = "noimage.png";	
				 	}else {
				 		$imageHashedName = $searchQuerySTMTResult['hash_filename'];
				 	}
				 	if (!file_exists('uploadedImages/'.$imageHashedName)){ 
						$imageHashedName = "noimage.png";
					}
				 	$descriptionQuerySQL = "SELECT * FROM description_svds WHERE id_desc= :idDesc";
					$descriptionQuerySTMT = $db->prepare($descriptionQuerySQL);
					$descriptionQuerySTMT->bindParam(':idDesc', $searchQuerySTMTResult['short_desc_e'], PDO::PARAM_INT); 
					$descriptionQuerySTMT->execute();
					$descriptionQuerySTMTResult=$descriptionQuerySTMT->fetch(PDO::FETCH_ASSOC);
					if ($searchQuerySTMTResult['quantity'] > 0){
						$availableQuantity = $searchQuerySTMTResult['quantity'];
					}else {
						$availableQuantity = 0;
					}
						echo '
			 						<tbody>
			 						<tr>
								    <td><a href=equipment_details.php?id_equip='.$searchQuerySTMTResult['id_equip'].'><img src="uploadedImages/'.$imageHashedName.'"/></a></td>
								    <td><a href=equipment_details.php?id_equip='.$searchQuerySTMTResult['id_equip'].'>'.$searchQuerySTMTResult['name_e'].'</a></td>
								    <td>'.$searchQuerySTMTResult['buy_year_e'].'</td>
								    <td>'.$searchQuerySTMTResult['location_e'].'</td>
								    <td>'.$availableQuantity.'</td>
							    	<td>'.$descriptionQuerySTMTResult['short_desc'].'</td>
							    	<td id="equipmentPageButtons"><a href=actions_equipment.php?action=delete&id_equip='.$searchQuerySTMTResult['id_equip'].' class="btn btn-dark" id="delete" name="delete">Διαγραφή</a><br><a href=actions_equipment.php?action=update&id_equip='.$searchQuerySTMTResult['id_equip'].' class="btn btn-dark" id="modify" name="modify">Αλλαγή</a><br><a href=addImage.php?id_equip='.$searchQuerySTMTResult['id_equip'].' class="btn btn-dark" id="addImage" name="addImage">Εικόνα</a></td>
								    </tr>
								    </tbody>
						';

						$itemsToImportCSV[].= PHP_EOL.$searchQuerySTMTResult['name_e']. ',' .$searchQuerySTMTResult['buy_year_e']. ',' .$searchQuerySTMTResult['location_e']. ',' .$searchQuerySTMTResult['quantity'];
						$_SESSION['itemsToImportCSV'] = $itemsToImportCSV;			
				}
				echo '
					<div id="createCSVForm">
						<form method="POST"><br>
							<button type="submit" id="createCSV" name="createCSV" class="btn btn-dark">Δημιουργία CSV</button>
						</form>
					</div>	
				';
			}else {
				echo '<p class="alert alert-warning">Δεν βρέθηκαν αποτελέσματα για την αναζήτηση σας.</p>';
			}	
		}else {
			while($equipQuerySTMTResult=$equipQuerySTMT->fetch(PDO::FETCH_ASSOC)){
				if (!$equipQuerySTMTResult['hash_filename']){
			 		$imageHashedName = "noimage.png";	
			 	}else {
			 		$imageHashedName = $equipQuerySTMTResult['hash_filename'];
			 	}
		 		if (!file_exists('uploadedImages/'.$imageHashedName)){ 
					$imageHashedName = "noimage.png";
				}
	 			$descriptionQuerySQL = "SELECT * FROM description_svds WHERE id_desc= :idDesc";
				$descriptionQuerySTMT = $db->prepare($descriptionQuerySQL);
				$descriptionQuerySTMT->bindParam(':idDesc', $equipQuerySTMTResult['short_desc_e'], PDO::PARAM_INT); 
				$descriptionQuerySTMT->execute();
				$descriptionQuerySTMTResult=$descriptionQuerySTMT->fetch(PDO::FETCH_ASSOC);
				if ($equipQuerySTMTResult['quantity'] > 0){
					$availableQuantity = $equipQuerySTMTResult['quantity'];
				}else {
					$availableQuantity = 0;
				}
		 		echo '
	 						<tbody>
	 						<tr>
						    <td><a href=equipment_details.php?id_equip='.$equipQuerySTMTResult['id_equip'].'><img src="uploadedImages/'.$imageHashedName.'"/></a></td>
						    <td><a href=equipment_details.php?id_equip='.$equipQuerySTMTResult['id_equip'].'>'.$equipQuerySTMTResult['name_e'].'</a></td>
						    <td>'.$equipQuerySTMTResult['buy_year_e'].'</td>
						    <td>'.$equipQuerySTMTResult['location_e'].'</td>
						    <td>'.$availableQuantity.'</td>
					    	<td>'.$descriptionQuerySTMTResult['short_desc'].'</td>
					    	<td id="equipmentPageButtons"><a href=actions_equipment.php?action=delete&id_equip='.$equipQuerySTMTResult['id_equip'].' class="btn btn-dark"  id="delete" name="delete">Διαγραφή</a><br><a href=actions_equipment.php?action=update&id_equip='.$equipQuerySTMTResult['id_equip'].' class="btn btn-dark" id="modify" name="modify">Αλλαγή</a><br><a href=addImage.php?id_equip='.$equipQuerySTMTResult['id_equip'].' class="btn btn-dark" id="addImage" name="addImage">Εικόνα</a></td>
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
	    		<a href=totalEquipmentForTeacher.php?p='.($pageOfPagination-1).' class="page-link"><<</a></li>
	    	';
	    }

	    for ($i=1; $i <= $totalCellsPagination; $i++) { 
            if ($pageOfPagination == $i){
                echo "<li class='page-item  active'><a class='page-link' href=totalEquipmentForTeacher.php?p=".$i.">".$i."</a></li>";                    
            }else {

        	    echo "<li class='page-item'><a class='page-link' href=totalEquipmentForTeacher.php?p=".$i.">".$i."</a></li>";
            }
        }


        if ($pageOfPagination < $totalCellsPagination){    	 
	    	echo'
	           	<li class="page-item">
	           	<a href=totalEquipmentForTeacher.php?p='.($pageOfPagination+1).' class="page-link">>></a></li>
	        ';
	    }       	
        echo '
          	</ul>
          	</table>
            </div>
            </div>
        ';
    }else{
    	header("Location: index.php");
		die("Δεν δικαιώματα εισόδου σε αυτή τη σελίδα.");
    }    


include("views/footer.php");
echo '
	</body>
	</html>
';
?>