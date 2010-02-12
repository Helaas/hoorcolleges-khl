<?php

    $config["server"] = true; //True = Thuisservers | false = web-k

    ## SQL en SQL abstractie

    require_once("adodb_lite/adodb.inc.php");
    $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
    $db = ADONewConnection("mysql"); # bvb. 'mysql' of 'oci8' en anderen
    //$db->debug = true;


    if ($config["server"]){
        $config["dbnaam"] = "web_k_be"; // Database naam
        $config["bdgebruiker"] = "root"; // Gebruikersnaam
        $config["dbwachtwoord"] = ""; // Password
    } else {
        $config["dbnaam"] = "web_k_be"; // Database naam
        $config["bdgebruiker"] = "web_k_be"; // Gebruikersnaam
        $config["dbwachtwoord"] = "pJ7xtbvU"; // Password
    }

    // Verbinden

    $db->Connect("localhost", $config["bdgebruiker"], $config["dbwachtwoord"], $config["dbnaam"]);


    ## Template en het scheiden van HTML en 
    require_once('TinyButStrong.php');
    require_once('tbsdb_pearADO.php'); //Speciale module door Kevin Vranken om communicatie TBS <-> ADodb Lite mogelijk te maken
    $TBS = new clsTinyButStrong;

    ## Gebruikers
    require_once('./includes/gebruiker.class.php');


?>
