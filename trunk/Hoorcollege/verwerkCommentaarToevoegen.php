<?php
/**
 * Deze file wordt opgeroepen in functies.js voor het toevoegen van ingegeven commentaar in de db
*/
include_once('./includes/kern.php');
session_start();

$gebruikerID = $_SESSION['gebruiker']->getIdGebruiker();
$hoorcollegeID = $_GET["hoorcollege"];

if(validateNumber($gebruikerID) && validateNumber($hoorcollegeID)){
    $commentaar = mysql_real_escape_string($_GET["commentaar"]);
    voegCommentaarToe($gebruikerID, $hoorcollegeID, $commentaar);
}
?>
