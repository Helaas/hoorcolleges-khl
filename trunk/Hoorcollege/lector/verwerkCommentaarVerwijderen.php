<?php
include_once('./../includes/kern.php');
session_start();

$commentaarId = $_GET['id'];
echo $commentaarId;

if(validateNumber($commentaarId)){
    $db->Execute('DELETE FROM hoorcollege_reactie WHERE idReactie='.$commentaarId);
}
?>
