<?php
//Access: Administrator
function adminDisplayInformation($id_borrow, $aem_borrow, $full_name, $email, $expire_date, $notify30, $notify20, $notify10) {
    include("views/connection.php");
    $send = -1;
    $nowDate =  strtotime(date("d-m-Y"));
    $dateToCheck = strtotime(date('d-m-Y',strtotime($expire_date)));
    $dateDiff = ($dateToCheck - $nowDate) / 86400;

    if ($dateDiff > 30){
        echo '-Δανεισμός : '.$id_borrow.' (Φοιτητής [ΑΕΜ: '.$aem_borrow.'])<br>  
                ΟΚ δεν χρειάζεται να στείλω ειδοποίηση λήγει σε '.round($dateDiff).' μέρες <br>              
        ';
    } elseif ($dateDiff > 20){
        if ($notify30 != -1){
            echo '-Δανεισμός : '.$id_borrow.' στέλνω email ειδοποίησης στον φοιτητή ['.$full_name.'] με [ΑΕΜ: '.$aem_borrow.']<br>    
                    Στέλνω email στο '.$email.' γιατί ο δανεισμός με ID = '.$id_borrow.' λήγει σε 30 ημέρες.<br>               
            ';  
            $userToSendEmailAndSMSQuerySQL = "SELECT * FROM users_svds WHERE aem= :aemBorrower";
            $userToSendEmailAndSMSQuerySTMT = $db->prepare($userToSendEmailAndSMSQuerySQL);
            $userToSendEmailAndSMSQuerySTMT->bindParam(':aemBorrower', $aem_borrow, PDO::PARAM_INT);
            if ($userToSendEmailAndSMSQuerySTMT->execute()) {
                while ($userToSendEmailAndSMSQuerySTMTResult=$userToSendEmailAndSMSQuerySTMT->fetch(PDO::FETCH_ASSOC)){
                    $telephone = $userToSendEmailAndSMSQuerySTMTResult['telephone'];   
                    $idUserBorrow = $userToSendEmailAndSMSQuerySTMTResult['id'];
                }
            }
            $equipmentNameToSendEmailAndSMSQuerySQL = "SELECT * FROM borrow_svds INNER JOIN equip_svds ON borrow_svds.id_equip_borrow = equip_svds.id_equip WHERE id_user_borrow= :idBorrower";
            $equipmentNameToSendEmailAndSMSQuerySTMT = $db->prepare($equipmentNameToSendEmailAndSMSQuerySQL);
            $equipmentNameToSendEmailAndSMSQuerySTMT->bindParam(':idBorrower', $idUserBorrow, PDO::PARAM_INT);
            if ($equipmentNameToSendEmailAndSMSQuerySTMT->execute()) {
                while ($equipmentNameToSendEmailAndSMSQuerySTMTResult=$equipmentNameToSendEmailAndSMSQuerySTMT->fetch(PDO::FETCH_ASSOC)){
                    $equipmentName = $equipmentNameToSendEmailAndSMSQuerySTMTResult['name_e'];   
                }
            } 
            sendEmail($email, $full_name, $aem_borrow, $dateDiff, $equipmentName);
            
            $changeDateSQL = "UPDATE borrow_svds SET notify30= :notify30 WHERE id_borrow= :idToChange";
            $changeDateSTMT = $db->prepare($changeDateSQL);
            $changeDateSTMT->bindParam(':idToChange', $id_borrow);
            $changeDateSTMT->bindParam(':notify30', $send);
            $changeDateSTMT->execute();
        } else {
            echo '-Δανεισμός : '.$id_borrow.' στέλνω email ειδοποίησης στον φοιτητή ['.$full_name.'] με [ΑΕΜ: '.$aem_borrow.']<br>
                    Το email ειδοποίησης για τις 30 μέρες έχει σταλθεί.<br>
            ';
        }    
    }elseif($dateDiff > 10){
        if ($notify20 != -1){
            echo '-Δανεισμός : '.$id_borrow.' στέλνω email ειδοποίησης στον φοιτητή ['.$full_name.'] με [ΑΕΜ: '.$aem_borrow.']<br>    
                    Στέλνω email στο '.$email.' γιατί ο δανεισμός με ID = '.$id_borrow.' λήγει σε 20 ημέρες.<br>               
            ';  
            $userToSendEmailAndSMSQuerySQL = "SELECT * FROM users_svds WHERE aem= :aemBorrower";
            $userToSendEmailAndSMSQuerySTMT = $db->prepare($userToSendEmailAndSMSQuerySQL);
            $userToSendEmailAndSMSQuerySTMT->bindParam(':aemBorrower', $aem_borrow, PDO::PARAM_INT);
            if ($userToSendEmailAndSMSQuerySTMT->execute()) {
                while ($userToSendEmailAndSMSQuerySTMTResult=$userToSendEmailAndSMSQuerySTMT->fetch(PDO::FETCH_ASSOC)){
                    $telephone = $userToSendEmailAndSMSQuerySTMTResult['telephone'];   
                    $idUserBorrow = $userToSendEmailAndSMSQuerySTMTResult['id'];
                }
            }
            $equipmentNameToSendEmailAndSMSQuerySQL = "SELECT * FROM borrow_svds INNER JOIN equip_svds ON borrow_svds.id_equip_borrow = equip_svds.id_equip WHERE id_user_borrow= :idBorrower";
            $equipmentNameToSendEmailAndSMSQuerySTMT = $db->prepare($equipmentNameToSendEmailAndSMSQuerySQL);
            $equipmentNameToSendEmailAndSMSQuerySTMT->bindParam(':idBorrower', $idUserBorrow, PDO::PARAM_INT);
            if ($equipmentNameToSendEmailAndSMSQuerySTMT->execute()) {
                while ($equipmentNameToSendEmailAndSMSQuerySTMTResult=$equipmentNameToSendEmailAndSMSQuerySTMT->fetch(PDO::FETCH_ASSOC)){
                    $equipmentName = $equipmentNameToSendEmailAndSMSQuerySTMTResult['name_e'];   
                }
            } 
            sendEmail($email, $full_name, $aem_borrow, $dateDiff, $equipmentName);

            $changeDateSQL = "UPDATE borrow_svds SET notify30= :notify30, notify20= :notify20 WHERE id_borrow= :idToChange";
            $changeDateSTMT = $db->prepare($changeDateSQL);
            $changeDateSTMT->bindParam(':idToChange', $id_borrow);
            $changeDateSTMT->bindParam(':notify30', $send);
            $changeDateSTMT->bindParam(':notify20', $send);
            $changeDateSTMT->execute();
        } else {
            echo '-Δανεισμός : '.$id_borrow.' στέλνω email ειδοποίησης στον φοιτητή ['.$full_name.'] με [ΑΕΜ: '.$aem_borrow.']<br>
                    Το email ειδοποίησης για τις 20 μέρες έχει σταλθεί.<br>
            ';
        }    
    }elseif($dateDiff > 0){
        if ($notify10 != -1){
            echo '-Δανεισμός : '.$id_borrow.' στέλνω email ειδοποίησης στον φοιτητή ['.$full_name.'] με [ΑΕΜ: '.$aem_borrow.']<br>    
                    Στέλνω email στο '.$email.' γιατί ο δανεισμός με ID = '.$id_borrow.' λήγει σε 10 ημέρες.<br>               
            ';  
            $userToSendEmailAndSMSQuerySQL = "SELECT * FROM users_svds WHERE aem= :aemBorrower";
            $userToSendEmailAndSMSQuerySTMT = $db->prepare($userToSendEmailAndSMSQuerySQL);
            $userToSendEmailAndSMSQuerySTMT->bindParam(':aemBorrower', $aem_borrow, PDO::PARAM_INT);
            if ($userToSendEmailAndSMSQuerySTMT->execute()) {
                while ($userToSendEmailAndSMSQuerySTMTResult=$userToSendEmailAndSMSQuerySTMT->fetch(PDO::FETCH_ASSOC)){
                    $telephone = $userToSendEmailAndSMSQuerySTMTResult['telephone'];   
                    $idUserBorrow = $userToSendEmailAndSMSQuerySTMTResult['id'];
                }
            }
            $equipmentNameToSendEmailAndSMSQuerySQL = "SELECT * FROM borrow_svds INNER JOIN equip_svds ON borrow_svds.id_equip_borrow = equip_svds.id_equip WHERE id_user_borrow= :idBorrower";
            $equipmentNameToSendEmailAndSMSQuerySTMT = $db->prepare($equipmentNameToSendEmailAndSMSQuerySQL);
            $equipmentNameToSendEmailAndSMSQuerySTMT->bindParam(':idBorrower', $idUserBorrow, PDO::PARAM_INT);
            if ($equipmentNameToSendEmailAndSMSQuerySTMT->execute()) {
                while ($equipmentNameToSendEmailAndSMSQuerySTMTResult=$equipmentNameToSendEmailAndSMSQuerySTMT->fetch(PDO::FETCH_ASSOC)){
                    $equipmentName = $equipmentNameToSendEmailAndSMSQuerySTMTResult['name_e'];   
                }
            } 
            sendEmail($email, $full_name, $aem_borrow, $dateDiff, $equipmentName);
            
            $changeDateSQL = "UPDATE borrow_svds SET notify30= :notify30, notify20= :notify20, notify10= :notify10 WHERE id_borrow= :idToChange";
            $changeDateSTMT = $db->prepare($changeDateSQL);
            $changeDateSTMT->bindParam(':idToChange', $id_borrow);
            $changeDateSTMT->bindParam(':notify30', $send);
            $changeDateSTMT->bindParam(':notify20', $send);
            $changeDateSTMT->bindParam(':notify10', $send);
            $changeDateSTMT->execute();
        } else {
            echo '-Δανεισμός : '.$id_borrow.' στέλνω email ειδοποίησης στον φοιτητή ['.$full_name.'] με [ΑΕΜ: '.$aem_borrow.']<br>
                    Το email ειδοποίησης για τις 10 μέρες έχει σταλθεί.<br>
            ';
        }   
    } elseif($dateDiff == 1 || $dateDiff == 0 ){
        
        echo '-Δανεισμός : '.$id_borrow.' στέλνω email ειδοποίησης στον φοιτητή ['.$full_name.'] με [ΑΕΜ: '.$aem_borrow.']<br>    
                Στέλνω email στο '.$email.' γιατί ο δανεισμός με ID = '.$id_borrow.' λήγει σήμερα στις '.date('d/m/Y',strtotime($expire_date)).'.<br>              
        ';

        $userToSendEmailAndSMSQuerySQL = "SELECT * FROM users_svds WHERE aem= :aemBorrower";
            $userToSendEmailAndSMSQuerySTMT = $db->prepare($userToSendEmailAndSMSQuerySQL);
            $userToSendEmailAndSMSQuerySTMT->bindParam(':aemBorrower', $aem_borrow, PDO::PARAM_INT);
            if ($userToSendEmailAndSMSQuerySTMT->execute()) {
                while ($userToSendEmailAndSMSQuerySTMTResult=$userToSendEmailAndSMSQuerySTMT->fetch(PDO::FETCH_ASSOC)){
                    $telephone = $userToSendEmailAndSMSQuerySTMTResult['telephone'];   
                    $idUserBorrow = $userToSendEmailAndSMSQuerySTMTResult['id'];
                }
            }
            $equipmentNameToSendEmailAndSMSQuerySQL = "SELECT * FROM borrow_svds INNER JOIN equip_svds ON borrow_svds.id_equip_borrow = equip_svds.id_equip WHERE id_user_borrow= :idBorrower";
            $equipmentNameToSendEmailAndSMSQuerySTMT = $db->prepare($equipmentNameToSendEmailAndSMSQuerySQL);
            $equipmentNameToSendEmailAndSMSQuerySTMT->bindParam(':idBorrower', $idUserBorrow, PDO::PARAM_INT);
            if ($equipmentNameToSendEmailAndSMSQuerySTMT->execute()) {
                while ($equipmentNameToSendEmailAndSMSQuerySTMTResult=$equipmentNameToSendEmailAndSMSQuerySTMT->fetch(PDO::FETCH_ASSOC)){
                    $equipmentName = $equipmentNameToSendEmailAndSMSQuerySTMTResult['name_e'];   
                }
            } 
            sendEmail($email, $full_name, $aem_borrow, $dateDiff, $equipmentName);
    }   
    if($dateDiff < 0 ){
        $daysThatMailIsSend = abs(round($dateDiff));
        if ($daysThatMailIsSend % 30 == 0){
            echo '-Δανεισμός : '.$id_borrow.' στέλνω email και SMS ειδοποίησης στον φοιτητή ['.$full_name.'] με [ΑΕΜ: '.$aem_borrow.']<br>
                    Στέλνω email στο  '.$email.' γιατί ο δανεισμός με ID = '.$id_borrow.' έχει λήξει εδώ και '.abs(round($dateDiff)).' μέρες.<br>    
            ';
            $userToSendEmailAndSMSQuerySQL = "SELECT * FROM users_svds WHERE aem= :aemBorrower";
            $userToSendEmailAndSMSQuerySTMT = $db->prepare($userToSendEmailAndSMSQuerySQL);
            $userToSendEmailAndSMSQuerySTMT->bindParam(':aemBorrower', $aem_borrow, PDO::PARAM_INT);
            if ($userToSendEmailAndSMSQuerySTMT->execute()) {
                while ($userToSendEmailAndSMSQuerySTMTResult=$userToSendEmailAndSMSQuerySTMT->fetch(PDO::FETCH_ASSOC)){
                    $telephone = $userToSendEmailAndSMSQuerySTMTResult['telephone'];   
                    $idUserBorrow = $userToSendEmailAndSMSQuerySTMTResult['id'];
                }
            }
            $equipmentNameToSendEmailAndSMSQuerySQL = "SELECT * FROM borrow_svds INNER JOIN equip_svds ON borrow_svds.id_equip_borrow = equip_svds.id_equip WHERE id_user_borrow= :idBorrower";
            $equipmentNameToSendEmailAndSMSQuerySTMT = $db->prepare($equipmentNameToSendEmailAndSMSQuerySQL);
            $equipmentNameToSendEmailAndSMSQuerySTMT->bindParam(':idBorrower', $idUserBorrow, PDO::PARAM_INT);
            if ($equipmentNameToSendEmailAndSMSQuerySTMT->execute()) {
                while ($equipmentNameToSendEmailAndSMSQuerySTMTResult=$equipmentNameToSendEmailAndSMSQuerySTMT->fetch(PDO::FETCH_ASSOC)){
                    $equipmentName = $equipmentNameToSendEmailAndSMSQuerySTMTResult['name_e'];   
                }
            }        

            $changeDateSQL = "UPDATE borrow_svds SET notify_expire= :notify_expire WHERE id_borrow= :idToChange";
            $changeDateSTMT = $db->prepare($changeDateSQL);
            $changeDateSTMT->bindParam(':idToChange', $id_borrow);
            $changeDateSTMT->bindParam(':notify_expire', $daysThatMailIsSend );
            $changeDateSTMT->execute();
            sendEmail($email, $full_name, $aem_borrow, $dateDiff, $equipmentName);
            sendSMS($full_name, $telephone, $aem_borrow, $dateDiff, $equipmentName);
        }    
    }   
}

