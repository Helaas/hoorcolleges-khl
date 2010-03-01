<?php
    require_once('./../includes/kern.php');
    //$TBS->NoErr = true;
    session_start();
    $fout = false;

if(isset($_GET["reset"])) unset($_SESSION["vraag"]);
if (!isset($_SESSION["vraag"])) $_SESSION["vraag"] = array();

    if(isset($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 40 && isset($_GET["id"]) && is_numeric($_GET["id"]) && geeftLectorHoorcollege($_SESSION['gebruiker']->getIdGebruiker(), $_GET["id"])){ //lector is ingelogged
        $foutboodschap = "";
        if (isset($_POST["nieuwevraag"])){
            if (empty($_POST["vraag"])){
                $fout = true;
                $foutboodschap = "De vraag mag niet leeg zijn";
            } else {
                $id = count($_SESSION["vraag"]);
                $_SESSION["vraag"][$id]["vraagstelling"] = $_POST["vraag"];
                $_SESSION["vraag"][$id]["id"] = $id;
            }

        }

        print_r($_POST);
        if (isset($_POST["nieuwant"])){
            foreach ($_POST["ant"] as $sleutel => $value) {
                if (!empty($value)){
                    @$_SESSION["vraag"][$sleutel]["mogelijkantwoorden"][] = array ("antwoord" => $value,
                                                            "id" => count($_SESSION["vraag"][$sleutel]["mogelijkantwoorden"]) );
                }
            }
        }

        $vragen = array();
        
        /**if (isset($_SESSION["vraag"])){
            foreach ($_SESSION["vraag"] as $sleutel => $value) {
                $vragen[$sleutel]["id"] = $sleutel;
                $vragen[$sleutel]["vraagstelling"] = $value["vraag"];
                foreach ($value["mogelijkantwoorden"] as $sleutel2 => $value2){
                    echo $sleutel2 ." " . $value2["antwoord"] . " " . $value2["id"] . "<br />";
                }
            }
        }**/

        echo "<pre>";
            print_r($_SESSION["vraag"]);
        echo "</pre>";
            //$vragen[1]["mogelijkantwoorden"][] = array ("antwoord" => "test",
                 //  "id" => "1" );
        
        $config["pagina"] = "./lector/activeerMC.html";
        $TBS->LoadTemplate('./../html/lector/templateLector.html');
        $TBS->MergeBlock("blk1",$_SESSION["vraag"]);

    } else {
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
    }
    $TBS->Show();

?>
