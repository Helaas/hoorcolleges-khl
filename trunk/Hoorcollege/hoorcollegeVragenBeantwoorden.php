<?php
    include_once('./includes/kern.php');
    session_start();
    
    if(isset($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 1){ //student is ingelogged

     if (isset($_GET["hoorcollege"]) && heeftHoorcollegeVragen($_GET["hoorcollege"]) && magGebruikerVragenBeantwoorden($_SESSION['gebruiker']->getIdGebruiker(),$_GET["hoorcollege"]) && !heeftGebruikerVragenGemaakt($_SESSION['gebruiker']->getIdGebruiker(),$_GET["hoorcollege"])){

         if (!isset($_POST["submit"])){ //vragen tonen

            $config["pagina"] = "hoorcollegeVragenBeantwoorden.html";

            $hoorcollegeNaam = getHoorcollegeNaam($_GET["hoorcollege"]);

             $vraagids = "";
             $vragen = array();
             $resultaat = $db->Execute('SELECT * FROM hoorcollege_vraag where Hoorcollege_idHoorcollege='.((int)$_GET["hoorcollege"]));
             while (!$resultaat->EOF) {
                $vraagids .= $resultaat->fields["idVraag"].",";
                $vragen[$resultaat->fields["idVraag"]]["vraagstelling"] =  $resultaat->fields["vraagstelling"];
                $vragen[$resultaat->fields["idVraag"]]["id"] =  $resultaat->fields["idVraag"];
                $resultaat->MoveNext();
            }

            if (strlen($vraagids) > 0){
                $vraagids = substr_replace($vraagids,"",-1);
            }


             $resultaat = $db->Execute('SELECT * FROM hoorcollege_mogelijkantwoord where Vraag_idVraag in('.$vraagids.')');
             while (!$resultaat->EOF) {
                $vragen[$resultaat->fields["Vraag_idVraag"]]["mogelijkantwoorden"][] = array ("antwoord" => $resultaat->fields["antwoord"],
                   "id" => $resultaat->fields["idMogelijkAntwoord"] );
                $resultaat->MoveNext();
            }
            $TBS->LoadTemplate('./html/template.html');
            $TBS->MergeBlock("blk1",$vragen);
         } else { //vragen verwerken
            foreach ($_POST['antwoorden'] as $key => $value) {
                $db->Execute("INSERT INTO hoorcollege_gegevenantwoord (idGegevenAntwoord,Gebruiker_idGebruiker, Vraag_idVraag, MogelijkAntwoord_idMogelijkAntwoord) VALUES (NULL, '" . (int)$_SESSION['gebruiker']->getIdGebruiker() . "', '" . (int)$key . "', '" . (int)$value . "')");
            }
            $boodschap["reden"] = "Vragen beantwoorden";
            $boodschap["inhoud"] = "Uw antwoorden zijn met succes opgeslagen.";
            $boodschap["link"] = "index.php";
            $config["pagina"] = "./algemeneBoodschap.html";
            $TBS->LoadTemplate('./html/template.html');
         }
        } else {
            $fout["reden"] = "Geen vragen beschikbaar";
            $fout["inhoud"] = "U hebt de multiple choice vragen voor dit hoorcollege reeds ingevuld, er zijn geen vragen beschikbaar voor dit hoorcollege of u bent niet gemachtigd om de vragen van dit hoorcollege te bekijken.";
            $config["pagina"] = "./algemeneFout.html";
            $TBS->LoadTemplate('./html/template.html');
        }
    } else { //geen student / niet ingelogged
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./html/template.html');
    }

    $TBS->Show();

?>
