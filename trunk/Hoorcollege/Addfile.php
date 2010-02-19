<?php
include_once('./includes/TinyButStrong.php');
include_once('./includes/Gebruiker.class.php');
$TBS = new clsTinyButStrong;

session_start();

$config["pagina"] = "./FileUpload/AddFile.html";

 if(isset ($_SESSION['gebruiker'])) {
        $gebruiker = $_SESSION['gebruiker'];
        $gebruikerNiv = $gebruiker->getNiveau();

        if($gebruikerNiv==40){
        $TBS->LoadTemplate('./html/lector/templateLector.html') ;
        $TBS->Show();
        }//Users met onvoldoende privileges voor deze pagina een foutpagina tonen
    else if($_SESSION['gebruiker']->getNiveau() == 1){
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./html/student/templateStudent.html');
         $TBS->Show() ;
    }else if($_SESSION['gebruiker']->getNiveau() == 99){
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
