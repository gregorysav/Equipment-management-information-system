<?php
include "views/connection.php";
include "variables_file.php";
include "functions.php";
	
	$msgEquip = "";
	$borrowQuerySQL = "SELECT * FROM borrow_svds WHERE aem_borrow= :idUser AND confirmation_borrow= :zero";
	$borrowQuerySTMT = $db->prepare($borrowQuerySQL); 
	$borrowQuerySTMT->bindParam(':zero', $zero, PDO::PARAM_INT);
	$borrowQuerySTMT->bindParam(':idUser', $aem, PDO::PARAM_INT);
	$borrowQuerySTMT->execute();  
	while ($borrowQuerySTMTResult=$borrowQuerySTMT->fetch(PDO::FETCH_ASSOC)){
		$userQuerySQL= "SELECT * FROM users_svds WHERE aem= :aemUser";
		$userQuerySTMT = $db->prepare($userQuerySQL);
		$userQuerySTMT->bindParam(':aemUser', $borrowQuerySTMTResult['aem_borrow'], PDO::PARAM_INT);
		$userQuerySTMT->execute();
		while ($userQuerySTMTResult=$userQuerySTMT->fetch(PDO::FETCH_ASSOC)){
			$last_name = $userQuerySTMTResult['last_name'];
			$first_name = $userQuerySTMTResult['first_name'];
			$aemToborrow = $userQuerySTMTResult['aem']; 
		}	
		$equipQuerySQL= "SELECT * FROM equip_svds WHERE id_equip= :idEquip";
		$equipQuerySTMT = $db->prepare($equipQuerySQL);
		$equipQuerySTMT->bindParam(':idEquip', $borrowQuerySTMTResult['id_equip_borrow'], PDO::PARAM_INT);
		$equipQuerySTMT->execute();
		while ($equipQuerySTMTResult=$equipQuerySTMT->fetch(PDO::FETCH_ASSOC)){
			$msgEquip .= $equipQuerySTMTResult['name_e'] ."\r\n";
		}	
		$startDate = $borrowQuerySTMTResult['start_date'];
		$endDate = $borrowQuerySTMTResult['expire_date'];
		$borrowReason = $borrowQuerySTMTResult['borrow_reason'] ."\r\n";
	}	

	$newStartFormat = date("d-m-Y", strtotime($startDate));
	$newFinishFormat = date("d-m-Y", strtotime($endDate));


ob_start();
require_once('tcpdf/tcpdf.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('freesans', 'BI', 16);
$pdf->setFont('freeserif');
// add a page
$pdf->AddPage();
$pdf->Image('images/uowmlogo.jpg', 10, 10, 70, '', 'JPG', '', 'T', false, 300, 'L', false, false, 0,     false, false, false);
$pdf->Multicell(0,45,"");
$pdf->SetFont('freesans','U');
$pdf->Cell(0, 0, 'ΕΝΤΥΠΟ ΧΡΕΩΣΗΣ ΗΛΕΚΤΡΟΝΙΚΟΥ ΕΞΟΠΛΙΣΜΟΥ', 0, 0, 'C', 0, '', 0);
$pdf->SetFont('freesans','',12);
$pdf->Multicell(0,35,"");
$html = "
Ο Μηνάς Δασυγένης, Μέλος ΔΕΠ, ΤΜΠΤ παραδίδω στο(ν) $last_name φοιτητή του ΤΜΠΤ, τον παρακάτω εξοπλισμό: 
Ο παραπάνω εξοπλισμός θα χρησιμοποιηθεί με σκοπό :  για χρονικό διάστημα από  μέχρι  
Ο ΠΑΡΑΔΙΔΩΝ                                                          Ο ΠΑΡΑΛΑΒΩΝ;  
</p>";

$pdf->writeHTML($html, true, false, true, false, '');


// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output();
ob_end_flush(); 
?>