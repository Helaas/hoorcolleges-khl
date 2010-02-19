<?php
include_once('./includes/kern.php');
session_start();
$config["pagina"] = "login.html";
$TBS = new clsTinyButStrong;

$foutboodschap = '';

if (isset($_POST["inloggen"])){
    $email = $_POST['email'];
    $paswoord = md5($_POST['paswoord']);
    //Validatie van emailadres
    if(validateEmail($email)){
         //Nagaan of gebruiker bestaat
         if(bestaatEmail($email)){
            //Nagaan of paswoord juist is
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
    }else{
        $fout = true;
        $foutboodschap = 'Ongeldig emailadres!';
    }
}


$TBS->LoadTemplate('./html/template.html') ;
$TBS->Show() ;

?>
