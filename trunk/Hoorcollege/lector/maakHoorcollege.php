<?php
    require_once('./../includes/kern.php');
    session_start();
getStudentenVoorvak(2,2);

    if(isset($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 40){ //lector is ingelogged
        if (isset($_POST["verzenden"])){
            print_r($_POST);
        }
        $beschikbareGroepen = getGroepenVoorvak(2);
        $config["pagina"] = "./lector/maakHoorcollege.html";
        $q = "select * from hoorcollege_vak where idVak in(SELECT Vak_idVak FROM hoorcollege_gebruiker_beheert_vak WHERE gebruiker_idgebruiker =".$_SESSION['gebruiker']->getIdGebruiker().")";
        $TBS->LoadTemplate('./../html/lector/templateLector.html');
        $TBS->MergeBlock("blk1",$db,$q);
    } else {
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
    }
    $TBS->Show();
?>
