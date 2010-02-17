<?php
    include_once('./includes/kern.php');
    session_start();
    if(isset ($_SESSION['gebruiker'])) {
        $gebruiker = $_SESSION['gebruiker'];
        $gebruikerNiv = $gebruiker->getNiveau();

        if($gebruikerNiv==40){
    header("Content-type: text/xml");
    //gegevens uit de db halen
    $result = $db->Execute("SELECT * from Hoorcollege_hoorcollege where idHoorcollege in (SELECT Hoorcollege_idHoorcollege FROM `hoorcollege_onderwerphoorcollege` WHERE Onderwerp_Vak_idVak=".$_GET["gevraagdVak"]." AND Onderwerp_idOnderwerp=".$_GET["gevraagdOnd"].")");
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
