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
    <li class="nav-item active">
      <a class="nav-link" href="confirmation.php">Επιβεβαίωση Δανεισμών</a>
    </li>
  ';
}
echo'    
    <li class="nav-item active">
      <a class="nav-link" href="new_borrow.php">Νέος Δανεισμός</a>
    </li> 
    </ul>
    <div class="form-inline my-2 my-lg-0" id="navbarButtons">
        <a href="basket.php" class="fas fa-shopping-basket"> Καλάθι </a>
        <a href="logout.php" class="fas fa-sign-out-alt">  Αποσύνδεση</a>
    </div>
    </div>      
    </nav>
    </div>
';
?>