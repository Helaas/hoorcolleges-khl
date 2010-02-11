<?php
session_start();
include_once('./includes/TinyButStrong.php');
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