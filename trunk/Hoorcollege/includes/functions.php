<?php
include_once('kern.php');

//methode om na te gaan of mensen een bepaalde vak krijgen van een bepaalde lector
function geeftLesAanVakLector($idVak, $idLector) {
    global $db;
    $resultaat = $db->Execute("SELECT count( DISTINCT Gebruiker_idGebruiker ) AS aantal
                               FROM hoorcollege_gebruiker_volgt_vak
                               WHERE Vak_idVak = '$idVak'
                               AND van = '$idLector'");
    if($resultaat->fields["aantal"] > 0) {
        return true;
    }
    else return false;
}

//methode om een groep te verwijderen uit de database
function verwijderGroep($idGroep) {
    global $db;

    //studenten die aan deze groep zijn gelinkt uit de tabel gebruikergroep verwijderen
    verwijderAlleStudentenVanGroep($idGroep);

    // het vak zelf verwijderen
    return $resultaat = $db->Execute("delete from hoorcollege_groep
                                          where idGroep = '$idGroep'");
}

//methode om de groepsnaam te wijzigen
function wijzigGroepsnaam($idGroep, $naam) {
    global $db;
    return $resultaat = $db->Execute("update hoorcollege_groep
                                          set naam = '$naam' where idGroep = '$idGroep'");
}

//methode om een volledige vak te verwijderen
function verwijderVak($idVak) {
    global $db;
    //alle onderwerpen en bijhorenden hoorcolleges enz verwijderen
    $resultaat = $db->Execute("SELECT idOnderwerp as id
                                   FROM hoorcollege_onderwerp
                                   where Vak_idVak = '$idVak'");
    while(!$resultaat->EOF) {
        verwijderOnderwerp($resultaat->fields["id"]);
        $resultaat->MoveNext();
    }

    //verwijderen van alle studenten die het vak volgen
    $resultaat = $db->Execute("delete from hoorcollege_gebruiker_volgt_vak
                                          where Vak_idVak = '$idVak'");

    //verwijderen van alle beheerders van het vak
    $resultaat = $db->Execute("delete from hoorcollege_gebruiker_beheert_vak
                                          where Vak_idVak = '$idVak'");

    //het vak zelf verwijderen
    return $resultaat = $db->Execute("delete from hoorcollege_vak
                                          where idVak = '$idVak'");
}

//methode om een beheerder van een vak te verwijderen
function verwijderBeheerderVanVak($idGebruiker, $idVak) {
    global $db;
    return $resultaat = $db->Execute("delete from hoorcollege_gebruiker_beheert_vak
                                          where Gebruiker_idGebruiker='$idGebruiker' and Vak_idVak = '$idVak'");
}

//methode om na te gaan hoeveel lectoren een bepaalde vak beheren
function aantalBeheertVak($idVak) {
    global $db;
    $resultaat = $db->Execute("SELECT count( Gebruiker_idGebruiker ) as aantal
                                   FROM hoorcollege_gebruiker_beheert_vak
                                   WHERE Vak_idVak = '$idVak'");
    return $resultaat->fields["aantal"];
}

//methode om de naam van een vak te wijzigen
function wijzigNaamVak($idVak, $naam) {
    global $db;
    return $resultaat = $db->Execute("update hoorcollege_vak
                                          set naam = '$naam' where idVak = '$idVak'");
}

//methode om de naam van een vak te bekomen via de id
function getVakNameViaId($vakId) {
    global $db;
    $resultaat = $db->Execute("select naam as naam
                                   from hoorcollege_vak where idVak = '$vakId'");
    return $resultaat->fields["naam"];
}

//function om de id van een vak te weten te komen
function getIdViaName($naam) {
    global $db;
    $resultaat = $db->Execute("select idVak
                                   from hoorcollege_vak where naam = '$naam'");
    return $resultaat->fields["idVak"];
}

//methode om de naam van een groep te bekomen via de id
function getGroepNameViaId($groepId) {
    global $db;
    $resultaat = $db->Execute("select naam as naam
                                   from hoorcollege_groep where idGroep = '$groepId'");
    return $resultaat->fields["naam"];
}

//methode om de naam van een gebruiker te bekomen via de id
function getGebruikerNaamViaId($gebruikerId) {
    global $db;
    $resultaat = $db->Execute("select concat(naam, ', ', voornaam) as naam
                                   from hoorcollege_gebruiker where idGebruiker = '$gebruikerId'");
    return $resultaat->fields["naam"];
}

//Functie voor het nakijken van een ingevoerd nummer:
//- is de string wel degelijk numeric en gevuld?
//- is de string niet verdacht lang?
//Onder andere gebruikt voor het controleren van het hoorcollegeID bij student
function validateNumber($string) {
    $type = 'is_numeric';
    if(!$type($string)) {
        return FALSE;
    }
    //Nagaan of er iets in de string staat
    elseif(empty($string)) {
        return FALSE;
    }
    else {
        //Alles ok
        return TRUE;
    }
}

function validateEmail($email) {
    if(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email)) {
        return true;
    }else {
        return false;
    }
}

//Functie om na te gaan of een gebruiker met deze email al bestaat
function bestaatEmail($email) {
    global $db;
    $resultaat = $db->Execute("select count( distinct email ) as aantal
                                   from hoorcollege_gebruiker where email = '$email'");

    if($resultaat->fields["aantal"] > 0) {
        return true;
    }
    else {
        return false;
    }
}

//methode om na te gaan of een vak al bestaat, wordt onder andere in admin.php gebruikt
function bestaatVak($vak) {
    global $db;
    $resultaat = $db->Execute("select count( distinct naam) as aantal
                                   from hoorcollege_vak where naam = '$vak'");

    if($resultaat->fields["aantal"] > 0) {
        return true;
    }
    else {
        return false;
    }
}

//methode om na te gaan of een groep al bestaat, wordt onder andere in admin.php gebruikt
function bestaatGroep($groep) {
    global $db;
    $resultaat = $db->Execute("select count( distinct naam) as aantal
                                   from hoorcollege_groep where naam = '$groep'");
    if($resultaat->fields["aantal"] > 0) {
        return true;
    }
    else {
        return false;
    }
}

//methode om na te gaan of een groep leeg is, dus geen studenten die eraan gekoppeld zijn
function isGroepLeeg($groepId) {
    global $db;
    $resultaat = $db->Execute("select count('$groepId') as aantal
                                   from hoorcollege_gebruikergroep where Groep_idGroep = '$groepId'");
    if($resultaat->fields["aantal"] == 0) {
        return true;
    }
    else {
        return false;
    }
}

//methode om te controleren of een lector een vak beheert
function beheertLectorVak($lectorId, $vakId) {
    global $db;
    $resultaat = $db->Execute("select count(*) as aantal from hoorcollege_gebruiker_beheert_vak
                                   WHERE Gebruiker_idGebruiker = '$lectorId' && Vak_idVak = '$vakId'");
    if($resultaat->fields["aantal"] > 0) {
        return true;
    }
    else {
        return false;
    }
}

//methode om te controleren of een student aan een groep is toegekent
function isStudentToegekentAanGroep($studentId) {
    global $db;
    $resultaat = $db->Execute("select count(*) as aantal from hoorcollege_gebruikergroep
                                   WHERE Gebruiker_idGebruiker = '$studentId'");
    if($resultaat->fields["aantal"] > 0) {
        return true;
    }
    else {
        return false;
    }
}


//methode om te controleren of een student aan een groep is toegekent
function isStudentToegekentAanGroep2($studentId, $groepId) {
    global $db;
    $resultaat = $db->Execute("select count(*) as aantal from hoorcollege_gebruikergroep
                                   WHERE Gebruiker_idGebruiker = '$studentId' && Groep_idGroep = '$groepId'");
    if($resultaat->fields["aantal"] > 0) {
        return true;
    }
    else {
        return false;
    }
}

//methode om na te gaan of een student aan een bepaald vak al is toegekent
function isStudentToegekentVak($studentId, $vakId) {
    global $db;
    $resultaat = $db->Execute("select count(*) as aantal from hoorcollege_gebruiker_volgt_vak
                                   WHERE Gebruiker_idGebruiker = '$studentId' && Vak_idVak = '$vakId'");
    if($resultaat->fields["aantal"] > 0) {
        return true;
    }
    else {
        return false;
    }
}

//methode om na ge gaan of een groep al toegekent is aan een vak
function isGroepToegekentAanVak($groepId, $vakId) {
    global $db;
    $gekent = false;
    $resultaat = $db->Execute("select Gebruiker_idGebruiker from hoorcollege_gebruikergroep where Groep_idGroep = '$groepId'");
    while (!$resultaat->EOF && !$gekent) {
        $gebruikerId = $resultaat->fields["Gebruiker_idGebruiker"];
        $res2 = $db->Execute("select count(Gebruiker_idGebruiker) as aantal from hoorcollege_gebruiker_volgt_vak
                                  where Gebruiker_idGebruiker = '$gebruikerId' && Vak_idVak = '$vakId'");
        if($res2->fields["aantal"] > 0) {
            $gekent = true;
        }
        else {
            $resultaat->MoveNext();
        }
    }
    return $gekent;

}
//methode om na ge gaan of een groep al toegekent is aan een vak en een hoorcollege
function isGroepToegekentAanVakEnHoorcollege($groepId, $vakId,$hoorcollegeid) {
    global $db;
    $gekent = false;
    $resultaat = $db->Execute("select Gebruiker_idGebruiker from hoorcollege_gebruikergroep where Groep_idGroep = '$groepId'");
    while (!$resultaat->EOF && !$gekent) {
        $gebruikerId = $resultaat->fields["Gebruiker_idGebruiker"];
        $res2 = $db->Execute("select count(Gebruiker_idGebruiker) as aantal from hoorcollege_gebruiker_volgt_vak
                                  where Gebruiker_idGebruiker = '$gebruikerId' && Vak_idVak = '$vakId' and Gebruiker_idGebruiker
                                  in (SELECT Gebruiker_idGebruiker
                                      FROM hoorcollege_gebruikerhoorcollege
                                       WHERE Hoorcollege_idHoorcollege='$hoorcollegeid')");
        if($res2->fields["aantal"] > 0) {
            $gekent = true;
        }
        else {
            $resultaat->MoveNext();
        }
    }
    return $gekent;

}

function voegGebruikerToe($naam, $voornaam, $email) {
    global $db;
    $pasww1 = generatePassword();
    $pasww = md5($pasww1);
    $verstuurd = false;
    $gelukt =  $db->Execute("insert into hoorcollege_gebruiker (naam, voornaam, email, wachtwoord, niveau, actief)
                                    values('$naam', '$voornaam', '$email', '$pasww', '1', '1')");
    if($gelukt) {
        //gebruiker mailen
        $boodschap = "Geachte $voornaam, $naam\n\nVanaf nu kan u hoorcollges volgen op KHL - Hoorcolleges.\n"
                . "U inlog gegevens: \n\nGebruikernaam: $email\nPasswoord: $pasww1\n\nMet vriendelijke groeten.\n\n"
                ."Het Katholieke Hogeschool Leuven.";
        $verstuurd = mail( "$email", "Subject: 'KHL - Belangrijk: login gegevens: Hoorcolleges'", $boodschap, "From: khl@khl.be" );
    }

    if(!$verstuurd) {
        //indien geen mail kon worden verzonden
        $gelukt = $verstuurd;
        verwijderGebruiker($email);
    }

    return $gelukt;
}

function voegVakToe($vak) {
    global $db;
    $gelukt =  $db->Execute("insert into hoorcollege_vak (naam)
                                    values('$vak')");
    return $gelukt;
}

//functie om lector toe te kennen aan een vak
function kenLectorToeAanVak($lectorId, $vakId) {
    global $db;
    $gelukt =  $db->Execute("insert into hoorcollege_gebruiker_beheert_vak (Gebruiker_idGebruiker, Vak_idVak)
                                    values('$lectorId', '$vakId')");
    return $gelukt;
}

//functie om student toe te voegen aan een groep
function kenStudentToeAanGroep($studentId, $groepId) {
    global $db;
    $gelukt =  $db->Execute("insert into hoorcollege_gebruikergroep (Gebruiker_idGebruiker, Groep_idGroep)
                                    values('$studentId', '$groepId')");
    return $gelukt;
}

//functie om een student toe te kennen aan een vak
function kenStudentToeAanVak($studentId, $vakId, $van) {
    global $db;
    $gelukt = true;
    if(!isStudentToegekentVak($studentId, $vakId)) {
        $gelukt = $db->Execute("insert into hoorcollege_gebruiker_volgt_vak (Gebruiker_idGebruiker, Vak_idVak, van)
                                        values('$studentId', '$vakId', '$van')");
    }
    return $gelukt;
}

//functie om groep toe te voegen aan een vak
function kenGroepToeAanVak($groepId, $vakId, $vanId) {
    global $db;
    $gelukt = true;
    $resultaat = $db->Execute("select * from hoorcollege_gebruikergroep where Groep_idGroep = '$groepId'");
    while (!$resultaat->EOF) {
        $gebruikerId = $resultaat->fields["Gebruiker_idGebruiker"];
        if(!isStudentToegekentVak($gebruikerId, $vakId)) { // geen dubbele entries
            $gelukt = $db->Execute("insert into hoorcollege_gebruiker_volgt_vak (Gebruiker_idGebruiker, Vak_idVak, van)
                                        values('$gebruikerId', '$vakId', '$vanId')");
        }
        $resultaat->MoveNext();
    }
    return $gelukt;
}

//functie om student te verwijderen uit een groep, wordt onderandere in admin.php gebruikt
function verwijderStudentVanAlleGroepen($studentId) {
    global $db;
    $gelukt =  $db->Execute("delete from hoorcollege_gebruikergroep
                                    where Gebruiker_idGebruiker='$studentId'");
    return $gelukt;
}

// NIET MEER GEBRUIKEN!!: functie om student te verwijderen uit een groep, wordt onderandere in admin.php gebruikt
function verwijderStudentVanGroep($studentId, $groepId) {
    global $db;
    $gelukt =  $db->Execute("delete from hoorcollege_gebruikergroep
                                    where Gebruiker_idGebruiker='$studentId' && Groep_idGroep='$groepId'");
    return $gelukt;
}


//functie om alle studenten te verwijderen uit een groep, wordt onderandere in admin.php gebruikt
function verwijderAlleStudentenVanGroep($groepId) {
    global $db;
    $gelukt =  $db->Execute("delete from hoorcollege_gebruikergroep
                                    where Groep_idGroep='$groepId'");
    return $gelukt;
}

//functie om de al de leden van een groep te ontkoppelen van een vak
function ontkoppelGroepVanVak($groepId, $vakId) {
    global $db;
    $gelukt = true;
    $resultaat = $db->Execute("select * from hoorcollege_gebruikergroep where Groep_idGroep = '$groepId'");
    while (!$resultaat->EOF) {
        $gebruikerId = $resultaat->fields["Gebruiker_idGebruiker"];
        if(isStudentToegekentVak($gebruikerId, $vakId)) { // nagaan of deze persoon wel in de groep zit
            $gelukt = $db->Execute("delete from hoorcollege_gebruiker_volgt_vak
                                        where Gebruiker_idGebruiker = '$gebruikerId' && Vak_idVak = '$vakId'");
        }
        $resultaat->MoveNext();
    }
    return $gelukt;
}

//methode om een student te ontkoppelen van een vak
function ontkoppelStudentVanVak($gebruikerId, $vakId) {
    global $db;
    return $db->Execute("delete from hoorcollege_gebruiker_volgt_vak
                             where Gebruiker_idGebruiker = '$gebruikerId' && Vak_idVak = '$vakId'");
}

//methode om een student te ontkoppelen van alle vakken, wordt gebruikt bij het verwijderen van een student(inactief)
function ontkoppelStudentVanAlleVakken($gebruikerId) {
    global $db;
    $db->Execute("delete from hoorcollege_gebruiker_volgt_vak
                      where Gebruiker_idGebruiker = '$gebruikerId'");
}

function voegGroepToe($groep) {
    global $db;
    $gelukt =  $db->Execute("insert into hoorcollege_groep (naam)
                                    values('$groep')");
    return $gelukt;
}

//deze functie verwijdert effectief een gebruiker, wordt enkel gebruikt indien blijkt dat een email niet bestaat, bij toevoegen van gebruiker door admin
function verwijderGebruiker($email) {
    global $db;
    $db->Execute("delete from hoorcollege_gebruiker WHERE email = '$email'");
}

//methode om een student te verwijderen, eigenlijk wordt die enkel op inactief gezet, ma niet effectief verwijderd
function verwijderStudent($studentId) {
    global $db;
    ontkoppelStudentVanAlleVakken($studentId);
    verwijderStudentVanAlleGroepen($studentId);
    return $db->Execute("update hoorcollege_gebruiker set actief = '0' where idGebruiker = '$studentId'");
}

//methode om een lector te promoveren tot een admin
function promoveerLector($idGebruiker) {
    global $db;
    return $db->Execute("update hoorcollege_gebruiker set niveau = '99' where idGebruiker = '$idGebruiker'");
}

//deze functie is niet zelf geschreven, bron: http://www.laughing-buddha.net/jon/php/password/
function generatePassword ($length = 8) {

    // start with a blank password
    $password = "";

    // define possible characters
    $possible = "0123456789bcdfghjkmnpqrstvwxyz";

    // set up a counter
    $i = 0;

    // add random characters to $password until $length is reached
    while ($i < $length) {

        // pick a random character from the possible ones
        $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);

        // we don't want this character if it's already in the password
        if (!strstr($password, $char)) {
            $password .= $char;
            $i++;
        }

    }

    // done!
    return $password;

}

//Functie om na te gaan of het paswoord juist is
function paswoordOk($email, $pw) {
    global $db;
    $resultaat = $db->Execute("select count( distinct email ) as aantal
                                   from hoorcollege_gebruiker where email LIKE '$email'
                                    and wachtwoord LIKE '$pw'");

    if($resultaat->fields["aantal"] > 0) {
        return true;
    }
    else {
        return false;
    }
}

//Functie om na te gaan of het ingegeven antwoord van de student juist of fout is
function antwoordOk($gebruikerid, $vraagID) {
    global $db;
    $resultaat = $db->Execute("SELECT COUNT( idVraag ) AS aantal
        FROM hoorcollege_vraag
        LEFT OUTER JOIN hoorcollege_gegevenantwoord ON idVraag = Vraag_idVraag
        WHERE Gebruiker_idGebruiker =".$gebruikerid."
        AND juistantwoord = MogelijkAntwoord_idMogelijkAntwoord
        AND idVraag = ".$vraagID);
    if($resultaat->fields["aantal"] > 0) {
        return true;
    }
    else {
        return false;
    }
}

function getGebruiker($email) {
    global $db;
    $resultaat = $db->GetRow("select * from hoorcollege_gebruiker where email = '$email'");
    return $resultaat;
}

function heeftHoorcollegeVragen($id) {
    $id = (int)$id;
    global $db;

    $resultaat = $db->GetRow("select count(idVraag) as aantal from hoorcollege_vraag where Hoorcollege_idHoorcollege = '$id'");
    return ($resultaat["aantal"]>=1);
}

function heeftGebruikerVragenGemaakt($gebruikersID, $hoorcollegeID) {
    $gebruikersID = (int)$gebruikersID;
    $hoorcollegeID = (int)$hoorcollegeID;
    global $db;

    $resultaat = $db->GetRow("SELECT count( *  ) as aantal
                                FROM hoorcollege_gegevenantwoord 
                                INNER JOIN hoorcollege_vraag ON ( Vraag_idVraag )
                                WHERE Hoorcollege_idHoorcollege =". $hoorcollegeID . "
                                AND Gebruiker_idGebruiker =".$gebruikersID."
                                AND Vraag_idVraag in (select idVraag from hoorcollege_vraag where Hoorcollege_idHoorcollege = ". $hoorcollegeID . ")");
    return ($resultaat["aantal"]>=1);
}

function getHoorcollegeNaam($id) {
    $id = (int)$id;
    global $db;

    $resultaat = $db->GetRow("SELECT naam FROM hoorcollege_hoorcollege WHERE idHoorcollege = ".$id);
    return $resultaat["naam"];
}

function getAntwoord($mogelijkAntwoordID) {
    global $db;
    $antwoordTekst = $db->Execute('SELECT antwoord
                                       FROM  hoorcollege_mogelijkantwoord
                                       WHERE idMogelijkAntwoord = '.$mogelijkAntwoordID);
    return $antwoordTekst->fields["antwoord"];
}

function ingevoerdNummerOk($string) {
    $type = 'is_numeric';
    if(!$type($string)) {
        return FALSE;
    }
    //Nagaan of er iets in de string staat
    elseif(empty($string)) {
        return FALSE;
    }
    //Nagaan dat de string niet overdreven lang is
    elseif(strlen($string) > 10 || strlen($string) < 1) {
        return FALSE;
    }
    else {
        //Alles ok
        return TRUE;
    }
}

function magGebruikerVragenBeantwoorden($gebruikerID, $hoorcollegeID) {
    global $db;
    $gebruikerID = (int)$gebruikerID;
    $hoorcollegeID = (int)$hoorcollegeID;

    $hoorcol_gebruiker = array();
    $resultaat = $db->Execute('SELECT Hoorcollege_idHoorcollege AS id
                                   FROM hoorcollege_gebruikerhoorcollege
                                    WHERE Gebruiker_idGebruiker ='.$gebruikerID);
    while (!$resultaat->EOF) {
        $hoorcol_gebruiker[] = $resultaat->fields["id"];
        $resultaat->MoveNext();
    }

    return in_array($hoorcollegeID,$hoorcol_gebruiker);
}

function voegCommentaarToe($gebruikerID, $hoorcollegeId, $tekst) {
    global $db;
    $db->Execute("INSERT INTO hoorcollege_reactie
            (idReactie, Hoorcollege_idHoorcollege, Gebruiker_idGebruiker, inhoud)
            VALUES (NULL, '$hoorcollegeId','$gebruikerID', '$tekst')");
}

function magGebruikerHoorcollegeZien($gebruikerID, $hoorcollegeID) {
    global $db;
    $gebruikerID = (int)$gebruikerID;
    $hoorcollegeID = (int)$hoorcollegeID;

    $hoorcol_gebruiker = array();
    $resultaat = $db->Execute('SELECT Hoorcollege_idHoorcollege AS id
                                    FROM hoorcollege_gebruikerhoorcollege
                                    WHERE Gebruiker_idGebruiker ='.$gebruikerID);
    while (!$resultaat->EOF) {
        $hoorcol_gebruiker[] = $resultaat->fields["id"];
        $resultaat->MoveNext();
    }

    return in_array($hoorcollegeID,$hoorcol_gebruiker);
}

function zetHoorcollegeBekeken($gebruikerID, $hoorcollegeID) {
    global $db;
    $gebruikerID = (int)$gebruikerID;
    $hoorcollegeID = (int)$hoorcollegeID;

    $resultaat = $db->Execute('UPDATE hoorcollege_gebruikerhoorcollege
                                    SET reedsBekeken = 1
                                    WHERE Gebruiker_idGebruiker  = ' . $gebruikerID . '
                                    AND Hoorcollege_idHoorcollege  = '.$hoorcollegeID);
    return $resultaat;
}

function getHoorcollegeInformatie($hoorcollegeID) {
    global $db;
    $hoorcollegeID = (int)$hoorcollegeID;

    $resultaat = $db->GetRow('SELECT *
                                    FROM hoorcollege_hoorcollege
                                    WHERE idHoorcollege = '.$hoorcollegeID);

    return $resultaat;

}

function heeftHoorcollegeVBC($gebruikerID, $hoorcollegeID) {
    global $db;
    $gebruikerID = (int)$gebruikerID;
    $hoorcollegeID = (int)$hoorcollegeID;

    $resultaat = $db->GetRow('SELECT VBCVerplicht
                                    FROM hoorcollege_gebruikerhoorcollege
                                    WHERE Gebruiker_idGebruiker = '. $gebruikerID .'
                                    AND Hoorcollege_idHoorcollege = '.$hoorcollegeID);

    return $resultaat["VBCVerplicht"];
}

function getHoorcollegeBibliotheekitems($hoorcollegeID) {
    global $db;
    $hoorcollegeID = (int)$hoorcollegeID;

    $items = array();
    $resultaat = $db->Execute('SELECT *
                                    FROM hoorcollege_bibliotheekitem
                                    WHERE idBibliotheekItem
                                    IN (
                                        SELECT BibliotheekItem_idBibliotheekItem
                                        FROM hoorcollege_hoorcollegbibliotheekitem
                                        WHERE Hoorcollege_idHoorcollege = '. $hoorcollegeID .'
                                    )');
    while (!$resultaat->EOF) {
        $teller = $resultaat->fields["mimetype"]; //maar 1 van elk type per hoorcollege
        $items[$teller]["idBibliotheekItem"] = $resultaat->fields["idBibliotheekItem"];
        $items[$teller]["naam"] = $resultaat->fields["naam"];
        $items[$teller]["beschrijving"] = $resultaat->fields["beschrijving"];
        $items[$teller]["locatie"] = $resultaat->fields["locatie"];
        $items[$teller]["tekst"] = $resultaat->fields["tekst"];
        $resultaat->MoveNext();
    }

    //beschrijvingen worden met addslashes in de db gezet
    foreach ($items as $key => $value) {
        //$items[$key]["naam"] = stripslashes($items[$key]["naam"]);
        $items[$key]["beschrijving"] = stripslashes($items[$key]["beschrijving"]);
    }

    arrayNaarUTF($items);
    return $items;
}

function arrayNaarUTF(&$complex_array) {
    if (is_array($complex_array)) {
        foreach ($complex_array as $n => &$v) {
            if (is_array($v))
                arrayNaarUTF($v);
            else {
                $v = utf8_encode($v);
            }
        }
    } else {
        if (is_string($complex_array)) {
            $complex_array = utf8_encode($complex_array);
        }
    }
}

function verwijderHoorcollege($hoorcollid) {
    global $db;
    //delete gegeven antwoorden voor dit hoorcollege
    $db->Execute("DELETE FROM hoorcollege_gegevenantwoord
                                 WHERE  Vraag_idVraag
                                    in (SELECT idVraag
                                    FROM hoorcollege_vraag
                                    WHERE Hoorcollege_idHoorcollege =".$hoorcollid.")");
    //delete mogelijke antwoorden voor dit hoorcollege
    $db->Execute("DELETE FROM hoorcollege_mogelijkantwoord
                                 WHERE  Vraag_idVraag
                                    in (SELECT idVraag
                                    FROM hoorcollege_vraag
                                        WHERE Hoorcollege_idHoorcollege =".$hoorcollid.")");
    //delete vragen voor dit hoorcollege
    $db->Execute("DELETE FROM hoorcollege_vraag
                                        WHERE Hoorcollege_idHoorcollege =".$hoorcollid);

    //delete video bekeken controle voor dit hoorcollege
    $db->Execute("DELETE FROM hoorcollege_vbc
                                        WHERE Hoorcollege_idHoorcollege =".$hoorcollid);

    //delete reacties op dit hoorcollege
    $db->Execute("DELETE FROM hoorcollege_reactie
                                        WHERE Hoorcollege_idHoorcollege =".$hoorcollid);

    //Verwijder relaties tussen gebruikers en dit hoorcollege
    $db->Execute("DELETE FROM hoorcollege_gebruikerhoorcollege
                                        WHERE Hoorcollege_idHoorcollege =".$hoorcollid);

    //Verwijder relaties tussen bilbiotheekitems en dit hoorcollege
    $db->Execute("DELETE FROM hoorcollege_hoorcollegbibliotheekitem
                                        WHERE Hoorcollege_idHoorcollege =".$hoorcollid);

    //Verwijder relaties tussen onderwerpen en dit hoorcollege
    $db->Execute("DELETE FROM hoorcollege_onderwerphoorcollege
                                        WHERE Hoorcollege_idHoorcollege =".$hoorcollid);

    //Verwijder relaties tussen onderwerpen en dit hoorcollege
    $db->Execute("DELETE FROM hoorcollege_hoorcollege
                                        WHERE idHoorcollege =".$hoorcollid);
}

function verwijderOnderwerp($gevraagdond) {
    global $db;

    $result=$db->Execute("SELECT Hoorcollege_idHoorcollege FROM hoorcollege_onderwerphoorcollege WHERE Onderwerp_idOnderwerp=".$gevraagdond);
    while (!$result->EOF) {
        verwijderHoorcollege($result->fields["Hoorcollege_idHoorcollege"]);
        $result->MoveNext();
    }
    $db->Execute("DELETE FROM hoorcollege_onderwerp
                                    WHERE idOnderwerp =".$gevraagdond);
}

function editOnderwerp($ondid,$ondnaam) {
    global $db;
    $db->Execute("UPDATE hoorcollege_onderwerp SET naam ='".$ondnaam."' WHERE idOnderwerp =".(int)$ondid );
}

function getGroepId($groepnaam) {
    global $db;

    $result=$db->GetRow("SELECT idGroep FROM hoorcollege_groep WHERE naam ='".$groepnaam."'");
    return $result["idGroep"];
}

function getBibliotheekCategorieId($cat) {
    global $db;
    $cat = (int)$cat;

    $resultaat=$db->GetRow("SELECT BibliotheekCategorie_idBibliotheekCategorie
                                FROM hoorcollege_bibliotheekitem
                                WHERE idBibliotheekItem = ".$cat);
    if (!isset($resultaat["BibliotheekCategorie_idBibliotheekCategorie"])) return -1;
    return $resultaat["BibliotheekCategorie_idBibliotheekCategorie"];
}

function getGroepenVoorvak($vakid) {
    global $db;
    $vakid = (int)$vakid;

    $items = array();
    $resultaat = $db->Execute('SELECT *
                                    FROM hoorcollege_groep
                                    WHERE idGroep
                                    IN (
                                            SELECT Groep_idGroep
                                            FROM hoorcollege_gebruikergroep
                                            WHERE Gebruiker_idGebruiker in (
                                                                            SELECT Gebruiker_idGebruiker
                                                                            FROM hoorcollege_gebruiker_volgt_vak
                                                                            WHERE Vak_idVak ='. $vakid .')
                                                                            )');
    while (!$resultaat->EOF) {
        $items[$resultaat->fields["idGroep"]] = $resultaat->fields["naam"];
        $resultaat->MoveNext();
    }


    arrayNaarUTF($items);
    return $items;
}

function veranderPaswoord($idGebruiker, $nieuwPW) {
    global $db;
    $db->Execute("UPDATE  hoorcollege_gebruiker SET wachtwoord = '".$nieuwPW."' WHERE idGebruiker =".$idGebruiker);
}

function getStudentenVoorvak($vakid,$groepid) {
    global $db;
    $vakid = (int)$vakid;
    $groepid = (int)$groepid;

    $alleStudentenVanVak = array();
    $resultaat = $db->Execute('SELECT idGebruiker
                                FROM hoorcollege_gebruiker
                                WHERE idGebruiker
                                IN (
                                    SELECT Gebruiker_idGebruiker
                                    FROM hoorcollege_gebruiker_volgt_vak
                                    WHERE Vak_idVak = '.$vakid.'
                                )');
    while (!$resultaat->EOF) {
        $alleStudentenVanVak[] = $resultaat->fields["idGebruiker"];
        $resultaat->MoveNext();
    }

    $alleStudentenInGroep = array();
    $resultaat = $db->Execute('SELECT Gebruiker_idGebruiker
                                FROM hoorcollege_gebruikergroep
                                WHERE Groep_idGroep = '.$groepid);
    while (!$resultaat->EOF) {
        $alleStudentenInGroep[] = $resultaat->fields["Gebruiker_idGebruiker"];
        $resultaat->MoveNext();
    }

    $filter = array_intersect($alleStudentenInGroep, $alleStudentenVanVak);

    $studentids = "";


    foreach ($filter as $value) {
        $studentids .= $value.",";
    }

    $items = array();
    if (strlen($studentids) > 0){
        $studentids = substr_replace($studentids,"",-1);
    

        $resultaat = $db->Execute('SELECT idGebruiker, naam, voornaam
                                    FROM hoorcollege_gebruiker
                                    WHERE idGebruiker
                                    IN ( '. $studentids .' )
                                    ORDER BY naam, voornaam');
        while (!$resultaat->EOF) {
            $items[$resultaat->fields["idGebruiker"]] = $resultaat->fields["naam"] . " " . $resultaat->fields["voornaam"];
            $resultaat->MoveNext();
        }

        /**echo "<pre>";
        print_r($alleStudentenVanVak);
        print_r($alleStudentenInGroep);
        print_r($filter);
        print_r($items);
        echo "</pre>";**/
    }
    
    arrayNaarUTF($items);
    return $items;
}

function getStudentenVoorvakAlles($vakid) {
    global $db;
    $vakid = (int)$vakid;

    $items = array();
    $resultaat = $db->Execute('SELECT idGebruiker, naam, voornaam
                                FROM hoorcollege_gebruiker
                                WHERE idGebruiker
                                IN (
                                    SELECT idGebruiker
                                    FROM hoorcollege_gebruiker
                                    WHERE idGebruiker
                                    IN (
                                        SELECT Gebruiker_idGebruiker
                                        FROM hoorcollege_gebruiker_volgt_vak
                                        WHERE Vak_idVak = '.$vakid.'
                                    )
                                 ) ORDER BY naam, voornaam');
    while (!$resultaat->EOF) {
        $items[$resultaat->fields["idGebruiker"]] = $resultaat->fields["naam"] . " " . $resultaat->fields["voornaam"];
        $resultaat->MoveNext();
    }

    arrayNaarUTF($items);
    return $items;
}

function getStudentenZonderGroep($vakid) {
    global $db;
    $vakid = (int)$vakid;

    $alleStudentenVanVak = array();
    $resultaat = $db->Execute('SELECT idGebruiker
                                FROM hoorcollege_gebruiker
                                WHERE idGebruiker
                                IN (
                                    SELECT Gebruiker_idGebruiker
                                    FROM hoorcollege_gebruiker_volgt_vak
                                    WHERE Vak_idVak = '.$vakid.'
                                )');
    while (!$resultaat->EOF) {
        $alleStudentenVanVak[] = $resultaat->fields["idGebruiker"];
        $resultaat->MoveNext();
    }

    $alleGroepen = getGroepenVoorvak($vakid);
    $groepids ="";
    
    foreach ($alleGroepen as $sleutel => $value) {
        $groepids .= $sleutel.",";
    }

    if (strlen($groepids) > 0){
        $groepids = substr_replace($groepids,"",-1);
    }

    $alleStudentenInGroep = array();
    $resultaat = $db->Execute('SELECT Gebruiker_idGebruiker
                                FROM hoorcollege_gebruikergroep
                                WHERE Groep_idGroep IN ('.$groepids.')');
    while (!$resultaat->EOF) {
        $alleStudentenInGroep[] = $resultaat->fields["Gebruiker_idGebruiker"];
        $resultaat->MoveNext();
    }


    $studentids = "";




    foreach ($alleStudentenVanVak as $value) {
        if (!in_array($value, $alleStudentenInGroep)) $studentids .= $value.",";
    }

    $items = array();
    if (strlen($studentids) > 0){
        $studentids = substr_replace($studentids,"",-1);

        $resultaat = $db->Execute('SELECT idGebruiker, naam, voornaam
                                    FROM hoorcollege_gebruiker
                                    WHERE idGebruiker
                                    IN ( '. $studentids .' )
                                    ORDER BY naam, voornaam');
        while (!$resultaat->EOF) {
            $items[$resultaat->fields["idGebruiker"]] = $resultaat->fields["naam"] . " " . $resultaat->fields["voornaam"];
            $resultaat->MoveNext();
        }
    }
    /**echo "<pre>";
    print_r($items);
    echo "</pre>";**/

    arrayNaarUTF($items);
    return $items;
}

function getBibliotheekitemNaam($itemid) {
    global $db;
    $itemid = (int)$itemid;
    return utf8_encode($db->GetOne("SELECT naam
                        FROM hoorcollege_bibliotheekitem
                        WHERE idBibliotheekItem =".$itemid));
}


function getStudenten($arrIds) {
    global $db;
    $studentids = "";

    foreach ($arrIds as $value) {
        $studentids .= (int)$value.",";
    }

    $items = array();
    if (strlen($studentids) > 0){
        $studentids = substr_replace($studentids,"",-1);

        $resultaat = $db->Execute('SELECT idGebruiker, naam, voornaam
                                    FROM hoorcollege_gebruiker
                                    WHERE idGebruiker
                                    IN ( '. $studentids .' )
                                    ORDER BY naam, voornaam');
        while (!$resultaat->EOF) {
            $items[$resultaat->fields["idGebruiker"]] = $resultaat->fields["naam"] . " " . $resultaat->fields["voornaam"];
            $resultaat->MoveNext();
        }
    }

    arrayNaarUTF($items);
    return $items;
}

function maakHoorcollege($vak, $ond, $naam, $keuze_flv, $keuze_mp3, $keuze_txt, $arrStudids){
    global $db;
    $vak = (int)$vak;
    $ond = (int)$ond;
    $keuze_flv = (int)$keuze_flv;
    $keuze_mp3 = (int)$keuze_mp3;
    $keuze_txt = (int)$keuze_txt;
    $naam = addslashes($naam);

    $resultaat = $db->Execute("INSERT INTO hoorcollege_hoorcollege (
                                                idHoorcollege ,
                                                naam ,
                                                VBC_aantal ,
                                                VBC_geluid
                                                )
                                                VALUES (
                                                NULL , '". $naam ."', '0', '0'
                                                )");

    $nieuweId = $db->Insert_ID();

    if ($keuze_flv>0){
        $resultaat = $db->Execute("INSERT INTO hoorcollege_hoorcollegbibliotheekitem (
                                    Hoorcollege_idHoorcollege ,
                                    BibliotheekItem_idBibliotheekItem
                                    )
                                    VALUES (
                                    '". $nieuweId . "', '" . $keuze_flv . "'
                                    )");
    }

    if ($keuze_mp3>0){
        $resultaat = $db->Execute("INSERT INTO hoorcollege_hoorcollegbibliotheekitem (
                                    Hoorcollege_idHoorcollege ,
                                    BibliotheekItem_idBibliotheekItem
                                    )
                                    VALUES (
                                    '". $nieuweId . "', '" . $keuze_mp3 . "'
                                    )");
    }

    if ($keuze_txt>0){
        $resultaat = $db->Execute("INSERT INTO hoorcollege_hoorcollegbibliotheekitem (
                                    Hoorcollege_idHoorcollege ,
                                    BibliotheekItem_idBibliotheekItem
                                    )
                                    VALUES (
                                    '". $nieuweId . "', '" . $keuze_txt . "'
                                    )");
    }

    $resultaat = $db->Execute("INSERT INTO hoorcollege_onderwerphoorcollege (
                                Onderwerp_idOnderwerp ,
                                Onderwerp_Vak_idVak ,
                                Hoorcollege_idHoorcollege
                                )
                                VALUES (
                                '". $ond . "', '". $vak . "', '". $nieuweId ."'
                                )");

    foreach ($arrStudids as $value) {
        $resultaat = $db->Execute("INSERT INTO hoorcollege_gebruikerhoorcollege (
                                    Gebruiker_idGebruiker,
                                    Hoorcollege_idHoorcollege ,
                                    reedsBekeken ,
                                    VBCVerplicht
                                    )
                                    VALUES (
                                    '". (int)$value . "', '". $nieuweId ."', '0', '0'
                                    )");
    }

    return $nieuweId;
}

function getWijzigenHoorcollege($id) {
    global $db;
    $id  = (int)$id;

    $uitvoer = array();
    $uitvoer["vak"] = 0;
    $uitvoer["ond"] = 0;
    $uitvoer["naam"] = "";
    $uitvoer["keuze_flv"] = -1;
    $uitvoer["keuze_mp3"] = -1;
    $uitvoer["keuze_txt"] = -1;
    $uitvoer["studenten"] = "";

    $resultaat = $db->GetRow("SELECT * FROM hoorcollege_onderwerphoorcollege WHERE Hoorcollege_idHoorcollege = ".$id);
    $uitvoer["vak"] = $resultaat["Onderwerp_Vak_idVak"];
    $uitvoer["ond"] = $resultaat["Onderwerp_idOnderwerp"];
    $uitvoer["naam"] = $db->GetOne("SELECT naam FROM hoorcollege_hoorcollege WHERE idHoorcollege  =".$id);

    $resultaat = $db->Execute('SELECT BibliotheekItem_idBibliotheekItem, mimetype
                                FROM hoorcollege_hoorcollegbibliotheekitem i
                                INNER JOIN hoorcollege_bibliotheekitem b ON ( i.BibliotheekItem_idBibliotheekItem = b.idBibliotheekItem )
                                WHERE i.Hoorcollege_idHoorcollege = ' . $id);
    while (!$resultaat->EOF) {
        $uitvoer["keuze_".$resultaat->fields["mimetype"]] = $resultaat->fields["BibliotheekItem_idBibliotheekItem"];
        $resultaat->MoveNext();
    }


    $studentids = "";
    
    $resultaat = $db->Execute('SELECT Gebruiker_idGebruiker
                                FROM hoorcollege_gebruikerhoorcollege
                                WHERE Hoorcollege_idHoorcollege = ' . $id);
    while (!$resultaat->EOF) {
        $studentids .= $resultaat->fields["Gebruiker_idGebruiker"].",";
        $resultaat->MoveNext();
    }

     if (strlen($studentids) > 0){
        $studentids = substr_replace($studentids,"",-1);
     }

     $uitvoer["studenten"] = $studentids;

     arrayNaarUTF($uitvoer);
     return $uitvoer;
}

function wijzigHoorcollege($id, $vak, $ond, $naam, $keuze_flv, $keuze_mp3, $keuze_txt, $arrStudids){
    global $db;
    $id = (int)$id;
    $vak = (int)$vak;
    $ond = (int)$ond;
    $keuze_flv = (int)$keuze_flv;
    $keuze_mp3 = (int)$keuze_mp3;
    $keuze_txt = (int)$keuze_txt;
    $naam = addslashes($naam);
    
    $db->Execute("UPDATE hoorcollege_onderwerphoorcollege SET Onderwerp_idOnderwerp  = '" . $ond . "', Onderwerp_Vak_idVak = '". $vak ."'
                WHERE Hoorcollege_idHoorcollege = ".$id);
    $db->Execute("UPDATE hoorcollege_hoorcollege SET naam = '" . $naam . "' WHERE idHoorcollege = ". $id);
    $db->Execute("DELETE FROM hoorcollege_hoorcollegbibliotheekitem WHERE Hoorcollege_idHoorcollege = ". $id);

    if ($keuze_flv>0){
        $resultaat = $db->Execute("INSERT INTO hoorcollege_hoorcollegbibliotheekitem (
                                Hoorcollege_idHoorcollege ,
                                BibliotheekItem_idBibliotheekItem
                                )
                                VALUES (
                                '". $id . "', '" . $keuze_flv . "'
                                )");
    }

    if ($keuze_mp3>0){
        $resultaat = $db->Execute("INSERT INTO hoorcollege_hoorcollegbibliotheekitem (
                                Hoorcollege_idHoorcollege ,
                                BibliotheekItem_idBibliotheekItem
                                )
                                VALUES (
                                '". $id . "', '" . $keuze_mp3 . "'
                                )");
    }

    if ($keuze_txt>0){
        $resultaat = $db->Execute("INSERT INTO hoorcollege_hoorcollegbibliotheekitem (
                                Hoorcollege_idHoorcollege ,
                                BibliotheekItem_idBibliotheekItem
                                )
                                VALUES (
                                '". $id . "', '" . $keuze_txt . "'
                                )");
    }



    //huidige studenten vergelijken
    $studentenOud = array();
    $resultaat = $db->Execute('SELECT Gebruiker_idGebruiker
                                FROM hoorcollege_gebruikerhoorcollege
                                WHERE Hoorcollege_idHoorcollege = ' . $id);
    while (!$resultaat->EOF) {
        $studentenOud[] = $resultaat->fields["Gebruiker_idGebruiker"];
        $resultaat->MoveNext();
    }


    $vergelijk = vergelijkArrays($studentenOud,$arrStudids);


    foreach ($vergelijk["verwijderd"] as $value) {
        $resultaat = $db->Execute("DELETE FROM hoorcollege_gegevenantwoord WHERE Gebruiker_idGebruiker = ".(int)$value);
        $resultaat = $db->Execute("DELETE FROM hoorcollege_vbc WHERE Gebruiker_idGebruiker = ".(int)$value);
        $resultaat = $db->Execute("DELETE FROM hoorcollege_gebruikerhoorcollege WHERE Gebruiker_idGebruiker = ". (int)$value ." AND Hoorcollege_idHoorcollege = ".$id);
    }

    foreach ($vergelijk["nieuw"] as $value) {
        $resultaat = $db->Execute("INSERT INTO hoorcollege_gebruikerhoorcollege (
                            Gebruiker_idGebruiker,
                            Hoorcollege_idHoorcollege ,
                            reedsBekeken ,
                            VBCVerplicht
                            )
                            VALUES (
                            '". (int)$value . "', '". $id ."', '0', '0'
                            )");
    }


}

function geeftLectorHoorcollege($lectorid, $hoorcollegeid){
    global $db;
    $lectorid = (int)$lectorid;
    $hoorcollegeid  = (int)$hoorcollegeid;

    $vakken = array();

    $resultaat = $db->Execute('SELECT Vak_idVak
                            FROM hoorcollege_gebruiker_beheert_vak
                            WHERE Gebruiker_idGebruiker = ' . $lectorid);
    while (!$resultaat->EOF) {
        $vakken[] = $resultaat->fields["Vak_idVak"];
        $resultaat->MoveNext();
    }

    return in_array($db->GetOne("SELECT Onderwerp_Vak_idVak
                                FROM hoorcollege_onderwerphoorcollege
                                WHERE Hoorcollege_idHoorcollege = ".$hoorcollegeid), $vakken);
}

function vergelijkArrays($oudeArray=array(), $nieuweArray=array()){
    if(!is_array($oudeArray) || !is_array($nieuweArray)){return false;};

    $oudeArray=array_unique($oudeArray);
    $nieuweArray=array_unique($nieuweArray);
    $beiden=array_intersect($oudeArray, $nieuweArray);

    return array(
    'beiden'=>$beiden,
    'nieuw'=>array_diff($nieuweArray, $oudeArray),
    'verwijderd'=>array_diff($oudeArray, $beiden)
    );
    
}

function arrTest(){
    $ids1 = array();
    $ids2 = array();

    $ids1[] = 1;$ids1[] = 2;$ids1[] = 3;$ids1[] = 4;
    $ids2[] = 5;$ids2[] = 2;$ids2[] = 3;$ids2[] = 4;

    echo "<pre>";
    print_r(vergelijkArrays($ids1,$ids2));
    echo "</pre>";
}

function maakMCVragen($id,$arr){
    global $db;
    $id = (int)$id;

    $juist = array();

    foreach ($arr as $sleutel => $waarde){
       $db->Execute("INSERT INTO hoorcollege_vraag (
                    idVraag ,
                    vraagstelling ,
                    juistantwoord ,
                    Hoorcollege_idHoorcollege
                    )
                    VALUES (
                    NULL , '" . addslashes($waarde["vraagstelling"]) . "', NULL , '" . $id . "'
                    )");
         $nieuweId = $db->Insert_ID();
        foreach ($waarde["mogelijkantwoorden"] as $sleutel2 => $waarde2 ){
            $db->Execute("INSERT INTO hoorcollege_mogelijkantwoord (
                            idMogelijkAntwoord ,
                            antwoord ,
                            Vraag_idVraag
                            )
                            VALUES (
                            NULL , '" . $waarde2["antwoord"] . "', '" . $nieuweId . "'
                            ) ");

            if ($waarde2["juist"] == "1"){
                $nieuweIdAnt = $db->Insert_ID();
                $juist[$nieuweId] = $nieuweIdAnt;
            }

        }
    }

    foreach ($juist as $sleutel => $waarde ){
        $db->Execute("UPDATE hoorcollege_vraag SET juistantwoord  = '". $waarde ."' WHERE hoorcollege_vraag.idVraag = ". $sleutel);
    }

}

function maakVBC($id,$aantal,$audio,$arrIds){
    global $db;
    $id = (int)$id;
    $aantal = (int)$aantal;
    $audio = (int)$audio;

    $db->Execute("UPDATE hoorcollege_hoorcollege SET VBC_aantal = '"  . $aantal .  "',
                  VBC_geluid = '" . $audio . "' WHERE idHoorcollege = " . $id);


    $studentids = "";


    foreach($arrIds as $waarde){
        $studentids .= (int)$waarde.",";
    }

     if (strlen($studentids) > 0){
        $studentids = substr_replace($studentids,"",-1);
     }

      $db->Execute("UPDATE hoorcollege_gebruikerhoorcollege SET VBCVerplicht = 1 WHERE Gebruiker_idGebruiker IN(". $studentids .") AND Hoorcollege_idHoorcollege = ".$id);
}

?>
