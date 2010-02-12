<?php
include_once('./includes/TinyButStrong.php');
include_once('./includes/gebruiker.class.php');
$TBS = new clsTinyButStrong;

session_start();

$config["pagina"] = "AddFile.html";

 if(isset ($_SESSION['gebruiker'])) {
        $gebruiker = $_SESSION['gebruiker'];
        $gebruikerNiv = $gebruiker->getNiveau();

        if($gebruikerNiv==20){
        $TBS->LoadTemplate('./html/template.html') ;
        $TBS->Show();
        }
            else {
        $config["pagina"] = "Error1Login.html";
         $TBS->LoadTemplate('./html/template.html') ;
        $TBS->Show() ;
    }


    }

    else {
        $config["pagina"] = "Error1Login.html";
         $TBS->LoadTemplate('./html/template.html') ;
        $TBS->Show() ;
    }


?>
