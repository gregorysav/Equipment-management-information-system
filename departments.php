<?php
//Access: Administrator
include("variables_file.php");
echo '
	<!DOCTYPE html>
	<html lang="en">
';
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");

	if ($type == 1 OR $type == 2 OR $type == 3) {
		if (isset($_SESSION['unableToDeleteDepartment'])){
			echo $_SESSION['unableToDeleteDepartment'];
			$_SESSION['unableToDeleteDepartment'] ="";
		}

		if (isset($_GET['p'])){
        	$pageOfPagination = filter_var($_GET['p'],FILTER_SANITIZE_NUMBER_FLOAT);
            $startPagination = ($pageOfPagination- 1) * $limitPagination;
        }

        $departmentQuerySQL = "SELECT * FROM department_svds LIMIT :startPagination, :limitPagination";
		$departmentQuerySTMT = $db->prepare($departmentQuerySQL);
		$departmentQuerySTMT->bindParam(':startPagination', $startPagination, PDO::PARAM_INT);
		$departmentQuerySTMT->bindParam(':limitPagination', $limitPagination, PDO::PARAM_INT); 
	 	$departmentQuerySTMT->execute();
	 	echo '
	 			 		<div class="container">
				 			<div class="form-inline" id="searchHolder">
							<h2 id="title">Καταχωρημένα Τμήματα </h2><br>
			 				<button type="submit" id="add_department" class="btn btn-success btn-info"> Προσθήκη Νέου Τμήματος</button>
					        </div>
							<table class="table table-bordered table-hover">
							<thead class="thead-dark">
							<tr>
							    <th scope="col">Όνομα Τμήματος</th>
							    <th scope="col">Τηλέφωνο</th>
							    <th scope="col"></th>
							</tr>
							</thead>
				 	
					';

	 	$url = $_SERVER['REQUEST_URI'];
		$value=(explode("=", $url));
		if (isset($value[1]) AND $value[1] == ""){
			while($de=$departmentQuerySTMT->fetch(PDO::FETCH_ASSOC)){
					echo'
						<tbody>
						<tr>
						    <td>'.$departmentQuerySTMTResult['name_dep'].'</td>
						    <td>'.$departmentQuerySTMTResult['telephone_dep'].'</td>
						    <td id="departmentPageButtons"><a href=functions_department.php?function=delete&id_dep='.$departmentQuerySTMTResult['id_dep'].' id="delete" name="delete" class="btn btn-dark">Διαγραφή</a><br><a href=functions_department.php?function=update&?id_dep='.$departmentQuerySTMTResult['id_dep'].' id="modify" name="modify" class="btn btn-dark">Αλλαγή</a></button></td>
				    	</tr>
				    	</tbody>
			    	';
				}
			}


	 	while($departmentQuerySTMTResult=$departmentQuerySTMT->fetch(PDO::FETCH_ASSOC)){
	 		echo'	
	 			<tbody>
				<tr>
				    <td>'.$departmentQuerySTMTResult['name_dep'].'</td>
				    <td>'.$departmentQuerySTMTResult['telephone_dep'].'</td>
				    <td id="departmentPageButtons"><a href=functions_department.php?function=delete&id_dep='.$departmentQuerySTMTResult['id_dep'].' id="delete" name="delete" class="btn btn-dark">Διαγραφή</a><br><a href=functions_department.php?function=update&id_dep='.$departmentQuerySTMTResult['id_dep'].' id="modify" name="modify" class="btn btn-dark">Αλλαγή</a></button></td>
		    	</tr>
		    	</tbody>
	    	';
	 	}	

	 	$rowsQuerySQL = "SELECT * FROM department_svds";
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
	    		<a href=departments.php?p='.($pageOfPagination-1).' class="page-link"><<</a></li>
	    	';
	    }

	    for ($i=1; $i <= $totalCellsPagination; $i++) { 
            if ($pageOfPagination == $i){
                echo "<li class='page-item  active'><a class='page-link' href=departments.php?p=".$i.">".$i."</a></li>";                    
            }else {

        	    echo "<li class='page-item'><a class='page-link' href=departments.php?p=".$i.">".$i."</a></li>";
            }
        }


        if ($pageOfPagination < $totalCellsPagination){    	 
	    	echo'
	           	<li class="page-item">
	           	<a href=departments.php?p='.($pageOfPagination+1).' class="page-link">>></a></li>
	        ';
	    } 
	 	 echo '
          	</table>
            </div>
        ';
	}else{
		header("Refresh:0; url=provider.php"); 
        die("Δεν έχετε δικαιώματα πρόσβασης σε αυτή τη σελίδα.");
	} 	


include("views/footer.php");
echo '
	</body>
	</html>
';
?>