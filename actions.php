<?php
//Access: Registered Users
include("variables_file.php");
include("checkUser.php");
include("views/connection.php");
include("function_cron.php");

//Συνάρτηση εισόδου τιμών στην βάση δεδομένων στους πίνακες basket_svds και borrow_svds με σκοπό την ολοκλήρωση της διαδικασίας δανεισμού 
if (isset($_GET) && ! empty($_GET) && $_GET["action"] == "basket") {

    $name_basket = filter_var($_POST['name_basket'],FILTER_SANITIZE_STRING);
    $id_user_basket = filter_var($_POST['id_user_basket'],FILTER_SANITIZE_NUMBER_FLOAT);
    $id_equip_basket = filter_var($_POST['id_equip_basket'],FILTER_SANITIZE_NUMBER_FLOAT);
    $basketQuerySQL = "INSERT INTO basket_svds (name_basket, id_equip_basket, id_user_basket) VALUES (:name_basket, :id_equip_basket, :id_user_basket)";
    $basketQuerySTMT = $db->prepare($basketQuerySQL);
    $basketQuerySTMT->bindParam(':name_basket', $name_basket);
    $basketQuerySTMT->bindParam(':id_equip_basket', $id_equip_basket, PDO::PARAM_INT);
    $basketQuerySTMT->bindParam(':id_user_basket', $id_user_basket, PDO::PARAM_INT);
    $basketQuerySTMT->execute();

    $borrowQuerySQL = "INSERT INTO borrow_svds (id_equip_borrow, isborrowed, id_user_borrow) VALUES (:id_equip_borrow, :isborrowed, :id_user_borrow)";
    $borrowQuerySTMT = $db->prepare($borrowQuerySQL);
    $borrowQuerySTMT->bindParam(':id_equip_borrow', $_POST['id_equip_basket'], PDO::PARAM_INT);
    $borrowQuerySTMT->bindParam(':isborrowed', $zero, PDO::PARAM_INT);
    $borrowQuerySTMT->bindParam(':id_user_borrow', $id_user_basket, PDO::PARAM_INT);
    $borrowQuerySTMT->execute(); 
 
}

//Συνάρτηση διαγραφής τιμών από τη βάση δεδομένων στους πίνακες basket_svds και borrow_svds με σκοπό την ολοκλήρωση της διαδικασίας καθαρισμού καλαθιού
if (isset($_GET) && ! empty($_GET) && $_GET["action"] == "clear") {
              
    $idUserToDelete = filter_var($_POST['id_user_basket'],FILTER_SANITIZE_NUMBER_FLOAT);
    $basketDeleteQuerySQL = "DELETE FROM basket_svds WHERE id_user_basket= :idUser";
    $basketDeleteQuerySTMT = $db->prepare($basketDeleteQuerySQL); 
    $basketDeleteQuerySTMT->bindParam(':idUser', $idUserToDelete, PDO::PARAM_INT);
    $basketDeleteQuerySTMT->execute(); 

    $borrowDeleteQuerySQL = "DELETE FROM borrow_svds WHERE id_user_borrow= :idUserToDelete AND isborrowed= :condition";
    $borrowDeleteQuerySTMT = $db->prepare($borrowDeleteQuerySQL);
    $borrowDeleteQuerySTMT->bindParam(':idUserToDelete', $idUserToDelete, PDO::PARAM_INT);
    $borrowDeleteQuerySTMT->bindParam(':condition', $zero, PDO::PARAM_INT); 
    $borrowDeleteQuerySTMT->execute(); 
    header("Refresh:0; url=basket.php"); 
    die("Δεν έχετε συνδεθεί");       
         
}

