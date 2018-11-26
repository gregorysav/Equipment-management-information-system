<?php
include("variables_file.php");
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");
	if ($_SESSION['email']){
		if ($type == 1){
			echo '
				<div class="container">
				<div class="form-inline" id="searchHolder">
				<h2>Σελίδα Διαχείρισης Εξαρτημάτων </h2>
			    <button type="submit" id="add_equipment" class="btn btn-success btn-info">Προσθήκη Νέου Εξαρτήματος</button><br>
		        <button type="submit" id="modify_equipment" class="btn btn-primary btn-danger">Διαγραφή / Τροποποίηση Εξαρτήματος</button>
		        </div><br>
				</div>
			';
		}


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
						      <th scope="col"></th>
						      <th scope="col"><form name="form" method="get">
  							  <input type="text" name="equipmentName"  class="form-control input-lg" id="equipmentName" autocomplete="off" placeholder="Όνομα εξαρτήματος"/>
							  </form></th>
						      <th scope="col"><form name="form" method="get">
  							  <input type="text" name="yearOfBuy"  class="form-control input-lg" id="yearOfBuy" autocomplete="off" placeholder="π.χ 2000"/>
							  </form></th>
						      <th scope="col"><form name="form" method="get">
  							  <input type="text" name="locationName"  class="form-control input-lg" id="locationName" autocomplete="off" placeholder="π.χ Κοζάνη"/>
							  </form></th>
						    </tr>
						    <tr>
						      <th scope="col">Εικόνα</th>
						      <th scope="col">Όνομα</th>
						      <th scope="col">Έτος απόκτησης</th>
						      <th scope="col">Τοποθεσία</th>
						    </tr>
						</thead>
		';
		$url = $_SERVER['REQUEST_URI'];
		$value=(explode("=", $url));
		if (isset($value[1]) AND $value[1] == ""){
			while($equipQuerySTMTResult=$equipQuerySTMT->fetch(PDO::FETCH_ASSOC)){
		 		if (($equipQuerySTMTResult['quantity']) > 0 ){
		 		echo '
	 						  <tbody>
	 						  <tr>
						      <td><img src="uploadedImages/'.$equipQuerySTMTResult['real_filename'].'"/></td>
						      <td><a href=equipment_details.php?id_equip='.$equipQuerySTMTResult['id_equip'].'>'.$equipQuerySTMTResult['name_e'].'</a></td>
						      <td>'.$equipQuerySTMTResult['buy_year_e'].'</td>
						      <td>'.$equipQuerySTMTResult['location_e'].'</td>
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
				echo '
	 						  <tbody>
	 						  <tr>
						      <td><img src="uploadedImages/'.$searchQuerySTMTResult['real_filename'].'"/></td>
						      <td><a href=equipment_details.php?id_equip='.$searchQuerySTMTResult['id_equip'].'>'.$searchQuerySTMTResult['name_e'].'</a></td>
						      <td>'.$searchQuerySTMTResult['buy_year_e'].'</td>
						      <td>'.$searchQuerySTMTResult['location_e'].'</td>
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
				echo '
	 						  <tbody>
	 						  <tr>
						      <td><img src="uploadedImages/'.$searchQuerySTMTResult['real_filename'].'"/></td>
						      <td><a href=equipment_details.php?id_equip='.$searchQuerySTMTResult['id_equip'].'>'.$searchQuerySTMTResult['name_e'].'</a></td>
						      <td>'.$searchQuerySTMTResult['buy_year_e'].'</td>
						      <td>'.$searchQuerySTMTResult['location_e'].'</td>
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
				echo '
	 						  <tbody>
	 						  <tr>
						      <td><img src="uploadedImages/'.$searchQuerySTMTResult['real_filename'].'"/></td>
						      <td><a href=equipment_details.php?id_equip='.$searchQuerySTMTResult['id_equip'].'>'.$searchQuerySTMTResult['name_e'].'</a></td>
						      <td>'.$searchQuerySTMTResult['buy_year_e'].'</td>
						      <td>'.$searchQuerySTMTResult['location_e'].'</td>
						      </tr>
						      </tbody>
				';
			}
    	}else {
			while($equipQuerySTMTResult=$equipQuerySTMT->fetch(PDO::FETCH_ASSOC)){
		 		if (($equipQuerySTMTResult['quantity']) > 0 ){
		 		echo '
	 						  <tbody>
	 						  <tr>
						      <td><img src="uploadedImages/'.$equipQuerySTMTResult['real_filename'].'"/></td>
						      <td><a href=equipment_details.php?id_equip='.$equipQuerySTMTResult['id_equip'].'>'.$equipQuerySTMTResult['name_e'].'</a></td>
						      <td>'.$equipQuerySTMTResult['buy_year_e'].'</td>
						      <td>'.$equipQuerySTMTResult['location_e'].'</td>
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
	       	<div style="display: flex; padding-left: 0px; list-style: none; justify-content: center;">
	        	 <ul class="pagination" style="position: fixed; bottom: 10px;">
	    ';
	    if ($pageOfPagination > 1){    	 
	    	echo'
	    		<li><a href=equipment.php?p='.($pageOfPagination-1).' class="button"><<</a></li>
	    	';
	    }

	    for ($i=1; $i <= $totalCellsPagination; $i++) { 
            if ($pageOfPagination == $i){
                echo "<li class='active'><a href=equipment.php?p=".$i.">".$i."</a></li>";                    
            }else {

        	    echo "<li><a href=equipment.php?p=".$i.">".$i."</a></li>";
            }
        }

        if ($pageOfPagination < $totalCellsPagination){    	 
	    	echo'
	           	<li><a href=equipment.php?p='.($pageOfPagination+1).' class="button">>></a></li>
	        ';
	    }       	
        echo '
          	</ul>
          	</table>
            </div>
        ';


	} else {
		header("Location: index.php");
	}

include("views/footer.php");
?>