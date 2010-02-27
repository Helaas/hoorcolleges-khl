<?php
include_once('./includes/kern.php');
session_start();
$config["pagina"] = "./admin/hulp.html";
$TBS = new clsTinyButStrong;
$TBS->LoadTemplate('./html/admin/templateAdmin.html') ;
$TBS->Show() ;
?>
