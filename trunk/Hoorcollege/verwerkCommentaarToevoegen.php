<?php
include_once('./includes/kern.php');
session_start();

$gebruikerID = $_SESSION['gebruiker']->getIdGebruiker();
//Nog even standaard hoorcollegeID
$hoorcollegeID = $_GET["hoorcollege"];

//Inhoud commentaar moet ik nog controleren
$commentaar = $_GET["commentaar"];

voegCommentaarToe($gebruikerID, $hoorcollegeID, $commentaar);


?>
