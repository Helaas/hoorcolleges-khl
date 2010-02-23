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
                //dropdown voor alle klassen
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
                
                /*
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
                                            GROUP BY g.naam, g.voornaam ASC");*/


            }
        }
    }
?>
