<?php
    require_once('./includes/kern.php');
    session_start();

    $TBS = new clsTinyButStrong;

    if(isset ($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 1){
        $config["pagina"] = "./student/resultaatStudent.html";
        $TBS->LoadTemplate('./html/student/templateStudent.html');
        $gebruikerID = $_SESSION['gebruiker']->getIdGebruiker();
        $resultaat = $db->GetRow('select * from hoorcollege_hoorcollege where idHoorcollege='.$_GET['hoorcollege']);
        $naamHoorcollege = $resultaat['naam'];

        $vragen = array();
        $idVraag;
        $resultaat = $db->Execute('SELECT * FROM hoorcollege_vraag WHERE Hoorcollege_idHoorcollege = '.$_GET['hoorcollege']);
        while (!$resultaat->EOF) {
                $idVraag = $resultaat->fields["idVraag"];
                $vragen[$resultaat->fields[$idVraag]]["vraagstelling"] =  $resultaat->fields["vraagstelling"];
                $vragen[$resultaat->fields[$idVraag]]["id"] =  $resultaat->fields["idVraag"];
                $vragen[$resultaat->fields[$idVraag]]["juistAntwoord"] =  $resultaat->fields["juistantwoord"];
                $resultaat->MoveNext();
            }

        $resultaat2 = $db->Execute('SELECT * FROM hoorcollege_gegevenantwoord WHERE Vraag_idVraag = '.$idVraag.'
             AND Gebruiker_idGebruiker = '.$gebruikerID);

            $gegevenAntwoord = $resultaat2->fields["MogelijkAntwoord_idMogelijkAntwoord"];
            $antwoordJuist = antwoordOk($gebruikerID, $idVraag);
            $vragen[$resultaat->fields[$idVraag]]["gegevenAntwoord"] = $gegevenAntwoord;
            $vragen[$resultaat->fields[$idVraag]]["juist"] = $antwoordJuist;

        $TBS->MergeBlock("blk1",$vragen);

    }else if(!isset ($_SESSION['gebruiker'])){
        header("location: login.php");
    }else{
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./html/template.html') ;
    }

    $TBS->Show();
?>
