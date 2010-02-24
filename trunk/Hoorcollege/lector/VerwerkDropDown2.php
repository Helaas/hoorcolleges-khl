<?php
include_once('./../includes/kern.php');
session_start();

//php file voor gegevens uit de db te halen en als xml terug te sturen,
//om zo de tabel met hoorcolleges op Overzichthoorcolleges op te vullen

if(isset ($_SESSION['gebruiker'])) {
    $gebruiker = $_SESSION['gebruiker'];
    $gebruikerNiv = $gebruiker->getNiveau();

    if($gebruikerNiv==40) {

        //aangeven dat het antwoord een xml bestand is
        header("Content-type: text/xml");

        //Enkel getallen mogen hier binnen
        if (preg_match('/^[0-9]+$/iD', $_GET["gevraagdVak"]) && preg_match('/^[0-9]+$/iD', $_GET["gevraagdOnd"])) {
            $result = $db->Execute("SELECT * from Hoorcollege_hoorcollege where idHoorcollege in (SELECT Hoorcollege_idHoorcollege FROM `hoorcollege_onderwerphoorcollege` WHERE Onderwerp_Vak_idVak=".$_GET["gevraagdVak"]." AND Onderwerp_idOnderwerp=".$_GET["gevraagdOnd"].")");
        }
        //xml file aanmaken
        $xml_file  = "<?xml version=\"1.0\"?>";
        $xml_file .= "<root>";


        while (!$result->EOF) {
            $xml_file .= "<Naam>".$result->fields["naam"]."</Naam>";
            $xml_file .= "<Id>".$result->fields["idHoorcollege"]."</Id>";

            $result->MoveNext();
        }

        $xml_file .= "</root>";

        echo $xml_file;

    }
}

?>
