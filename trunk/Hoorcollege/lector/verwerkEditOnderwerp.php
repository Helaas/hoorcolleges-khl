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

              if (preg_match('/^[0-9]+$/iD', $_POST['OndID']) && preg_match('/^[a-z0-9\+\#\ ]+$/iD', $_POST['veld1'])) {
                 //controle of het onderwerp wel bij de lector hoort
                 $result = $db->Execute("    select idOnderwerp
                                             from hoorcollege_onderwerp
                                             where Vak_idVak in (
                                                             SELECT Vak_idVak
                                                             FROM hoorcollege_gebruiker_beheert_vak
                                                             WHERE Gebruiker_idGebruiker=".(int)$gebruikerID.") AND idOnderwerp=".(int)$_POST['OndID']);

                 if($result->fields["idOnderwerp"]!=null) {
               //edit onderwerp indien lector rechten tot dit onderwerp heeft
                 editOnderwerp($_POST['OndID'],$_POST['veld1']);

                $config["pagina"] = "./Lector/Boodschap.html";
                $Titel="Onderwerp wijzigen";
                $tekstinhoud = "Het onderwerp werd met succes aangepast.";
                 }
                 else{
                $config["pagina"] = "./Lector/Boodschap.html";
                $Titel="Onderwerp wijzigen";
                $tekstinhoud = "U probeerde een onderwerp te wijzigen dat niet onder uw bevoegdheid valt.";
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
        $TBS->LoadTemplate('./../html/template.html') ;
        $TBS->Show() ;
    }

}
else {
    $config["pagina"] = "./FileUpload/Error1Login.html";
    $TBS->LoadTemplate('./../html/template.html') ;
    $TBS->Show() ;
}
?>
