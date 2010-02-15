<?php
require_once('./includes/kern.php');

session_start();

$config["pagina"] = "./lector/lector.html";

if(isset ($_SESSION['gebruiker'])) {
        $gebruiker = $_SESSION['gebruiker'];
        $gebruikerNiv = $gebruiker->getNiveau();

        if($gebruikerNiv==40){
        $TBS->LoadTemplate('./html/lector/templateLector.html') ;
        $TBS->Show();
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
