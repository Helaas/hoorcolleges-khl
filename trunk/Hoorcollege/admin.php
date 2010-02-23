<?php
    require_once('./includes/kern.php');    //kern is essentieel voor DB en heeft includes ivm met functions, etc
    require_once('./admin/adminSpecifiekeFunctions.php');
    session_start();    //functie die werken met sessie enabled
    $TBS = new clsTinyButStrong;            //variable om functies van TinyButStrong te gebruikern
    
    verwerkLogin();

    $config["pagina"] = verwerkPagina();

    $typeboodschap = "fout";

    if(isset ($_POST['terug']) || isset ($_POST['annuleer'])) {
        $config["pagina"] = "./admin/admin.html";
    }
    else if(isset ($_POST['knopvoegtoestudent'])) {
            $config["pagina"] = "./admin/studentToevoegen.html";
            //controleren of alle gegevens correct zijn
            if(empty ($_POST['naam']) || empty ($_POST['voornaam']) || empty ($_POST['email']) || bestaatEmail($_POST['email'])) {
                $email = $_POST['email'];
                if(bestaatEmail($email)) {
                    $foutboodschap = "Email adres is al toegekent aan een andere gebruiker!";
                }
                else {
                    $foutboodschap = "Alle velden moeten ingevuld zijn!";
                }
            }
            else {
                if(!voegGebruikerToe($_POST['naam'], $_POST['voornaam'], $_POST['email'])) {
                    $foutboodschap = "Gebruiker niet toegevoegd, oorzaak: mogelijk onbestaand email adres of technische problemen!";
                }
                else {
                    $typeboodschap = "juist";
                    $foutboodschap = 'Gebruiker is succesvol aangemaakt!'; //dit is geen foutboodschap
                }
            }
        }
        else if(isset ($_POST['lectortotadminpromoveren'])) {
            $config["pagina"] = "./admin/lectorPromoveren.html";
            if(promoveerLector($_POST['selectlector']) && $_POST['selectlector'] != 'kies') {
                $typeboodschap = "juist";
                $foutboodschap = 'Lector is succesvol gepromoveerd!'; // dit is geen foutboodschap
            }
            else if($_POST['selectlector'] == 'kies') {
                $typeboodschap = "fout";
                $foutboodschap = 'U moet een lector selecteren!';
            }
            else {
                $typeboodschap = "fout";
                $foutboodschap = 'Actie werd onderbroken door een technisch probleem!';
            }
        }
        else if(isset ($_POST['knopvoegtoevak'])) { //indien men een nieuwe vak probeert aan te maken in vak.html
            $config["pagina"] = "./admin/aanmakenVakken.html";
            //validatie vak
            if(empty ($_POST['vaknaam'])) {
                $foutboodschap = "Vaknaam mag niet leeg zijn!";
            }
            else if(bestaatVak($_POST['vaknaam'])) {
                $foutboodschap = "Vak bestaat al!";
            }
            else {
                //vak toe voegen
                if(!voegVakToe($_POST['vaknaam'])) {
                    $foutboodschap = "Vak niet toegevoegd omwille van technische problemen, probeer later nog eens!";
                }
                else {
                    //vak koppelen aan lector
                    $naam = (string) $_POST['vaknaam'];
                    $vak = getIdViaName($naam);
                    $lector = (int) $_POST['selectlector'];
                    kenLectorToeAanVak($lector, $vak);
                    $typeboodschap = "juist";
                    $foutboodschap = 'Vak is succesvol aangemaakt!'; //dit is geen foutboodschap
                }
            }
        }
        else if(isset ($_POST['verdertoekennengroepaanvakknop'])) {
            if($_POST['selectvak'] != 'kies') {
                $checkbox2 = serialize($_POST['checkboxGroepen']);
                $config["pagina"] = "./admin/toekennengroepaanvak.html";                
                $vak = $_POST['selectvak'];
                $vaknaam = getVakNameViaId($_POST['selectvak']);
            }
            else {
                $config["pagina"] = "./admin/groepVak.html";
            }
        }
        else if(isset ($_POST['toekennengroepaanvakknop'])) { //alle studenten uit groep toekennen aan een vak in vak.html            
            $ch = unserialize($_POST['checkbox2']);
            $vak = (int) $_POST['vak'];
            $van = (int) $_POST['vakvan'];

            $count = count($ch);
            $gelukt = true;
            $allemaal = true;
            for($i=0; $i < $count; $i++) {
                //$gelukt = kenStudentToeAanVak($ch[$i], $vak, $van);                
                if(!isGroepToegekentAanVak($ch[$i], $vak)) {
                    $gelukt = kenGroepToeAanVak($ch[$i], $vak, $van);                    
                }
                else {
                    $allemaal = false;
                }
            }

            if(!$allemaal && $gelukt) {
                $typeboodschap = "juist";
                $foutboodschap = 'Sommige groepen waren al reeds aan het vak toegekent, overige geselecteerden zijn aan het vak toegekent.';
            }
            else if($gelukt == true) {
                $typeboodschap = "juist";
                $foutboodschap = 'Alle leerlingen van de geselecteerde groepen zijn aan het vak toegekent.';
            }
            else {
                $typeboodschap = "fout";
                       $foutboodschap = 'Actie is niet volledig uitgevoerd omwille van een technisch probleem, gelieve zelf te controleren of alle studenten van deze groepen correct zijn gelinkt aan het vak!';
            }

            /*
            if(!isGroepToegekentAanVak($_POST['groep'], $_POST['vak'])) {
                if(beheertLectorVak($_POST['vakvan'], $_POST['vak'])) {
                    if(kenGroepToeAanVak($_POST['groep'], $_POST['vak'], $_POST['vakvan'])) {
                        $typeboodschap = "juist";
                        $foutboodschap = 'Alle leerlingen van deze groep zijn aan het vak toegekent.';
                    }
                    else {
                       $typeboodschap = "fout";
                       $foutboodschap = 'Actie is niet volledig uitgevoerd omwille van een technisch probleem, gelieve zelf te controleren of alle studenten van deze groep correct zijn gelinkt aan het vak!';
                    }
                }*/
            }


    $TBS->LoadTemplate('./html/admin/templateAdmin.html');

    verwerkMergeGegevens($TBS); //noodzakelijk om de correcte    

    $TBS->Show() ;
?>
