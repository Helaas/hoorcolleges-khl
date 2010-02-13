<?php
include_once('./includes/kern.php');
$config["pagina"] = "login.html";

$TBS = new clsTinyButStrong;


session_start();

$foutboodschap = 'Dit is de foutboodschap';
$fout = false;

if (isset($_POST["inloggen"])){
    $email = $_POST['email'];
    $paswoord = md5($_POST['paswoord']);
    if(bestaatEmail($email)){
            if(paswoordOk($email, $pw)){
                $fout = true;
                $foutboodschap = getGebruiker($email);
               //$gebruiker =
               //$_SESSION['gebruiker'] = $gebruiker;
            }else{
               $fout = true;
               $foutboodschap = 'Fout wachtwoord';
          }
    }else{
          $fout = true;
          $foutboodschap = 'Gelieve u eerst te registreren';
    } 
}


$TBS->LoadTemplate('./html/template.html') ;
$TBS->Show() ;

?>
