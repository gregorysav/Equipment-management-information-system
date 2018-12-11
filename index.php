<?php
include("variables_file.php");
include("views/connection.php");
include("views/header.php");
  if ($_SESSION['email']){
  	if(isset($_GET['logout']) == 1){
  		session_unset();
  		header("Location: login.php");
  		die("Δεν έχετε συνδεθεί");
  	}

  	$dateSQL = "SELECT * FROM borrow_svds";
    $dateSTMT = $db->prepare($dateSQL);
    $dateSTMT->execute();
    while($dateSTMTResult=$dateSTMT->fetch(PDO::FETCH_ASSOC)){	
    	$now = new DateTime();
		$findEndDate = $dateSTMTResult['expire_date'];
	 	$endDate = date_create($findEndDate);
	 	$daysToEnd = date_diff($startToday,$endDate)->format('%a');
	 	$dateToCheck = new DateTime($dateSTMTResult['expire_date']);
		if($dateToCheck > $now) {
    		$dateChangeSQL = "UPDATE borrow_svds SET  notify30= :notify30, notify20= :notify20, notify10= :notify10 WHERE id_borrow= :idBorrow";
			$dateChangeSTMT = $db->prepare($dateChangeSQL);
			$dateChangeSTMT->bindParam(':idBorrow', $dateSTMTResult['id_borrow'], PDO::PARAM_INT);
			$dateChangeSTMT->bindParam(':notify30', $daysToEnd, PDO::PARAM_INT);
			$dateChangeSTMT->bindParam(':notify20', $daysToEnd, PDO::PARAM_INT);
			$dateChangeSTMT->bindParam(':notify10', $daysToEnd, PDO::PARAM_INT);
			$dateChangeSTMT->execute();
		}else {
			$dateChangeSQL = "UPDATE borrow_svds SET  notify30= :notify30, notify20= :notify20, notify10= :notify10 WHERE id_borrow= :idBorrow";
			$dateChangeSTMT = $db->prepare($dateChangeSQL);
			$dateChangeSTMT->bindParam(':idBorrow', $dateSTMTResult['id_borrow'], PDO::PARAM_INT);
			$dateChangeSTMT->bindParam(':notify30', $zero, PDO::PARAM_INT);
			$dateChangeSTMT->bindParam(':notify20', $zero, PDO::PARAM_INT);
			$dateChangeSTMT->bindParam(':notify10', $zero, PDO::PARAM_INT);
			$dateChangeSTMT->execute();
		}
    }
  		
    echo '
      <div class="container">
      	<a href="index.php" id="indexPicture"><img src="images/uowmicon.jpg" id="uowmicon"></a><h2 id="welcome">Ηλεκτρονική Σελίδα Δανεισμού του ΠΔΜ</h2>
      	<div class="form-inline">
      	  <p>Καλώς ήρθες, '.$last_name.' '.$first_name.'</p>	
	      <p><a href="logout.php" class="btn btn-light" id="welcomeMessage">Αποσύνδεση</a></p><br>
      	</div>
      	<hr>
    </div>  	
    ';  
    }else {
    header("Location: login.php");
    die("Δεν έχετε συνδεθεί");
    }
echo '
	<div class="container" id="indexPage">  
  	'.$warningMessage.'
	    <div class="row">
	        <div class="col-md-3 col-xs-3">
	        	<p>Προφίλ</p>
	        	<a href="account.php">
	  			<img src="images/customericon.png" title="Προφίλ" alt="Card image cap">
	  			</a>
			</div>
	        <div class="col-md-3 col-xs-3">
	        	<p>Εξαρτήματα</p>
	        	<a href="equipment.php">
	  			<img src="images/componentsicon.png" title="Εξαρτήματα" alt="Card image cap">
			</div>
';
if ($_SESSION['type'] == 1){				
	echo '
	    <div class="col-md-3 col-xs-3">
	      	<p>Δανεισμοί</p>
	       	<a href="confirmation.php">
			<img src="images/borrowicon.png" title="Δανεισμοί" alt="Card image cap">
		</div>
	';
}else {
	echo '
	    <div class="col-md-3 col-xs-3">
	      	<p>Δανεισμοί</p>
	       	<a href="active.php">
			<img src="images/borrowicon.png" title="Δανεισμοί" alt="Card image cap">
		</div>
	';
}
echo '			
		<div class="col-md-3 col-xs-3">
			<p>Αναζήτηση</p>
	       	<a href="new_borrow.php">
			<img src="images/searchicon.png" title="Αναζήτηση" alt="Card image cap">
		</div>		
		</div>
	</div><br><br><br>   
';

include("views/footer.php");
?>