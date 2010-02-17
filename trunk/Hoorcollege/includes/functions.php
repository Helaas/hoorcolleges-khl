<?php
    include_once('kern.php');


    //methode om na te gaan of een gebruiker met deze email al bestaat
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

    function voegGroepToe($groep) {
        global $db;
        $gelukt =  $db->Execute("insert into hoorcollege_groep (naam)
                                    values('$groep')");
        return $gelukt;
    }

    function verwijderGebruiker($email) {
       global $db;       
       $db->Execute("delete from hoorcollege_gebruiker WHERE email = '$email'");
    }

    //deze functie is niet zelf geschreven, bron: http://www.laughing-buddha.net/jon/php/password/
    function generatePassword ($length = 8)
    {

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

    //functie om na te gaan of het paswoord juist is
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

    function antwoordOk($gebruikerid, $vraagID){
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

    function getGebruiker($email){
        global $db;
        $resultaat = $db->GetRow("select * from hoorcollege_gebruiker where email = '$email'");
        return $resultaat;
    }

    function heeftHoorcollegeVragen($id){
       $id = (int)$id;
       global $db;

       $resultaat = $db->GetRow("select count(idVraag) as aantal from hoorcollege_vraag where Hoorcollege_idHoorcollege = '$id'");
       return ($resultaat["aantal"]>=1);
    }

?>
