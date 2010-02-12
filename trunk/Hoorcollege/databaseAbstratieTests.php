<?php
    include_once('./includes/kern.php');
    echo "<pre>";

    ## Voorbeeldqueries

    $resultaat = $db->Execute("select * from hoorcollege_gebruiker");
    while (!$resultaat->EOF) {
        print_r($resultaat->fields);
        $resultaat->MoveNext();
    }

    echo "\n----------------------\n\n";


     ## Bvb: we willen de id's en voornamen van iedereen die Beslic noemt
     $allebeslic = array();
     $resultaat = $db->Execute('select * from hoorcollege_gebruiker where naam="Beslic"');
     while (!$resultaat->EOF) {
        $allebeslic["id"][] =  $resultaat->fields["idGebruiker"];
        $allebeslic["voornaam"][] =  $resultaat->fields["voornaam"];
        $resultaat->MoveNext();
    }
    echo "Gevonden rijen: " . $resultaat->RecordCount() . "\n"; //RecordCount() zijn het aantal gevonden rijen
    print_r($allebeslic);

    echo "\n----------------------\n\n";

     ## Bvb: we willen alle informatie van iemand met een bepaalde id
     ## Met GetRow kunnen we afdwingen dat hij maar 1 rij selecteerd
     $resultaat = $db->GetRow('select * from hoorcollege_gebruiker where idGebruiker=1');

    print_r($resultaat);
    $gebruikersvoornaam = $resultaat['voornaam'];
    $gebruikerswachtwoord = $resultaat['wachtwoord'];
    $gebruikersniveau = $resultaat['niveau'];
    //etc
    echo "\n Gebruiker " . $gebruikersvoornaam . " heeft als wachtwoord " . $gebruikerswachtwoord . " en zijn adminniveau is " . $gebruikersniveau;
    echo "</pre>";


?>
