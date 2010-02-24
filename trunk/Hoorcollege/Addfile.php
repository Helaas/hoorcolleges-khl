<?php
include_once('./includes/TinyButStrong.php');
include_once('./includes/Gebruiker.class.php');
include_once('./includes/kern.php');
$TBS = new clsTinyButStrong;

session_start();

$config["pagina"] = "./FileUpload/AddFile.html";

if(isset ($_SESSION['gebruiker'])) {
    $gebruiker = $_SESSION['gebruiker'];
    $gebruikerNiv = $gebruiker->getNiveau();

    if($gebruikerNiv==40) {
        //Enkel getallen mogen hier binnen
        if (preg_match('/^[0-9]+$/iD', $_SESSION['gebruiker']->getIdGebruiker())) {
            $q = "select * from hoorcollege_bibliotheekcategorie WHERE Gebruiker_idGebruiker =".$_SESSION['gebruiker']->getIdGebruiker();
            $TBS->LoadTemplate('./html/lector/templateLector.html');
            $TBS->MergeBlock("blk1",$db,$q);
            $TBS->Show();
        }
        else {
            //Geen Speciale Tekens toegestaan
            $Titel="Foutmelding";
            $tekstinhoud = "Onderwerpen konden niet opgevraagd worden, probeer het later opnieuw.";
            $config["pagina"] = "./lector/Boodschap.html";
            $TBS->LoadTemplate('./html/lector/templateLector.html') ;
            $TBS->Show() ;
        }
    //Users met onvoldoende privileges voor deze pagina een foutpagina tonen
    }else if($_SESSION['gebruiker']->getNiveau() == 1) {
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./html/student/templateStudent.html');
        $TBS->Show() ;
    }else if($_SESSION['gebruiker']->getNiveau() == 99) {
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./html/admin/templateAdmin.html');
        $TBS->Show() ;
    }

    else {
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./html/template.html') ;
        $TBS->Show() ;
    }

}
else {
    $config["pagina"] = "./FileUpload/Error1Login.html";
    $TBS->LoadTemplate('./html/template.html') ;
    $TBS->Show() ;
}

?>
