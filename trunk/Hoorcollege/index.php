<?php
include_once('./includes/kern.php');
session_start();

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
$config["pagina"] = "index.html";


if(isset($_SESSION['gebruiker'])){
    if($_SESSION['gebruiker']->getNiveau() == 1){
        $TBS->LoadTemplate('./html/student/templateStudent.html');
    }else if($_SESSION['gebruiker']->getNiveau() == 40){
        $TBS->LoadTemplate('./html/lector/templateLector.html');
    }else if($_SESSION['gebruiker']->getNiveau() == 99){
        $TBS->LoadTemplate('./html/admin/templateAdmin.html');
    }
    else {
        $TBS->LoadTemplate('./html/template.html');
        //Hier moest ergens nog rekening gehouden worden met userlevel 99 enzo
        //Slordige code jongens, ik me maar afvragen waarom mijn index altijd leeg was
    }
}else{
    $TBS->LoadTemplate('./html/template.html') ;
}


$TBS->Show() ;

?>
