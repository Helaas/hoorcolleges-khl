<?php
require_once('./includes/kern.php');
session_start();

$TBS = new clsTinyButStrong;

if(isset ($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 1) {
    if(validateNumber($_GET['hoorcollege'])) {
            if (heeftHoorcollegeVragen($_GET["hoorcollege"]) && heeftGebruikerVragenGemaakt($_SESSION['gebruiker']->getIdGebruiker(),$_GET["hoorcollege"])){
                $config["pagina"] = "./student/resultaatStudent.html";
                $TBS->LoadTemplate('./html/student/templateStudent.html');
                $gebruikerID = $_SESSION['gebruiker']->getIdGebruiker();
                $hoorcollegeInfo = $db->GetRow('select * from hoorcollege_hoorcollege where idHoorcollege='.$_GET['hoorcollege']);
                $naamHoorcollege = $hoorcollegeInfo['naam'];

                $vragen = array();
                $idVraag;
                //Alle vragen van het hoorcollege
                $alleVragenQuery = $db->Execute('SELECT * FROM hoorcollege_vraag WHERE Hoorcollege_idHoorcollege = '.$_GET['hoorcollege']);
                while (!$alleVragenQuery->EOF) {
                    $vragen[$alleVragenQuery->fields["idVraag"]]["vraagstelling"] =  $alleVragenQuery->fields["vraagstelling"];
                    $vragen[$alleVragenQuery->fields["idVraag"]]["id"] =  $alleVragenQuery->fields["idVraag"];
                    $vragen[$alleVragenQuery->fields["idVraag"]]["juistAntwoord"] = getAntwoord($alleVragenQuery->fields["juistantwoord"]);
                    $alleVragenQuery->MoveNext();
                }

                //Alle vragen van het hoorcollege die bij de gebruiker hoort
                $resultaat = $db->Execute('SELECT * FROM hoorcollege_gegevenantwoord 
                    LEFT OUTER JOIN hoorcollege_vraag ON Vraag_idVraag = idVraag 
                    WHERE Gebruiker_idGebruiker = '.$gebruikerID.' AND Hoorcollege_idHoorcollege ='.$_GET['hoorcollege']);
                while (!$resultaat->EOF) {
                    $vragen[$resultaat->fields["Vraag_idVraag"]]["gegevenAntwoord"] = getAntwoord($resultaat->fields["MogelijkAntwoord_idMogelijkAntwoord"]);
                    $vragen[$resultaat->fields["Vraag_idVraag"]]["juist"] = antwoordOk($gebruikerID, $resultaat->fields["Vraag_idVraag"]);
                    $resultaat->MoveNext();
                }

                $TBS->MergeBlock("blk1",$vragen);
            } else {
                $fout["reden"] = "Geen resultaten beschikbaar";
                $fout["inhoud"] = "U heeft nog geen vragen beantwoord voor dit hoorcollege, dit hoorcollege heeft geen vragen, of u mag de vragen niet bekijken.";
                $config["pagina"] = "./algemeneFout.html";
                $TBS->LoadTemplate('./html/student/templateStudent.html');
            }
    }else {
        $fout["reden"] = "Technische reden";
        $fout["inhoud"] = "Er is een technische fout opgetreden.";
        $config["pagina"] = "./algemeneFout.html";
        $TBS->LoadTemplate('./html/student/templateStudent.html') ;
    }
}else if(!isset ($_SESSION['gebruiker'])) {
    header("location: login.php");
}else {
    $config["pagina"] = "./FileUpload/Error1Login.html";
    $TBS->LoadTemplate('./html/template.html') ;
}

$TBS->Show();
?>
