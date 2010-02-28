<?php
include_once('./../includes/kern.php');
session_start();

$config["pagina"] = "./FileUpload/Error1Login.html";
$tekstinhoud = "";

if(isset ($_SESSION['gebruiker'])) {
    $gebruiker = $_SESSION['gebruiker'];
    $gebruikerNiv = $gebruiker->getNiveau();
    $gebruikerID = $gebruiker->getIdGebruiker();

    $hoorcollid=$_GET['gevraagdhoorcoll'];
    if($gebruikerNiv==40) {

              if (preg_match('/^[0-9]+$/iD', $_GET['gevraagdhoorcoll'])  ) {
                 //controle of het hoorcollege wel bij de lector hoort
                 $result = $db->Execute("
                                        select Hoorcollege_idHoorcollege
                                        from hoorcollege_onderwerphoorcollege
                                        where Onderwerp_idOnderwerp in (
                                                       select idOnderwerp
                                                       from hoorcollege_onderwerp
                                                       where Vak_idVak in (
                                                             SELECT Vak_idVak
                                                             FROM hoorcollege_gebruiker_beheert_vak
                                                             WHERE Gebruiker_idGebruiker=".(int)$gebruikerID.")) AND Hoorcollege_idHoorcollege=".(int)$_GET['gevraagdhoorcoll']);

                 if($result->fields["Hoorcollege_idHoorcollege"]!=null) {

               //Lector heeft rechten tot het hoorcollege
                 $config["pagina"] = "./../html/lector/ToonResultaten.html";
                 }
                 else{
                $config["pagina"] = "./Lector/Boodschap.html";
                $Titel="Resultaten bekijken";
                $tekstinhoud = "U probeerde de resultaten van een hoorcollege dat niet onder uw bevoegdheid valt te bekijken.";
                 }


            $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
            $TBS->MergeBlock('blk2', $db, 'SELECT * FROM hoorcollege_groep GROUP BY naam asc');
            $TBS->Show() ;
        }
        else {
            //Geen Speciale Tekens toegestaan
            $Titel="Foutmelding";
            $tekstinhoud = "Poging tot SQL injection gedetecteerd.";
            $config["pagina"] = "./Lector/Boodschap.html";
            $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
            $TBS->Show() ;
        }
    }  //Users met onvoldoende privileges voor deze pagina een foutpagina tonen
    else if($_SESSION['gebruiker']->getNiveau() == 1) {
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./../html/lector/templateLector.html');
        $TBS->Show() ;
    }else if($_SESSION['gebruiker']->getNiveau() == 99) {
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./../html/lector/templateLector.html');
        $TBS->Show() ;
    }

    else {
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
        $TBS->Show() ;
    }

}
else {
    $config["pagina"] = "./FileUpload/Error1Login.html";
    $TBS->LoadTemplate('./../html/template.html') ;
    $TBS->Show() ;
}
?>
