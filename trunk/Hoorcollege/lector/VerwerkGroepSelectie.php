<?php
include_once('./../includes/kern.php');
session_start();

//php file voor gegevens uit de db te halen en als xml terug te sturen,
//om zo de dropdown met onderwerpen uit Beheer en Overzichthoorcolleges op te vullen

if(isset ($_SESSION['gebruiker'])) {
    $gebruiker = $_SESSION['gebruiker'];
    $gebruikerNiv = $gebruiker->getNiveau();

    if($gebruikerNiv==40) {

        //aangeven dat het antwoord een xml bestand is
        header("Content-type: text/xml");

        //Enkel getallen mogen hier binnen
        if (preg_match('/^[0-9]+$/iD', $_GET["gevraagdGroep"]) &&preg_match('/^[0-9]+$/iD', $_GET["gevraagdhoorcoll"])) {

            $studenten = $db->Execute("SELECT Gebruiker_idGebruiker FROM hoorcollege_gebruikergroep WHERE Gebruiker_idGebruiker in (SELECT Gebruiker_idGebruiker FROM hoorcollege_gebruikerhoorcollege WHERE hoorcollege_idHoorcollege =".(int)$_GET["gevraagdhoorcoll"].") and Groep_idGroep =".(int)$_GET["gevraagdGroep"]);
        }

        //xml file aanmaken
        $xml_file  = "<?xml version=\"1.0\"?>";
        $xml_file .= "<root>";

        
        while(!$studenten->EOF){
            $xml_file .= "<student>";

            $naamQuery= $db->Execute('SELECT naam,voornaam FROM hoorcollege_gebruiker WHERE idGebruiker='.(int)$studenten->fields["Gebruiker_idGebruiker"]);
            $xml_file .= "<naam>".$naamQuery->fields["voornaam"]." ".$naamQuery->fields["naam"]."</naam>";


              $vragen = array();
                $idVraag;
                //select vragen van het gekozen hoorcollege
                $alleVragenQuery = $db->Execute('SELECT * FROM hoorcollege_vraag WHERE Hoorcollege_idHoorcollege = '.(int)$_GET["gevraagdhoorcoll"]);
                while (!$alleVragenQuery->EOF) {
                    $naamQuery= $db->Execute('SELECT naam,voornaam FROM hoorcollege_gebruiker WHERE idGebruiker='.(int)$studenten->fields["Gebruiker_idGebruiker"]);
//                    $vragen[$alleVragenQuery->fields["idVraag"]]["voornaam"]=  $naamQuery->fields["voornaam"];
//                    $vragen[$alleVragenQuery->fields["idVraag"]]["naam"]=  $naamQuery->fields["naam"];
                    $vragen[$alleVragenQuery->fields["idVraag"]]["vraagstelling"] =  $alleVragenQuery->fields["vraagstelling"];
                    $vragen[$alleVragenQuery->fields["idVraag"]]["id"] =  $alleVragenQuery->fields["idVraag"];
                    $vragen[$alleVragenQuery->fields["idVraag"]]["juistAntwoord"] = getAntwoord($alleVragenQuery->fields["juistantwoord"]);
                    $alleVragenQuery->MoveNext();
                }

                //select antwoorden van de huidige student
                $resultaat = $db->Execute('SELECT * FROM hoorcollege_gegevenantwoord
                    LEFT OUTER JOIN hoorcollege_vraag ON Vraag_idVraag = idVraag
                    WHERE Gebruiker_idGebruiker = '.(int)$studenten->fields["Gebruiker_idGebruiker"].' AND Hoorcollege_idHoorcollege ='.(int)$_GET["gevraagdhoorcoll"]);
                if($resultaat->fields["Vraag_idVraag"]!=null){
                while (!$resultaat->EOF) {
                                       $vragen[$resultaat->fields["Vraag_idVraag"]]["gegevenAntwoord"] = getAntwoord($resultaat->fields["MogelijkAntwoord_idMogelijkAntwoord"]);
                   if(antwoordOk((int)$studenten->fields["Gebruiker_idGebruiker"], $resultaat->fields["Vraag_idVraag"])){
                    $vragen[$resultaat->fields["Vraag_idVraag"]]["juist"] = "Juist";
                    }else {$vragen[$resultaat->fields["Vraag_idVraag"]]["juist"] = "Fout";}
                    $resultaat->MoveNext();
                }}
                else{   for($tel=1;$tel<=count($vragen);$tel++){
                    $vragen[$tel]["gegevenAntwoord"] = "De gebruiker heeft deze vraag nog niet opgelost.";
                    $vragen[$tel]["juist"] = "-";
                    $resultaat->MoveNext();
                }}


            for($teller=1;$teller<=count($vragen);$teller++){
               
            $xml_file .= "<rootvraag>";

//            $xml_file .= "<Gebruiker>".$vragen[$teller]["voornaam"]." ".$vragen[$teller]["naam"]."</Gebruiker>";
            $xml_file .= "<Vraag>".$vragen[$teller]["vraagstelling"]."</Vraag>";
            $xml_file .= "<juistantwoord>".$vragen[$teller]["juistAntwoord"]."</juistantwoord>";
            $xml_file .= "<gegevenantwoord>".$vragen[$teller]["gegevenAntwoord"]."</gegevenantwoord>";
            $xml_file .= "<correct>".$vragen[$teller]["juist"]."</correct>";
            
             $xml_file .= "</rootvraag>";
            }

            $xml_file .= "</student>";

            $studenten->MoveNext();
        }






        $xml_file .= "</root>";

       echo $xml_file;

    }
}


?>
