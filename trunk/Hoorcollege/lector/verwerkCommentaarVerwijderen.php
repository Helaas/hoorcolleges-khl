<?php
include_once('./../includes/kern.php');
session_start();
if(isset($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() >= 40) { //lector is ingelogged
    $commentaarId = $_GET['id'];
    echo $commentaarId;

    if(validateNumber($commentaarId)){
        $db->Execute('DELETE FROM hoorcollege_reactie WHERE idReactie='.$commentaarId);
    }
}
?>
