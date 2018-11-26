<?php
include("variables_file.php");
include("views/connection.php");


//Συνάρτηση εισόδου τιμών στην βάση δεδομένων στους πίνακες basket_svds και borrow_svds με σκοπό την ολοκλήρωση της διαδικασίας δανεισμού 
if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "basket") {

        $basketQuerySQL = "INSERT INTO basket_svds (name_basket, id_equip_basket, id_user_basket) 
    VALUES (:name_basket, :id_equip_basket, :id_user_basket)";
        $basketQuerySTMT = $db->prepare($basketQuerySQL);
        $basketQuerySTMT->bindParam(':name_basket', $_POST['name_basket']);
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

//Συνάρτηση διαγραφής τιμών από τη βάση δεδομένων στους πίνακες basket_svds και borrow_svds με σκοπό την ολοκλήρωση της διαδικασίας καθαρισμού καλαθιού
if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "clear") {
         
        
        $idUserToDelete = $_POST['id_user_basket'];
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
         
}

//Συνάρτηση εισόδου τιμών στην βάση δεδομένων στον πίνακα basket_svds με σκοπό την ολοκλήρωση της διαδικασίας επιβεβαίωσης δανεισμού
if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "confirm") {
         
        $idToChange = $_POST['id_to_confirm'];
        $borrowQuerySQL = "UPDATE borrow_svds SET confirmation_borrow= :confirmation_borrow WHERE id_borrow= :idToChange";
        $borrowQuerySTMT = $db->prepare($borrowQuerySQL);
        $borrowQuerySTMT->bindParam(':idToChange', $idToChange, PDO::PARAM_INT);	
        $borrowQuerySTMT->bindParam(':confirmation_borrow', $one);
        $borrowQuerySTMT->execute();         
}

//Συνάρτηση διαγραφής τιμών από την βάση δεδομένων στους πίνακες basket_svds και borrow_svds με σκοπό την ολοκλήρωση της διαδικασίας κατάργησης δανεισμού
if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "remove") {
         
        $idToDelete = $_GET['id_basket'];
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
        $search =$_POST['query'];
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
        $search =$_POST['query'];
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
        $search =$_POST['query'];
        $searchQuerySQL = "SELECT * FROM equip_svds WHERE location_e LIKE '$search%'";  
        $searchQuerySTMT = $db->prepare($searchQuerySQL);
        $searchQuerySTMT->execute();
            
        $data=array();
        while ($searchQuerySTMTResult=$searchQuerySTMT->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $searchQuerySTMTResult['location_e'];
        }           
        echo json_encode($data);
  
}

//Συνάρτηση εμφάνισης ενημερωτικού μηνύματος κατά την ολοκλήρωση της σύμβασης δανεισμού 
function PDFPrint($array, $startDate, $endDate){ 
	// Η συνάρτηση δέχεται 3 μεταβητές. Η πρώτη είναι τύπου array και περιέχει τα εξαρτήματα που περιαμβάνει ο δανεισμός. Η δεύτερη είναι τύπου date και περιέχει την ημερομηνία έναρξης του δανεισμού. Η τρίτη είναι τύπου date και περιέχει την ημερομηνία ολοκήρωσης του δανεισμού 
    echo '
        Ο Μηνάς Δασυγένης, <strong>Μέλος ΔΕΠ</strong>, ΤΜΠΤ παραδίδω στο(ν) '.$_SESSION['last_name'].' '.$_SESSION['first_name'].' (AEM = '.$_SESSION['aem'].') φοιτητή του ΤΜΠΤ, τον παρακάτω εξοπλισμό:
    '; 
    foreach ($array as $var) {
            echo'
            
                <li>'.$var.'</li>
                
            ';
    }   
    echo '    
        Ο παραπάνω εξοπλισμός θα χρησιμοποιηθεί στα πλαίσια της εκπόνησης εργασίας για τα μαθήματα που επιβλέπει ο παραδίδων για χρονικό διάστημα από '.$startDate.' μέχρι '.$endDate.'.

    ';  
    // Η συνάρτηση δεν επιστρέφει κάποια τιμή αλλά εμφανίζει ενημερωτικό μήνυμα
}

 

?>     