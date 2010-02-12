<?php
    include_once('kern.php');


    //methode om na te gaan of een gebruiker met deze email al bestaat
    function bestaatEmail($email) {
        global $db;
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
