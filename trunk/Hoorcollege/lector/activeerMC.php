<?php
    require_once('./../includes/kern.php');
    $TBS->NoErr = true;
    session_start();
    $fout = false;

    if(isset($_GET["reset"])) unset($_SESSION["vraag"]);
    if (!isset($_SESSION["vraag"])) $_SESSION["vraag"] = array();

    /**
     * Ajax functies
     */
    if(isset($_GET["actie"]) && $_GET["actie"] == "select" && isset($_GET["zetGeselecteerdVraag"]) && is_numeric($_GET["zetGeselecteerdVraag"]) && isset($_GET["zetGeselecteerdAnt"]) && is_numeric($_GET["zetGeselecteerdAnt"])){
        foreach($_SESSION["vraag"][$_GET["zetGeselecteerdVraag"]]["mogelijkantwoorden"] as &$waarde){
            $waarde["juist"] = 0;
        }
        $_SESSION["vraag"][$_GET["zetGeselecteerdVraag"]]["mogelijkantwoorden"][$_GET["zetGeselecteerdAnt"]]["juist"] = 1;
        if (!isset($_SESSION["anderAntwoord"])) $_SESSION["anderAntwoord"] = array();
        $_SESSION["anderAntwoord"][$_GET["zetGeselecteerdVraag"]] = $_GET["zetGeselecteerdAnt"];
        exit();
    }

    if(isset($_GET["actie"]) && $_GET["actie"] == "del" && isset($_GET["zetGeselecteerdVraag"]) && is_numeric($_GET["zetGeselecteerdVraag"]) && isset($_GET["zetGeselecteerdAnt"]) && is_numeric($_GET["zetGeselecteerdAnt"])){
        unset($_SESSION["vraag"][$_GET["zetGeselecteerdVraag"]]["mogelijkantwoorden"][$_GET["zetGeselecteerdAnt"]]);
        exit();
    }

    if(isset($_GET["actie"]) && $_GET["actie"] == "delVraag" && isset($_GET["zetGeselecteerdVraag"]) && is_numeric($_GET["zetGeselecteerdVraag"])){
        unset($_SESSION["vraag"][$_GET["zetGeselecteerdVraag"]]);
        exit();
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
                $config["pagina"] = "./lector/activeerMC.html";
                $TBS->LoadTemplate('./../html/lector/templateLector.html');
                $TBS->MergeBlock("blk1",$_SESSION["vraag"]);
            } else { //alles ok, inserten
                maakMCVragen($_GET["id"],$_SESSION["vraag"]);
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

            $config["pagina"] = "./lector/activeerMC.html";
            $TBS->LoadTemplate('./../html/lector/templateLector.html');
            $TBS->MergeBlock("blk1",$_SESSION["vraag"]);
        }
    } else {
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
    }
    $TBS->Show();

?>
