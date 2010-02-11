<?php
session_start();
include_once('./includes/TinyButStrong.php');
$TBS = new clsTinyButStrong;

$config["pagina"] = "index.html";


$testinhoud = "Hallo wereld";

$TBS->LoadTemplate('./html/template.html') ;


$TBS->Show() ;

?>