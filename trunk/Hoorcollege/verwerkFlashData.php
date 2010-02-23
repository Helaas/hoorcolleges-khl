<?php

   require("./includes/kern.php");

/**
 * 	contactVariables.teBekijken = teBekijken.text.toString();
	contactVariables.getoonde = getoonde.text.toString();
	contactVariables.geklikt = geklikt.text.toString();
	contactVariables.hoorcollegeid = hoorcollegeId;
	contactVariables.gebruikerid = gebruikerId;

        $_POST["teBekijken"];
        $_POST["getoonde"];
        $_POST["geklikt"];
        $_POST["hoorcollegeid"];
        $_POST["gebruikerid"];
 *
 */


 if (isset($_POST["hoorcollegeid"]) && isset($_POST["gebruikerid"]) && isset($_POST["teBekijken"]) && isset($_POST["getoonde"]) && isset($_POST["geklikt"])){
    $resultaat = $db->Execute("INSERT INTO hoorcollege_vbc (Gebruiker_idGebruiker, Hoorcollege_idHoorcollege, teBekijken, getoonde, geklikt) VALUES ('" . (int)$_POST["gebruikerid"] . "', '" . (int)$_POST["hoorcollegeid"] . "', '" . (int)$_POST["teBekijken"] . "', '" . (int)$_POST["getoonde"] . "', '" . (int)$_POST["geklikt"] . "')");
    
    if ($resultaat) echo "ok";
    else echo "al";

 }


?>