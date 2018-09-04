<?php
session_start();
include("views/connection.php");

    
if ($_GET["function"] == "borrow") {

	echo $_POST["aem_borrow"];

    if ($_SESSION['email']){

                    $borrowQuery = $db->prepare("INSERT INTO borrow_svds (aem_borrow, id_equip_borrow) 
    VALUES (:aem_borrow, :id_equip_borrow)");

            $borrowQuery->bindParam(':aem_borrow', $_POST['aem_borrow']);
            $borrowQuery->bindParam(':id_equip_borrow', $_POST['id_equip_borrow']);
            $borrowQuery->execute(); 

            
        } else {
            
             exit(); 
        }
}





?>     