//Συνάρτηση εισόδου τιμών στην βάση δεδομένων στον πίνακα basket_svds με σκοπό την ολοκλήρωση της διαδικασίας επιβεβαίωσης δανεισμού
if (isset($_GET) && ! empty($_GET) && $_GET["action"] == "confirm") {
        
        $idToChange = filter_var($_POST['id_to_confirm'],FILTER_SANITIZE_NUMBER_FLOAT);

        $sendEmailAndSMSQuerySQL = "SELECT * FROM borrow_svds WHERE id_borrow= :idToChange";
        $sendEmailAndSMSQuerySTMT = $db->prepare($sendEmailAndSMSQuerySQL);
        $sendEmailAndSMSQuerySTMT->bindParam(':idToChange', $idToChange, PDO::PARAM_INT);
        if ($sendEmailAndSMSQuerySTMT->execute()) {    
            while ($sendEmailAndSMSQuerySTMTResult=$sendEmailAndSMSQuerySTMT->fetch(PDO::FETCH_ASSOC)){
                $equipmentToSendEmailAndSMSQuerySQL = "SELECT * FROM equip_svds WHERE id_equip= :idEquipement";
                $equipmentToSendEmailAndSMSQuerySTMT = $db->prepare($equipmentToSendEmailAndSMSQuerySQL);
                $equipmentToSendEmailAndSMSQuerySTMT->bindParam(':idEquipement', $sendEmailAndSMSQuerySTMTResult['id_equip_borrow'], PDO::PARAM_INT);
                if ($equipmentToSendEmailAndSMSQuerySTMT->execute()) {
                    while ($equipmentToSendEmailAndSMSQuerySTMTResult=$equipmentToSendEmailAndSMSQuerySTMT->fetch(PDO::FETCH_ASSOC)){
                        $equipmentName = $equipmentToSendEmailAndSMSQuerySTMTResult['name_e'];
                    }
                } 
                $userToSendEmailAndSMSQuerySQL = "SELECT * FROM users_svds WHERE id= :idBorrower";
                $userToSendEmailAndSMSQuerySTMT = $db->prepare($userToSendEmailAndSMSQuerySQL);
                $userToSendEmailAndSMSQuerySTMT->bindParam(':idBorrower', $sendEmailAndSMSQuerySTMTResult['id_user_borrow'], PDO::PARAM_INT);
                if ($userToSendEmailAndSMSQuerySTMT->execute()) {
                    while ($userToSendEmailAndSMSQuerySTMTResult=$userToSendEmailAndSMSQuerySTMT->fetch(PDO::FETCH_ASSOC)){
                        $aem_borrow = $userToSendEmailAndSMSQuerySTMTResult['aem']; 
                        $email = $userToSendEmailAndSMSQuerySTMTResult['email'];
                        $full_name = $userToSendEmailAndSMSQuerySTMTResult['last_name'].' '.$userToSendEmailAndSMSQuerySTMTResult['first_name'];     
                        $dateDiff = $sendEmailAndSMSQuerySTMTResult['notify10'];
                        $url = 'http://vlsi.gr/sms/webservice/process.php';;
                        //The data you want to send via POST
                        $fields = [
                            'authcode'  => 546743,
                            'method'  => 'POST',
                            'mobilenr' => $userToSendEmailAndSMSQuerySTMTResult['telephone'],
                            'message' => 'Αυτοματοποιημένο μήνυμα i-loan προς: [ '.$full_name.' ], AEM ['.$aem_borrow.'] η αίτηση δανεισμού για ['.$equipmentName.'] έχει επιβεβαιωθεί.'
                        ];

                        //url-ify the data for the POST
                        $fields_string = http_build_query($fields);
                    
                        //open connection
                        $ch = curl_init();

                        //set the url, number of POST vars, POST data
                        curl_setopt($ch,CURLOPT_URL, $url);
                        curl_setopt($ch,CURLOPT_POST, count($fields));
                        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
                    
                        //So that curl_exec returns the contents of the cURL; rather than echoing it
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

                        //execute post
                        $result = curl_exec($ch);
                        echo $result;
                
                        sendEmail($email, $full_name, $aem_borrow, $dateDiff, $equipmentName); 
                    }
                }
            }    
        }else {
            echo "Δεν μπόρεσα να βρώ τις πληροφορίες του χρήστη";
        }


        $borrowQuerySQL = "UPDATE borrow_svds SET confirmation_borrow= :confirmation_borrow WHERE id_borrow= :idToChange";
        $borrowQuerySTMT = $db->prepare($borrowQuerySQL);
        $borrowQuerySTMT->bindParam(':idToChange', $idToChange, PDO::PARAM_INT);    
        $borrowQuerySTMT->bindParam(':confirmation_borrow', $one);
        $borrowQuerySTMT->execute();         

}

