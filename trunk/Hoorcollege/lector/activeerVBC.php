<?php
require_once('./../includes/kern.php');
    session_start();
    $fout = false;

    if(isset($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 40 && isset($_GET["id"]) && is_numeric($_GET["id"]) && geeftLectorHoorcollege($_SESSION['gebruiker']->getIdGebruiker(), $_GET["id"])){ //lector is ingelogged
            $config["pagina"] = "./lector/activeerVBC.html";
            $TBS->LoadTemplate('./../html/lector/templateLector.html');
            $TBS->MergeBlock("blk1",$db,"SELECT idGebruiker, naam, voornaam
                            FROM hoorcollege_gebruiker
                            WHERE idGebruiker
                                IN (
                                SELECT Gebruiker_idGebruiker
                                FROM hoorcollege_gebruikerhoorcollege
                                WHERE Hoorcollege_idHoorcollege = ".(int)$_GET["id"]."
                                )");
    } else {
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
    }
    $TBS->Show();

?>
