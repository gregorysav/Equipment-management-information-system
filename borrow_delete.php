<?php
include("variables_file.php");
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");

if (isset($_GET['id_borrow']) AND $type == 0){	
	$idToDelete= filter_var($_GET['id_borrow'],FILTER_SANITIZE_NUMBER_FLOAT);
	$deleteQuerySQL = "DELETE  FROM borrow_svds WHERE id_borrow=  :idToDelete";
	$deleteQuerySTMT = $db->prepare($deleteQuerySQL);
	$deleteQuerySTMT->bindParam(':idToDelete', $idToDelete, PDO::PARAM_INT);
	$deleteQuerySTMT->execute();

	header("Location: active.php");
	die("Δεν έχετε συνδεθεί");
}

if (isset($_GET['id_borrow']) AND $type == 1){	
	$idToDelete= filter_var($_GET['id_borrow'],FILTER_SANITIZE_NUMBER_FLOAT);
	$deleteQuerySQL = "DELETE  FROM borrow_svds WHERE id_borrow=  :idToDelete";
	$deleteQuerySTMT = $db->prepare($deleteQuerySQL);
	$deleteQuerySTMT->bindParam(':idToDelete', $idToDelete, PDO::PARAM_INT);
	$deleteQuerySTMT->execute();

	header("Location: backend.php");
	die("Δεν έχετε συνδεθεί");
}

include("views/footer.php");
?>
