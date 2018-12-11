<?php
include("variables_file.php");
include("views/connection.php");


//Συνάρτηση εισόδου τιμών στην βάση δεδομένων στους πίνακες basket_svds και borrow_svds με σκοπό την ολοκλήρωση της διαδικασίας δανεισμού 
if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "basket") {

        $name_basket = filter_var($_POST['name_basket'],FILTER_SANITIZE_STRING);

        $basketQuerySQL = "INSERT INTO basket_svds (name_basket, id_equip_basket, id_user_basket) 
    VALUES (:name_basket, :id_equip_basket, :id_user_basket)";
        $basketQuerySTMT = $db->prepare($basketQuerySQL);
        $basketQuerySTMT->bindParam(':name_basket', $name_basket);
        $basketQuerySTMT->bindParam(':id_equip_basket', $_POST['id_equip_basket']);
        $basketQuerySTMT->bindParam(':id_user_basket', $_POST['id_user_basket']);
        $basketQuerySTMT->execute();

        $borrowQuerySQL = "INSERT INTO borrow_svds (id_equip_borrow, isborrowed, aem_borrow) 
    VALUES (:id_equip_borrow, :isborrowed, :aem_borrow)";
        $borrowQuerySTMT = $db->prepare($borrowQuerySQL);
        $borrowQuerySTMT->bindParam(':id_equip_borrow', $_POST['id_equip_basket']);
        $borrowQuerySTMT->bindParam(':isborrowed', $zero);
        $borrowQuerySTMT->bindParam(':aem_borrow', $_POST['id_user_basket']);
        $borrowQuerySTMT->execute(); 
 
}

if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "basketUpdate") {


    $borrowQuerySQL = $db->prepare("UPDATE borrow_svds SET aem_borrow= :aem_borrow WHERE aem_borrow= :idToChange");
    $borrowQuerySTMT = $db->prepare($borrowQuerySQL);
    $borrowQuerySTMT->bindParam(':idToChange', $two);
    $borrowQuerySTMT->bindParam(':aem_borrow', $_POST['aem_borrow']);
    $borrowQuerySTMT->execute(); 
 
}

//Συνάρτηση διαγραφής τιμών από τη βάση δεδομένων στους πίνακες basket_svds και borrow_svds με σκοπό την ολοκλήρωση της διαδικασίας καθαρισμού καλαθιού
if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "clear") {
         
        
        $idUserToDelete = filter_var($_POST['id_user_basket'],FILTER_SANITIZE_NUMBER_FLOAT);
        $basketDeleteQuerySQL = "DELETE FROM basket_svds WHERE id_user_basket= :idUser";
        $basketDeleteQuerySTMT = $db->prepare($basketDeleteQuerySQL); 
        $basketDeleteQuerySTMT->bindParam(':idUser', $_SESSION['id'], PDO::PARAM_INT);
        $basketDeleteQuerySTMT->execute(); 

        $borrowDeleteQuerySQL = "DELETE FROM borrow_svds WHERE aem_borrow= :idUserToDelete AND isborrowed= :condition";
        $borrowDeleteQuerySTMT = $db->prepare($borrowDeleteQuerySQL);
        $borrowDeleteQuerySTMT->bindParam(':idUserToDelete', $idUserToDelete, PDO::PARAM_INT);
        $borrowDeleteQuerySTMT->bindParam(':condition', $zero, PDO::PARAM_INT); 
        $borrowDeleteQuerySTMT->execute(); 
        header("Refresh:0; url=basket.php"); 
        die("Δεν έχετε συνδεθεί");       
         
}

//Συνάρτηση εισόδου τιμών στην βάση δεδομένων στον πίνακα basket_svds με σκοπό την ολοκλήρωση της διαδικασίας επιβεβαίωσης δανεισμού
if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "confirm") {
         
        $idToChange = filter_var($_POST['id_to_confirm'],FILTER_SANITIZE_NUMBER_FLOAT);
        $borrowQuerySQL = "UPDATE borrow_svds SET confirmation_borrow= :confirmation_borrow WHERE id_borrow= :idToChange";
        $borrowQuerySTMT = $db->prepare($borrowQuerySQL);
        $borrowQuerySTMT->bindParam(':idToChange', $idToChange, PDO::PARAM_INT);	
        $borrowQuerySTMT->bindParam(':confirmation_borrow', $one);
        $borrowQuerySTMT->execute();         
}

