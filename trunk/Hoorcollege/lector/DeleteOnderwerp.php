<?php
include_once('./../includes/kern.php');
session_start();

$config["pagina"] = "./FileUpload/Error1Login.html";
$tekstinhoud = "";

if(isset ($_SESSION['gebruiker'])) {
    $gebruiker = $_SESSION['gebruiker'];
    $gebruikerNiv = $gebruiker->getNiveau();
    $gebruikerID = $gebruiker->getIdGebruiker();

    if($gebruikerNiv>= 40) {

              if (preg_match('/^[0-9]+$/iD', $_GET['gevraagdond'])  ) {
                 //controle of het onderwerp wel bij de lector hoort
                 $result = $db->Execute("    select idOnderwerp
                                             from hoorcollege_onderwerp
                                             where Vak_idVak in (
                                                             SELECT Vak_idVak
                                                             FROM hoorcollege_gebruiker_beheert_vak
                                                             WHERE Gebruiker_idGebruiker=".(int)$gebruikerID.") AND idOnderwerp=".(int)$_GET['gevraagdond']);

                 if($result->fields["idOnderwerp"]!=null) {
               //Delete onderwerp indien lector rechten tot dit onderwerp heeft
               verwijderOnderwerp($_GET['gevraagdond']);

                $config["pagina"] = "./Lector/Boodschap.html";
                $Titel="Onderwerp verwijderen";
                $tekstinhoud = "Het onderwerp werd met succes verwijderd.";
                 }
                 else{
                $config["pagina"] = "./Lector/Boodschap.html";
                $Titel="Onderwerp verwijderen";
                $tekstinhoud = "U probeerde een onderwerp te verwijderen dat niet onder uw bevoegdheid valt.";
                 }


            $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
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
    $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
    $TBS->Show() ;
}
?>
