<?php
include_once('./includes/kern.php');
session_start();

$TBS = new clsTinyButStrong;
$config["pagina"] = "./lector/OndToegevoegd.html";


//IfSucces->
//$tekstinhoud="Het onderwerp ".$_POST['onderwerp'].$_POST['vakID'].$_POST['onderwerpID']." werd toegevoegd";

//Code voor toevoegen van het gekozen onderwerp indien het voldoet aan enkele voorwaarden(validatie), + 'are you sure' check?
//voor query zie TODO file

if(isset($_SESSION['gebruiker'])){
    if($_SESSION['gebruiker']->getNiveau() == 1){
        $TBS->LoadTemplate('./html/student/templateStudent.html');
    }else if($_SESSION['gebruiker']->getNiveau() == 40){



        $TBS->LoadTemplate('./html/lector/templateLector.html');
    }else if($_SESSION['gebruiker']->getNiveau() == 99){
        $TBS->LoadTemplate('./html/admin/templateAdmin.html');
    }
}else{
    $TBS->LoadTemplate('./html/template.html') ;
}


$TBS->Show() ;

?>