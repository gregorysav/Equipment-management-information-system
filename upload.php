<?php
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");


            $idToChange=$_GET['id_equip'];
            $target_dir = "uploadedImages/";
            $target_file = $target_dir . basename($_FILES["filename"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            if(isset($_POST["add"])) {
                $check = getimagesize($_FILES["filename"]["tmp_name"]);
                if($check !== false) {
                    $uploadOk = 1;
                } else {
                    $uploadOk = 0;
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
            }
            if ($uploadOk == 0) {
                echo "Λυπούμαστε, το αρχείο δεν έχει ανέβει επιτυχώς.";
            } else {
                if (move_uploaded_file($_FILES["filename"]["tmp_name"], $target_file)) {
                    echo "Η εικόνα ανέβηκε με επιτυχία";
                    $realFilename = basename( $_FILES["filename"]["name"]);
                    echo '
                        <button id="updateModify" name ="updateModify" type="submit"><a href=equipment_modify.php?id_equip='.$idToChange.'&file='.$realFilename.'>Τροποποίηση</a></button>     
                    
                    ';                    
                } else {
                    echo "Παρουσιάστηκε πρόβλημα κατά το ανέβασμα της εικόνας.";
                }
            }

        
include("views/footer.php");            	

?>
