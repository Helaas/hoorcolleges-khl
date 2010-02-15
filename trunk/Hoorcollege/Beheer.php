<?php

    include_once('./includes/kern.php');
    $config["pagina"] = "lector/beheer.html";

    session_start();



    $q = "select * from hoorcollege_vak where idVak in(SELECT Vak_idVak FROM `hoorcollege_gebruiker_beheert_vak` WHERE gebruiker_idgebruiker =".$_SESSION['gebruiker']->getIdGebruiker().")";
    $TBS->LoadTemplate('./html/Lector/templateLector.html');
    $TBS->MergeBlock("blk1",$db,$q);
    $TBS->Show();
?>