//Συνάρτηση διαγραφής τιμών από την βάση δεδομένων στους πίνακες basket_svds και borrow_svds με σκοπό την ολοκλήρωση της διαδικασίας κατάργησης δανεισμού
if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "remove") {
         
        $idToDelete = filter_var($_GET['id_basket'],FILTER_SANITIZE_NUMBER_FLOAT);
        $idUserToDelete = $_GET['id_user_basket'];
        $basketRemoveQuerySQL = "DELETE FROM basket_svds WHERE id_basket= :idToDelete";
        $basketRemoveQuerySTMT = $db->prepare($basketRemoveQuerySQL);
        $basketRemoveQuerySTMT->bindParam(':idToDelete', $idToDelete, PDO::PARAM_INT); 
        $basketRemoveQuerySTMT->execute(); 

        $borrowRemoveQuerySQL = "DELETE FROM borrow_svds WHERE aem_borrow= :idUserToDelete";
        $borrowRemoveQuerySTMT = $db->prepare($borrowRemoveQuerySQL);
        $borrowRemoveQuerySTMT->bindParam(':idUserToDelete', $idUserToDelete, PDO::PARAM_INT); 
        $borrowRemoveQuerySTMT->execute();
        header("Refresh:0; url=basket.php");
        die("Δεν έχετε συνδεθεί"); 
                
}   

if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "imageDelete") {
         
        $idToDelete = $_GET['id_equip'];
        $filenameToDelete = $_GET['image_name'];
        $imageRemoveQuery = $db->prepare("UPDATE equip_svds SET real_filename= :real_filename, hash_filename= :hash_filename WHERE id_equip= :idToDelete");
        $imageRemoveQuery->bindParam(':idToDelete', $idToDelete, PDO::PARAM_INT);
        $imageRemoveQuery->bindParam(':real_filename', $noImageToDisplay, PDO::PARAM_INT);
        $imageRemoveQuery->bindParam(':hash_filename', $zero, PDO::PARAM_INT); 
        $imageRemoveQuery->execute(); 
        unlink("uploadedImages/$filenameToDelete");
  
}

if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "nameQuery") {
        $search = filter_var($_POST['query'],FILTER_SANITIZE_STRING);
        $searchQuerySQL = "SELECT * FROM equip_svds WHERE name_e LIKE '$search%'";  
        $searchQuerySTMT = $db->prepare($searchQuerySQL);
        $searchQuerySTMT->execute();
            
        $data=array();
        while ($searchQuerySTMTResult=$searchQuerySTMT->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $searchQuerySTMTResult['name_e'];
        }           
        echo json_encode($data);
  
}

if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "dateQuery") {
        $search = filter_var($_POST['query'],FILTER_SANITIZE_STRING);
        $searchQuerySQL = "SELECT * FROM equip_svds WHERE buy_year_e LIKE '$search%'";  
        $searchQuerySTMT = $db->prepare($searchQuerySQL);
        $searchQuerySTMT->execute();
            
        $data=array();
        while ($searchQuerySTMTResult=$searchQuerySTMT->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $searchQuerySTMTResult['buy_year_e'];
        }           
        echo json_encode($data);
  
}

if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "locationQuery") {
        $search = filter_var($_POST['query'],FILTER_SANITIZE_STRING);
        $searchQuerySQL = "SELECT * FROM equip_svds WHERE location_e LIKE '$search%'";  
        $searchQuerySTMT = $db->prepare($searchQuerySQL);
        $searchQuerySTMT->execute();
            
        $data=array();
        while ($searchQuerySTMTResult=$searchQuerySTMT->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $searchQuerySTMTResult['location_e'];
        }           
        echo json_encode($data);
  
}

if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "AEMQuery") {
        $search = filter_var($_POST['query'],FILTER_SANITIZE_STRING);
        $searchQuerySQL = "SELECT * FROM users_svds WHERE aem LIKE '$search%'";  
        $searchQuerySTMT = $db->prepare($searchQuerySQL);
        $searchQuerySTMT->execute();
            
        $data= array();
        while ($searchQuerySTMTResult=$searchQuerySTMT->fetch(PDO::FETCH_ASSOC)) {
            $data[]= $searchQuerySTMTResult['last_name'].' '. $searchQuerySTMTResult['first_name'].' '.$searchQuerySTMTResult['aem'];
        }           
        echo json_encode($data);
  
}

if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "saveComment") {
         
        
    $idEquipToSave = filter_var($_POST['id_equip'],FILTER_SANITIZE_NUMBER_FLOAT);
    $answerComment = filter_var($_POST['answerComment'],FILTER_SANITIZE_STRING);
    $commentSaveQuerySQL = "INSERT INTO comments_svds (id_equip_com, id_user_com, comments, date_com) 
    VALUES (:id_equip_com, :id_user_com, :comments, NOW())";
    $commentSaveQuerySTMT = $db->prepare($commentSaveQuerySQL);
    $commentSaveQuerySTMT->bindParam(':id_equip_com', $idEquipToSave, PDO::PARAM_INT);
    $commentSaveQuerySTMT->bindParam(':id_user_com', $aem, PDO::PARAM_INT);
    $commentSaveQuerySTMT->bindParam(':comments', $answerComment, PDO::PARAM_INT); 
    $commentSaveQuerySTMT->execute(); 
    header("Refresh:0; url=backend.php"); 
    die("Δεν έχετε συνδεθεί");       
         
}

 

?>     