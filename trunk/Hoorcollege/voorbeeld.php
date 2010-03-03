<?php
session_start();
include_once('./includes/TinyButStrong.php');
//$subject="http://www.experts-exchange.com";
$url = str_replace(basename($_SERVER["PHP_SELF"]), " ", $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"]);
echo $url;
$TBS = new clsTinyButStrong ;

$fout = false;

//qkelskdlsdls

if (isset($_POST["zenden"])){
    $fout = true;

}

$TBS->LoadTemplate('./html/voorbeeld.html') ;

//wafelhoofd

$TBS->Show() ;

?>