function sendEmail($email, $full_name, $aem_borrow, $dateDiff, $equipmentName) {
    $date=date('l jS \of F Y h:i:s A');
    $crlf = chr(13) . chr(10);
    $to = $email;
    $subject = "[ILoan] Notification Email";
    $message = 'Αυτοματοποιημένο μήνυμα i-loan"'.$crlf.''.$crlf.''.$crlf.'"Προς: [ '.$full_name.' ], AEM ['.$aem_borrow.'] "'.$crlf.'"Ο δανεισμός για ['.$equipmentName.'] που έχεις κάνει λήγει σε '.$dateDiff.' ημέρες. Μην απαντήσετε σε αυτό το email, γιατί δεν παρακολουθείται η συγκεκριμένη διεύθυνση."'.$crlf.''.$crlf.'"Παρακαλώ διατηρήστε αυτό το email στο αρχείο σας έως το τέλος του εξαμήνου".';
    $headers = 'From: noreply@spam.vlsi.gr'."\r\n".'Reply-To: noreply@spam.vlsi.gr'."\r\n".'Content-Type: text/plain; charset=UTF-8' . "\r\n" .'MIME-Version: 1.0' .    "\r\n" .'Content-Transfer-Encoding: quoted-printable' . "\r\n" .'X-Mailer: PHP/'.phpversion();


    if( mail($to,$subject,$message,$headers)){
       echo "Το email στάλθηκε με επιτυχία";
    }else{
       echo "Το email δεν στάλθηκε με επιτυχία γιατί :" .$mail->ErrorInfo;
    }
}

