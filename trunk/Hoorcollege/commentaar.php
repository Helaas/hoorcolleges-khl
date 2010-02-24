<?php

include_once('./includes/kern.php');
session_start();

$TBS = new clsTinyButStrong;


if(isset ($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 1) {
    $config["pagina"] = "./student/commentaar.html";
    $TBS->LoadTemplate('./html/student/templateStudent.html') ;

    //idHoorcollege moet opgehaald worden eens geïmplementeerd in de pagina van het hoorcollege
    $idHoorcollege = 1;
    $alleCommentarenQuery = 'SELECT voornaam, naam, inhoud
                                     FROM hoorcollege_reactie LEFT OUTER JOIN hoorcollege_gebruiker
                                     ON Gebruiker_idGebruiker = idGebruiker
                                     WHERE Hoorcollege_idHoorcollege =1';

    $TBS->MergeBlock('blk1', $db, $alleCommentarenQuery);

}else if(!isset ($_SESSION['gebruiker'])) {
    header("location: login.php");
}else {
    $config["pagina"] = "./FileUpload/Error1Login.html";
    $TBS->LoadTemplate('./html/template.html') ;
}
$TBS->Show();
?>
