<?php
require_once('./includes/kern.php');
session_start();

$TBS = new clsTinyButStrong;

if(isset ($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 1) {
    //Controle van $_Get['hoorcollege']
    if(ingevoerdNummerOk($_GET['hoorcollege'])) {
            if (heeftHoorcollegeVragen($_GET["hoorcollege"]) && magGebruikerVragenBeantwoorden($_SESSION['gebruiker']->getIdGebruiker(),$_GET["hoorcollege"])){
                $config["pagina"] = "./student/resultaatStudent.html";
                $TBS->LoadTemplate('./html/student/templateStudent.html');
                $gebruikerID = $_SESSION['gebruiker']->getIdGebruiker();
                $resultaat = $db->GetRow('select * from hoorcollege_hoorcollege where idHoorcollege='.$_GET['hoorcollege']);
                $naamHoorcollege = $resultaat['naam'];

                $vragen = array();
                $idVraag;
                $resultaat = $db->Execute('SELECT * FROM hoorcollege_vraag WHERE Hoorcollege_idHoorcollege = '.$_GET['hoorcollege']);
                while (!$resultaat->EOF) {
                    $vragen[$resultaat->fields["idVraag"]]["vraagstelling"] =  $resultaat->fields["vraagstelling"];
                    $vragen[$resultaat->fields["idVraag"]]["id"] =  $resultaat->fields["idVraag"];
                    $vragen[$resultaat->fields["idVraag"]]["juistAntwoord"] = getAntwoord($resultaat->fields["juistantwoord"]);
                    $resultaat->MoveNext();
                }

                //Join nodig voor wanneer de student zelf het hoorcollegeId verandert naar
                //een ander hoorcollegeId dat nog niet volledig gemaakt werd.
                //De student krijgt door de join  een lege tabel te zien
                $resultaat = $db->Execute('SELECT * FROM hoorcollege_gegevenantwoord
                                            LEFT OUTER JOIN hoorcollege_vraag ON Vraag_idVraag = idVraag
                                            WHERE Gebruiker_idGebruiker = '.$gebruikerID.'
                                            AND Hoorcollege_idHoorcollege = '.$_GET['hoorcollege']);
                while (!$resultaat->EOF) {
                    $vragen[$resultaat->fields["Vraag_idVraag"]]["gegevenAntwoord"] = getAntwoord($resultaat->fields["MogelijkAntwoord_idMogelijkAntwoord"]);
                    $vragen[$resultaat->fields["Vraag_idVraag"]]["juist"] = antwoordOk($gebruikerID, $resultaat->fields["Vraag_idVraag"]);
                    $resultaat->MoveNext();
                }

                $TBS->MergeBlock("blk1",$vragen);
            } else {
                $fout["reden"] = "Geen resultaten beschikbaar";
                $fout["inhoud"] = "Dit hoorcollege heeft geen vragen, of u mag de vragen niet bekijken.";
                $config["pagina"] = "./algemeneFout.html";
                $TBS->LoadTemplate('./html/template.html');
            }
    }else {
        $fout["reden"] = "Technische reden";
        $fout["inhoud"] = "Er is een technische fout opgetreden.";
        $config["pagina"] = "./algemeneFout.html";
        $TBS->LoadTemplate('./html/template.html') ;
    }
}else if(!isset ($_SESSION['gebruiker'])) {
    header("location: login.php");
}else {
    $config["pagina"] = "./FileUpload/Error1Login.html";
    $TBS->LoadTemplate('./html/template.html') ;
}

$TBS->Show();
?>
