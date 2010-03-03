<?php
    require_once('./../includes/kern.php');
    $TBS->NoErr = true;
    session_start();
    $fout = false;

   /** echo "<pre>";
    print_r($_SESSION["vraag"]);
echo "</pre>";**/
    if(isset($_GET["reset"])) unset($_SESSION["vraag"]);
    if (!isset($_SESSION["vraag"])){ //hier komt de magie
        $_SESSION["vraag"] = array();
        $resultaat = $db->Execute('SELECT idVraag, vraagstelling, juistantwoord
                                    FROM hoorcollege_vraag
                                    WHERE Hoorcollege_idHoorcollege = ' . (int)$_GET["id"]);
        while (!$resultaat->EOF) {
            $_SESSION["vraag"][$resultaat->fields["idVraag"]]["vraagstelling"] = $resultaat->fields["vraagstelling"];

            $resultaatAntwoord = $db->Execute('SELECT idMogelijkAntwoord, antwoord
                                                FROM hoorcollege_mogelijkantwoord
                                                WHERE Vraag_idVraag =' . $resultaat->fields["idVraag"]);
             while (!$resultaatAntwoord->EOF) {
                 $juist = 0;
                 if ($resultaat->fields["juistantwoord"] == $resultaatAntwoord->fields["idMogelijkAntwoord"]) $juist = 1;
                 $_SESSION["vraag"][$resultaat->fields["idVraag"]]["mogelijkantwoorden"][$resultaatAntwoord->fields["idMogelijkAntwoord"]] =
                                                                                array ("antwoord" => $resultaatAntwoord->fields["antwoord"],
                                                                                "juist" => $juist);
                 $resultaatAntwoord->MoveNext();
             }

            $resultaat->MoveNext();
        }
    }
    
    /**
     * Eigenlijke pagina
     */
    if(isset($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 40 && isset($_GET["id"]) && is_numeric($_GET["id"]) && geeftLectorHoorcollege($_SESSION['gebruiker']->getIdGebruiker(), $_GET["id"])){ //lector is ingelogged
        $foutboodschap = "";
        //evalueren en indien juist opslaan
        if (isset($_POST["opslaan"])){
            $foutboodschap = "";

            if (count($_SESSION["vraag"])<=0){
                $foutboodschap .= "- U moet ten minste één vraag opgeven";
            }

            $heeftAllesEenAntwoord = true;
            foreach ($_SESSION["vraag"] as $waarde){
                $juist = false;
                if (!isset($waarde["mogelijkantwoorden"]) || !is_array($waarde["mogelijkantwoorden"])){$heeftAllesEenAntwoord = false; break; }
                foreach ($waarde["mogelijkantwoorden"] as $waarde2 ){
                    if($waarde2["juist"] == "1"){
                        $juist = true;
                        break;
                    }
                }
                if (!$juist){
                    $heeftAllesEenAntwoord = false;
                    break;
                }
            }

            if (!$heeftAllesEenAntwoord){
                    $foutboodschap .= "- Elke vraag moet minstens één mogelijk antwoord hebben, waarvan er één antwoord moet geselecteerd worden als het juiste antwoord. Selecteer het bolletje naast een mogelijk antwoord om het te markeren als het juiste antwoord.";
            }

            if (strlen($foutboodschap)>0) { //fouten gevonden
                $fout = true;
                $config["pagina"] = "./lector/wijzigMC.html";
                $TBS->LoadTemplate('./../html/lector/templateLector.html');
                $TBS->MergeBlock("blk1",$_SESSION["vraag"]);
            } else { //alles ok, inserten
                wijzigMCVragen($_GET["id"],$_SESSION["vraag"]);
                unset($_SESSION["vraag"]);
                $nieuweID = $_GET["id"];
                $config["pagina"] = "./lector/activeerMCOK.html";
                $TBS->LoadTemplate('./../html/lector/templateLector.html');
            }

        } else { //vragen en stuff kunnen toevoegen
            if (isset($_POST["nieuwevraag"])){
                if (empty($_POST["vraag"])){
                    $fout = true;
                    $foutboodschap = "- De vraag mag niet leeg zijn";
                } else {
                    $id = count($_SESSION["vraag"]);
                    $_SESSION["vraag"][$id]["vraagstelling"] = $_POST["vraag"];
                }

            }

            if (isset($_POST["nieuwant"])){
                foreach ($_POST["ant"] as $sleutel => $value) {
                    if (!empty($value)){
                        @$_SESSION["vraag"][$sleutel]["mogelijkantwoorden"][] = array ("antwoord" => $value,
                                                                "juist" => "0");
                    }
                }
            }

            $config["pagina"] = "./lector/wijzigMC.html";
            $TBS->LoadTemplate('./../html/lector/templateLector.html');
            $TBS->MergeBlock("blk1",$_SESSION["vraag"]);
        }
    } else {
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
    }
    $TBS->Show();
