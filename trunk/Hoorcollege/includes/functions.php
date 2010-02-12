<?php
    include_once('kern.php');
    global $db ;


    //methode om na te gaan of een gebruiker met deze email al bestaat
    function bestaatEmail($email) {
        $resultaat = $db->Execute("SELECT COUNT( DISTINCT email ) AS aantal
                                   FROM `hoorcollege_gebruiker` WHERE email = '$email'");

        if($resultaat->fields["aantal"] > 0) {
            return true;
        }
        else {
            return false;
        }
    }
?>
