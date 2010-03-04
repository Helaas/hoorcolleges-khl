<?php
require_once('./../includes/kern.php');
session_start();
$fout = false;

if (isset($_GET["id"]) && is_numeric($_GET["id"]) && !heeftVBC($_GET["id"])) header("location: activeerVBC.php?id=".$_GET["id"]);
if(isset($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() >= 40 && isset($_GET["id"]) && is_numeric($_GET["id"]) && geeftLectorHoorcollege($_SESSION['gebruiker']->getIdGebruiker(), $_GET["id"])) { //lector is ingelogged
    $foutboodschap = "";
    if (isset($_POST["verzenden"])) {
        if (!isset($_POST["aantal"]) || !is_numeric($_POST["aantal"]) || empty($_POST["aantal"])|| $_POST["aantal"]<=0) $foutboodschap .= "- Het aantal logo's moet een numeriek getal zijn groter dan 0.\n";
        if (!isset($_POST["audio"]) || !is_numeric($_POST["audio"])) $foutboodschap .= "- U moet een kiezen of u geluidseffecten wenst of niet.\n";

        if (!isset($_POST["studentGeselecteerd"])) $_POST["studentGeselecteerd"] = array();

        wijzigVBC($_GET["id"],$_POST["aantal"],$_POST["audio"],$_POST["studentGeselecteerd"]);
        $nieuweID = $_GET["id"];
        $config["pagina"] = "./lector/wijzigingActivatieVBCOK.html";
        $TBS->LoadTemplate('./../html/lector/templateLector.html');


    } else if(isset($_POST["reset"])) {
        if($_POST["student"] == "alle") {
            vbcVanVolledigHoorcollegeResetten((int)$_GET["id"]);
            $config["pagina"] = "./lector/resetVbcStudentOk.html";
        }else {
            vbcBepaaldeStudentResetten($_POST["student"], (int)$_GET["id"]);
            $config["pagina"] = "./lector/resetVbcStudentOk.html";
        }

        $TBS->LoadTemplate('./../html/lector/templateLector.html');
    }else {
        if (strlen($foutboodschap)>0) {
            $fout =1;
        }
        $query = $db->Execute("SELECT VBC_aantal, VBC_geluid FROM hoorcollege_hoorcollege WHERE idHoorcollege = ".$_GET["id"]);
        $aantal = $query->fields['VBC_aantal'];
        $audioAan = $query->fields['VBC_geluid'];
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
        $TBS->MergeBlock("blk3",$db,"SELECT idGebruiker, naam, voornaam
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
