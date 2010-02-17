<?php
    require_once('./includes/kern.php');    
    $TBS = new clsTinyButStrong;

    session_start();

    $typeboodschap = 'fout';

    $foutboodschap = '';
    $foutboodschap2 = '';
    $foutboodschap3 = '';
    $foutboodschap4 = '';    

    //dit is de main content
    $config["pagina"] = "./admin/admin.html";

    if(!isset ($_SESSION['gebruiker']) || !$_SESSION['gebruiker']->getNiveau() == "99") {
        header("location: index.php");
    }
    else if(isset ($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == "99" ) {
        $correct = true;
        //bepalen of er een sub-content geladen moet worden
        if(isset ($_GET['actie'])) {
            $config["pagina"] = "./admin/" . $_GET['actie'] . ".html";
        }

        if(isset ($_POST['knopvoegtoe'])) {
            $config["pagina"] = "./admin/student.html";
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
        else if(isset ($_POST['knopvoegtoevak'])) { //indien men een nieuwe vak probeert aan te maken in vak.html
            $config["pagina"] = "./admin/vak.html";
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
                    $typeboodschap = "juist";
                    $foutboodschap = 'Vak is succesvol aangemaakt!'; //dit is geen foutboodschap
                }
            }
        }
        else if(isset ($_POST['toekennenknop'])) { //indien men een lector wil toekennen aan een vak in vak.html
            $config["pagina"] = "./admin/vak.html";
            //validatie van de toekenning
            if(beheertLectorVak($_POST['selectlector'], $_POST['selectvak'])) {
                $foutboodschap2 = "Actie niet gelukt: lector is al toegekend aan dit vak of er is een technisch probleem opgedoken!";
            }
            else {
                if(!kenLectorToeAanVak($_POST['selectlector'], $_POST['selectvak'])) {
                    $foutboodschap2 = "Actie niet gelukt: waarschijnlijk te wijten aan een technisch probleem!";
                }
                else {
                    $typeboodschap = "juist";
                    $foutboodschap2 = 'Lector is succesvol aan vak toegekent!'; //dit is geen foutboodschap
                }
            }
            
        }
        else if(isset ($_POST['knopvoegtoegroep'])) { //indien men een groep probeert aan te maken via groep.html
            $config["pagina"] = "./admin/groep.html";
            //validatie van aanmaak groep
            if(empty ($_POST['groepnaam'])) {
                $foutboodschap = 'U moet een naam opgeven!';
            }
            else if(bestaatGroep($_POST['groepnaam'])) {
                $foutboodschap = 'Deze groep bestaat al!';
            }
            else {
                if(!voegGroepToe($_POST['groepnaam'])) {
                    $foutboodschap = 'Door een technische probleem kon de groep niet aangemaakt worden, proper later nog eens!';
                }
                else {
                    $typeboodschap = "juist";
                    $foutboodschap = 'Groep is succesvol aangemaakt!'; //dit is geen foutboodschap
                }
            }
        }
        else if(isset ($_POST['toekennenstudentknop'])) { //indien men een student aan een groep wilt toekennen in groep.html
            $config["pagina"] = "./admin/groep.html";

            //validatie van toekennen van student aan groep
            if($_POST['selectstudent'] != 'kies' && $_POST['selectgroep'] != 'kies') {
                if(isStudentToegekentAanGroep($_POST['selectstudent'])) {
                    $foutboodschap2 = 'Student behoort al tot een groep!';
                }
                else {
                    if(!kenStudentToeAanGroep($_POST['selectstudent'], $_POST['selectgroep'])) {
                        $foutboodschap2 = 'Door een technische probleem kon de actie niet uitgevoerd worden, proper later nog eens!';
                    }
                    else {
                        $typeboodschap = "juist";
                        $foutboodschap2 = 'Student is succesvol aan groep toegevoegd!!'; //dit is geen foutboodschap
                    }
                }
            }            
        }
        else if(isset ($_POST['verwijderstudentuitgroepknop'])) { //student uit groep verwijderen in groep.html
            $config["pagina"] = "./admin/groep.html";            
            if($_POST['selectstudentverwijder'] != 'kies' && $_POST['selectgroepverwijder'] != 'kies') {
                if(!isStudentToegekentAanGroep2($_POST['selectstudentverwijder'], $_POST['selectgroepverwijder'])) {
                    $foutboodschap3 = 'Student behoort niet tot deze groep!';
                }
                else {
                    verwijderStudentVanGroep($_POST['selectstudentverwijder'], $_POST['selectgroepverwijder']);
                    $typeboodschap = "juist";
                    $foutboodschap3 = 'Student is succesvol verwijderd van de groep!'; //dit is geen foutboodschap
                }
            }
        }
        else if(isset ($_POST['verwijderallestudentuitgroepknop'])) { //alle studenten uit groep verwijderen in groep.html
            $config["pagina"] = "./admin/groep.html";
            if($_POST['selectgroepverwijderhelegroep'] != 'kies') {
                if(isGroepLeeg($_POST['selectgroepverwijderhelegroep'])) {
                    $foutboodschap4 = "Deze groep heeft geen studenten!";
                }
                else {
                    verwijderAlleStudentenVanGroep($_POST['selectgroepverwijderhelegroep']);
                    $typeboodschap = "juist";
                    $foutboodschap4 = 'Studenten zijn succesvol verwijderd van de groep!'; //dit is geen foutboodschap
                }
            }
        }
        else if(isset ($_POST['verdertoekennengroepaanvakknop'])) {
            if($_POST['selectgroep'] != 'kies' && $_POST['selectvak2'] != 'kies') {
                $config["pagina"] = "./admin/toekennengroepaanvak.html";
                $groep = $_POST['selectgroep'];
                $vak = $_POST['selectvak2'];
                $vaknaam = getVakNameViaId($_POST['selectvak2']);
            }
            else {
                $config["pagina"] = "./admin/vak.html";
            }
        }
        else if(isset ($_POST['toekennengroepaanvakknop'])) { //alle studenten uit groep toekennen aan een vak in vak.html           
            $config["pagina"] = "./admin/vak.html";
            if(!isGroepToegekentAanVak($_POST['groep'], $_POST['vak'])) {
                if(beheertLectorVak($_POST['vakvan'], $_POST['vak'])) {
                    if(kenGroepToeAanVak($_POST['groep'], $_POST['vak'], $_POST['vakvan'])) {
                        $typeboodschap = "juist";
                        $foutboodschap3 = 'Alle leerlingen van deze groep zijn aan het vak toegekent.';
                    }
                    else {
                       $typeboodschap = "fout";
                       $foutboodschap3 = 'Actie is niet volledig uitgevoerd omwille van een technisch probleem, gelieve zelf te controleren of alle studenten van deze groep correct zijn gelinkt aan het vak!';
                    }
                }
            }
            else {
                $typeboodschap = "fout";
                $foutboodschap3 = 'Actie niet gelukt: één of meerdere studenten zijn al aan dit vak toegekent, gelieve eerst de groep te ontkoppelen van dit vak vooraleerst dit te voltooien!';
            }
        }
        else if(isset ($_POST['annuleertoekennengroepaanvakknop'])) {
            header('location: admin.php?actie=vak');
        }
        else if(isset ($_POST['verderontkoppelengroepvanvakknop'])) {
            if(isGroepToegekentAanVak($_POST['selectgroep2'], $_POST['selectvak3'])) {
                if($_POST['selectgroep2'] != 'kies' && $_POST['selectvak3'] != 'kies') {
                    $config["pagina"] = "./admin/ontkoppelengroepvanvak.html";
                    $groep = $_POST['selectgroep2'];
                    $vak = $_POST['selectvak3'];
                    $vaknaam = getVakNameViaId($_POST['selectvak3']);
                }
                else {
                    $config["pagina"] = "./admin/vak.html";
                }
            }
            else {
                $config["pagina"] = "./admin/vak.html";
                $typeboodschap = "fout";
                $foutboodschap4 = 'Groep is niet aan dit vak gelinkt!';
            }
        }


        
        $TBS->LoadTemplate('./html/admin/templateAdmin.html');
        //indien bepaalde subcontenten geladen moeten worden, moeten bepaalde gegevens uit de db worden gehaald
        if($config["pagina"] == "./admin/student.html") {
            //tabel aanmaken voor overzicht studenten
            $TBS->MergeBlock('blk1', $db, 'SELECT * FROM hoorcollege_gebruiker WHERE actief = 1 GROUP BY naam, voornaam asc');
        }
        else if($config["pagina"] == "./admin/vak.html") {
            //tabel aanmaken voor overzicht vakken
            $TBS->MergeBlock('blk2', $db, 'SELECT * FROM hoorcollege_vak GROUP BY naam asc');
            //select veld aanmaken voor overzicht lectoren
            $TBS->MergeBlock('blk3', $db, 'SELECT * FROM hoorcollege_gebruiker WHERE niveau != 1 GROUP BY naam, voornaam asc');
            //select veld aanmaken voor overzicht vakken
            $TBS->MergeBlock('blk4', $db, 'SELECT * FROM hoorcollege_vak GROUP BY naam asc');
            //select veld aanmaken voor overzicht groepen
            $TBS->MergeBlock('blk10', $db, 'SELECT * FROM hoorcollege_groep GROUP BY naam asc');
            //select veld aanmaken voor overzicht vakken
            $TBS->MergeBlock('blk11', $db, 'SELECT * FROM hoorcollege_vak GROUP BY naam asc');  
            //select veld aanmaken voor overzicht groepen
            $TBS->MergeBlock('blk14', $db, 'SELECT * FROM hoorcollege_groep GROUP BY naam asc');
            //select veld aanmaken voor overzicht vakken
            $TBS->MergeBlock('blk15', $db, 'SELECT * FROM hoorcollege_vak GROUP BY naam asc');
        }
        else if($config["pagina"] == "./admin/groep.html") {
            //select veld voor overzicht studenten
            $TBS->MergeBlock('blk5', $db, 'SELECT * FROM hoorcollege_gebruiker WHERE niveau = 1 GROUP BY naam, voornaam asc');
            //select veld voor overzicht groepen
            $TBS->MergeBlock('blk6', $db, 'SELECT * FROM hoorcollege_groep GROUP BY naam asc');

            //select veld voor overzicht studenten voor te verwijderen functie
            $TBS->MergeBlock('blk7', $db, 'SELECT * FROM hoorcollege_gebruiker WHERE niveau = 1 GROUP BY naam, voornaam asc');
            //select veld voor overzicht groepen voor te verwijderen functie
            $TBS->MergeBlock('blk8', $db, 'SELECT * FROM hoorcollege_groep GROUP BY naam asc');

            //select veld voor overzicht groepen voor te verwijderen alle studenten uit groep functie
            $TBS->MergeBlock('blk9', $db, 'SELECT * FROM hoorcollege_groep GROUP BY naam asc');
        }
        else if($config["pagina"] == "./admin/toekennengroepaanvak.html") {
            //select veld aanmaken voor overzicht lectoren
            $TBS->MergeBlock('blk12', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                            FROM hoorcollege_gebruiker AS g
                                            LEFT JOIN hoorcollege_gebruiker_beheert_vak AS hb ON g.idGebruiker = hb.Gebruiker_idGebruiker
                                            WHERE g.niveau !=1 && hb.Vak_idVak = '$vak'
                                            GROUP BY g.naam, g.voornaam ASC");
            //overzicht alle leerlingen van een groep
            $TBS->MergeBlock('blk13', $db, "SELECT CONCAT( g.naam, ', ', g.voornaam ) AS student
                                            FROM hoorcollege_gebruiker g
                                            LEFT JOIN hoorcollege_gebruikergroep gr ON g.idGebruiker = gr.Gebruiker_idGebruiker
                                            WHERE gr.Groep_idGroep = '$groep'
                                            GROUP BY g.naam, g.voornaam ASC");
        }
        else if($config["pagina"] == "./admin/ontkoppelengroepvanvak.html") {
            //overzicht alle leerlingen van een groep
            $TBS->MergeBlock('blk15', $db, "SELECT CONCAT( g.naam, ', ', g.voornaam ) AS student
                                            FROM hoorcollege_gebruiker g
                                            LEFT JOIN hoorcollege_gebruikergroep gr ON g.idGebruiker = gr.Gebruiker_idGebruiker
                                            WHERE gr.Groep_idGroep = '$groep'
                                            GROUP BY g.naam, g.voornaam ASC");
        }

        $TBS->Show() ;
    }    
?>
