<?php
include_once('./includes/kern.php');
session_start();

$TBS = new clsTinyButStrong;
$config["pagina"] = "index.html";


if(isset($_SESSION['gebruiker'])){
    if($_SESSION['gebruiker']->getNiveau() == 1){
        $TBS->LoadTemplate('./html/student/templateStudent.html');
    }elseif($_SESSION['gebruiker']->getNiveau() == 40){
        $TBS->LoadTemplate('./html/lector/templateLector.html');
    }else if($_SESSION['gebruiker']->getNiveau() == 99){
        $TBS->LoadTemplate('./html/admin/templateAdmin.html');
    }

}else{
    $TBS->LoadTemplate('./html/template.html') ;
}


$TBS->Show() ;

?>