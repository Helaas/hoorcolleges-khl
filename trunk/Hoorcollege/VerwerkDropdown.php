<?php
    include_once('./includes/kern.php');

    header("Content-type: text/xml");
    //gegevens uit de db halen
    $result = $db->Execute("select * from hoorcollege_onderwerp where vak_idVak=".$_GET["gevraagdVak"]);

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




?>
