<?php
include_once('./includes/kern.php');
session_start();
$config["pagina"] = "/student/paswoordVeranderen.html";
$TBS = new clsTinyButStrong;

$foutboodschap = '';
$gelukt = false;

if(isset ($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 1) {
    if (isset($_POST["paswoordVeranderen"])) {
        $paswoord = $_POST['oudPW'];
        $email = $_SESSION['gebruiker']->getEmail();
        if(paswoordOk($email, md5($paswoord))) {
            if($_POST['nieuwPW'] == $_POST['herhaalNieuwPW']) {
                $nieuwPW = md5($_POST['nieuwPW']);
                veranderPaswoord($_SESSION['gebruiker']->getIdGebruiker(), $nieuwPW);
                $gelukt = true;
            }else {
                $fout = true;
                $foutboodschap = 'Het ingegeven nieuwe paswoord komt niet overeen met de herhaling van het paswoord.';
            }
        }else{
            $fout = true;
            $foutboodschap = 'Uw ingegeven paswoord is incorrect.';
        }
    }
}else if(!isset ($_SESSION['gebruiker'])) {
    header("location: login.php");
}else {
    $config["pagina"] = "./FileUpload/Error1Login.html";
    $TBS->LoadTemplate('./html/template.html') ;
}


$TBS->LoadTemplate('./html/student/templateStudent.html') ;
$TBS->Show() ;

?>
