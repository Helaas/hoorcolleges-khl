<?php

    $config["server"] = true; //True = Thuisservers | false = web-k
    $fout = false;

    if ($_SERVER["SERVER_NAME"] == "www.web-k.be") $config["server"] = false;

    ## SQL en SQL abstractie

    require_once("adodb_lite/adodb.inc.php");
    $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
    $db = ADONewConnection("mysql"); # bvb. 'mysql' of 'oci8' en anderen
    //$db->debug = true;


    if ($config["server"]){
        $config["dbnaam"] = "hoorcolleges"; // Database naam
        $config["bdgebruiker"] = "root"; // Gebruikersnaam
        $config["dbwachtwoord"] = ""; // Password
    } else {
        $config["dbnaam"] = "web_k_be"; // Database naam
        $config["bdgebruiker"] = "web_k_be"; // Gebruikersnaam
        $config["dbwachtwoord"] = "pJ7xtbvU"; // Password
    }

    // Verbinden

    $db->Connect("localhost", $config["bdgebruiker"], $config["dbwachtwoord"], $config["dbnaam"]);

    //Eens die amazon dinges proberen
    //$db->Connect("ec2-79-125-51-239.eu-west-1.compute.amazonaws.com:3700","wortel", "nutella", "hoorcolleges");



    ## Template en het scheiden van HTML en
    require_once('TinyButStrong.php');
    require_once('tbsdb_pearADO.php'); //Speciale module door Kevin Vranken om communicatie TBS <-> ADodb Lite mogelijk te maken
    $TBS = new clsTinyButStrong;

    ## Gebruikers
    require_once('Gebruiker.class.php');

   ## Gebruikers
    require_once('functions.php');


?>