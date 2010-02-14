<?php
include_once('./includes/kern.php');
$config["pagina"] = "login.html";
 session_start();
$TBS = new clsTinyButStrong;

$foutboodschap = 'Dit is de foutboodschap';
$fout = false;

if (isset($_POST["inloggen"])){
    $email = $_POST['email'];
    $paswoord = md5($_POST['paswoord']);
   if(bestaatEmail($email)){
            if(paswoordOk($email, $paswoord)){
                $resultaat = getGebruiker($email);
                $gebruiker = new Gebruiker($resultaat['idGebruiker'], $resultaat['naam'], $resultaat['voornaam'], $resultaat['email'], $resultaat['wachtwoord'], $resultaat['niveau']);
                $_SESSION['gebruiker'] = $gebruiker;

                if($gebruiker->getNiveau() == 99){                       //Wanneer gebruiker admin is
                    header('location: admin.php');
                }else if($gebruiker->getNiveau() >= 40){                  //Wanneer gebruiker lector is
                    header('location: lector.php');
                }else{     
                    header('location: student.php');                    //Wanneer gebruiker student is
                }

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
