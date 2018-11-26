<?php
include("variables_file.php");
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");
include("functions.php");

	
	$itemsToBorrow = array();
	$borrowQuerySQL = "SELECT * FROM borrow_svds WHERE aem_borrow= :userID"; 
  $borrowQuerySTMT = $db->prepare($borrowQuerySQL);
  $borrowQuerySTMT->bindParam(':userID', $_SESSION['id'], PDO::PARAM_INT);
  $borrowQuerySTMT->execute();
  while($borrowQuerySTMTResult=$borrowQuerySTMT->fetch(PDO::FETCH_ASSOC)){
  	$startDate = $borrowQuerySTMTResult['start_date'];
  	$endDate = $borrowQuerySTMTResult['expire_date'];
   	$id_equip = $borrowQuerySTMTResult['id_equip_borrow'];
   	$equipQuerySQL = "SELECT * FROM equip_svds WHERE id_equip= :id_equip";
  	$equipQuerySTMT = $db->prepare($equipQuerySQL);
    $equipQuerySTMT->bindParam(':id_equip', $id_equip, PDO::PARAM_INT);
  	$equipQuerySTMT->execute();
  	while($equipQuerySTMTResult=$equipQuerySTMT->fetch(PDO::FETCH_ASSOC)){
  		$itemsToBorrow[] = $equipQuerySTMTResult['name_e'];
  	}
  }
	

 			
echo '
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
  Προβολή Συμφωνητικού
</button>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Έγγραφο Ιδιωτικό Συμφωνητικό προς Υπογραφή</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
';
PDFPrint($itemsToBorrow,$startDate,$endDate);
echo '      
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Κλείσιμο</button>
        <button type="button" class="btn btn-primary">Εκτύπωση</button>
      </div>
    </div>
  </div>
</div>    	
';




include("views/footer.php");
?>