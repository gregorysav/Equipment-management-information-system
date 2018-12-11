<?php
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
$msg1 = "ΕΝΤΥΠΟ ΧΡΕΩΣΗΣ ΗΛΕΚΤΡΟΝΙΚΟΥ ΕΞΟΠΛΙΣΜΟΥ"."\r\n";
$msg2 = "\r\n".  'Ο Μηνάς Δασυγένης, Μέλος ΔΕΠ, ΤΜΠΤ παραδίδω στο(ν) '.$last_name.' '.$first_name.' (AEM = '.$aemToborrow.') φοιτητή του ΤΜΠΤ, τον παρακάτω εξοπλισμό: '."\r\n";

$msg3 = "\r\n". 'Ο παραπάνω εξοπλισμός θα χρησιμοποιηθεί με σκοπό : '.$borrowReason.' για χρονικό διάστημα από '.$newStartFormat.' μέχρι '.$newFinishFormat.' .'."\r\n";
$msg4 = "\r\n". "Ο ΠΑΡΑΔΙΔΩΝ                                                          Ο ΠΑΡΑΛΑΒΩΝ";  
$name = "borrower.txt";
$path = $aemToborrow.$name;
 
$file = fopen($path,"w");
echo fwrite($file,""); 
fclose($file);

$file = $path;
$current = file_get_contents($file);
$current .= "$msg2";
$current .= "$msgEquip";
$current .= "$msg3";
file_put_contents($file, $current);
ob_start();
require('tfpdf.php');

$pdf = new tFPDF();
$pdf->AddPage();
$pdf->Image('images/uowmlogo.jpg',10,10,-300);
$pdf->Multicell(0,45,"");
$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
$pdf->SetFont('DejaVu','',16);
$pdf->SetFont('Dejavu','U');
$pdf->Write(8,$msg1);
$pdf->Multicell(0,-15,"");
$pdf->SetFont('DejaVu','',12);
$pdf->Multicell(0,25,"");
$pdf->Write(8,$current);
$pdf->Multicell(0,15,"");
$pdf->Write(8,$msg4);

$pdf->Output();
ob_end_flush(); 


?>