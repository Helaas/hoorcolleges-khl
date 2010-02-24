<?php
include_once('./../includes/kern.php');
session_start();


$config["pagina"] = "index.html";


if(isset($_SESSION['gebruiker'])) {
    //Bepalen welke template laden (afhankelijk van niveau gebruiker)
    if($_SESSION['gebruiker']->getNiveau() == 40) {

        $cat=$_POST['cat'];

        if(!preg_match('/^[a-z0-9\+\#\ ]+$/iD', $cat)) {
            $Titel="Foutmelding";
            $tekstinhoud="U heeft geen geldige categorie geselecteerd.";

            $config["pagina"] = "./lector/Boodschap.html";
            $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
            $TBS->Show() ;

        }
        else if($_POST['filetype']=='Audio' || $_POST['filetype']=='Video') {
            $config["pagina"] = "./FileUpload/UploadBestand.html";
            $TBS->LoadTemplate('./../html/lector/templateLector.html');
            $TBS->Show() ;
        }
        else {
            $config["pagina"] = "./FileUpload/UploadTekst.html";
            $TBS->LoadTemplate('./../html/lector/templateLector.html');
            $TBS->Show() ;
        }
    }
    ////Users met onvoldoende privileges voor deze pagina een foutpagina tonen
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
