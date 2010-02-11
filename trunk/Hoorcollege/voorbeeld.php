<?php
session_start();
include_once('./includes/TinyButStrong.php');
$TBS = new clsTinyButStrong ;

$fout = false;

if (isset($_POST["zenden"])){
    $fout = true;

}

$TBS->LoadTemplate('./html/voorbeeld.html') ;



$TBS->Show() ;

?>