<?php
    require_once('./includes/kern.php');
    require_once('./includes/functions.php');
    $TBS = new clsTinyButStrong;

    session_start();

    $foutboodschap = '';

    if(isset ($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == "99" && isset ($_GET['actie'])) {
        //bepalen welke content geladen moet worden
        $config["pagina"] = "./admin/" . $_GET['actie'] . ".html";

        if($_GET['actie'] == 'student') {
            //table aanmaken voor de studentenoverzicht op student.html
            $TBS->LoadTemplate('./html/template.html') ;
            $TBS->MergeBlock('blk1', $db, 'SELECT * FROM hoorcollege_gebruiker');
            $TBS->Show() ;
        }
        else {
            $TBS->LoadTemplate('./html/template.html') ;
            $TBS->Show() ;
        }


    }
    else if(isset ($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == "99" ) {
        $correct = true;
        //bepalen welke content geladen moet worden
        $config["pagina"] = "./admin/admin.html";

        if(isset ($_POST['knopvoegtoe'])) {
            //controleren of alle gegevens correct zijn            
            if(empty ($_POST['naam']) || empty ($_POST['voornaam']) || empty ($_POST['email'])) {
                $correct = false;

                $email = $_POST['email'];
                //gegevens ophalen uit database
                $resultaat = $db->Execute("SELECT COUNT( DISTINCT email ) AS aantal
                                   FROM `hoorcollege_gebruiker` WHERE email = '$email'");

                if(bestaatEmail($email)) {
                    $foutboodschap = "Email adres is al toegekent aan een andere gebruiker!";
                }
                else {
                    $foutboodschap = "Alle velden moeten ingevuld zijn!";
                }
            }

            if(!$correct) {                
                //content wijzigen, omdat er een fout is, moet terug de content van student.html opgehaald worden                
                $config["pagina"] = "./admin/student.html";
            }
            else {
                //gebruiker toevoegen aan databank
            }
        }

        $gebruiker = $_SESSION['gebruiker'];
        $TBS->LoadTemplate('./html/template.html') ;
        if(!$correct) {
            //tabel aanmaken voor overzicht
            $TBS->MergeBlock('blk1', $db, 'SELECT * FROM hoorcollege_gebruiker');
        }
        $TBS->Show() ;
    }
    else {
        echo 'Niet ingelogd';
    }
  
?>
