<?php
    require_once('./../includes/kern.php');
    session_start();
    if(isset($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() >= 40) { //lector is ingelogged
        $config["pagina"] = "./lector/pasBibliotheekItemsAan.html";
        $TBS->LoadTemplate('./../html/lector/templateLector.html');
        $TBS->Show();
    } else {
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
        $TBS->Show() ;
    }

?>
