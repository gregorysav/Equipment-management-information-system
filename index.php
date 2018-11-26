<?php
include("variables_file.php");
include("views/connection.php");
include("views/header.php");
  if ($_SESSION['email']){
  	if(isset($_GET['logout']) == 1){
  		session_unset();
  		header("Location: login.php");
  	}

  	$dateSQL = "SELECT * FROM borrow_svds";
    $dateSTMT = $db->prepare($dateSQL);
    $dateSTMT->execute();
    while($dateSTMTResult=$dateSTMT->fetch(PDO::FETCH_ASSOC)){	
    	$findEndDate = $dateSTMTResult['expire_date'];
	 	$endDate = date_create($findEndDate);
	 	$daysToEnd = date_diff($startToday,$endDate)->format('%a');
	 	$dateChangeSQL = "UPDATE borrow_svds SET  notify30= :notify30, notify20= :notify20, notify10= :notify10 WHERE id_borrow= :idBorrow";
		$dateChangeSTMT = $db->prepare($dateChangeSQL);
		$dateChangeSTMT->bindParam(':idBorrow', $dateSTMTResult['id_borrow'], PDO::PARAM_INT);
		$dateChangeSTMT->bindParam(':notify30', $daysToEnd, PDO::PARAM_INT);
		$dateChangeSTMT->bindParam(':notify20', $daysToEnd, PDO::PARAM_INT);
		$dateChangeSTMT->bindParam(':notify10', $daysToEnd, PDO::PARAM_INT);
		$dateChangeSTMT->execute();
    }	
    echo '
      <div class="container">
      <a href="index.php" id="indexPicture"><img src="images/uowmicon.jpg" id="uowmicon"></a><h1 id="welcome">Καλώς ήλθατε στην Ηλεκτρονική Σελίδα Δανεισμού το ΠΔΜ</h1><hr>
      </div>
    ';  
    }else {
    header("Location: login.php");
    }
echo '
	<div class="container" id="indexPage">  
  	'.$warningMessage.'
	    <div class="row">
	        <div class="col-md-4 col-xs-4">
	        	<a href="account.php">
	  				<img src="images/customericon.png" title="Προφίλ" alt="Card image cap">
	  			</a>	
			</div>
	        <div class="col-md-4 col-xs-4">
	        	<a href="equipment.php">
	  				<img src="images/componentsicon.png" title="Εξαρτήματα" alt="Card image cap">
			</div>	
	        <div class="col-md-4 col-xs-4">
	        	<a href="active.php">
	  				<img src="images/borrowicon.png" title="Δανεισμοί" alt="Card image cap">
			</div>	
			<div class="col-md-4 col-xs-4">
	        	<a href="new_borrow.php">
	  				<img src="images/searchicon.png" title="Αναζήτηση" alt="Card image cap">
			</div>	
	        <div class="col-md-4 col-xs-4">
	        	<a href="logout.php">
	  				<img src="images/logouticon.png" title="Αποσύνδεση" alt="Card image cap">
			</div>	
		</div>
	</div><br><br><br>   
';

include("views/footer.php");
?>