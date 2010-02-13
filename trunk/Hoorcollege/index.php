<?php
session_start();
include_once('./includes/kern.php');

$config["pagina"] = "index.html";

$TBS->LoadTemplate('./html/template.html') ;


$TBS->Show() ;

?>