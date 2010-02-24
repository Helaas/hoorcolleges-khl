<?php

    //indien men niet ingelogd is als admin zal deze methode redirecten naar index.php (gebruikt in admin.php)
    function verwerkLogin() {
        if(!isset ($_SESSION['gebruiker']) || !$_SESSION['gebruiker']->getNiveau() == "99") {
            header("location: index.php");
        }
    }

    //indien men niet ingelogd is als admin zal deze methode redirecten naar index.php (gebruikt in admin.php)
    function verwerkLogin2() {
        if(!isset ($_SESSION['gebruiker']) || !$_SESSION['gebruiker']->getNiveau() == "99") {
            header("location: ../index.php");
        }
    }

    //methode om na te gaan naar welke pagina men moet navigeren (gebruikt in admin.php)
    function verwerkPagina() {
        if(isset ($_GET['pagina'])) {
            return "./admin/" . $_GET['pagina'] . ".html";
        }
        else {
            return "./admin/admin.html";
        }
    }

    function verwerkMergeGegevens($TBS) {
        global $db;
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
                        }
                        else {
                            $data = $TBS->MergeBlock('blk1,*', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                                           FROM hoorcollege_gebruiker g
                                                           WHERE g.naam LIKE '$naamBegintMet%' AND g.niveau = '1' AND g.idGebruiker
                                                           NOT IN (SELECT Gebruiker_idGebruiker
                                                                FROM hoorcollege_gebruikergroep)
                                                           GROUP BY g.naam, g.voornaam ASC"); 
                        }
                    }
                    else if($filteroptiesNaam) { //indien enkel de eerste is geselcteerd
                        $naamBegintMet = (string) $_POST['naamBegintMet'];
                        $data = $TBS->MergeBlock('blk1,*', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                                       FROM hoorcollege_gebruiker g
                                                       WHERE g.naam LIKE '$naamBegintMet%' AND g.niveau = '1'");
                    }
                    else if($filteroptiesVak) { //indien enkel de tweede is geselcteerd
                        $vak = (int) $_POST['selectVak'];
                         $data = $TBS->MergeBlock('blk1,*', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                            FROM hoorcollege_gebruiker g
                                            LEFT JOIN hoorcollege_gebruiker_volgt_vak vv ON g.idGebruiker = vv.Gebruiker_idGebruiker
                                            WHERE vv.Vak_idVak = '$vak'
                                            GROUP BY g.naam, g.voornaam ASC"); 
                    }
                    else if($filteroptiesGroep) { //indien enkel het laatste veldje is geselcteerd
                        $groep = (int) $_POST['selectGroep'];
                        if($groep != 'zonder') {
                            $data = $TBS->MergeBlock('blk1,*', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                              FROM hoorcollege_gebruiker g
                                              LEFT JOIN hoorcollege_gebruikergroep gr ON g.idGebruiker = gr.Gebruiker_idGebruiker
                                              WHERE gr.Groep_idGroep = '$groep' AND g.niveau = '1'
                                              GROUP BY g.naam, g.voornaam ASC");
                        }
                        else {
                            $data = $TBS->MergeBlock('blk1,*', $db, "SELECT * FROM hoorcollege_gebruiker
                                              WHERE niveau = 1 AND idGebruiker NOT IN
                                              (SELECT Gebruiker_idGebruiker FROM hoorcollege_gebruikergroep)");
                        }
                    }
                }
                else {  //beveiliging
                    if(!isset ($_GET['foutboodschap'])) {
                        header("location: index.php");
                    }
                    else {
                        $count = count($_POST['checkboxStudenten']);
                        for($i=0; $i < $count; $i++) {
                            $namen[$i] = getGebruikerNaamViaId($_POST['checkboxStudenten'][$i]);
                        }
                         $TBS->MergeBlock('blk1',$namen);
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
        }
    }
?>
