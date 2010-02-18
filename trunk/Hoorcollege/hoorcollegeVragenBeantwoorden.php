<?php
    include_once('./includes/kern.php');
    session_start();

    if(isset($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 1){ //student is ingelogged

     if (isset($_GET["hoorcollege"]) && heeftHoorcollegeVragen($_GET["hoorcollege"]) && !heeftGebruikerVragenGemaakt($_SESSION['gebruiker']->getIdGebruiker(),$_GET["hoorcollege"])){

         if (!isset($_POST["submit"])){ //vragen tonen

            $config["pagina"] = "hoorcollegeVragenBeantwoorden.html";

            $hoorcollegeNaam = getHoorcollegeNaam($_GET["hoorcollege"]);

            $vragen = array();
             $resultaat = $db->Execute('SELECT * FROM hoorcollege_vraag');
             while (!$resultaat->EOF) {
                $vragen[$resultaat->fields["idVraag"]]["vraagstelling"] =  $resultaat->fields["vraagstelling"];
                $vragen[$resultaat->fields["idVraag"]]["id"] =  $resultaat->fields["idVraag"];
                $resultaat->MoveNext();
            }

             $resultaat = $db->Execute('SELECT * FROM hoorcollege_mogelijkantwoord');
             while (!$resultaat->EOF) {
                $vragen[$resultaat->fields["Vraag_idVraag"]]["mogelijkantwoorden"][] = array ("antwoord" => $resultaat->fields["antwoord"],
                   "id" => $resultaat->fields["idMogelijkAntwoord"] );
                $resultaat->MoveNext();
            }
            $TBS->LoadTemplate('./html/template.html');
            $TBS->MergeBlock("blk1",$vragen);
         } else { //vragen verwerken
            //TODO: De antwoorden inserten in de databank

            $fout["reden"] = "Nog niet geïmplementeerd";
            $fout["inhoud"] = "Vragen inserten we hier";
            $config["pagina"] = "./algemeneFout.html";
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
