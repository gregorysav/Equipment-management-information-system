<?php
//Access: Registered Users
include("variables_file.php");
include("checkUser.php");
echo '
	<!DOCTYPE html>
	<html lang="en">
';
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");
//  Η μεταβλητή $type έχει τεθεί από το $_SESSION['type'] και ελέγχει το επίπεδο δικαιωμάτων του συνδεδεμένου χρήστη
if ($type != 0){
//  Η μεταβλητή $_SESSION['imageDeleteInformMessage'] εμφανίζει κατάλληλο μήνυμα εάν έχει διαγραφεί η εικόνα του εξαρτήματος	
	if (isset($_SESSION['imageDeleteInformMessage']) AND !empty($_SESSION['imageDeleteInformMessage'])){
		echo '
			<div class="container imageDeleteInformMessage">'.$_SESSION['imageDeleteInformMessage'].'</div>
		';
	}	
	$_SESSION['imageDeleteInformMessage']= "";	
	$idToChange=filter_var($_GET['id_equip'],FILTER_SANITIZE_NUMBER_FLOAT);

	$imageToDisplaySQL = "SELECT * FROM equip_svds WHERE id_equip = :id_equip";
	$imageToDisplaySTMT = $db->prepare($imageToDisplaySQL);
	$imageToDisplaySTMT->bindParam(':id_equip', $idToChange, PDO::PARAM_INT);
	$imageToDisplaySTMT->execute();
	while($imageToDisplaySTMTResult=$imageToDisplaySTMT->fetch(PDO::FETCH_ASSOC)){
	 	$imageRealName = $imageToDisplaySTMTResult['real_filename'];
	 	$imageHashName = $imageToDisplaySTMTResult['hash_filename'];
	 	$equipmentName = $imageToDisplaySTMTResult['name_e'];
	 	if (!$imageToDisplaySTMTResult['hash_filename']){
	 		$imageHashedName = "noimage.png";	
	 	}else {
	 		$imageHashedName = $imageToDisplaySTMTResult['hash_filename'];
	 	} 	
	 	$idDescription = $imageToDisplaySTMTResult['short_desc_e'];
	 	$descriptionQuerySQL = "SELECT * FROM description_svds WHERE id_desc= :idDescription";
 		$descriptionQuerySTMT = $db->prepare($descriptionQuerySQL);
 		$descriptionQuerySTMT->bindParam(':idDescription', $idDescription, PDO::PARAM_INT); 
 		$descriptionQuerySTMT->execute();
 		while($descriptionQuerySTMTResult=$descriptionQuerySTMT->fetch(PDO::FETCH_ASSOC)){
 			$equipmentShortComment = $descriptionQuerySTMTResult['short_desc'];
 		}		
	}

	echo '
		<div class="container">
			<h4>Υπάρχουσα φωτογραφία για το εξάρτημα '.$equipmentName.' με περιγραφή '.$equipmentShortComment.'</h4>
				<img src="uploadedImages/'.$imageHashedName.'"/>
				<form action="upload.php?id_equip='.$idToChange.'" method="post" enctype="multipart/form-data">
				    <input type="file" name="filename" id="filename"><br>
			    <input type="submit" id="imageUpload" class="btn btn-primary" value="Ανεβάστε Εικόνα" name="submit" disabled>
				</form>
				<button type="submit" id="imageDelete" class="btn btn-primary btn-danger" id_equip='.$idToChange.' image_name='.$imageHashName.'>Διαγραφή Εικόνας</button>		
		</div>	
	';
}else {
    header("Location: index.php");
    die("Δεν δικαιώματα εισόδου σε αυτή τη σελίδα.");
}
include("views/footer.php");
echo '
	</body>
	</html>
';
?>