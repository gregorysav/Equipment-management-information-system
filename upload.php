<?php
include("variables_file.php");
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");


    $idToChange= filter_var($_GET['id_equip'],FILTER_SANITIZE_NUMBER_FLOAT);
    $target_dir = "uploadedImages/";
    $target_file = $target_dir . md5(basename($_FILES["filename"]["name"]));
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($_FILES["filename"]["name"],PATHINFO_EXTENSION));
    if(isset($_POST["add"])) {
        $check = getimagesize($_FILES["filename"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            $uploadOk = 0;
            $extrmsg="Δεν μπόρεσα να βρω το μέγεθος της εικόνας. Σφάλμα κατά την εκτέλεση του getimagesize.";
        }
    }
    if (file_exists($target_file)) {
        echo "Λυπούμαστε, το όνομα του αρχείο υπάρχει ήδη.";
        $uploadOk = 0;
    }
    if ($_FILES["filename"]["size"] > 500000) {
        echo "Λυπούμαστε, το αρχείο έχει πολύ μεγάλο μέγεθος.";
        $uploadOk = 0;
    }
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        $uploadOk = 0;
    	$extrmsg="Δεν βρήκα αποδεκτό τύπο εικόνας. Ο τύπος που βρήκα είναι: ".$imageFileType;
    }
    if ($uploadOk == 0) {
        echo "Λυπούμαστε, το αρχείο δεν έχει ανέβει επιτυχώς.";
    } else {
        if (move_uploaded_file($_FILES["filename"]["tmp_name"], $target_file)) {
            echo '<div class="container">Η εικόνα ανέβηκε με επιτυχία</div>';
            $realFilename = basename( $_FILES["filename"]["name"]);
            $hashFilename = md5($realFilename);
            $updateEquipmentSQL = "UPDATE equip_svds SET real_filename= :real_filename, hash_filename= :hash_filename WHERE id_equip= :idToChange";
            $updateEquipmentSTMT = $db->prepare($updateEquipmentSQL);
            $updateEquipmentSTMT->bindParam(':idToChange', $idToChange, PDO::PARAM_INT);
            $updateEquipmentSTMT->bindParam(':real_filename', $realFilename, PDO::PARAM_INT);
            $updateEquipmentSTMT->bindParam(':hash_filename', $hashFilename, PDO::PARAM_INT);
            $updateEquipmentSTMT->execute();   
            header("Refresh:0; url=equipment.php");               
        } else {
            echo "Παρουσιάστηκε πρόβλημα κατά το ανέβασμα της εικόνας.";
        }
    }

        
include("views/footer.php");            	

?>
