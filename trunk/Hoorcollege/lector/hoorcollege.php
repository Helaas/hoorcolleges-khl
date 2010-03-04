<?php
// && heeftHoorcollegeVragen($_GET["hoorcollege"]) && magGebruikerVragenBeantwoorden($_SESSION['gebruiker']->getIdGebruiker(),$_GET["hoorcollege"])
include_once('./../includes/kern.php');
session_start();

if(isset($_SESSION['gebruiker']) && ($_SESSION['gebruiker']->getNiveau() >= 40)) { //student is ingelogged

   $result = $db->Execute("
                                        select Hoorcollege_idHoorcollege
                                        from hoorcollege_onderwerphoorcollege
                                        where Onderwerp_idOnderwerp in (
                                                       select idOnderwerp
                                                       from hoorcollege_onderwerp
                                                       where Vak_idVak in (
                                                             SELECT Vak_idVak
                                                             FROM hoorcollege_gebruiker_beheert_vak
                                                             WHERE Gebruiker_idGebruiker=".(int)$_SESSION['gebruiker']->getIdGebruiker().")) AND Hoorcollege_idHoorcollege=".(int)$_GET['hoorcollege']);

                 if($result->fields["Hoorcollege_idHoorcollege"]!=null) {
                //lector heeft rechten tot het hoorcollege

        /**
         * Hoorcollege informatie algemeen
         */

        $hoorcolInfo = getHoorcollegeInformatie($_GET["hoorcollege"]);
//        $hoorcolInfo["VBC_geluid"] = $hoorcolInfo["VBC_geluid"] == "1" ? "true" : "false";
//        $hoorcolInfo["heeftVragen"] = heeftHoorcollegeVragen($_GET["hoorcollege"])  == true ? "true" : "false";
//        $hoorcolInfo["heeftVBC"] = "false";
        $hoorcolInfo["VBC_geluid"] = "false";
        $hoorcolInfo["heeftVragen"] = "false";
        $hoorcolInfo["heeftVBC"] = "false";
        $hoorcolInfo["gebruiker"] = (int)$_SESSION['gebruiker']->getIdGebruiker();
        $hoorcolInfo["url"] = "http://".str_replace("lector/".basename($_SERVER["PHP_SELF"]), "", $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"]);

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


        $config["pagina"] = "./hoorcollege/templatelector.html";


            $TBS->LoadTemplate('./../html/lector/templateLector.html');


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
         $TBS->LoadTemplate('./../html/lector/hoorcollegeTemplate.html');
    }

} else { //geen student / niet ingelogged
    $config["pagina"] = "./FileUpload/Error1Login.html";
      $TBS->LoadTemplate('./../html/lector/hoorcollegeTemplate.html');
}

$TBS->Show();


?>
