<?php
    require_once('./includes/kern.php');    //kern is essentieel voor DB en heeft includes ivm met functions, etc
    require_once('./admin/adminSpecifiekeFunctions.php');
    session_start();    //functie die werken met sessie enabled
    $TBS = new clsTinyButStrong;            //variable om functies van TinyButStrong te gebruikern
    
    verwerkLogin();

    $config["pagina"] = verwerkPagina();

    //foutafhandeling
    $typeboodschap = "fout";
    
    if(isset ($_GET['foutboodschap'])) {
        $foutboodschap = $_GET['foutboodschap'];
    }

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
            if($_POST['selectvak'] != 'kies' && count($_POST['checkboxGroepen']) > 0) {
                $checkbox2 = serialize($_POST['checkboxGroepen']);                
                $vak = $_POST['selectvak'];
                $vaknaam = getVakNameViaId($_POST['selectvak']);
            }
            else {                
                header("location: admin.php?pagina=groepVak&foutboodschap=U dient een groep en een vak te kiezen!");
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
        }
        else if(isset ($_POST['toekennenstudentenaanvakoverzichtknop'])) {
            if($_POST['selectvak'] != 'leeg' && count($_POST['checkboxStudenten']) > 0) {
                $checkbox2 = serialize($_POST['checkboxStudenten']);
                $vak = $_POST['selectvak'];
                $vaknaam = getVakNameViaId($_POST['selectvak']);
            }
            else {                
                header("location: admin.php?pagina=studentVakVerder&foutboodschap=U dient studenten alsook een vak te kiezen!");
            }
        }
        else if(isset ($_POST['toekennenstudentenaanvakknop'])) {
            $ch = unserialize($_POST['checkbox2']);
            $vak = (int) $_POST['vak'];
            $van = (int) $_POST['vakvan'];

            $count = count($ch);           
            for($i=0; $i < $count; $i++) {                
                $gelukt = kenStudentToeAanVak($ch[$i], $vak, $van);
            }

            if($gelukt) {
                $typeboodschap = "juist";
                $foutboodschap = 'Alle geselecteerden zijn aan het vak toegevoegd!'; // dit is geen foutboodschap
            }
            else {
                $typeboodschap = "fout";
                $foutboodschap = 'Technisch probleem, mogelijk is niet iedereen toegekent aan het vak, gelieve manueel te controleren';
            }
        }
        else if(isset ($_POST['lectorVakVoltooiKnop'])) { //indien men een lector wil toekennen aan een vak in vak.html            
            //validatie van de toekenning
            if(beheertLectorVak($_POST['selectlector'], $_POST['selectvak'])) {
                $foutboodschap2 = "Actie niet gelukt: lector is al toegekend aan dit vak of er is een technisch probleem opgedoken!";
            }
            else {
                if(!kenLectorToeAanVak($_POST['selectlector'], $_POST['selectvak'])) {
                    $foutboodschap = "Actie niet gelukt: waarschijnlijk te wijten aan een technisch probleem!";
                }
                else {
                    $typeboodschap = "juist";
                    $foutboodschap = 'Lector is succesvol aan vak toegekend!'; //dit is geen foutboodschap
                }
            }
        }
        else if(isset ($_POST['studentenselectiefontkoppelen'])) {
            if($_POST['selectvak'] == 'leeg') {                
                header("location: admin.php?pagina=studentVakOnt&foutboodschap=U dient een vak te kiezen!");
            }
            else {                
                $vak = $_POST['selectvak'];
                $vaknaam = getVakNameViaId($vak);
            }
        }
        else if(isset ($_POST['verderstudentenontkoppelenselectief'])) {
            if(count($_POST['checkbox']) > 0) {
                $vak = $_POST['vak'];
                $vaknaam = getVakNameViaId($vak);
            }
            else {
                //hier nog fout opvangen indien men niemand selecteerd bij studentenkoppelenselectief
            }
        }
        else if(isset ($_POST['ontkopelgeselecteerdenvanvak'])) {            
            $idVak = $_POST['vak'];
            $ch = unserialize($_POST['checkbox2']);
            $count = count($ch);

            $gelukt = true;
            for($i=0; $i < $count; $i++) {
                $gelukt = ontkoppelStudentVanVak($ch[$i], $idVak);
            }

            if($gelukt) {
                $typeboodschap = "juist";
                $foutboodschap = 'Alle geselecteerden zijn ontkoppeld!'; // dit is geen foutboodschap
            }
            else {
                $typeboodschap = "fout";
                $foutboodschap = 'Technisch probleem, mogelijk is niet iedereen ontkoppeld, gelieve manueel te controleren';
            }
        }






    $TBS->LoadTemplate('./html/admin/templateAdmin.html');

    //verwerkMergeGegevens($TBS); //noodzakelijk om de correcte


    if(isset ($_GET['pagina'])) {
            if($_GET['pagina'] == 'studentOverzicht') {
                //dropdown voor alle groepen
                $TBS->MergeBlock('blk1', $db, 'SELECT * FROM hoorcollege_vak GROUP BY naam asc');
                //dropdown opvullen voor filter selectie klas
                $TBS->MergeBlock('blk2', $db, 'SELECT * FROM hoorcollege_groep GROUP BY naam asc');



                if(!isset ($_POST['filteroptiesVak']) && !isset ($_POST['filteroptiesGroep'])) {
                    //standaard geladen als men nog geen filteropties heeft gekozen
                    $TBS->MergeBlock('blk3', $db, "SELECT * FROM hoorcollege_Gebruiker where idGebruiker = '-1' GROUP BY naam asc");
                }
                //indien men de filteropties gebruikt van de opties kiest
                else if(isset ($_POST['filteroptiesVak']) && isset ($_POST['filteroptiesGroep'])) {
                    if($_POST['selectGroep'] != 'zonder' && $_POST['selectVak'] != 'leeg') {
                        $vak = (int) $_POST['selectVak'];
                        $groep = (int) $_POST['selectGroep'];

                        $TBS->MergeBlock('blk3', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                                            FROM hoorcollege_gebruiker g
                                                            LEFT JOIN hoorcollege_gebruiker_volgt_vak vv ON g.idGebruiker = vv.Gebruiker_idGebruiker
                                                            WHERE g.niveau = '1' AND vv.Vak_idVak = '$vak'
                                                            AND g.idGebruiker
                                                            IN (SELECT Gebruiker_idGebruiker
                                                                FROM hoorcollege_gebruikergroep
                                                                WHERE Groep_idGroep = '$groep')
                                                            GROUP BY g.naam, g.voornaam ASC");
                    }
                    else if($_POST['selectGroep'] == 'zonder' && $_POST['selectVak'] != 'leeg') {
                        $vak = (int) $_POST['selectVak'];
                        $TBS->MergeBlock('blk3', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                                            FROM hoorcollege_gebruiker g
                                                            LEFT JOIN hoorcollege_gebruiker_volgt_vak vv ON g.idGebruiker = vv.Gebruiker_idGebruiker
                                                            WHERE g.niveau = '1' AND vv.Vak_idVak = '$vak'
                                                            AND g.idGebruiker
                                                            NOT IN (SELECT Gebruiker_idGebruiker
                                                                FROM hoorcollege_gebruikergroep)
                                                            GROUP BY g.naam, g.voornaam ASC");
                    }
                    else if($_POST['selectGroep'] == 'zonder' && $_POST['selectVak'] == 'leeg') {
                        $TBS->MergeBlock('blk3', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                                            FROM hoorcollege_gebruiker g
                                                            WHERE g.niveau = '1' AND g.idGebruiker
                                                            NOT IN (SELECT Gebruiker_idGebruiker
                                                                FROM hoorcollege_gebruikergroep)
                                                            GROUP BY g.naam, g.voornaam ASC");
                    }
                    else {
                         $TBS->MergeBlock('blk3', $db, "SELECT * FROM hoorcollege_Gebruiker where idGebruiker = '-1' GROUP BY naam asc");
                    }
                }
                else if(isset ($_POST['filteroptiesVak'])) {
                    if($_POST['selectVak'] != 'leeg') {
                        $vak = (int) $_POST['selectVak'];
                        $TBS->MergeBlock('blk3', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                                            FROM hoorcollege_gebruiker g
                                                            LEFT JOIN hoorcollege_gebruiker_volgt_vak vv ON g.idGebruiker = vv.Gebruiker_idGebruiker
                                                            WHERE g.niveau = '1' AND vv.Vak_idVak = '$vak'
                                                            GROUP BY g.naam, g.voornaam ASC");
                    }
                    else {
                        $TBS->MergeBlock('blk3', $db, "SELECT * FROM hoorcollege_Gebruiker where idGebruiker = '-1' GROUP BY naam asc");
                    }
                }
                else {
                   if($_POST['selectGroep'] == 'zonder') {
                        $TBS->MergeBlock('blk3', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                                            FROM hoorcollege_gebruiker g
                                                            WHERE g.niveau = '1' AND g.idGebruiker
                                                            NOT IN (SELECT Gebruiker_idGebruiker
                                                                FROM hoorcollege_gebruikergroep)
                                                            GROUP BY g.naam, g.voornaam ASC");
                   }
                   else {
                       $groep = (int) $_POST['selectGroep'];
                       $TBS->MergeBlock('blk3', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                                      FROM hoorcollege_gebruiker g
                                                      LEFT JOIN hoorcollege_gebruikergroep gg ON g.idGebruiker = gg.Gebruiker_idGebruiker
                                                      WHERE g.niveau = '1' AND gg.Groep_idGroep = '$groep'");
                   }
                }
            }
            else if($_GET['pagina'] == 'lectorPromoveren') {
                //select veld aanmaken voor overzicht lectoren
                $TBS->MergeBlock('blk1', $db, 'SELECT * FROM hoorcollege_gebruiker WHERE niveau = 40 GROUP BY naam, voornaam asc');
            }
            else if($_GET['pagina'] == 'lectorOverzicht') {
                //overzicht alle lectoren
                $TBS->MergeBlock('blk1', $db, 'SELECT * FROM hoorcollege_gebruiker WHERE niveau != 1 GROUP BY naam, voornaam asc');
            }
            else if($_GET['pagina'] == 'vakkenOverzicht') {
                //tabel aanmaken voor overzicht vakken
                $TBS->MergeBlock('blk1', $db, 'SELECT * FROM hoorcollege_vak GROUP BY naam asc');
            }
            else if($_GET['pagina'] == 'aanmakenVakken') {
                //select veld aanmaken voor overzicht lectoren
                $TBS->MergeBlock('blk1', $db, 'SELECT * FROM hoorcollege_gebruiker WHERE niveau != 1 GROUP BY naam, voornaam asc');
            }
            else if($_GET['pagina'] == 'groepVak') {
                //select lijst voor groepen
                $TBS->MergeBlock('blk1', $db, 'SELECT * FROM hoorcollege_groep GROUP BY naam asc');
                //select lijst voor vakken
                $TBS->MergeBlock('blk2', $db, 'SELECT * FROM hoorcollege_vak GROUP BY naam asc');
            }
            else if($_GET['pagina'] == 'toekennengroepaanvak') {
                //overzicht van alle studenten die aan het vak worden toegevoegd
                //$checkbox2 = serialize($_POST['checkboxGroepen']);  zie admin.php
                $count = count($_POST['checkboxGroepen']);
                for($i=0; $i < $count; $i++) {
                    $namen[$i] = getGroepNameViaId($_POST['checkboxGroepen'][$i]);
                }
                $TBS->MergeBlock('blk1',$namen);

                //select veld aanmaken voor overzicht lectoren
                $vak = (int) $_POST['selectvak'];
                $TBS->MergeBlock('blk12', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                            FROM hoorcollege_gebruiker AS g
                                            LEFT JOIN hoorcollege_gebruiker_beheert_vak AS hb ON g.idGebruiker = hb.Gebruiker_idGebruiker
                                            WHERE g.niveau !=1 && hb.Vak_idVak = '$vak'
                                            GROUP BY g.naam, g.voornaam ASC");
            }
            else if($_GET['pagina'] == 'studentVak') {
                //dropdown voor alle vakken
                $TBS->MergeBlock('blk1', $db, 'SELECT * FROM hoorcollege_vak GROUP BY naam asc');
                //dropdown opvullen voor filter selectie klas
                $TBS->MergeBlock('blk2', $db, 'SELECT * FROM hoorcollege_groep GROUP BY naam asc');
            }
            else if($_GET['pagina'] == 'studentVakVerder') {
                //dropdown voor alle vakken
                $TBS->MergeBlock('blk2', $db, 'SELECT * FROM hoorcollege_vak GROUP BY naam asc');

                $filteroptiesNaam = false;
                $filteroptiesVak = false;
                $filteroptiesGroep = false;
                
                if(isset ($_POST['studentVakVerder'])) { //nagaan of men op deze pagina wel via correcte weg is gekomen
                    //aantal controles uitvoeren:
                    if(isset ($_POST['filteroptiesNaam']) && !empty ($_POST['naamBegintMet'])) { //nagaan of filteroptiesnaam is ingevuld en deze niet leeg is, indien dat wel is wordt dat genegeerd
                        $filteroptiesNaam = true;
                    }
                    if(isset ($_POST['filteroptiesVak']) && $_POST['naamBegintMet'] != 'leeg') { //nagaan op de filteroptiesVak zijn aangevinkt en of men effectief een vak heeft gekozen, anders wordt dit genegeerd
                        $filteroptiesVak = true;
                    }
                    if(isset ($_POST['filteroptiesGroep'])) {
                        $filteroptiesGroep = true;
                    }

                    //verwerking:
                    //indien alle 3 opties zijn gekozen
                    if($filteroptiesNaam && $filteroptiesVak && $filteroptiesGroep) { //indien alle opties zijn gekozen en alles correct is geselcteerd/ingevuld
                        $naamBegintMet = (string) $_POST['naamBegintMet'];
                        $vak = (int) $_POST['selectVak'];
                        $groep = (int) $_POST['selectGroep'];
                        if($groep != 'zonder') {  //indien men een groep heeft geselecteerd
                            $data = $TBS->MergeBlock('blk1,*', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                                            FROM hoorcollege_gebruiker g
                                                            LEFT JOIN hoorcollege_gebruiker_volgt_vak vv ON g.idGebruiker = vv.Gebruiker_idGebruiker
                                                            WHERE g.niveau = '1' AND vv.Vak_idVak = '$vak' AND g.naam LIKE '$naamBegintMet%'
                                                            AND g.idGebruiker
                                                            IN (SELECT Gebruiker_idGebruiker
                                                                FROM hoorcollege_gebruikergroep
                                                                WHERE Groep_idGroep = '$groep')
                                                            GROUP BY g.naam, g.voornaam ASC");
                            $studdata = serialize($data);
                        }
                        else { //indien men bij groep de optie zonder groep heeft genomen
                            $data = $TBS->MergeBlock('blk1,*', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                                            FROM hoorcollege_gebruiker g
                                                            LEFT JOIN hoorcollege_gebruiker_volgt_vak vv ON g.idGebruiker = vv.Gebruiker_idGebruiker
                                                            WHERE g.niveau = '1' AND vv.Vak_idVak = '$vak' AND g.naam LIKE '$naamBegintMet%'
                                                            AND g.idGebruiker
                                                            NOT IN (SELECT Gebruiker_idGebruiker
                                                                FROM hoorcollege_gebruikergroep)
                                                            GROUP BY g.naam, g.voornaam ASC");
                            $studdata = serialize($data);
                        }
                    }
                    else if($filteroptiesNaam && $filteroptiesVak && !$filteroptiesGroep) { //indien men enkel de 2 bovenste veldjes heeft aangevinkt
                        $naamBegintMet = (string) $_POST['naamBegintMet'];
                        $vak = (int) $_POST['selectVak'];
                        $data = $TBS->MergeBlock('blk1,*', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                                            FROM hoorcollege_gebruiker g
                                                            LEFT JOIN hoorcollege_gebruiker_volgt_vak vv ON g.idGebruiker = vv.Gebruiker_idGebruiker
                                                            WHERE g.niveau = '1' AND vv.Vak_idVak = '$vak' AND g.naam LIKE '$naamBegintMet%'
                                                            GROUP BY g.naam, g.voornaam ASC");
                        $studdata = serialize($data);
                    }
                    else if(!$filteroptiesNaam && $filteroptiesVak && $filteroptiesGroep) { //indien men enkel de 2 onderste veldjes heeft aangevinkt
                        $vak = (int) $_POST['selectVak'];
                        $groep = (int) $_POST['selectGroep'];
                        if($groep != 'zonder') {
                            $data = $TBS->MergeBlock('blk1,*', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                                            FROM hoorcollege_gebruiker g
                                                            LEFT JOIN hoorcollege_gebruiker_volgt_vak vv ON g.idGebruiker = vv.Gebruiker_idGebruiker
                                                            WHERE g.niveau = '1' AND vv.Vak_idVak = '$vak' AND g.niveau = '1'
                                                            AND g.idGebruiker
                                                            IN (SELECT Gebruiker_idGebruiker
                                                                FROM hoorcollege_gebruikergroep
                                                                WHERE Groep_idGroep = '$groep')
                                                            GROUP BY g.naam, g.voornaam ASC");
                            $studdata = serialize($data);
                        }
                        else {
                            $data = $TBS->MergeBlock('blk1,*', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                                            FROM hoorcollege_gebruiker g
                                                            LEFT JOIN hoorcollege_gebruiker_volgt_vak vv ON g.idGebruiker = vv.Gebruiker_idGebruiker
                                                            WHERE g.niveau = '1' AND vv.Vak_idVak = '$vak' AND g.niveau = '1'
                                                            AND g.idGebruiker
                                                            NOT IN (SELECT Gebruiker_idGebruiker
                                                                FROM hoorcollege_gebruikergroep)
                                                            GROUP BY g.naam, g.voornaam ASC");
                            $studdata = serialize($data);
                        }
                    }
                    else if($filteroptiesNaam && !$filteroptiesVak && $filteroptiesGroep) { //indien men de bovenste en onderste veldjes heeft aangevinkt
                        $naamBegintMet = (string) $_POST['naamBegintMet'];
                        $groep = (int) $_POST['selectGroep'];
                        if($groep != 'zonder') {
                            $data = $TBS->MergeBlock('blk1,*', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                                           FROM hoorcollege_gebruiker g
                                                           LEFT JOIN hoorcollege_gebruikergroep gg ON g.idGebruiker = gg.Gebruiker_idGebruiker
                                                           WHERE gg.Groep_idGroep = '$groep' AND g.naam LIKE '$naamBegintMet%' AND g.niveau = '1'");
                            $studdata = serialize($data);
                        }
                        else {
                            $data = $TBS->MergeBlock('blk1,*', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                                           FROM hoorcollege_gebruiker g
                                                           WHERE g.naam LIKE '$naamBegintMet%' AND g.niveau = '1' AND g.idGebruiker
                                                           NOT IN (SELECT Gebruiker_idGebruiker
                                                                FROM hoorcollege_gebruikergroep)
                                                           GROUP BY g.naam, g.voornaam ASC");
                            $studdata = serialize($data);
                        }
                    }
                    else if($filteroptiesNaam) { //indien enkel de eerste is geselcteerd
                        $naamBegintMet = (string) $_POST['naamBegintMet'];
                        $data = $TBS->MergeBlock('blk1,*', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                                       FROM hoorcollege_gebruiker g
                                                       WHERE g.naam LIKE '$naamBegintMet%' AND g.niveau = '1'");
                        $studdata = serialize($data);
                    }
                    else if($filteroptiesVak) { //indien enkel de tweede is geselcteerd
                        $vak = (int) $_POST['selectVak'];
                         $data = $TBS->MergeBlock('blk1,*', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                            FROM hoorcollege_gebruiker g
                                            LEFT JOIN hoorcollege_gebruiker_volgt_vak vv ON g.idGebruiker = vv.Gebruiker_idGebruiker
                                            WHERE vv.Vak_idVak = '$vak'
                                            GROUP BY g.naam, g.voornaam ASC");
                         $studdata = serialize($data);
                    }
                    else if($filteroptiesGroep) { //indien enkel het laatste veldje is geselcteerd
                        $groep = (int) $_POST['selectGroep'];
                        if($groep != 'zonder') {
                            $data = $TBS->MergeBlock('blk1,*', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                              FROM hoorcollege_gebruiker g
                                              LEFT JOIN hoorcollege_gebruikergroep gr ON g.idGebruiker = gr.Gebruiker_idGebruiker
                                              WHERE gr.Groep_idGroep = '$groep' AND g.niveau = '1'
                                              GROUP BY g.naam, g.voornaam ASC");
                            $studdata = serialize($data);
                        }
                        else {
                            $data = $TBS->MergeBlock('blk1,*', $db, "SELECT * FROM hoorcollege_gebruiker
                                              WHERE niveau = 1 AND idGebruiker NOT IN
                                              (SELECT Gebruiker_idGebruiker FROM hoorcollege_gebruikergroep)");
                            $studdata = serialize($data);
                        }
                    }                         
                }
                else {  //beveiliging
                    if(!isset ($_GET['foutboodschap'])) {
                        header("location: index.php");
                    }
                    else {
                        //$data = $_POST['data'];
                        //$data = unserialize($_POST['data']);
                        //$TBS->MergeBlock('blk1',$data);
                    }
                }
            }
            else if($_GET['pagina'] == 'studentVakVerderOverzicht') {
                $count = count($_POST['checkboxStudenten']);
                for($i=0; $i < $count; $i++) {
                    $namen[$i] = getGebruikerNaamViaId($_POST['checkboxStudenten'][$i]);
                }
                $TBS->MergeBlock('blk1',$namen);

                //select veld aanmaken voor overzicht lectoren
                $vak = (int) $_POST['selectvak'];
                $TBS->MergeBlock('blk2', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                            FROM hoorcollege_gebruiker AS g
                                            LEFT JOIN hoorcollege_gebruiker_beheert_vak AS hb ON g.idGebruiker = hb.Gebruiker_idGebruiker
                                            WHERE g.niveau !=1 && hb.Vak_idVak = '$vak'
                                            GROUP BY g.naam, g.voornaam ASC");
            }
            else if($_GET['pagina'] == 'lectorVak') { //indien men op pagina komt om een lector aan een vak toe te kennen
                //select veld aanmaken voor overzicht lectoren
                $TBS->MergeBlock('blk1', $db, 'SELECT * FROM hoorcollege_gebruiker WHERE niveau != 1 GROUP BY naam, voornaam asc');
                //tabel aanmaken voor overzicht vakken
                $TBS->MergeBlock('blk2', $db, 'SELECT * FROM hoorcollege_vak GROUP BY naam asc');
            }
            else if($_GET['pagina'] == 'studentVakOnt') { //indien men op de pagina studentVakOnt komt
                //tabel aanmaken voor overzicht vakken
                $TBS->MergeBlock('blk1', $db, 'SELECT * FROM hoorcollege_vak GROUP BY naam asc');
            }
            else if($_GET['pagina'] == 'studentenkoppelenselectief') {
                //overzicht van alle studenten die het vak volgen
                $TBS->MergeBlock('blk1', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                               FROM hoorcollege_gebruiker g
                                               LEFT JOIN hoorcollege_gebruiker_volgt_vak vv ON g.idGebruiker = vv.Gebruiker_idGebruiker
                                               WHERE vv.Vak_idVak = '$vak'
                                               GROUP BY g.naam, g.voornaam ASC");
            }
            else if($_GET['pagina'] == 'overzichtstudentenkoppelenselectief') {
                //veld aanmaken voor de overzicht van gekozen studenten
                $namen = array();
                $checkbox2 = serialize($_POST['checkbox']);
                $count = count($_POST['checkbox']);
                for($i=0; $i < $count; $i++) {
                    $namen[$i] = getGebruikerNaamViaId($_POST['checkbox'][$i]);
                }
                $TBS->MergeBlock('blk1',$namen);
            }
        }        


    $TBS->Show() ;
?>
