<?php
session_start();
include_once('./includes/kern.php');
session_destroy();

$TBS = new clsTinyButStrong;

$config["pagina"] = "logout.html";

$TBS->LoadTemplate('./html/template.html') ;
$TBS->Show() ;


?>
