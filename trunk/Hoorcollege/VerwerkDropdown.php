<?php
include_once('./includes/kern.php');
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
        if (preg_match('/^[0-9]+$/iD', $_GET["gevraagdVak"])) {

            $result = $db->Execute("select * from hoorcollege_onderwerp where vak_idVak=".$_GET["gevraagdVak"]);
        }

        //xml file aanmaken
        $xml_file  = "<?xml version=\"1.0\"?>";
        $xml_file .= "<root>";


        while (!$result->EOF) {
            $xml_file .= "<Onderwerp>".$result->fields["naam"]."</Onderwerp>";
            $xml_file .= "<Id>".$result->fields["idOnderwerp"]."</Id>";

            $result->MoveNext();
        }

        $xml_file .= "</root>";

        echo $xml_file;

    }
}


?>
