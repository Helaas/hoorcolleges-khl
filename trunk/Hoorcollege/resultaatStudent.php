<?php
require_once('./includes/kern.php');
session_start();

$TBS = new clsTinyButStrong;

if(isset ($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 1) {
    //Controle van $_Get['hoorcollege']
    if(controleerNummer($_GET['hoorcollege'])) {
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

        $resultaat = $db->Execute('SELECT * FROM hoorcollege_gegevenantwoord WHERE Gebruiker_idGebruiker = '.$gebruikerID);
        while (!$resultaat->EOF) {
            $vragen[$resultaat->fields["Vraag_idVraag"]]["gegevenAntwoord"] = getAntwoord($resultaat->fields["MogelijkAntwoord_idMogelijkAntwoord"]);
            $vragen[$resultaat->fields["Vraag_idVraag"]]["juist"] = antwoordOk($gebruikerID, $resultaat->fields["Vraag_idVraag"]);
            $resultaat->MoveNext();
        }

        $TBS->MergeBlock("blk1",$vragen);
    }else {
        $config["pagina"] = "./ErrorInputAlgemeen.html";
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
