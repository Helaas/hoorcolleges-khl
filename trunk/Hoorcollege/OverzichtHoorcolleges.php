<?php

    include_once('./includes/kern.php');
    $config["pagina"] = "lector/OverzichtHoorcolleges.html";

    session_start();

if(isset ($_SESSION['gebruiker'])) {
        $gebruiker = $_SESSION['gebruiker'];
        $gebruikerNiv = $gebruiker->getNiveau();

        if($gebruikerNiv==40){
    $q = "select * from hoorcollege_vak where idVak in(SELECT Vak_idVak FROM `hoorcollege_gebruiker_beheert_vak` WHERE gebruiker_idgebruiker =".$_SESSION['gebruiker']->getIdGebruiker().")";
    $TBS->LoadTemplate('./html/lector/templateLector.html');
    $TBS->MergeBlock("blk1",$db,$q);
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
