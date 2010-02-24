<!-- Php file die instaat voor het uploaden van het bestand -->
<?php
include_once('./../includes/kern.php');
session_start();


$config["pagina"] = "./FileUpload/FileAdded.html";
$testinhoud = "";

if(isset ($_SESSION['gebruiker'])) {
    $gebruiker = $_SESSION['gebruiker'];
    $gebruikerNiv = $gebruiker->getNiveau();
    $gebruikerID = $gebruiker->getIdGebruiker();





    if($gebruikerNiv==40 ) {
        //Folder mag geen speciale tekens zoals een punt bevatten, anders zou een vak als .NET bvb een hidden folder aanmaken
        if (preg_match('/^[a-z0-9\+\#\ ]+$/iD', $_POST['cat']) && preg_match('/^[0-9]+$/iD', $gebruikerID) && preg_match('/^[a-z0-9\+\#\ ]+$/iD', $_POST['bestandsnaam'])) {
            $testinhoud="Het tekstbestand ".'"'.$_POST['bestandsnaam'].'"'." werd geüpload!";
            $db->Execute("INSERT INTO hoorcollege_bibliotheekitem (mimetype, beschrijving,  naam, BibliotheekCategorie_idBibliotheekCategorie, BibliotheekCategorie_Gebruiker_idGebruiker, tekst) VALUES ('txt', '".addslashes($_POST['rte1'])."', '".$_POST['bestandsnaam']."','".$_POST['cat']."','".$gebruikerID."', '".addslashes($_POST['rte2'])."')");


            $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
            $TBS->Show() ;
        }
        else {
            if(!preg_match('/^[a-z0-9\+\#\ ]+$/iD', $_POST['cat'])) {
                $Titel="Foutmelding";
                $tekstinhoud="U heeft geen categorie geselecteerd.";

                $config["pagina"] = "./lector/Boodschap.html";
                $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
                $TBS->Show() ;
            }
            else {
                $config["pagina"] = "./FileUpload/Error2Input.html";
                $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
                $TBS->Show() ;
            }
        }
    }

    //Users met onvoldoende privileges voor deze pagina een foutpagina tonen
    else if($_SESSION['gebruiker']->getNiveau() == 1) {
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./../html/student/templateStudent.html');
        $TBS->Show() ;
    }else if($_SESSION['gebruiker']->getNiveau() == 99) {
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./../html/admin/templateAdmin.html');
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
