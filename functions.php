<?php
//Access: Registered Users
include("variables_file.php");
include("views/connection.php");
include("function_cron.php");

//Συνάρτηση εισόδου τιμών στην βάση δεδομένων στους πίνακες basket_svds και borrow_svds με σκοπό την ολοκλήρωση της διαδικασίας δανεισμού 
if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "basket") {

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
if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "clear") {
              
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
if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "confirm") {
        
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
if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "removeFromBasket") {
         
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

if (isset($_GET) && ! empty($_GET) && $_GET["function"] == "removeFromFinish") {
         
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
        $searchQuerySQL = "SELECT DISTINCT name_e FROM equip_svds WHERE name_e LIKE '%$search%'";  
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
        $searchQuerySQL = "SELECT DISTINCT buy_year_e FROM equip_svds WHERE buy_year_e LIKE '$search%'";  
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
        $searchQuerySQL = "SELECT DISTINCT location_e FROM equip_svds WHERE location_e LIKE '%$search%'";  
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


function PDFPrint ($fullName, $aem_borrow, $type, $itemsToPrint, $borrowReason, $startDate, $endDate){
    include("views/connection.php");
    date_default_timezone_set('Europe/Athens');
    setlocale(LC_TIME, 'el_GR.UTF-8');
    $day = date("w");
    $greekDays = array( "Κυριακή", "Δευτέρα", "Τρίτη", "Τετάρτη", "Πέμπτη", "Παρασκευή", "Σάββατο" ); 
    $greekMonths = array('Ιανουαρίου','Φεβρουαρίου','Μαρτίου','Απριλίου','Μαΐου','Ιουνίου','Ιουλίου','Αυγούστου','Σεπτεμβρίου','Οκτωβρίου','Νοεμβρίου','Δεκεμβρίου');
    $greekDate = $greekDays[$day] . ', ' . date('j') . ' ' . $greekMonths[intval(date('m'))-1] . ' ' . date('Y');
    $nowDate =  $greekDate;


    if ($type == 0 OR $type > 3){
        $teacherThatProvide = "Δασυγένης Μηνάς Μέλος ΔΕΠ";
    }elseif ( $type ==1 OR $type == 2 OR $type == 3){
        switch ($type) {
            case 1:
                $typeToprint= "Διαχειριστής";
                break;
            case 2:
                $typeToprint= "Μέλος ΔΕΠ";
                break;
            case 3:
                $typeToprint= "Μέλος Ε.Ε.ΔΙ.Π";
                break;
            case 4:
                $typeToprint= "Μέλος ΕΤΕΠ";
                break;
            case 5:
                $typeToprint= "Διοικητικό Προσωπικό";
                break;        
        }         
        $searchQuerySQL = "SELECT * FROM users_svds WHERE type= :typeToSearch";  
        $searchQuerySTMT = $db->prepare($searchQuerySQL);
        $searchQuerySTMT->bindParam(':typeToSearch', $type, PDO::PARAM_INT);
        $searchQuerySTMT->execute();
        while ($searchQuerySTMTResult=$searchQuerySTMT->fetch(PDO::FETCH_ASSOC)) {
            $teacherThatProvide = $searchQuerySTMTResult['last_name'].' '.$searchQuerySTMTResult['first_name'].' '.$typeToprint;
        }          
    }    
    require_once('tcpdf/tcpdf.php');

    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        require_once(dirname(__FILE__).'/lang/eng.php');
        $pdf->setLanguageArray($l);
    }


    $pdf->SetFont('freesans', 'BI', 16);
    $pdf->setFont('freeserif');

    $pdf->AddPage();
    $pdf->Image('images/uowmlogo.jpg', 10, 10, 70, '', 'JPG', '', 'T', false, 300, 'L', false, false, 0,     false, false, false);
    $pdf->Cell(0, 10, $nowDate, 0, false, 'R', 0, '', 3, false, 'T', 'M');
    $pdf->Multicell(0,45,"");
    $pdf->SetFont('freesans','U');
    $pdf->Cell(0, 0, 'ΕΝΤΥΠΟ ΧΡΕΩΣΗΣ ΗΛΕΚΤΡΟΝΙΚΟΥ ΕΞΟΠΛΙΣΜΟΥ', 0, 0, 'C', 0, '', 0);
    $pdf->SetFont('freesans','',12);
    $pdf->Multicell(0,35,"");
    $html = "
    Ο $teacherThatProvide, ΤΜΠΤ παραδίδω στο(ν) $fullName (ΑΕΜ $aem_borrow) φοιτητή του ΠΔΜ, τον παρακάτω εξοπλισμό: 
    ";
    
    $html .= " $itemsToPrint <br>
    Ο παραπάνω εξοπλισμός θα χρησιμοποιηθεί με σκοπό: $borrowReason";

    $html .=" <br><br>για χρονικό διάστημα από $startDate  μέχρι $endDate </p>";
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Multicell(0,25,"");
    $pdf->Cell(0, 10, 'Ο ΠΑΡΑΔΙΔΩΝ', 0, false, 'L', 0, '', 0, false, 'T', 'M');
    $pdf->Cell(0, 10, 'Ο ΠΑΡΑΛΑΒΩΝ', 0, false, 'R', 0, '', 0, false, 'T', 'M');     

    ob_end_clean(); 
    $pdf->Output(); 
}

function checkQuantity() {
    include("views/connection.php");
    $basketQuerySQL = "SELECT COUNT(id_equip_basket), name_basket FROM basket_svds GROUP BY name_basket"; 
    $basketQuerySTMT = $db->prepare($basketQuerySQL);
    $basketQuerySTMT->execute();
    $_SESSION['message'] = "";
    while ($basketQuerySTMTResult=$basketQuerySTMT->fetch(PDO::FETCH_ASSOC)){
        $updateQuantitySQL = "SELECT quantity FROM equip_svds WHERE name_e= :nameToCheck";
        $updateQuantitySTMT = $db->prepare($updateQuantitySQL);
        $updateQuantitySTMT->bindParam(':nameToCheck', $basketQuerySTMTResult['name_basket'], PDO::PARAM_INT);
        $updateQuantitySTMT->execute();
        while ($updateQuantitySTMTResult=$updateQuantitySTMT->fetch(PDO::FETCH_ASSOC)){
            
            if ($updateQuantitySTMTResult['quantity'] < $basketQuerySTMTResult['COUNT(id_equip_basket)']){
                $_SESSION['message'] .= '<p class="alert alert-warning">Έχετε ζητήσει '.$basketQuerySTMTResult['COUNT(id_equip_basket)'].' '.$basketQuerySTMTResult['name_basket'].' ενώ το απόθεμα είναι '.$updateQuantitySTMTResult['quantity'].'</p>'; 
            }
            
        }
    }
     

}
?>     

