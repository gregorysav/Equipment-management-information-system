<?php
include("variables_file.php");
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");

	if (array_key_exists("logout", $_GET)){
	unset($_SESSION);
	}
	if (!isset($_SESSION['email'])){

		header("Location: index.php");
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
							  <th scope="col"></th>
						    </tr>
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
		 		if (($equipQuerySTMTResult['quantity']) > 0 ){
		 		echo '
	 						  <tbody>
	 						  <tr>
						      <td><img src="uploadedImages/'.$equipQuerySTMTResult['real_filename'].'"/></td>
						      <td><a href=equipment_details.php?id_equip='.$equipQuerySTMTResult['id_equip'].'>'.$equipQuerySTMTResult['name_e'].'</a></td>
						      <td>'.$equipQuerySTMTResult['buy_year_e'].'</td>
						      <td>'.$equipQuerySTMTResult['location_e'].'</td>
						      <td><button id="delete" title=Διαγραφή name ="delete" type="submit"><a href=equipment_delete.php?id_equip='.$equipQuerySTMTResult['id_equip'].' class="fa fa-recycle btn btn-dark"></a></button><br><br><button id="modify" title=Τροποποίηση name ="modify"  type="submit"><a href=equipment_modify.php?id_equip='.$equipQuerySTMTResult['id_equip'].' class="fa fa-wrench btn btn-dark"></a></button><br><br><button id="addImage" title=Φωτογραφία name ="addImage"  type="submit"><a href=addImage.php?id_equip='.$equipQuerySTMTResult['id_equip'].' class="fa fa-camera btn btn-dark"></a></button></td>
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
						      <td><button id="delete" title=Διαγραφή name ="delete" type="submit"><a href=equipment_delete.php?id_equip='.$searchQuerySTMTResult['id_equip'].' class="fa fa-recycle btn btn-dark"></a></button><br><br><button id="modify" title=Τροποποίηση name ="modify"  type="submit"><a href=equipment_modify.php?id_equip='.$searchQuerySTMTResult['id_equip'].' class="fa fa-wrench btn btn-dark"></a></button><br><br><button id="addImage" title=Φωτογραφία name ="addImage"  type="submit"><a href=addImage.php?id_equip='.$searchQuerySTMTResult['id_equip'].' class="fa fa-camera btn btn-dark"></a></button></td>
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
						      <td><button id="delete" title=Διαγραφή name ="delete" type="submit"><a href=equipment_delete.php?id_equip='.$searchQuerySTMTResult['id_equip'].' class="fa fa-recycle btn btn-dark"></a></button><br><br><button id="modify" title=Τροποποίηση name ="modify"  type="submit"><a href=equipment_modify.php?id_equip='.$searchQuerySTMTResult['id_equip'].' class="fa fa-wrench btn btn-dark"></a></button><br><br><button id="addImage" title=Φωτογραφία name ="addImage"  type="submit"><a href=addImage.php?id_equip='.$searchQuerySTMTResult['id_equip'].' class="fa fa-camera btn btn-dark"></a></button></td>
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
						      <td><button id="delete" title=Διαγραφή name ="delete" type="submit"><a href=equipment_delete.php?id_equip='.$searchQuerySTMTResult['id_equip'].' class="fa fa-recycle btn btn-dark"></a></button><br><br><button id="modify" title=Τροποποίηση name ="modify"  type="submit"><a href=equipment_modify.php?id_equip='.$searchQuerySTMTResult['id_equip'].' class="fa fa-wrench btn btn-dark"></a></button><br><br><button id="addImage" title=Φωτογραφία name ="addImage"  type="submit"><a href=addImage.php?id_equip='.$searchQuerySTMTResult['id_equip'].' class="fa fa-camera btn btn-dark"></a></button></td>
						      </tr>
						      </tbody>
				';
			}
    	}else{
			while($equipQuerySTMTResult=$equipQuerySTMT->fetch(PDO::FETCH_ASSOC)){
			echo '
				<tbody>
				<tr>
		      	<td><img src="uploadedImages/'.$equipQuerySTMTResult['real_filename'].'"/></td>
		      	<td>'.$equipQuerySTMTResult['name_e'].'</td>
		      	<td>'.$equipQuerySTMTResult['buy_year_e'].'</td>
		      	<td>'.$equipQuerySTMTResult['location_e'].'</td>
		      	<td><button id="delete" title=Διαγραφή name ="delete" type="submit"><a href=equipment_delete.php?id_equip='.$equipQuerySTMTResult['id_equip'].' class="fa fa-recycle btn btn-dark"></a></button><br><br><button id="modify" title=Τροποποίηση name ="modify"  type="submit"><a href=equipment_modify.php?id_equip='.$equipQuerySTMTResult['id_equip'].' class="fa fa-wrench btn btn-dark"></a></button><br><br><button id="addImage" title=Φωτογραφία name ="addImage"  type="submit"><a href=addImage.php?id_equip='.$equipQuerySTMTResult['id_equip'].' class="fa fa-camera btn btn-dark"></a></button></td>
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
	    if ($pageOfPagination > 1){    	 
	    	echo'
	    		<li><a href=equipment_manage.php?p='.($pageOfPagination-1).' class="button"><<</a></li>
	    	';
	    }

	    for ($i=1; $i <= $totalCellsPagination; $i++) { 
            if ($pageOfPagination == $i){
                echo "<li class='active'><a href=equipment_manage.php?p=".$i.">".$i."</a></li>";                    
            }else {

        	    echo "<li><a href=equipment_manage.php?p=".$i.">".$i."</a></li>";
            }
        }

        if ($pageOfPagination < $totalCellsPagination){    	 
	    	echo'
	           	<li><a href=equipment_manage.php?p='.($pageOfPagination+1).' class="button">>></a></li>
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