<?php
include("variables_file.php");
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");

	
	$idToDelete=$_GET['id_borrow'];
	$deleteQuerySQL = "DELETE  FROM borrow_svds WHERE id_borrow=  :idToDelete";
	$deleteQuerySTMT = $db->prepare($deleteQuery);
	$deleteQuerySTMT->bindParam(':idToDelete', $idToDelete, PDO::PARAM_INT);
	$deleteQuerySTMT->execute();

	header("Location: active.php");


include("views/footer.php");
?>