function sendSMS($full_name, $telephone, $aem_borrow, $dateDiff, $equipmentName){
    include("views/connection.php");
    $aemTest = 541;
    $number = 69;
    $telephoneToMatch = $number.'%';
    $borrowQuerySQL = "SELECT * FROM users_svds WHERE telephone LIKE :telephoneToSearch AND aem= :aemUser";
    $borrowQuerySTMT = $db->prepare($borrowQuerySQL);
    $borrowQuerySTMT->bindParam(':telephoneToSearch', $telephoneToMatch, PDO::PARAM_INT);
    $borrowQuerySTMT->bindParam(':aemUser', $aemTest, PDO::PARAM_INT); 
    $borrowQuerySTMT->execute();

    while ($borrowQuerySTMTResult=$borrowQuerySTMT->fetch(PDO::FETCH_ASSOC)){
        if ($borrowQuerySTMT->rowCount() > 0 AND strlen($borrowQuerySTMTResult['telephone']) == 10){
            $url = 'http://vlsi.gr/sms/webservice/process.php';;
            if ($dateDiff < 0) {
                $fields = [
                'authcode'  => 546743,
                'method'  => 'POST',
                'mobilenr' => $telephone,
                'message' => 'I-Loan: κ. [ '.$full_name.' ], AEM ['.$aem_borrow.'] ο δανεισμός για ['.$equipmentName.'] έχει ολοκληρωθεί πριν '.abs($dateDiff).' μέρες.'
                ];    
            } elseif ($dateDiff == 0) {
                $fields = [
                'authcode'  => 546743,
                'method'  => 'POST',
                'mobilenr' => $telephone,
                'message' => 'I-Loan: κ. [ '.$full_name.' ], AEM ['.$aem_borrow.'] ο δανεισμός για ['.$equipmentName.'] έχει τερματιστεί από το διδάσκοντα.'
                ]; 
            }else {
                $fields = [
                'authcode'  => 546743,
                'method'  => 'POST',
                'mobilenr' => $telephone,
                'message' => 'I-Loan: κ. [ '.$full_name.' ], AEM ['.$aem_borrow.'] ο δανεισμός για ['.$equipmentName.'] έχει επιβεβαιωθεί.'
                ];        
            }
            
            $fields_string = http_build_query($fields);
    
            $ch = curl_init();

            curl_setopt($ch,CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_POST, count($fields));
            curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

            $result = curl_exec($ch);
            echo $result;
            }else {
                echo "Για το χρήστη δεν έχουμε νούμερο που να αρχίζει από 69 ή δεν είναι 10ψήφιος.";
            }
    }
}


?>