//Συνάρτηση διαγραφής τιμών από την βάση δεδομένων στους πίνακες basket_svds και borrow_svds με σκοπό την ολοκλήρωση της διαδικασίας κατάργησης δανεισμού
if (isset($_GET) && ! empty($_GET) && $_GET["action"] == "removeFromBasket") {
         
        $idToDelete = filter_var($_GET['id_basket'],FILTER_SANITIZE_NUMBER_FLOAT);
        $idUserToDelete = filter_var($_GET['id_user_basket'],FILTER_SANITIZE_NUMBER_FLOAT);
        $idEquipToDelete = filter_var($_GET['id_equip_basket'],FILTER_SANITIZE_NUMBER_FLOAT);
        
        $borrowRemoveQuerySQL = "DELETE FROM borrow_svds WHERE id_user_borrow= :idUserToDelete AND isborrowed= :isBorrowed AND id_equip_borrow= :idEquipToDelete";
        $borrowRemoveQuerySTMT = $db->prepare($borrowRemoveQuerySQL);
        $borrowRemoveQuerySTMT->bindParam(':idUserToDelete', $idUserToDelete, PDO::PARAM_INT);
        $borrowRemoveQuerySTMT->bindParam(':isBorrowed', $zero, PDO::PARAM_INT); 
        $borrowRemoveQuerySTMT->bindParam(':idEquipToDelete', $idEquipToDelete, PDO::PARAM_INT); 
        $borrowRemoveQuerySTMT->execute();
        
        $basketRemoveQuerySQL = "DELETE FROM basket_svds WHERE id_basket= :idToDelete";
        $basketRemoveQuerySTMT = $db->prepare($basketRemoveQuerySQL);
        $basketRemoveQuerySTMT->bindParam(':idToDelete', $idToDelete, PDO::PARAM_INT); 
        $basketRemoveQuerySTMT->execute(); 

        
        header("Refresh:0; url=basket.php");
        die(); 
                
}

if (isset($_GET) && ! empty($_GET) && $_GET["action"] == "removeFromFinish") {
         
        $idToDelete = filter_var($_GET['id_basket'],FILTER_SANITIZE_NUMBER_FLOAT);
        $idUserToDelete = $_GET['id_user_basket'];
        $idEquipToDelete = filter_var($_GET['id_equip_basket'],FILTER_SANITIZE_NUMBER_FLOAT);
        $basketRemoveQuerySQL = "DELETE FROM basket_svds WHERE id_basket= :idToDelete";
        $basketRemoveQuerySTMT = $db->prepare($basketRemoveQuerySQL);
        $basketRemoveQuerySTMT->bindParam(':idToDelete', $idToDelete, PDO::PARAM_INT); 
        $basketRemoveQuerySTMT->execute(); 

        $borrowRemoveQuerySQL = "DELETE FROM borrow_svds WHERE id_user_borrow= :idUserToDelete AND id_equip_borrow= :idEquipToDelete AND isborrowed= :isBorrowed";
        $borrowRemoveQuerySTMT = $db->prepare($borrowRemoveQuerySQL);
        $borrowRemoveQuerySTMT->bindParam(':idUserToDelete', $idUserToDelete, PDO::PARAM_INT);
        $borrowRemoveQuerySTMT->bindParam(':idEquipToDelete', $idEquipToDelete, PDO::PARAM_INT); 
        $borrowRemoveQuerySTMT->bindParam(':isBorrowed', $zero, PDO::PARAM_INT); 
        $borrowRemoveQuerySTMT->execute();
        header("Refresh:0; url=finish.php");
        die(); 
                
}   

