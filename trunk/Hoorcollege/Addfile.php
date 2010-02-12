<?php
session_start();
include_once('./includes/TinyButStrong.php');
$TBS = new clsTinyButStrong;

$config["pagina"] = "AddFile.html";


$testinhoud = "";

$TBS->LoadTemplate('./html/template.html') ;


$TBS->Show() ;

?>
