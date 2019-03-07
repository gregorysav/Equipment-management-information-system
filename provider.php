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
//  Η μεταβλητή $type έχει τεθεί από το $_SESSION['type'] και ελέγχει το επίπεδο δικαιωμάτων του συνδεδεμένου χρήστη
	if ($type == 1 OR $type == 2 OR $type == 3){
		if (isset($_SESSION['unableToDeleteProvider'])){
			echo $_SESSION['unableToDeleteProvider'];
			$_SESSION['unableToDeleteProvider'] ="";
		}
//  Η μεταβλητή $_GET['p'] και ελέγχει τη σελίδα που βρισκόμαστε βάση του pagination
		if (isset($_GET['p'])){
        	$pageOfPagination = filter_var($_GET['p'],FILTER_SANITIZE_NUMBER_FLOAT);
            $startPagination = ($pageOfPagination- 1) * $limitPagination;
        }

        $providerQuerySQL = "SELECT * FROM provider_svds LIMIT :startPagination, :limitPagination";
		$providerQuerySTMT = $db->prepare($providerQuerySQL);
		$providerQuerySTMT->bindParam(':startPagination', $startPagination, PDO::PARAM_INT);
		$providerQuerySTMT->bindParam(':limitPagination', $limitPagination, PDO::PARAM_INT); 
	 	$providerQuerySTMT->execute();
	 	echo '
	 			 		<div class="container">
				 			<div class="form-inline" id="searchHolder">
							<h2 id="title">Καταχωρημένοι Προμηθευτές </h2><br>
					        </div>
							<table class="table table-bordered table-hover">
							<thead class="thead-dark">
							<tr>
							    <th scope="col">Όνομα Προμηθευτή</th>
							    <th scope="col">Τηλέφωνο</th>
							    <th scope="col">Ιστοσελίδα</th>
							    <th scope="col">Email Επικοινωνίας</th>
							    <th scope="col"></th>
							</tr>
							</thead>
				 	
					';
//  Η μεταβλητή $url έχει τεθεί από το $_SERVER['REQUEST_URI'] και ελέγχει το ακριβές url που έχει η σελίδα που βρισκόμαστε		
	 	$url = $_SERVER['REQUEST_URI'];
		$value=(explode("=", $url));
		if (isset($value[1]) AND $value[1] == ""){
			while($providerQuerySTMTResult=$providerQuerySTMT->fetch(PDO::FETCH_ASSOC)){
					echo'
	 		<tbody>
			<tr>
			    <td><a href=provider_details.php?id_p='.$providerQuerySTMTResult['id_p'].'>'.$providerQuerySTMTResult['name_p'].'</a></td>
			    <td>'.$providerQuerySTMTResult['telephone_p'].'</td>
			    <td>'.$providerQuerySTMTResult['website_p'].'</td>
			    <td>'.$providerQuerySTMTResult['email_p'].'</td>
			    <td id="providerPageButtons"><a href=actions_provider.php?action=delete&id_p='.$providerQuerySTMTResult['id_p'].' id="delete" name="delete"class="btn btn-dark">Διαγραφή</a><br><a href=actions_provider.php?action=update&id_p='.$providerQuerySTMTResult['id_p'].' id="modify" name="modify" class="btn btn-dark">Αλλαγή</a></td>
	    	</tr>
	    	</tbody>
	    	';
				}
			}


	 	while($providerQuerySTMTResult=$providerQuerySTMT->fetch(PDO::FETCH_ASSOC)){
	 		echo'
	 		<tbody>
			<tr>
				<td><a href=provider_details.php?id_p='.$providerQuerySTMTResult['id_p'].'>'.$providerQuerySTMTResult['name_p'].'</a></td>
			    <td>'.$providerQuerySTMTResult['telephone_p'].'</td>
			    <td>'.$providerQuerySTMTResult['website_p'].'</td>
			    <td>'.$providerQuerySTMTResult['email_p'].'</td>
			    <td id="providerPageButtons"><a href=actions_provider.php?action=delete&id_p='.$providerQuerySTMTResult['id_p'].' id="delete" name="delete" class="btn btn-dark">Διαγραφή</a><br><a href=actions_provider.php?action=update&id_p='.$providerQuerySTMTResult['id_p'].' id="modify" name="modify" class="btn btn-dark">Αλλαγή</a></td>
	    	</tr>
	    	</tbody>
	    	';
	 	}	

	 	$rowsQuerySQL = "SELECT * FROM provider_svds";
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
	    		<a href=provider.php?p='.($pageOfPagination-1).' class="page-link"><<</a></li>
	    	';
	    }

	    for ($i=1; $i <= $totalCellsPagination; $i++) { 
            if ($pageOfPagination == $i){
                echo "<li class='page-item  active'><a class='page-link' href=provider.php?p=".$i.">".$i."</a></li>";                    
            }else {

        	    echo "<li class='page-item'><a class='page-link' href=provider.php?p=".$i.">".$i."</a></li>";
            }
        }


        if ($pageOfPagination < $totalCellsPagination){    	 
	    	echo'
	           	<li class="page-item">
	           	<a href=provider.php?p='.($pageOfPagination+1).' class="page-link">>></a></li>
	        ';
	    } 
	 	 echo '
          	</table>
            </div>
        ';
	}else{
		header("Refresh:0; url=index.php"); 
        die("Δεν έχετε δικαιώματα εισόδου σε αυτή τη σελίδα.");
	} 	


include("views/footer.php");
echo '
	</body>
	</html>
';
?>