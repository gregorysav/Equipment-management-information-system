<?php
session_start();
include("views/connection.php");
$zero = 0;
$one = 1;

    
if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "borrow") {


    if ($_SESSION['email']){

            $basketQuery = $db->prepare("INSERT INTO basket_svds (name_basket, id_equip_basket) 
    VALUES (:name_basket, :id_equip_basket)");

            $basketQuery->bindParam(':name_basket', $_POST['name_basket']);
            $basketQuery->bindParam(':id_equip_basket', $_POST['id_equip_basket']);
            $basketQuery->execute(); 

            $borrowQuery = $db->prepare("INSERT INTO borrow_svds (id_equip_borrow, isborrowed) 
    VALUES (:id_equip_borrow, :isborrowed)");

            $borrowQuery->bindParam(':id_equip_borrow', $_POST['id_equip_basket']);
            $borrowQuery->bindParam(':isborrowed', $zero);
            $borrowQuery->execute();
            
        } else {
            
             exit(); 
        }
}

if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "clear") {
         
         $idToDelete = $_POST['aem_borrow'];
         $deleteQuery = "DELETE FROM basket_svds";
         $deleteQuery_stmt = $db->prepare($deleteQuery);
         $deleteQuery_stmt->execute();
                 
         
     }

if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "confirm") {
         
         $idToChange = $_POST['id_to_confirm'];
         $borrowQuery = $db->prepare("UPDATE borrow_svds SET confirmation_borrow= :confirmation_borrow WHERE id_borrow= $idToChange");
            $borrowQuery->bindParam(':confirmation_borrow', $one);
            $borrowQuery->execute(); 
                 
         
     }     

?>     