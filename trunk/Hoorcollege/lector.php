<?php
include_once('./includes/kern.php');
session_start();

$TBS = new clsTinyButStrong;


if(isset ($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 40){
    $config["pagina"] = "./lector/lector.html";
}else if(!isset ($_SESSION['gebruiker'])){
    header("location: login.php");
}else{
    $config["pagina"] = "./FileUpload/Error1Login.html";
}


$TBS->LoadTemplate('./html/template.html') ;
$TBS->Show() ;

?>
