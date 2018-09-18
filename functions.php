<?php
session_start();
include("views/connection.php");

    
if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "borrow") {


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

if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "clear") {
         
         $idToDelete = $_POST['aem_borrow'];
         $deleteQuery = "DELETE  FROM borrow_svds WHERE aem_borrow =  $idToDelete";
         $deleteQuery_stmt = $db->prepare($deleteQuery);
         $deleteQuery_stmt->bindParam(':aem_borrow', $idToDelete, PDO::PARAM_INT);
         $deleteQuery_stmt->execute();
                 
         
     }

?>     