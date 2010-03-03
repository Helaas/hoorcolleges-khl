<?php
include_once('./../includes/kern.php');
session_start();

$foutboodschap = '';
$fout = false;

if (isset($_POST["bepaalOverzicht"])){
    if($_SESSION['gebruiker']->getNiveau() == 99){
        header("location:admin.php");
    }elseif($_SESSION['gebruiker']->getNiveau() == 40){
        header("location:lector.php");
    }else {
        header("location:student.php");
    }
    exit();
}

$TBS = new clsTinyButStrong;
$config["pagina"] = "./lector/lector.html";


if(isset($_SESSION['gebruiker'])){
    //Bepalen welke template laden (afhankelijk van niveau gebruiker)
    if($_SESSION['gebruiker']->getNiveau() == 1){
        $TBS->LoadTemplate('./../html/lector/templateLector.html');
    }else if($_SESSION['gebruiker']->getNiveau() == 40){
        $TBS->LoadTemplate('./../html/lector/templateLector.html');
    }else if($_SESSION['gebruiker']->getNiveau() == 99){
        $TBS->LoadTemplate('./../html/lector/templateLector.html');
    }
    else {
        $TBS->LoadTemplate('./../html/lector/templateLector.html');
    }
}else{
    $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
}


$TBS->Show() ;

?>
