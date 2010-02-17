<?php
include_once('./includes/kern.php');
session_start();

$TBS = new clsTinyButStrong;
$config["pagina"] = "index.html";


if(isset($_SESSION['gebruiker'])){
    if($_SESSION['gebruiker']->getNiveau() == 1){
        $TBS->LoadTemplate('./html/student/templateStudent.html');
    }else if($_SESSION['gebruiker']->getNiveau() == 40){
        $TBS->LoadTemplate('./html/lector/templateLector.html');
    }
}else{
    $TBS->LoadTemplate('./html/template.html') ;
}


$TBS->Show() ;

?>