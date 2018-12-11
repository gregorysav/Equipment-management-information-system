<?php
include "variables_file.php";
echo '
	<div class="container">	
		<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  		<a class="navbar-brand" href="index.php">Αρχική Σελίδα</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
		</button>

  		<div class="collapse navbar-collapse" id="navbarSupportedContent">
    	<ul class="navbar-nav mr-auto">
      	<li class="nav-item active">
        	<a class="nav-link" href="account.php">Προφίλ</a>
      	</li>
        <li class="nav-item active">
          <a class="nav-link" href="equipment.php">Εξαρτήματα</a>
        </li>
';
if ($_SESSION['type'] == 0) {
  echo '
   	<li class="nav-item active">
    <a class="nav-link" href="active.php">Ενεργοί Δανεισμοί</a>
  	</li>
  '; 
}
if ($_SESSION['type'] == 1) {
  echo '  
    <div class="btn-group">
    <button class="btn btn-dark btn-sm" id="dropDownButton" type="button"><a class="dropdown-item" href="new_borrow.php">Νέος Δανεισμός</a>
    </button>
    <button type="button" class="btn btn-sm btn-dark dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <span class="sr-only">Toggle Dropdown</span>
    </button>
    <div class="dropdown-menu" id="dropDown">
      <a class="dropdown-item" href="confirmation.php">Επιβεβαίωση Δανεισμών</a>
      <a class="dropdown-item" href="backend.php">Όλοι οι Δανεισμοί</a>
      <a class="dropdown-item" id="pdfPrint" href="">Εκτύπωση Εντύπου</a>
    </div>
    </div>

  ';
}
if ($_SESSION['type'] == 0) {
  echo'    
      <li class="nav-item active">
        <a class="nav-link" href="new_borrow.php">Νέος Δανεισμός</a>
      </li>
  ';
}

$basketQuerySQL = "SELECT * FROM basket_svds WHERE id_user_basket= :userBorrow";
$basketQuerySTMT = $db->prepare($basketQuerySQL);
$basketQuerySTMT->bindParam(':userBorrow', $aem); 
$basketQuerySTMT->execute();
$basketItems= $basketQuerySTMT->rowCount();

echo'     
    </ul>
    <div class="form-inline my-2 my-lg-0" id="navbarButtons">
        <a href="basket.php" class="fas fa-shopping-basket">('.$basketItems.') Καλάθι </a>
        <a href="logout.php" class="fas fa-sign-out-alt">  Αποσύνδεση</a>
    </div>
    </div>      
    </nav>
    </div>
';
?>