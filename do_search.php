<?php
include "views/connection.php";
 
$search =$_POST['query'];
$searchQuerySQL = "SELECT * FROM equip_svds WHERE name_e LIKE :keyword"; 
$searchQuerySTMT = $db->prepare($searchQuerySQL);
$searchQuerySTMT->bindParam(':keyword', $search, PDO::PARAM_INT);
$searchQuerySTMT->execute();
			
$data=array();
while ($searchQuerySTMTResult=$searchQuerySTMT->fetch(PDO::FETCH_ASSOC)) {
	$data[] = $searchQuerySTMTResult['name_e'];
}			
echo json_encode($data);
?>