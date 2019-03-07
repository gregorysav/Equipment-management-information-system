<?php
//Access: Registered Users
include("variables_file.php");
include("checkUser.php");
include("views/connection.php");
include("function_cron.php");

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

function createCSV($addToCSV) {  
    $filename = uniqid(rand(), true) . 'csvreport.csv';
    ob_end_clean();
    $file = fopen($filename, "w");
    $line = implode("",$addToCSV); 
    fwrite($file, $line);
    fclose($file);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Equipment.csv');
    header('Pragma: no-cache');
    readfile($filename); 
    unlink($filename);
    exit();
}

function PDFPrintReturn ($fullName, $aem_borrow, $type, $itemsToPrint, $borrowReason, $startDate, $endDate){
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
    $pdf->Cell(0, 0, 'ΕΝΤΥΠΟ ΕΠΙΣΤΡΟΦΗΣ ΔΑΝΕΙΣΜΕΝΟΥ ΕΞΟΠΛΙΣΜΟΥ', 0, 0, 'C', 0, '', 0);
    $pdf->SetFont('freesans','',12);
    $pdf->Multicell(0,35,"");
    $html = "
    Ο $teacherThatProvide, ΤΜΠΤ έλαβα από τον $fullName (ΑΕΜ $aem_borrow) φοιτητή του ΠΔΜ, τον παρακάτω εξοπλισμό που του είχε δανειστεί κατά την περίοδο $startDate  μέχρι $endDate με σκοπό: $borrowReason: 
    ";
    
    $html .= " $itemsToPrint <br>";

    $html .=" Ο παραπάνω εξοπλισμός ελέγχθηκε και βρέθηκε να είναι στην ίδια κατάσταση που είχε παραχωρηθεί, με όλα τα συνοδευτικά (π.χ. CD,καλώδια,έγγραφα/ κ.α.). <br>Το έγγραφο αυτό είναι αποδεικτικό παράδοσης εξοπλισμού, και πρέπει να διατηρηθεί έως την αποφοίτηση. </p>";
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Multicell(0,25,"");
    $pdf->Cell(0, 10, 'Ο ΠΑΡΑΛΑΒΩΝ', 0, false, 'R', 0, '', 0, false, 'T', 'M');     

    ob_end_clean(); 
    $pdf->Output(); 
}
?>