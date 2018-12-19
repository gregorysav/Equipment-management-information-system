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

        $borrowRemoveQuerySQL = "DELETE FROM borrow_svds WHERE aem_borrow= :idUserToDelete AND isborrowed= :isBorrowed";
        $borrowRemoveQuerySTMT = $db->prepare($borrowRemoveQuerySQL);
        $borrowRemoveQuerySTMT->bindParam(':idUserToDelete', $idUserToDelete, PDO::PARAM_INT);
        $borrowRemoveQuerySTMT->bindParam(':isBorrowed', $zero, PDO::PARAM_INT); 
        $borrowRemoveQuerySTMT->execute();
        header("Refresh:0; url=basket.php");
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
        $searchQuerySQL = "SELECT * FROM users_svds WHERE aem LIKE '$search%' OR first_name LIKE '$search%' OR last_name LIKE '$search%'";  
        $searchQuerySTMT = $db->prepare($searchQuerySQL);
        $searchQuerySTMT->execute();
            
        $data= '<ul class="list-unstyled">';
        if ($searchQuerySTMT->rowCount() > 0){
            while ($searchQuerySTMTResult=$searchQuerySTMT->fetch(PDO::FETCH_ASSOC)) {
            $data .= '<li>'.$searchQuerySTMTResult['last_name'].' '. $searchQuerySTMTResult['first_name'].' '.$searchQuerySTMTResult['aem']. '</li>';
           }           
        }else {
            $data .= '<li> Δεν βρέθηκαν χρήστες με αυτό το ΑΕΜ</li>';
        }  
        $data .= '</ul>';
        echo $data;
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

function PDFPrint ($fullName, $aem_borrow, $itemsToPrint, $borrowReason, $startDate, $endDate){
    date_default_timezone_set('Europe/Athens');
    setlocale(LC_TIME, 'el_GR.UTF-8');
    $day = date("w");
    $greekDays = array( "Κυριακή", "Δευτέρα", "Τρίτη", "Τετάρτη", "Πέμπτη", "Παρασκευή", "Σάββατο" ); 
    $greekMonths = array('Ιανουαρίου','Φεβρουαρίου','Μαρτίου','Απριλίου','Μαΐου','Ιουνίου','Ιουλίου','Αυγούστου','Σεπτεμβρίου','Οκτωβρίου','Νοεμβρίου','Δεκεμβρίου');
    $greekDate = $greekDays[$day] . ', ' . date('j') . ' ' . $greekMonths[intval(date('m'))-1] . ' ' . date('Y');
    $nowDate =  $greekDate;
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
    Ο Μηνάς Δασυγένης, Μέλος ΔΕΠ, ΤΜΠΤ παραδίδω στο(ν) $fullName (ΑΕΜ $aem_borrow) φοιτητή του ΤΜΠΤ, τον παρακάτω εξοπλισμό: 
    ";
    
    $html .= " $itemsToPrint
    Ο παραπάνω εξοπλισμός θα χρησιμοποιηθεί με σκοπό : $borrowReason";

    $html .=" <br><br>για χρονικό διάστημα από $startDate  μέχρι $endDate </p>";
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Multicell(0,25,"");
    $pdf->Cell(0, 10, 'Ο ΠΑΡΑΔΙΔΩΝ', 0, false, 'L', 0, '', 0, false, 'T', 'M');
    $pdf->Cell(0, 10, 'Ο ΠΑΡΑΛΑΒΩΝ', 0, false, 'R', 0, '', 0, false, 'T', 'M');     

    ob_end_clean(); 
    $pdf->Output(); 
}

