<?php
//Access: Registered Users
include "variables_file.php";
echo '
  <div class="container"> 
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <a class="navbar-brand" href="index.php">Αρχική Σελίδα</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
    </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav">
        <li class="nav-item active">
          <a class="nav-link" href="account.php">Προφίλ</a>
        </li>
  </ul>
';
if ($type == 0) {
  echo '
    <li class="nav-item active">
      <a class="nav-link" href="equipmentViewForUser.php">Εξαρτήματα</a>
    </li>
    <li class="nav-item active">
      <a class="nav-link" href="active.php">Ενεργοί Δανεισμοί</a>
    </li>
    <li class="nav-item active">
      <a class="nav-link" href="new_borrow.php">Νέος Δανεισμός</a>
    </li> 
    </ul> 
  '; 
}
if ($type == 1 OR $type == 2 OR $type == 3) {
  echo '  
    <div class="btn-group">
    <button type="button" class="btn btn-sm btn-dark dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Εξαρτήματα </button>
    <div class="dropdown-menu" id="dropDown">
      <a class="dropdown-item" href="actions_equipment.php?action=add">Νέο Εξάρτημα</a>
      <a class="dropdown-item" href="totalEquipmentForTeacher.php">Όλα τα εξαρτήματα</a>
      <a class="dropdown-item" href="equipmentViewForTeacher.php">Διαθέσιμα εξαρτήματα</a>
      <a class="dropdown-item" href="provider.php">Διαχείριση Προμηθευτών</a>
      <a class="dropdown-item" href="actions_provider.php?action=add">Νέος Προμηθευτής</a>
    </div>
    </div>
    <div class="btn-group">
    <button type="button" class="btn btn-sm btn-dark dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Δανεισμοί </button>
    <div class="dropdown-menu" id="dropDown">
      <a class="dropdown-item" href="new_borrow.php">Νέος Δανεισμός</a>
      <a class="dropdown-item" href="confirmation.php">Επιβεβαίωση Δανεισμών</a>
      <a class="dropdown-item" href="equipment_return.php">Ενεργοί Δανεισμοί</a>
      <a class="dropdown-item" href="borrow_history.php">Ιστορικό Δανεισμών</a>
      <a class="dropdown-item" href="find_user.php">Εκτύπωση Εντύπου</a>
      <a class="dropdown-item" href="borrow_user.php">Δανεισμοί Χρήστη</a>
    </div>
    </div>

  ';
}


$basketQuerySQL = "SELECT * FROM basket_svds WHERE id_user_basket= :userBorrow";
$basketQuerySTMT = $db->prepare($basketQuerySQL);
$basketQuerySTMT->bindParam(':userBorrow', $id); 
$basketQuerySTMT->execute();
$basketItems= $basketQuerySTMT->rowCount();

echo'    
    <div class="form-inline my-2 my-lg-0 mr-auto" id="navbarButtons">
        <a href="basket.php" class="fas fa-shopping-basket">('.$basketItems.') Καλάθι |</a>
        <a href="logout.php" class="fas fa-sign-out-alt">  Αποσύνδεση</a>
    </div>
    </div>      
    </nav>
    </div>
';
?>