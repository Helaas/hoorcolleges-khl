<?php

include_once('./includes/kern.php');
session_start();

$TBS = new clsTinyButStrong;


if(isset ($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 1) {
    $config["pagina"] = "./student/commentaar.html";
    $TBS->LoadTemplate('./html/student/templateStudent.html') ;

}else if(!isset ($_SESSION['gebruiker'])) {
    header("location: login.php");
}else {
    $config["pagina"] = "./FileUpload/Error1Login.html";
    $TBS->LoadTemplate('./html/template.html') ;
}
$TBS->Show();
?>
