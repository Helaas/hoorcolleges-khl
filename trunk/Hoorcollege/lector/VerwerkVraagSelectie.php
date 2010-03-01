<?php
include_once('./../includes/kern.php');
session_start();

//php file voor gegevens uit de db te halen en als xml terug te sturen,
//om zo de dropdown met onderwerpen uit Beheer en Overzichthoorcolleges op te vullen

if(isset ($_SESSION['gebruiker'])) {
    $gebruiker = $_SESSION['gebruiker'];
    $gebruikerNiv = $gebruiker->getNiveau();

    if($gebruikerNiv==40) {
        $hid=(int)$_GET["gevraagdhoorcoll"];
                 $result = $db->Execute("
                                        select Hoorcollege_idHoorcollege
                                        from hoorcollege_onderwerphoorcollege
                                        where Onderwerp_idOnderwerp in (
                                                       select idOnderwerp
                                                       from hoorcollege_onderwerp
                                                       where Vak_idVak in (
                                                             SELECT Vak_idVak
                                                             FROM hoorcollege_gebruiker_beheert_vak
                                                             WHERE Gebruiker_idGebruiker=".(int)$gebruiker->getIdGebruiker().")) AND Hoorcollege_idHoorcollege=".$hid);

          if($result->fields["Hoorcollege_idHoorcollege"]!=null) {
        //aangeven dat het antwoord een xml bestand is
        header("Content-type: text/xml");

        //Enkel getallen mogen hier binnen
        if (preg_match('/^[0-9]+$/iD', $hid)) {

             $vragen = $db->Execute("SELECT * FROM hoorcollege_vraag WHERE Hoorcollege_idHoorcollege=".$hid);

        }

        //xml file aanmaken
        $xml_file  = "<?xml version=\"1.0\"?>";
        $xml_file .= "<root>";

        while(!$vragen->EOF){


            $id= (int)$vragen->fields["idVraag"];


            $xml_file .= "<Vraag>";
            $xml_file .="<Vraagstelling>";
            $xml_file .=$vragen->fields["vraagstelling"];
            $xml_file .="</Vraagstelling>";
            $xml_file .="<Vraagid>";
            $xml_file .=$id;
            $xml_file .="</Vraagid>";


            $JuistAntwoordQuery= $db->Execute("select * from hoorcollege_mogelijkantwoord where idMogelijkAntwoord in (select juistantwoord from hoorcollege_vraag WHERE Hoorcollege_idHoorcollege='$hid' AND idVraag='$id')");

            $xml_file .= "<JuistAntwoord>".$JuistAntwoordQuery->fields["antwoord"]."</JuistAntwoord>";
            $xml_file .= "<JuistAntwoordId>".$JuistAntwoordQuery->fields["idMogelijkAntwoord"]."</JuistAntwoordId>";


              $mogAntwoorden = array();
                $idVraag;
                //select vragen van het gekozen hoorcollege
                $alleAntwQuery = $db->Execute('SELECT * FROM hoorcollege_mogelijkantwoord WHERE Vraag_idVraag='.$id.' and idMogelijkAntwoord not in( select idMogelijkAntwoord from hoorcollege_mogelijkantwoord where idMogelijkAntwoord in (select juistantwoord from hoorcollege_vraag WHERE Hoorcollege_idHoorcollege='.$hid.' AND idVraag='.$id.'))');
                $tell1=1;

               $xml_file .= "<rootantwoorden>";
                while (!$alleAntwQuery->EOF) {

                     $xml_file .= "<antwoord>".$alleAntwQuery->fields["antwoord"]."</antwoord>";
                     $xml_file .= "<antwoordid>".$alleAntwQuery->fields["idMogelijkAntwoord"]."</antwoordid>";
                    $alleAntwQuery->MoveNext();
                    $tell1++;
                }

                 $xml_file .= "</rootantwoorden>";



            $xml_file .= "</Vraag>";

            $vragen->MoveNext();
        

    }
            $xml_file .= "</root>";
           echo $xml_file;
    }
}
}
?>