<?php
    require_once('./../includes/kern.php');
    session_start();

    if(isset($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 40){ //lector is ingelogged
        $config["pagina"] = "./lector/maakHoorcollege.html";
        $q = "select * from hoorcollege_vak where idVak in(SELECT Vak_idVak FROM `hoorcollege_gebruiker_beheert_vak` WHERE gebruiker_idgebruiker =".$_SESSION['gebruiker']->getIdGebruiker().")";
        $TBS->LoadTemplate('./../html/lector/templateLector.html');
        $TBS->MergeBlock("blk1",$db,$q);
    } else {
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
    }
    $TBS->Show();
?>
