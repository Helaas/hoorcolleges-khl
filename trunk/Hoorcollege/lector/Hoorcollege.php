<?php

include_once('./../includes/kern.php');
session_start();

if(isset($_SESSION['gebruiker']) &&  $_SESSION['gebruiker']->getNiveau() == 40) {

    //validateNumber controleert of de variabele gevuld is + numeriek is
    if (validateNumber($_GET["hoorcollege"]) && magGebruikerHoorcollegeZien($_SESSION['gebruiker']->getIdGebruiker(),$_GET["hoorcollege"])) {

        //pagina ingeladen dus het hoorcollege is al "bekeken"
        zetHoorcollegeBekeken($_SESSION['gebruiker']->getIdGebruiker(),$_GET["hoorcollege"]);

        /**
         * Hoorcollege informatie algemeen
         */

        $hoorcolInfo = getHoorcollegeInformatie($_GET["hoorcollege"]);
        $hoorcolInfo["VBC_geluid"] = $hoorcolInfo["VBC_geluid"] == "1" ? "true" : "false"; //ik wil letterlijk de strings
        $hoorcolInfo["heeftVragen"] = heeftHoorcollegeVragen($_GET["hoorcollege"])  == true ? "true" : "false"; //ik wil letterlijk de strings
        $hoorcolInfo["heeftVBC"] = heeftHoorcollegeVBC($_SESSION['gebruiker']->getIdGebruiker(),$_GET["hoorcollege"]) == "1" ? "true" : "false"; //ik wil letterlijk de strings
        $hoorcolInfo["gebruiker"] = (int)$_SESSION['gebruiker']->getIdGebruiker();

        /**
         * De items in dit hoorcollege
         */

        //toegestande types
        $video = false;
        $audio = false;
        $tekst = false;

        $items = getHoorcollegeBibliotheekitems($_GET["hoorcollege"]);


        if (isset($items["flv"])) $video = true;
        if (isset($items["mp3"])) $audio = true;
        if (isset($items["txt"])) $tekst = true;


        $config["pagina"] = "./hoorcollege/templateVoorLector.html";
       $TBS->LoadTemplate('./html/lector/templateLectorVoorHoorcollege.html');

        /**
         * Hoorcollege reacties bekijken + plaatsen
         */

        $idHoorcollege = $_GET["hoorcollege"];
        $gebruikersNaam = getGebruikerNaamViaId($_SESSION['gebruiker']->getIdGebruiker());
        $alleCommentarenQuery = $db->Execute('SELECT voornaam, naam, inhoud, datum
                                     FROM hoorcollege_reactie LEFT OUTER JOIN hoorcollege_gebruiker
                                     ON Gebruiker_idGebruiker = idGebruiker
                                     WHERE Hoorcollege_idHoorcollege ='.$idHoorcollege);

        //Noodzakelijk voor het weglaten van de slahes in de inhoud
        $alleCommentarenTabel = array();
        $i = 0;
        while(!$alleCommentarenQuery->EOF){
          $alleCommentarenTabel[$i]["voornaam"] = $alleCommentarenQuery->fields["voornaam"];
          $alleCommentarenTabel[$i]["naam"] = $alleCommentarenQuery->fields["naam"];
          $alleCommentarenTabel[$i]["inhoud"] = stripslashes($alleCommentarenQuery->fields["inhoud"]);
          $alleCommentarenTabel[$i]["datum"] = $alleCommentarenQuery->fields["datum"];
          $i++;
          $alleCommentarenQuery->MoveNext();
        }

        $TBS->MergeBlock('blk1', $alleCommentarenTabel);

    } else {
        $fout["reden"] = "Geen hoorcollege beschikbaar";
        $fout["inhoud"] = "U beschikt over onvoldoende rechten om dit hoorcollege te bekijken.";
        $config["pagina"] = "./algemeneFout.html";
        $TBS->LoadTemplate('./html/lector/templateLectorVoorHoorcollege.html');
    }

} else { //geen student / niet ingelogged
    $config["pagina"] = "./FileUpload/Error1Login.html";
    $TBS->LoadTemplate('./html/lector/templateLectorVoorHoorcollege.html');
}

$TBS->Show();


?>
