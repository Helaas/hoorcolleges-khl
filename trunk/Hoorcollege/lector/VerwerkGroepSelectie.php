<?php
include_once('./../includes/kern.php');
session_start();

//php file voor gegevens uit de db te halen en als xml terug te sturen,
//om zo de dropdown met onderwerpen uit Beheer en Overzichthoorcolleges op te vullen

if(isset ($_SESSION['gebruiker'])) {
    $gebruiker = $_SESSION['gebruiker'];
    $gebruikerNiv = $gebruiker->getNiveau();

    if($gebruikerNiv==40) {
                 $result = $db->Execute("
                                        select Hoorcollege_idHoorcollege
                                        from hoorcollege_onderwerphoorcollege
                                        where Onderwerp_idOnderwerp in (
                                                       select idOnderwerp
                                                       from hoorcollege_onderwerp
                                                       where Vak_idVak in (
                                                             SELECT Vak_idVak
                                                             FROM hoorcollege_gebruiker_beheert_vak
                                                             WHERE Gebruiker_idGebruiker=".(int)$gebruiker->getIdGebruiker().")) AND Hoorcollege_idHoorcollege=".(int)$_GET['gevraagdhoorcoll']);

          if($result->fields["Hoorcollege_idHoorcollege"]!=null) {
        //aangeven dat het antwoord een xml bestand is
        header("Content-type: text/xml");
        $geengroep=false;
        //Enkel getallen mogen hier binnen
        if ((preg_match('/^[0-9]+$/iD', $_GET["gevraagdGroep"]) || $_GET["gevraagdGroep"]=='geen') &&preg_match('/^[0-9]+$/iD', $_GET["gevraagdhoorcoll"])) {
            if($_GET["gevraagdGroep"]=='geen'){
            
            $studenten = $db->Execute("select idGebruiker from hoorcollege_gebruiker where idGebruiker not in(select Gebruiker_idGebruiker from hoorcollege_gebruikergroep) and idGebruiker in(SELECT Gebruiker_idGebruiker FROM hoorcollege_gebruikerhoorcollege WHERE hoorcollege_idHoorcollege =".(int)$_GET["gevraagdhoorcoll"].")");
            $geengroep=true;
            }
            else{
             $studenten = $db->Execute("SELECT Gebruiker_idGebruiker FROM hoorcollege_gebruikergroep WHERE Gebruiker_idGebruiker in (SELECT Gebruiker_idGebruiker FROM hoorcollege_gebruikerhoorcollege WHERE hoorcollege_idHoorcollege =".(int)$_GET["gevraagdhoorcoll"].") and Groep_idGroep =".(int)$_GET["gevraagdGroep"]);
             }
        }

        //xml file aanmaken
        $xml_file  = "<?xml version=\"1.0\"?>";
        $xml_file .= "<root>";

        while(!$studenten->EOF){

       if($geengroep){
            $id= (int)$studenten->fields["idGebruiker"];
            }
        else{
            $id= (int)$studenten->fields["Gebruiker_idGebruiker"];
             }

            $xml_file .= "<student>";

            $naamQuery= $db->Execute('SELECT naam,voornaam FROM hoorcollege_gebruiker WHERE idGebruiker='.$id);
      
            $xml_file .= "<naam>".$naamQuery->fields["voornaam"]." ".$naamQuery->fields["naam"]."</naam>";


              $vragen = array();
                $idVraag;
                //select vragen van het gekozen hoorcollege
                $alleVragenQuery = $db->Execute('SELECT * FROM hoorcollege_vraag WHERE Hoorcollege_idHoorcollege = '.(int)$_GET["gevraagdhoorcoll"]);
                $tell1=1;
                while (!$alleVragenQuery->EOF) {
                    $naamQuery= $db->Execute('SELECT naam,voornaam FROM hoorcollege_gebruiker WHERE idGebruiker='.$id);
                    $vragen[$tell1]["vraagstelling"] =  $alleVragenQuery->fields["vraagstelling"];
                    $vragen[$tell1]["id"] =  $alleVragenQuery->fields["idVraag"];
                    $vragen[$tell1]["juistAntwoord"] = getAntwoord($alleVragenQuery->fields["juistantwoord"]);
                    $alleVragenQuery->MoveNext();
                    $tell1++;
                }

                //select antwoorden van de huidige student
                $resultaat = $db->Execute('SELECT * FROM hoorcollege_gegevenantwoord
                    LEFT OUTER JOIN hoorcollege_vraag ON Vraag_idVraag = idVraag
                    WHERE Gebruiker_idGebruiker = '.$id.' AND Hoorcollege_idHoorcollege ='.(int)$_GET["gevraagdhoorcoll"]);
                if($resultaat->fields["Vraag_idVraag"]!=null){

                $tell2=1;
                while (!$resultaat->EOF) {                
                   $vragen[$tell2]["gegevenAntwoord"] = getAntwoord($resultaat->fields["MogelijkAntwoord_idMogelijkAntwoord"]);
                   if(antwoordOk($id, $resultaat->fields["Vraag_idVraag"])){
                    $vragen[$tell2]["juist"] = "Juist";
                    }else {$vragen[$tell2]["juist"] = "Fout";}
                    $resultaat->MoveNext();
                    $tell2++;
                   
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
}


?>
