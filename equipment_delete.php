<?php
include("variables_file.php");
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");

	$idToDelete=$_GET['id_equip'];
	$deleteQuerySQL = "DELETE  FROM borrow_svds WHERE id_equip_borrow=  :idToDelete";
	$deleteQuerySTMT = $db->prepare($deleteQuerySQL);
	$deleteQuerySTMT->bindParam(':idToDelete', $idToDelete, PDO::PARAM_INT);
	$deleteQuerySTMT->execute();
	
	$deleteQuerySQL = "DELETE  FROM equip_svds WHERE id_equip=  :idToDelete";
	$deleteQuerySTMT = $db->prepare($deleteQuerySQL);
	$deleteQuerySTMT->bindParam(':idToDelete', $idToDelete, PDO::PARAM_INT);
	$deleteQuerySTMT->execute();
	header("Location: equipment_manage.php");

include("views/footer.php");
?>
