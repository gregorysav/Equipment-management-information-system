<?php
include("variables_file.php");
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");


	if (!isset($_SESSION['email'])){

		header("Location: index.php");
		die("Δεν έχετε συνδεθεί");
	}	
		

	try{
	 
		$equipQuerySQL = "SELECT * FROM equip_svds";
		$equipQuerySTMT = $db->prepare($equipQuerySQL); 
		$equipQuerySTMT->execute();

		if (isset($_GET['p'])){
            $pageOfPagination = $_GET['p'];
            $startPagination = ($pageOfPagination- 1) * $limitPagination;
        }

        $equipQuerySQL = "SELECT * FROM equip_svds LIMIT $startPagination, $limitPagination";
		$equipQuerySTMT = $db->prepare($equipQuerySQL); 
	 	$equipQuerySTMT->execute();

		echo '
			<div class="container" id="tableEquipment">
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
				    <th scope="col">Ενέργειες</th>
				    </tr>
				</thead>
		';
		$url = $_SERVER['REQUEST_URI'];
		$value=(explode("=", $url));
		if (isset($value[1]) AND $value[1] == ""){
			while($equipQuerySTMTResult=$equipQuerySTMT->fetch(PDO::FETCH_ASSOC)){
				if (!$imageToDisplaySTMTResult['hash_filename']){
			 		$imageHashedName = "noimage.jpg";	
			 	}else {
			 		$imageHashedName = $imageToDisplaySTMTResult['hash_filename'];
			 	}
		 		if (($equipQuerySTMTResult['quantity']) > 0 ){
		 		echo '
	 						  <tbody>
	 						  <tr>
						      <td><img src="uploadedImages/'.$imageHashedName.'"/></td>
						      <td><a href=equipment_details.php?id_equip='.$equipQuerySTMTResult['id_equip'].'>'.$equipQuerySTMTResult['name_e'].'</a></td>
						      <td>'.$equipQuerySTMTResult['buy_year_e'].'</td>
						      <td>'.$equipQuerySTMTResult['location_e'].'</td>
						      <td><button id="delete" title=Διαγραφή name ="delete" type="submit"><a href=equipment_delete.php?id_equip='.$equipQuerySTMTResult['id_equip'].' class="btn btn-dark">Διαγραφή</a></button><br><button id="modify" title=Τροποποίηση name ="modify"  type="submit"><a href=equipment_modify.php?id_equip='.$equipQuerySTMTResult['id_equip'].' class="btn btn-dark">Αλλαγή</a></button><br><button id="addImage" title=Φωτογραφία name ="addImage"  type="submit"><a href=addImage.php?id_equip='.$equipQuerySTMTResult['id_equip'].' class="btn btn-dark">Εικόνα</a></button></td>
						      </tr>
						      </tbody>
				';
				}
			}
		}

		if (isset($_GET['equipmentName'])){
    		$searchQuerySQL = "SELECT * FROM equip_svds WHERE name_e LIKE :keyword";
    		$searchQuerySTMT = $db->prepare($searchQuerySQL);	
    		$searchQuerySTMT->bindParam(':keyword', $_GET['equipmentName']); 
    		$searchQuerySTMT->execute();
			while ($searchQuerySTMTResult=$searchQuerySTMT->fetch(PDO::FETCH_ASSOC)){
				if (!$searchQuerySTMTResult['hash_filename']){
			 		$imageHashedName = "noimage.jpg";	
			 	}else {
			 		$imageHashedName = $searchQuerySTMTResult['hash_filename'];
			 	}
				echo '
	 						  <tbody>
	 						  <tr>
						      <td><img src="uploadedImages/'.$imageHashedName.'"/></td>
						      <td><a href=equipment_details.php?id_equip='.$searchQuerySTMTResult['id_equip'].'>'.$searchQuerySTMTResult['name_e'].'</a></td>
						      <td>'.$searchQuerySTMTResult['buy_year_e'].'</td>
						      <td>'.$searchQuerySTMTResult['location_e'].'</td>
						      <td><a href=equipment_delete.php?id_equip='.$searchQuerySTMTResult['id_equip'].' class="btn btn-dark"><button id="delete" title=Διαγραφή name ="delete">Διαγραφή</button></a><br><a href=equipment_modify.php?id_equip='.$searchQuerySTMTResult['id_equip'].' class="btn btn-dark"><button id="modify" title=Τροποποίηση name ="modify">Αλλαγή</button></a><br><a href=addImage.php?id_equip='.$searchQuerySTMTResult['id_equip'].' class="btn btn-dark"><button id="addImage" title=Φωτογραφία name ="addImage">Εικόνα</button></a></td>
						      </tr>
						      </tbody>
				';
			}

    	}elseif (isset($_GET['yearOfBuy'])) {
			$searchQuerySQL = "SELECT * FROM equip_svds WHERE buy_year_e LIKE :keyword";
			$searchQuerySTMT = $db->prepare($searchQuerySQL);
    		$searchQuerySTMT->bindParam(':keyword', $_GET['yearOfBuy']); 
    		$searchQuerySTMT->execute();
			while ($searchQuerySTMTResult=$searchQuerySTMT->fetch(PDO::FETCH_ASSOC)){
				if (!$searchQuerySTMTResult['hash_filename']){
			 		$imageHashedName = "noimage.jpg";	
			 	}else {
			 		$imageHashedName = $searchQuerySTMTResult['hash_filename'];
			 	}
				echo '
	 						  <tbody>
	 						  <tr>
						      <td><img src="uploadedImages/'.$imageHashedName.'"/></td>
						      <td><a href=equipment_details.php?id_equip='.$searchQuerySTMTResult['id_equip'].'>'.$searchQuerySTMTResult['name_e'].'</a></td>
						      <td>'.$searchQuerySTMTResult['buy_year_e'].'</td>
						      <td>'.$searchQuerySTMTResult['location_e'].'</td>
						      <td><a href=equipment_delete.php?id_equip='.$searchQuerySTMTResult['id_equip'].' class="btn btn-dark"><button id="delete" title=Διαγραφή name ="delete">Διαγραφή</button></a><br><a href=equipment_modify.php?id_equip='.$searchQuerySTMTResult['id_equip'].' class="btn btn-dark"><button id="modify" title=Τροποποίηση name ="modify">Αλλαγή</button></a><br><a href=addImage.php?id_equip='.$searchQuerySTMTResult['id_equip'].' class="btn btn-dark"><button id="addImage" title=Φωτογραφία name ="addImage">Εικόνα</button></a></td>
						      </tr>
						      </tbody>
				';
			}
    	}elseif (isset($_GET['locationName'])) {
    		$searchQuerySQL = "SELECT * FROM equip_svds WHERE location_e LIKE :keyword";
    		$searchQuerySTMT = $db->prepare($searchQuerySQL);
    		$searchQuerySTMT->bindParam(':keyword', $_GET['locationName']); 
    		$searchQuerySTMT->execute();
			while ($searchQuerySTMTResult=$searchQuerySTMT->fetch(PDO::FETCH_ASSOC)){
				if (!$searchQuerySTMTResult['hash_filename']){
			 		$imageHashedName = "noimage.jpg";	
			 	}else {
			 		$imageHashedName = $searchQuerySTMTResult['hash_filename'];
			 	}
				echo '
	 						  <tbody>
	 						  <tr>
						      <td><img src="uploadedImages/'.$imageHashedName.'"/></td>
						      <td><a href=equipment_details.php?id_equip='.$searchQuerySTMTResult['id_equip'].'>'.$searchQuerySTMTResult['name_e'].'</a></td>
						      <td>'.$searchQuerySTMTResult['buy_year_e'].'</td>
						      <td>'.$searchQuerySTMTResult['location_e'].'</td>
						      <td><a href=equipment_delete.php?id_equip='.$searchQuerySTMTResult['id_equip'].' class="btn btn-dark"><button id="delete" title=Διαγραφή name ="delete">Διαγραφή</button></a><br><a href=equipment_modify.php?id_equip='.$searchQuerySTMTResult['id_equip'].' class="btn btn-dark"><button id="modify" title=Τροποποίηση name ="modify">Αλλαγή</button></a><br><a href=addImage.php?id_equip='.$searchQuerySTMTResult['id_equip'].' class="btn btn-dark"><button id="addImage" title=Φωτογραφία name ="addImage">Εικόνα</button></a></td>
						      </tr>
						      </tbody>
				';
			}
    	}else{
			while($equipQuerySTMTResult=$equipQuerySTMT->fetch(PDO::FETCH_ASSOC)){
				if (!$equipQuerySTMTResult['hash_filename']){
			 		$imageHashedName = "noimage.jpg";	
			 	}else {
			 		$imageHashedName = $equipQuerySTMTResult['hash_filename'];
			 	}
			echo '
				<tbody>
				<tr>
		      	<td><img src="uploadedImages/'.$imageHashedName.'"/></td>
		      	<td>'.$equipQuerySTMTResult['name_e'].'</td>
		      	<td>'.$equipQuerySTMTResult['buy_year_e'].'</td>
		      	<td>'.$equipQuerySTMTResult['location_e'].'</td>
		      	<td><a href=equipment_delete.php?id_equip='.$equipQuerySTMTResult['id_equip'].' class="btn btn-dark" id_equip='.$equipQuerySTMTResult['id_equip'].'><button id="delete" name ="delete">Διαγραφή</button></a><br><a href=equipment_modify.php?id_equip='.$equipQuerySTMTResult['id_equip'].' class="btn btn-dark"><button id="modify"  name ="modify">Αλλαγή</button></a><br><a href=addImage.php?id_equip='.$equipQuerySTMTResult['id_equip'].' class="btn btn-dark"><button id="addImage" name ="addImage">Εικόνα</button></a></td>
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
	    		<a class="page-link" href=equipment_manage.php?p='.($pageOfPagination-1).'><<</a></li>
	    	';
	    }

	    for ($i=1; $i <= $totalCellsPagination; $i++) { 
            if ($pageOfPagination == $i){
                echo "<li class='page-item  active'>
                <a class='page-link' href=equipment_manage.php?p=".$i.">".$i."</a></li>";                    
            }else {

        	    echo "<li class='page-item'>
        	    <a class='page-link' href=equipment_manage.php?p=".$i.">".$i."</a></li>";
            }
        }

        if ($pageOfPagination < $totalCellsPagination){    	 
	    	echo'
	           	<li class="page-item">
	           	<a class="page-link" href=equipment_manage.php?p='.($pageOfPagination+1).'>>></a></li>
	        ';
	    }       	
        echo '
          	</ul>
          	</table>
            </div>
        ';    
	}		    
	catch(PDOException $e)
	{
	   echo "Error: " . $e->getMessage();
	} 

include("views/footer.php");

?>