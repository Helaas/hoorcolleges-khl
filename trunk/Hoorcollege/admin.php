<?php
    require_once('./includes/kern.php');    
    $TBS = new clsTinyButStrong;

    session_start();

    $typeboodschap = 'fout';

    $foutboodschap = '';
    $foutboodschap2 = '';
    $foutboodschap3 = '';
    $foutboodschap4 = '';
    $foutboodschap5 = '';

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
        else if(isset ($_POST['verderontkoppelengroepvanvakknop'])) {            
            $config["pagina"] = "./admin/vak.html";
            if($_POST['selectgroep2'] != 'kies' && $_POST['selectvak3'] != 'kies') {
                if(isGroepToegekentAanVak($_POST['selectgroep2'], $_POST['selectvak3'])) {
                    $config["pagina"] = "./admin/ontkoppelengroepvanvak.html";
                    $groep = $_POST['selectgroep2'];
                    $vak = $_POST['selectvak3'];
                    $vaknaam = getVakNameViaId($_POST['selectvak3']);
                }
                else {
                    $config["pagina"] = "./admin/vak.html";
                    $typeboodschap = "fout";
                    $foutboodschap4 = 'Groep is niet aan dit vak gelinkt!';
                }
            }
        }
        else if(isset ($_POST['ontkoppelengroepvanvakknop'])) {
            $config["pagina"] = "./admin/vak.html";
            if(ontkoppelGroepVanVak($_POST['groep'], $_POST['vak'])) {
                $typeboodschap = "juist";
                $foutboodschap4 = 'Alle leden van deze groep zijn succesvol van het vak ontkoppeld!'; // dit is geen foutboodschap
            }
            else {
                $typeboodschap = "fout";
                $foutboodschap4 = 'Actie werd mogelijk niet of niet volledig uitgevoerd, mogelijk door een technisch probleem!';
            }
        }
        else if(isset ($_POST['toonFilterRes'])) {
            $config["pagina"] = "./admin/student.html";
        }
        else if(isset ($_POST['annuleertoekennengroepaanvakknop']) || isset ($_POST['annuleerontkoppelengroepvanvakknop'])) {
            header('location: admin.php?actie=vak');
        }
        else if(isset ($_GET['detailsGebruikerId'])) {
            $config["pagina"] = "./admin/detailsstudent.html";
        }
        else if(isset ($_POST['verderVakIndivudueel'])) {
            $config["pagina"] = "./admin/vakindividueel.html";
        }
        else if(isset ($_POST['verdertoekennenselectiefaanvakknop'])) {
            $config["pagina"] = "./admin/toekennenselectiefaanvak.html";
            $vak = $_POST['selectvak3'];
            $vaknaam = getVakNameViaId($_POST['selectvak3']);            
        }
        else if(isset ($_POST['toekennenselectiefaanvakknop'])) {
            $config["pagina"] = "./admin/vak.html";
            if($_POST['vakvan'] != 'leeg') {                
                $vak = $_POST['vak'];
                $van = $_POST['vakvan'];
                $ch = unserialize($_POST['checkbox2']);
                $count = count($ch);

                $gelukt = true;
                for($i=0; $i < $count; $i++) {
                    $gelukt = kenStudentToeAanVak($ch[$i], $vak, $van);
                }

                if($gelukt) {
                    $typeboodschap = "juist";
                    $foutboodschap5 = 'Alle geselecteerden zijn aan het vak toegevoegd!'; // dit is geen foutboodschap
                }
                else {
                    $typeboodschap = "fout";
                    $foutboodschap5 = 'Technisch probleem, mogelijk is niet iedereen toegekent aan het vak, gelieve manueel te controleren';
                }
            }
            else {
                $typeboodschap = "fout";
                $foutboodschap5 = 'Er moet eerst een lector toegekent worden aan het vak!';
            }
        }
        else if(isset ($_POST['studentenverwijderen'])) {
            $config["pagina"] = "./admin/verwijderenstudent.html";
        }
        else if(isset ($_POST['verderstudentenverwijderen'])) {
            $config["pagina"] = "./admin/overzichtteverwijderenstudenten.html";            
        }
        else if(isset ($_POST['bevestigingverwijderstudentenknop'])) {
            $config["pagina"] = "./admin/student.html";

            $ch = unserialize($_POST['checkbox2']);
            $count = count($ch);

            $gelukt = true;
            for($i=0; $i < $count; $i++) {                
                $gelukt = verwijderStudent($ch[$i]);
            }

            if($gelukt) {
                $typeboodschap = "juist";
                $foutboodschap5 = 'Alle geselecteerden zijn verwijderd!'; // dit is geen foutboodschap
            }
            else {
                $typeboodschap = "fout";
                $foutboodschap5 = 'Technisch probleem, mogelijk is niet iedereen verwijdert, gelieve manueel te controleren';
            }           
        }
        else if(isset ($_POST['lectortotadminpromoveren'])) {
            $config["pagina"] = "./admin/lector.html";
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
        else if(isset ($_GET['detailsLectorId'])) {
            $config["pagina"] = "./admin/lectorDetails.html";
        }
        else if(isset ($_GET['detailsVakId'])) {
            $config["pagina"] = "./admin/vakDetails.html";
        }




        
        $TBS->LoadTemplate('./html/admin/templateAdmin.html');
        //indien bepaalde subcontenten geladen moeten worden, moeten bepaalde gegevens uit de db worden gehaald
        if($config["pagina"] == "./admin/student.html") {
            //tabel aanmaken voor overzicht studenten
            $TBS->MergeBlock('blk1', $db, 'SELECT * FROM hoorcollege_gebruiker WHERE actief = 1 GROUP BY naam, voornaam asc');
            //dropdown opvullen voor filter selectie klas
            $TBS->MergeBlock('blk16', $db, 'SELECT * FROM hoorcollege_groep GROUP BY naam asc');

            if(!isset ($_POST['toonFilterRes'])) {
                $TBS->MergeBlock('blk17', $db, "SELECT * FROM hoorcollege_gebruiker
                                                WHERE actief = 1 AND niveau = 1 AND (naam LIKE 'A%' OR naam LIKE 'B%')
                                                GROUP BY naam, voornaam asc");
            }
            else if(isset ($_POST['toonFilterRes'])) {
                if($_POST['filteropties'] == 'naam') {
                    $letters = explode("-", $_POST['selectNaamBegintMet']);
                    $l1 = $letters[0];
                    $l2 = $letters[1];                                 
                    $TBS->MergeBlock('blk17', $db, "SELECT * FROM hoorcollege_gebruiker
                                            WHERE actief = 1 AND niveau = 1 AND (naam LIKE '$l1%' OR naam LIKE '$l2%')
                                            GROUP BY naam, voornaam asc");
                }
                else if($_POST['filteropties'] == 'groep') {
                    if($_POST['selectKlas'] != 'zonder') {
                        $k = $_POST['selectKlas'];
                        $TBS->MergeBlock('blk17', $db, "SELECT *
                                                    FROM hoorcollege_gebruiker g
                                                    LEFT JOIN hoorcollege_gebruikergroep gr ON g.idGebruiker = gr.Gebruiker_idGebruiker
                                                    WHERE gr.Groep_idGroep = '$k' AND g.niveau = 1
                                                    GROUP BY g.naam, g.voornaam ASC");
                    }
                    else  {
                        $TBS->MergeBlock('blk17', $db, "SELECT * FROM hoorcollege_gebruiker
                                                        WHERE niveau = 1 AND idGebruiker NOT IN
                                                        (SELECT Gebruiker_idGebruiker FROM hoorcollege_gebruikergroep)");
                    }
                }                
            }
            //dropdown opvullen voor filter selectie klas
            $TBS->MergeBlock('blk27', $db, 'SELECT * FROM hoorcollege_groep GROUP BY naam asc');
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
            //dropdown opvullen voor filter selectie klas
            $TBS->MergeBlock('blk18', $db, 'SELECT * FROM hoorcollege_groep GROUP BY naam asc');
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
        else if($config["pagina"] == "./admin/vakindividueel.html") {            
            if(isset ($_POST['verderVakIndivudueel'])) {
                //select veld aanmaken voor overzicht vakken
                $TBS->MergeBlock('blk21', $db, 'SELECT * FROM hoorcollege_vak GROUP BY naam asc');

                if($_POST['filteropties'] == 'naam') {
                    $letters = explode("-", $_POST['selectNaamBegintMet']);
                    $l1 = $letters[0];
                    $l2 = $letters[1];
                    $TBS->MergeBlock('blk20', $db, "SELECT * FROM hoorcollege_gebruiker
                                            WHERE actief = 1 AND niveau = 1 AND (naam LIKE '$l1%' OR naam LIKE '$l2%')
                                            GROUP BY naam, voornaam asc");
                }
                else if($_POST['filteropties'] == 'groep') {
                    if($_POST['selectKlas'] != 'zonder') {
                        $k = $_POST['selectKlas'];
                        $TBS->MergeBlock('blk20', $db, "SELECT *
                                                    FROM hoorcollege_gebruiker g
                                                    LEFT JOIN hoorcollege_gebruikergroep gr ON g.idGebruiker = gr.Gebruiker_idGebruiker
                                                    WHERE gr.Groep_idGroep = '$k' AND g.niveau = '1'
                                                    GROUP BY g.naam, g.voornaam ASC");
                    }
                    else  {
                        $TBS->MergeBlock('blk20', $db, "SELECT * FROM hoorcollege_gebruiker
                                                        WHERE niveau = 1 AND idGebruiker NOT IN
                                                        (SELECT Gebruiker_idGebruiker FROM hoorcollege_gebruikergroep)");
                    }
                }
            }
        }
        else if($config["pagina"] == "./admin/toekennenselectiefaanvak.html") {
            //select veld aanmaken voor overzicht lectoren
            $TBS->MergeBlock('blk22', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                            FROM hoorcollege_gebruiker AS g
                                            LEFT JOIN hoorcollege_gebruiker_beheert_vak AS hb ON g.idGebruiker = hb.Gebruiker_idGebruiker
                                            WHERE g.niveau !=1 && hb.Vak_idVak = '$vak'
                                            GROUP BY g.naam, g.voornaam ASC");
            //veld aanmaken voor de overzicht van gekozen studenten
            $namen = array();
            $checkbox2 = serialize($_POST['checkbox']);            
            $count = count($_POST['checkbox']);
            for($i=0; $i < $count; $i++) {                
                $namen[$i] = getGebruikerNaamViaId($_POST['checkbox'][$i]);                
            }            
            $TBS->MergeBlock('blk23',$namen);
        }
        else if($config["pagina"] == "./admin/verwijderenstudent.html") {
            if(isset ($_POST['studentenverwijderen'])) {
                //select veld aanmaken voor overzicht vakken
                $TBS->MergeBlock('blk25', $db, 'SELECT * FROM hoorcollege_vak GROUP BY naam asc');

                if($_POST['filteropties'] == 'naam') {
                    $letters = explode("-", $_POST['selectNaamBegintMet']);
                    $l1 = $letters[0];
                    $l2 = $letters[1];
                    $TBS->MergeBlock('blk26', $db, "SELECT * FROM hoorcollege_gebruiker
                                            WHERE actief = 1 AND niveau = 1 AND (naam LIKE '$l1%' OR naam LIKE '$l2%')
                                            GROUP BY naam, voornaam asc");
                }
                else if($_POST['filteropties'] == 'groep') {
                    if($_POST['selectKlas'] != 'zonder') {
                        $k = $_POST['selectKlas'];
                        $TBS->MergeBlock('blk26', $db, "SELECT *
                                                    FROM hoorcollege_gebruiker g
                                                    LEFT JOIN hoorcollege_gebruikergroep gr ON g.idGebruiker = gr.Gebruiker_idGebruiker
                                                    WHERE gr.Groep_idGroep = '$k' AND g.niveau = '1'
                                                    GROUP BY g.naam, g.voornaam ASC");
                    }
                    else  {
                        $TBS->MergeBlock('blk26', $db, "SELECT * FROM hoorcollege_gebruiker
                                                        WHERE niveau = 1 AND idGebruiker NOT IN
                                                        (SELECT Gebruiker_idGebruiker FROM hoorcollege_gebruikergroep)");
                    }
                }
            }
        }
        else if($config["pagina"] == "./admin/overzichtteverwijderenstudenten.html") {            
            //veld aanmaken voor de overzicht van gekozen studenten
            $namen = array();
            $checkbox2 = serialize($_POST['checkbox']);
            $count = count($_POST['checkbox']);
            for($i=0; $i < $count; $i++) {
                $namen[$i] = getGebruikerNaamViaId($_POST['checkbox'][$i]);
            }
            $TBS->MergeBlock('blk28',$namen);
        }
        else if($config["pagina"] == "./admin/lector.html") {
           //select veld aanmaken voor overzicht lectoren
            $TBS->MergeBlock('blk29', $db, 'SELECT * FROM hoorcollege_gebruiker WHERE niveau = 40 GROUP BY naam, voornaam asc');
            //overzicht alle lectoren
            $TBS->MergeBlock('blk30', $db, 'SELECT * FROM hoorcollege_gebruiker WHERE niveau != 1 GROUP BY naam, voornaam asc');
        }
        else if($config["pagina"] == "./admin/lectorDetails.html") {
            $id = $_GET['detailsLectorId'];
            //overzicht van persoonlijke gegevens
            $TBS->MergeBlock('blk32', $db, "SELECT * FROM hoorcollege_gebruiker WHERE niveau != 1 AND idGebruiker = '$id' GROUP BY naam, voornaam asc");
            //overzicht alle vakken van een lector
            $TBS->MergeBlock('blk31', $db, "SELECT v.naam as naam
                                            FROM hoorcollege_vak v
                                            LEFT JOIN hoorcollege_gebruiker_beheert_vak bv ON v.idVak = bv.Vak_idVak
                                            WHERE bv.Gebruiker_idGebruiker = '$id'
                                            GROUP BY v.naam ASC");
        }
        else if($config["pagina"] == "./admin/vakDetails.html") {
            $id = $_GET['detailsVakId'];
            //overzicht om de beheerderders van een vak te tonen
            $TBS->MergeBlock('blk33', $db, "SELECT *
                                            FROM hoorcollege_gebruiker g
                                            LEFT JOIN hoorcollege_gebruiker_beheert_vak bv ON g.idGebruiker = bv.Gebruiker_idGebruiker
                                            WHERE bv.Vak_idVak = '$id'");

            //overzicht van alle studenten die het vak volgen
            $TBS->MergeBlock('blk34', $db, "SELECT g.naam, g.voornaam
                                            FROM hoorcollege_gebruiker g
                                            LEFT JOIN hoorcollege_gebruiker_volgt_vak vv ON g.idGebruiker = vv.Gebruiker_idGebruiker
                                            WHERE vv.Vak_idVak = '$id'
                                            GROUP BY g.naam, g.voornaam ASC");
        }
        else if($config["pagina"] == "./admin/detailsstudent.html") {
            $id = $_GET['detailsGebruikerId'];
            //overzicht persoonlijke gegevens
            $TBS->MergeBlock('blk35', $db, "SELECT * FROM hoorcollege_gebruiker WHERE idGebruiker = '$id' GROUP BY naam, voornaam asc");
            //overzicht van alle vakken die de student volgt
            $TBS->MergeBlock('blk36', $db, "SELECT v.naam, vv.van
                                            FROM hoorcollege_vak v
                                            LEFT JOIN hoorcollege_gebruiker_volgt_vak vv ON v.idVak = vv.Vak_idVak
                                            WHERE vv.Gebruiker_idGebruiker = '$id'
                                            GROUP BY v.naam ASC");
            
        }

        $TBS->Show() ;
    }    
?>
