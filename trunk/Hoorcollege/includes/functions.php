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

    function voegGebruikerToe($naam, $voornaam, $email) {
        global $db;
        $pasww = generatePassword();
        $gelukt =  $db->Execute("insert into hoorcollege_gebruiker (naam, voornaam, email, wachtwoord, niveau)
                                    values('$naam', '$voornaam', '$email', '$pasww', '1')");
        //gebruiker mailen
        $boodschap = "Geachte $voornaam, $naam\n\nVanaf nu kan u hoorcollges volgen op KHL - Hoorcolleges.\n"
        . "U inlog gegevens: \n\nGebruikernaam: $email\nPasswoord: $pasww\n\nMet vriendelijke groeten.\n\n"
        ."Het Katholieke Hogeschool Leuven.";
        $verstuurd = mail("$email", 'KHL - Belangrijk: login gegevens: Hoorcolleges', $boodschap);
        if(!$verstuurd) {
            //indien geen mail kon worden verzonden
            $gelukt = $verstuurd;
            verwijderGebruiker($email);
        }

        return $gelukt;
    }

    function verwijderGebruiker($email) {
       global $db;       
       $db->Execute("delete from hoorcollege_gebruiker WHERE email = '$email'");
    }

    //deze functie is niet zelf geschreven, wel lichtjes aangepast, bron: http://www.laughing-buddha.net/jon/php/password/
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
      return md5($password);

    }

    //functie om na te gaan of het paswoord juist is
    function paswoordOk($email, $pw) {
        global $db;
        $resultaat = $db->Execute("select count( distinct email ) as aantal
                                   from hoorcollege_gebruiker where email = '$email'
                                    and wachtwoord = '$pw'");

        if($resultaat->fields["aantal"] > 0) {
            return true;
        }
        else {
            return false;
        }
    }

?>
