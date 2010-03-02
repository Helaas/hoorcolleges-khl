<?php
require_once('./../includes/kern.php');
    session_start();
    $fout = false;

    if(isset($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 40 && isset($_GET["id"]) && is_numeric($_GET["id"]) && geeftLectorHoorcollege($_SESSION['gebruiker']->getIdGebruiker(), $_GET["id"])){ //lector is ingelogged
        
        if (isset($_POST["verzenden"])){
            $foutboodschap = "";
            if (!isset($_POST["aantal"]) || !is_numeric($_POST["aantal"]) || empty($_POST["aantal"])|| $_POST["aantal"]<=0) $foutboodschap .= "- Het aantal logo's moet een numeriek getal zijn groter dan 0.\n";
            if (!isset($_POST["audio"]) || !is_numeric($_POST["audio"])) $foutboodschap .= "- U moet een kiezen of u geluidseffecten wenst of niet.\n";
            if (!isset($_POST["studentGeselecteerd"]) || !is_array($_POST["studentGeselecteerd"]) ||  count($_POST["studentGeselecteerd"])<=0) $foutboodschap .= "- U moet minstens één student selecteren.";

            if (strlen($foutboodschap)>0){
                $fout =1;
                $config["pagina"] = "./lector/activeerVBC.html";
                $TBS->LoadTemplate('./../html/lector/templateLector.html');
                $TBS->MergeBlock("blk1",$db,"SELECT idGebruiker, naam, voornaam
                                FROM hoorcollege_gebruiker
                                WHERE idGebruiker
                                    IN (
                                    SELECT Gebruiker_idGebruiker
                                    FROM hoorcollege_gebruikerhoorcollege
                                    WHERE Hoorcollege_idHoorcollege = ".(int)$_GET["id"]."
                                    ) order by naam, voornaam");

            } else {
                maakVBC($_GET["id"],$_POST["aantal"],$_POST["audio"],$_POST["studentGeselecteerd"]);
                $nieuweID = $_GET["id"];
                $config["pagina"] = "./lector/activeerVBCOK.html";
                $TBS->LoadTemplate('./../html/lector/templateLector.html');
            }
            
            
        } else {
            $config["pagina"] = "./lector/activeerVBC.html";
            $TBS->LoadTemplate('./../html/lector/templateLector.html');
            $TBS->MergeBlock("blk1",$db,"SELECT idGebruiker, naam, voornaam
                            FROM hoorcollege_gebruiker
                            WHERE idGebruiker
                                IN (
                                SELECT Gebruiker_idGebruiker
                                FROM hoorcollege_gebruikerhoorcollege
                                WHERE Hoorcollege_idHoorcollege = ".(int)$_GET["id"]."
                                ) order by naam, voornaam");
        }
    } else {
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
    }
    $TBS->Show();

?>
