<?php
require_once('./../includes/kern.php');
    session_start();
    $fout = false;

    if(isset($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 40 && isset($_GET["id"]) && is_numeric($_GET["id"]) && geeftLectorHoorcollege($_SESSION['gebruiker']->getIdGebruiker(), $_GET["id"])){ //lector is ingelogged

        if (isset($_POST["verzenden"])){
           
                wijzigVBC($_GET["id"], $_POST["studentGeselecteerd"]);
                $nieuweID = $_GET["id"];
                $config["pagina"] = "./lector/wijzigingActivatieVBCOK.html";
                $TBS->LoadTemplate('./../html/lector/templateLector.html');


        } else {
            $config["pagina"] = "./lector/wijzigActivatieVBC.html";
            $TBS->LoadTemplate('./../html/lector/templateLector.html');
            $TBS->MergeBlock("blk1",$db,"SELECT idGebruiker, naam, voornaam
                            FROM hoorcollege_gebruiker
                            WHERE idGebruiker
                                IN (
                                SELECT Gebruiker_idGebruiker
                                FROM hoorcollege_gebruikerhoorcollege
                                WHERE Hoorcollege_idHoorcollege = ".(int)$_GET["id"]."
                                ) order by naam, voornaam");
            $TBS->MergeBlock("blk2",$db,"SELECT idGebruiker, naam, voornaam
                                FROM hoorcollege_gebruiker
                                WHERE idGebruiker
                                    IN (
                                    SELECT Gebruiker_idGebruiker
                                    FROM hoorcollege_gebruikerhoorcollege
                                    WHERE Hoorcollege_idHoorcollege = ".(int)$_GET["id"]."
                                         AND VBCVerplicht = 1
                                    ) order by naam, voornaam");
        }
    } else {
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
    }
    $TBS->Show();

?>