<?php
include_once('./includes/kern.php');
session_start();

$TBS = new clsTinyButStrong;


if(isset ($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 1){
    $config["pagina"] = "./student/student.html";
    $TBS->LoadTemplate('./html/template.html') ;
    $gebruikerID = $_SESSION['gebruiker']->getIdGebruiker();
    //tabel aanmaken voor overzicht hoorcolleges
    $query = 'SELECT * FROM hoorcollege_hoorcollege WHERE idHoorcollege IN (
              SELECT Hoorcollege_idHoorcollege FROM hoorcollege_onderwerphoorcollege WHERE Onderwerp_Vak_idVak IN(
              SELECT Vak_idVak FROM hoorcollege_gebruiker_volgt_vak WHERE Gebruiker_idGebruiker = '.$gebruikerID.' ))';

    $TBS->MergeBlock('blk1', $db, $query);

}else if(!isset ($_SESSION['gebruiker'])){
    header("location: login.php");
}else{
    $config["pagina"] = "./FileUpload/Error1Login.html";
}



$TBS->Show() ;

?>
