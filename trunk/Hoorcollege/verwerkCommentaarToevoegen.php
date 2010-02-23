<?php
include_once('./includes/kern.php');
session_start();

$gebruikerID = $_SESSION['gebruiker']->getIdGebruiker();
//Nog even standaard hoorcollegeID
$hoorcollegeID = 1;

//Inhoud commentaar moet ik nog controleren
$commentaar = $_GET["commentaar"];

voegCommentaarToe($gebruikerID, $hoorcollegeID, $commentaar);

$alleCommentarenVanHoorcollege = $db->Execute("select * from hoorcollege_reactie where hoorcollege_idHoorcollege=".$hoorcollegeID);
header("Content-type: text/xml");
//xml file aanmaken
$xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
$xml .= "<root>";

while (!$alleCommentarenVanHoorcollege->EOF) {
    $gebruiker = getGebruikerNaamViaId($alleCommentarenVanHoorcollege->fields["Gebruiker_idGebruiker"]);
    $xml .= "<Gebruiker>".$gebruiker."</Gebruiker>";
    $xml .= "<Tekst>".$alleCommentarenVanHoorcollege->fields["inhoud"]."</Tekst>";
    $alleCommentarenVanHoorcollege->MoveNext();
}

$xml .= "</root>";

echo $xml;


?>
