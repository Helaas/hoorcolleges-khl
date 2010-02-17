<?php
include_once('./includes/kern.php');
session_start();

$TBS = new clsTinyButStrong;


if(isset ($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 1){
    $config["pagina"] = "./student/student.html";
    $TBS->LoadTemplate('./html/student/templateStudent.html') ;
    $gebruikerID = $_SESSION['gebruiker']->getIdGebruiker();

    //Query voor overzicht hoorcolleges nog te bekijken
    $queryNogTeBekijken = 'SELECT idHoorcollege, naam, vbc
                          FROM hoorcollege_hoorcollege LEFT OUTER JOIN hoorcollege_gebruikerhoorcollege
                          ON Hoorcollege_idHoorcollege = idHoorcollege
                          WHERE Gebruiker_idGebruiker = '.$gebruikerID.' AND reedsBekeken = false';

    //Query voor overzicht hoorcolleges reeds bekeken
    $queryReedsBekeken = 'SELECT idHoorcollege, naam, vbc
                         FROM hoorcollege_hoorcollege LEFT OUTER JOIN hoorcollege_gebruikerhoorcollege
                         ON Hoorcollege_idHoorcollege = idHoorcollege
                         WHERE Gebruiker_idGebruiker = '.$gebruikerID.' AND reedsBekeken = true';

    //Tabel nog te bekijken hoorcolleges opvullen (nog aan te passen)
    $TBS->MergeBlock('blk1', $db, $queryNogTeBekijken);

    //Tabel reeds bekeken hoorcolleges opvullen (nog aan te passen)
    $TBS->MergeBlock('blk2', $db, $queryReedsBekeken);

}else if(!isset ($_SESSION['gebruiker'])){
    header("location: login.php");
}else{
    $config["pagina"] = "./FileUpload/Error1Login.html";
    $TBS->LoadTemplate('./html/template.html') ;
}



$TBS->Show() ;

?>