function adminDisplayInformation($id_borrow, $aem_borrow, $email, $expire_date) {
    include("views/connection.php");
    $send = -1;
    $nowDate =  strtotime(date("d-m-Y"));
    $dateToCheck = strtotime(date('d-m-Y',strtotime($expire_date)));
    $dateDiff = ($dateToCheck - $nowDate) / 86400;
    echo '<ul>';
    if ($dateDiff > 31){
        echo '<li><p>-Δανεισμός : '.$id_borrow.' (Φοιτητής ΑΕΜ: '.$aem_borrow.')</p>  
              <p>-ΟΚ δεν χρειάζεται να στείλω ειδοποίηση λήγει σε '.$dateDiff.' μέρες </p></li>              
        ';
    } else {
        if ($dateDiff == 30){
            echo '<li><p>-Δανεισμός : '.$id_borrow.' στέλνω email ειδοποίησης στον φοιτητή με ΑΕΜ: '.$aem_borrow.'</p>    
                  <p>-Στέλνω email στο '.$email.' γιατί ο δανεισμός με ID = '.$id_borrow.' λήγει σε 30 ημέρες.</p></li>               
            ';  
            $to = $email;
            $subject = "Λήξη Δανεισμού";
            $txt = 'Ο δανεισμός που έχεις κάνει λήγει σε '.$dateDiff.' ημέρες.';
            $headers = "From: webmaster@example.com" . "\r\n" .
            "CC: somebodyelse@example.com";
            mail($to,$subject,$txt,$headers);
            if( mail($to,$subject,$txt,$headers)){
               echo "Το email στάλθηκε με επιτυχία";
            }else{
               echo "Το email δεν στάλθηκε με επιτυχία γιατί :" .$mail->ErrorInfo;
            }
            
            $changeDateSQL = "UPDATE borrow_svds SET notify30= :notify30 WHERE id_borrow= :idToChange";
            $changeDateSTMT = $db->prepare($changeDateSQL);
            $changeDateSTMT->bindParam(':idToChange', $id_borrow);
            $changeDateSTMT->bindParam(':notify30', $send);
            $changeDateSTMT->execute();
        }   
        if($dateDiff == 20){
            echo '<li><p>-Δανεισμός : '.$id_borrow.' στέλνω email ειδοποίησης στον φοιτητή με ΑΕΜ: '.$aem_borrow.'</p>    
                  <p>-Στέλνω email στο '.$email.' γιατί ο δανεισμός με ID = '.$id_borrow.' λήγει σε 20 ημέρες.</p></li>               
            ';  
            $to = $email;
            $subject = "Λήξη Δανεισμού";
            $txt = 'Ο δανεισμός που έχεις κάνει λήγει σε '.$dateDiff.' ημέρες.';
            $headers = "From: webmaster@example.com" . "\r\n" .
            "CC: somebodyelse@example.com";
            mail($to,$subject,$txt,$headers);
            if( mail($to,$subject,$txt,$headers)){
               echo "Το email στάλθηκε με επιτυχία";
            }else{
               echo "Το email δεν στάλθηκε με επιτυχία γιατί :" .$mail->ErrorInfo;
            }

            $changeDateSQL = "UPDATE borrow_svds SET notify30= :notify30, notify20= :notify20 WHERE id_borrow= :idToChange";
            $changeDateSTMT = $db->prepare($changeDateSQL);
            $changeDateSTMT->bindParam(':idToChange', $id_borrow);
            $changeDateSTMT->bindParam(':notify30', $send);
            $changeDateSTMT->bindParam(':notify20', $send);
            $changeDateSTMT->execute();
        }   
        if($dateDiff == 10){
            echo '<li><p>-Δανεισμός : '.$id_borrow.' στέλνω email ειδοποίησης στον φοιτητή με ΑΕΜ: '.$aem_borrow.'</p>    
                  <p>-Στέλνω email στο '.$email.' γιατί ο δανεισμός με ID = '.$id_borrow.' λήγει σε 10 ημέρες.</p></li>               
            ';  
            $to = $email;
            $subject = "Λήξη Δανεισμού";
            $txt = 'Ο δανεισμός που έχεις κάνει λήγει σε '.$dateDiff.' ημέρες.';
            $headers = "From: webmaster@example.com" . "\r\n" .
            "CC: somebodyelse@example.com";
            mail($to,$subject,$txt,$headers);
            if( mail($to,$subject,$txt,$headers)){
               echo "Το email στάλθηκε με επιτυχία";
            }else{
               echo "Το email δεν στάλθηκε με επιτυχία γιατί :" .$mail->ErrorInfo;
            }
            
            $changeDateSQL = "UPDATE borrow_svds SET notify30= :notify30, notify20= :notify20, notify10= :notify10 WHERE id_borrow= :idToChange";
            $changeDateSTMT = $db->prepare($changeDateSQL);
            $changeDateSTMT->bindParam(':idToChange', $id_borrow);
            $changeDateSTMT->bindParam(':notify30', $send);
            $changeDateSTMT->bindParam(':notify20', $send);
            $changeDateSTMT->bindParam(':notify10', $send);
            $changeDateSTMT->execute();
        }
        if($dateDiff == 1 || $dateDiff == 0 ){
            
            echo '<li><p>-Δανεισμός : '.$id_borrow.' στέλνω email ειδοποίησης στον φοιτητή με ΑΕΜ: '.$aem_borrow.'</p>    
                  <p>-Στέλνω email στο '.$email.' γιατί ο δανεισμός με ID = '.$id_borrow.' λήγει σήμερα στις '.date('d/m/Y',strtotime($expire_date)).'.</p></li>              
            ';

            $to = $email;
            $subject = "Λήξη Δανεισμού";
            $txt = 'Ο δανεισμός που έχεις λήγει σήμερα.';
            $headers = "From: webmaster@example.com" . "\r\n" .
            "CC: somebodyelse@example.com";
            mail($to,$subject,$txt,$headers);
            if( mail($to,$subject,$txt,$headers)){
               echo "Το email στάλθηκε με επιτυχία";
            }else{
               echo "Το email δεν στάλθηκε με επιτυχία γιατί :" .$mail->ErrorInfo;
            }
        }   
        if($dateDiff < 0 ){
            $daysThatMailIsSend = abs(round($dateDiff));
            if ($daysThatMailIsSend % 30 == 0){
                echo '<li><p>-Δανεισμός : '.$id_borrow.' στέλνω email ειδοποίησης στον φοιτητή με ΑΕΜ: '.$aem_borrow.'</p>
                      <p>-Στέλνω email στο  '.$email.' γιατί ο δανεισμός με ID = '.$id_borrow.' έχει λήξει εδώ και '.abs(round($dateDiff)).' μέρες.</p></li>    
                ';
                $changeDateSQL = "UPDATE borrow_svds SET notify_expire= :notify_expire WHERE id_borrow= :idToChange";
                $changeDateSTMT = $db->prepare($changeDateSQL);
                $changeDateSTMT->bindParam(':idToChange', $id_borrow);
                $changeDateSTMT->bindParam(':notify_expire', $daysThatMailIsSend );
                $changeDateSTMT->execute();
                $to = $email;
                $subject = "Λήξη Δανεισμού";
                $txt = 'Ο δανεισμός που έχεις κάνει λήγει σε '.abs(round($dateDiff)).' ημέρες.';
                $headers = "From: webmaster@example.com" . "\r\n" .
                "CC: somebodyelse@example.com";
                mail($to,$subject,$txt,$headers);
                if( mail($to,$subject,$txt,$headers)){
                   echo "Το email στάλθηκε με επιτυχία";
                }else{
                   echo "Το email δεν στάλθηκε με επιτυχία γιατί :" .$mail->ErrorInfo;
                }
            }    
        }   
    }
    echo '</ul>';
} 

?>     