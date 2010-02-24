<?php
// && heeftHoorcollegeVragen($_GET["hoorcollege"]) && magGebruikerVragenBeantwoorden($_SESSION['gebruiker']->getIdGebruiker(),$_GET["hoorcollege"])
    require("./includes/kern.php");
    session_start();

    if(isset($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 1){ //student is ingelogged

        if (isset($_GET["hoorcollege"]) && is_numeric($_GET["hoorcollege"]) && magGebruikerHoorcollegeZien($_SESSION['gebruiker']->getIdGebruiker(),$_GET["hoorcollege"])){

            //pagina ingeladen dus het hoorcollege is al "bekeken"
            zetHoorcollegeBekeken($_SESSION['gebruiker']->getIdGebruiker(),$_GET["hoorcollege"]);

            $hoorcolInfo = getHoorcollegeInformatie($_GET["hoorcollege"]);
            $hoorcolInfo["VBC_aantal"] = $hoorcolInfo["VBC_aantal"] == "1" ? "true" : "false"; //ik wil letterlijk de strings
            $hoorcolInfo["VBC_geluid"] = $hoorcolInfo["VBC_geluid"] == "1" ? "true" : "false"; //ik wil letterlijk de strings
            $hoorcolInfo["heeftVragen"] = heeftHoorcollegeVragen($_GET["hoorcollege"])  == true ? "true" : "false"; //ik wil letterlijk de strings
            $hoorcolInfo["heeftVBC"] = heeftHoorcollegeVBC($_SESSION['gebruiker']->getIdGebruiker(),$_GET["hoorcollege"]) == "1" ? "true" : "false"; //ik wil letterlijk de strings
            

            $config["pagina"] = "./hoorcollege/temp.html";
            $TBS->LoadTemplate('./html/student/templateStudent.html');
        } else {
            $fout["reden"] = "Geen hoorcollege beschikbaar";
            $fout["inhoud"] = "U beschikt over onvoldoende rechten om dit hoorcollege te bekijken.";
            $config["pagina"] = "./algemeneFout.html";
            $TBS->LoadTemplate('./html/student/templateStudent.html');
        }

    } else { //geen student / niet ingelogged
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./html/template.html');
    }

    $TBS->Show();


?>