if (isset($_GET) && ! empty($_GET) && $_GET["action"] == "imageDelete") {
         
        $idToDelete = $_GET['id_equip'];
        $filenameToDelete = $_GET['image_name'];
        $imageRemoveQuery = $db->prepare("UPDATE equip_svds SET real_filename= :real_filename, hash_filename= :hash_filename WHERE id_equip= :idToDelete");
        $imageRemoveQuery->bindParam(':idToDelete', $idToDelete, PDO::PARAM_INT);
        $imageRemoveQuery->bindParam(':real_filename', $noImageToDisplay, PDO::PARAM_INT);
        $imageRemoveQuery->bindParam(':hash_filename', $zero, PDO::PARAM_INT); 
        $imageRemoveQuery->execute();
        if ($imageRemoveQuery->rowCount() > 0 ){
            $_SESSION['imageDeleteInformMessage']= '<p class="alert alert-info">Η εικόνα διαγράφηκε επιτυχώς.</p>';
        } else {
            $_SESSION['imageDeleteInformMessage']= '<p class="alert alert-info">Δεν βρέθηκε εικόνα για να διαγραφεί.</p>';
        } 
        $pathOfFile= 'uploadedImages/'.$filenameToDelete.'';
        unlink($pathOfFile);
  
}

if (isset($_GET) && ! empty($_GET) && $_GET["action"] == "nameQuery") {
        $search = filter_var($_POST['query'],FILTER_SANITIZE_STRING);
        $searchQuerySQL = "SELECT DISTINCT name_e FROM equip_svds WHERE name_e LIKE '%$search%'";  
        $searchQuerySTMT = $db->prepare($searchQuerySQL);
        $searchQuerySTMT->execute();
            
        $data=array();
        while ($searchQuerySTMTResult=$searchQuerySTMT->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $searchQuerySTMTResult['name_e'];
        }           
        echo json_encode($data);
  
}

if (isset($_GET) && ! empty($_GET) && $_GET["action"] == "dateQuery") {
        $search = filter_var($_POST['query'],FILTER_SANITIZE_STRING);
        $searchQuerySQL = "SELECT DISTINCT buy_year_e FROM equip_svds WHERE buy_year_e LIKE '$search%'";  
        $searchQuerySTMT = $db->prepare($searchQuerySQL);
        $searchQuerySTMT->execute();
            
        $data=array();
        while ($searchQuerySTMTResult=$searchQuerySTMT->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $searchQuerySTMTResult['buy_year_e'];
        }           
        echo json_encode($data);
  
}

if (isset($_GET) && ! empty($_GET) && $_GET["action"] == "locationQuery") {
        $search = filter_var($_POST['query'],FILTER_SANITIZE_STRING);
        $searchQuerySQL = "SELECT DISTINCT location_e FROM equip_svds WHERE location_e LIKE '%$search%'";  
        $searchQuerySTMT = $db->prepare($searchQuerySQL);
        $searchQuerySTMT->execute();
            
        $data=array();
        while ($searchQuerySTMTResult=$searchQuerySTMT->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $searchQuerySTMTResult['location_e'];
        }           
        echo json_encode($data);
  
}

if (isset($_GET) && ! empty($_GET) && $_GET["action"] == "AEMQuery") {
        $search = filter_var($_POST['query'],FILTER_SANITIZE_STRING);
        $searchQuerySQL = "SELECT * FROM users_svds WHERE aem LIKE '%$search%' OR first_name LIKE '%$search%' OR last_name LIKE '%$search%'";  
        $searchQuerySTMT = $db->prepare($searchQuerySQL);
        $searchQuerySTMT->execute();
            
        $data= '<ul class="list-unstyled">';
        if ($searchQuerySTMT->rowCount() > 0){
            while ($searchQuerySTMTResult=$searchQuerySTMT->fetch(PDO::FETCH_ASSOC)) {
            $data .= '<li>'.$searchQuerySTMTResult['last_name'].' '. $searchQuerySTMTResult['first_name'].' '.$searchQuerySTMTResult['aem']. '</li>';
           }           
        }else {
            $data .= '<li>Δεν βρέθηκαν χρήστες με αυτό το ΑΕΜ</li>';
        }  
        $data .= '</ul>';
        echo $data;
}

?>