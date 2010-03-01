<?php
require_once('./includes/kern.php');    //kern is essentieel voor DB en heeft includes ivm met functions, etc
require_once('./admin/adminSpecifiekeFunctions.php');
session_start();    //functie die werken met sessie enabled
$TBS = new clsTinyButStrong;            //variable om functies van TinyButStrong te gebruikern

verwerkLogin();

$config["pagina"] = verwerkPagina();

//foutafhandeling
$typeboodschap = "fout";

$link = "admin.php?pagina=admin";


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
else if(isset ($_POST['verderontkoppelengroepvanvakknop'])) {
    if($_POST['selectvak'] == 'kies') {
        header("location: admin.php?pagina=groepVakOnt&foutboodschap=U moet een vak kiezen");
    }
    else {
        //nagaan of knop getoond zal moeten worden
        $i = 0;
        $vak = $_POST['selectvak'];
        $groepen = array();
        $array = $db->Execute('SELECT * FROM hoorcollege_groep GROUP BY naam asc');
        while(!$array->EOF) {
            if(isGroepToegekentAanVak($array->fields['idGroep'], $vak)) {
                $groepen[$i]['idGroep'] = $array->fields['idGroep'];
                $groepen[$i]['naam'] = $array->fields['naam'];
                $i++;
            }
            $array->MoveNext();
        }
        if(count($groepen) > 0) {
            $knoptonen = true;
        }
        else {
            $knoptonen = false;
        }
        $vak = $_POST['selectvak'];
    }
}
else if(isset ($_POST['ontkoppelengroepvanvakknopselectie'])) {
    if(count($_POST['checkbox']) > 0) {
        //$checkbox2 = serialize($_POST['checkbox']);
        $vak = $_POST['vak'];
        $vaknaam = getVakNameViaId($vak);
    }
    else {
        header("admin.php?pagina=groepVakOnt");
    }
}
else if(isset ($_POST['ontkoppelengroepvanvakknop'])) {
    $ch = unserialize($_POST['checkbox2']);
    $vak = (int) $_POST['vak'];

    $count = count($ch);
    $gelukt = false;

    for($i=0; $i < $count; $i++) {
        echo $ch[$i];
        $gelukt = ontkoppelGroepVanVak($ch[$i], $vak);
    }

    if($gelukt == true) {
        $typeboodschap = "juist";
        $foutboodschap = 'Alle leerlingen van de geselecteerde groepen zijn van het vak ontkoppeld.';
    }
    else {
        $typeboodschap = "fout";
        $foutboodschap = 'Actie is niet volledig uitgevoerd omwille van een technisch probleem, gelieve zelf te controleren of alle studenten van deze groepen correct zijn ontkoppeld van het vak!';
    }
}
else if(isset ($_POST['wijzigvaknaamknop'])) {
    $vak = $_POST['selectvak'];
    $nieuweNaam = $_POST['nieuweVaknaam'];
    if($vak == 'kies') {
        $typeboodschap = "fout";
        $foutboodschap = 'U dient een vak te kiezen!';
    }
    else if(empty ($nieuweNaam)) {
        $typeboodschap = "fout";
        $foutboodschap = 'Nieuwe naam mag niet leeg zijn!';
    }
    else {
        if(wijzigNaamVak($vak, $nieuweNaam)) {
            $typeboodschap = "juist";
            $foutboodschap = 'Nieuwe naam is toegekent aan vak!'; //dit is geen foutboodschap
        }
        else {
            $typeboodschap = "fout";
            $foutboodschap = 'Technisch probleem! Actie mogelijk niet uitgevoerd!';
        }
    }
}
else if(isset ($_POST['verderverwijderbeheerder'])) {
    $vak = $_POST['selectvak'];
    $vaknaam = getVakNameViaId($vak);
    if($vak == 'kies') {
        header("location: admin.php?pagina=verwijderBeheerder&foutboodschap=U moet een vak kiezen");
    }
    else {
        //nagaan hoeveel beheerders dit vak heeft
        if(aantalBeheertVak($vak) < 2) { //indien deze slechts 1 beheerder heeft
            header("location: admin.php?pagina=verwijderBeheerder&foutboodschap=Vak heeft slechts 1 beheerder, u moet eerst nog een beheerder toevoegen, vooraleer u verder kunt gaan!");
        }
    }
}
else if(isset ($_POST['verderverwijderbeheerdervoltooien'])) {
    $vak = $_POST['vak'];
    $lector = $_POST['selectlector'];
    if(geeftLesAanVakLector($vak, $lector)) {
        $typeboodschap = "fout";
        $foutboodschap = 'Actie niet gelukt: er zijn studenten die dit vak krijgen van deze lector!';
    }
    else {
        if(verwijderBeheerderVanVak($lector, $vak)) {
            $typeboodschap = "juist";
            $foutboodschap = 'Lector is niet meer aan het vak toegekend!'; //dit is geen foutboodschap
        }
        else {
            $typeboodschap = "fout";
            $foutboodschap = 'Technisch probleem! Mogelijk is de actie niet uitgevoerd!';
        }
    }
}
else if(isset ($_POST['verwijderenvakverder'])) {
    $vak = $_POST['selectvak'];
    $vaknaam = getVakNameViaId($vak);
    if($vak == 'kies') {
        header("location: admin.php?pagina=verwijderenVakken&foutboodschap=U moet een vak kiezen");
    }
}
else if(isset ($_POST['voltooienverwijderenvak'])) {
    $vak = $_POST['vak'];
    if(verwijderVak($vak)) {
        $typeboodschap = "juist";
        $foutboodschap = 'Vak is succesvol verwijderd!'; //dit is geen foutboodschap
    }
    else {
        $typeboodschap = "fout";
        $foutboodschap = 'Technisch probleem! Mogelijk is de actie niet uitgevoerd!';
    }
}
else if(isset ($_POST['knopvoegtoegroep'])) { //indien men een groep probeert aan te maken via aanmakenGroepen.html
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
else if(isset ($_POST['wijziggroepnaam'])) { //indien men een groepsnaam probeert te wijzigen in wijzigenGroepen
    if(!empty ($_POST['nieuwenaam'])) {
        $idGroep = (int) $_POST['selectgroep'];
        $naam = (string) $_POST['nieuwenaam'];
        if(!bestaatGroep($naam)) {
            if(wijzigGroepsnaam($idGroep, $naam)) {
                $typeboodschap = "juist";
                $foutboodschap = 'Groepsnaam is succesvol gewijzigd!'; //dit is geen foutboodschap
            }
            else {
                $typeboodschap = "fout";
                $foutboodschap = 'Technisch probleem! Mogelijk is de actie niet uitgevoerd!';
            }
        }
        else {
            $typeboodschap = "fout";
            $foutboodschap = 'Deze groep bestaat al!';
        }
    }
    else if($_POST['selectgroep'] == 'leeg') {
        $typeboodschap = "fout";
        $foutboodschap = 'U moet eerst een groep selecteren!';
    }
    else {
        $typeboodschap = "fout";
        $foutboodschap = 'Nieuwe naam mag niet leeg zijn!';
    }
}
else if(isset ($_POST['verderverwijdergroepknop'])) {
    if($_POST['selectgroep'] != "leeg") {
        $idGroep = (int) $_POST['selectgroep'];
        $groepsnaam = getGroepNameViaId($idGroep);
    }
    else {
        header("location: admin.php?pagina=verwijderenGroepen&foutboodschap=U moet een groep kiezen");
    }
}
else if(isset ($_POST['voltooiengroepverwijderen'])) {
    $idGroep = $_POST['idGroep'];
    if(verwijderGroep($idGroep)) {
        $typeboodschap = "juist";
        $foutboodschap = 'Groep is succesvol verwijderd!'; //dit is geen foutboodschap
    }
    else {
        $typeboodschap = "fout";
        $foutboodschap = 'Technisch probleem! Mogelijk is de actie niet uitgevoerd!';
    }
}
else if(isset ($_POST['toekennenstudentenaangroepoverzichtknop'])) {
    if($_POST['selectgroep'] != 'leeg' && count($_POST['checkboxStudenten']) > 0) {
        $checkbox2 = serialize($_POST['checkboxStudenten']);
        $groep = $_POST['selectgroep'];
        $groepsnaam = getGroepNameViaId($_POST['selectgroep']);
    }
    else {
        header("location: admin.php?pagina=studentGroepVerder&foutboodschap=U dient studenten alsook een vak te kiezen!");
    }
}
else if(isset ($_POST['toekennenstudentenaangroepknop'])) {
    $ch = unserialize($_POST['checkbox2']);
    $groep = (int) $_POST['groep'];
    $allemaal = true;

    $count = count($ch);
    for($i=0; $i < $count; $i++) {
        if(!isStudentToegekentAanGroep($ch[$i])) {
            $gelukt = kenStudentToeAanGroep($ch[$i], $groep);
        }
        else {
            $allemaal = false;
            verwijderStudentVanAlleGroepen($ch[$i]);
            if(!isStudentToegekentAanGroep($ch[$i])) {
                $gelukt = kenStudentToeAanGroep($ch[$i], $groep);
            }
        }
    }

    if($gelukt) {
        if($allemaal) {
            $typeboodschap = "juist";
            $foutboodschap = 'Alle geselecteerden zijn aan de groep toegevoegd!'; // dit is geen foutboodschap
        }
        else {
            $typeboodschap = "juist";
            $foutboodschap = 'Opgelet: sommige studenten waren al aan een groep toegekent, alle geselecteerden zijn nu aan deze groep toegevoegd!'; // dit is geen foutboodschap
        }
    }
    else {
        $typeboodschap = "fout";
        $foutboodschap = 'Technisch probleem, mogelijk is niet iedereen toegekent aan de groep, gelieve manueel te controleren';
    }
}
else if(isset ($_POST['zoekopnaamStudGroep'])) {
    $naam = (string) $_POST['naam'];
    if(empty ($naam)) {
        header("location: admin.php?pagina=studentGroepOntNaam&foutboodschap=Naam mag niet leeg zijn!");
    }
}
else if(isset ($_POST['toekennenstudentenaanvakoverzichtknop'])) {
    if(count($_POST['checkboxStudenten']) > 0) {
        $checkbox2 = serialize($_POST['checkboxStudenten']);
    }
}
else if(isset ($_POST['verderopnaamStudGroep'])) {
    $checkbox2 = serialize($_POST['checkboxStudenten']);
    $link = "admin.php?pagina=studentGroepOntNaam";
}
else if(isset ($_POST['voltStudentGroepOntknop'])) {
    $ch = unserialize($_POST['checkbox2']);

    $count = count($ch);
    $gelukt = false;

    for($i=0; $i < $count; $i++) {
        if(isStudentToegekentAanGroep($ch[$i])) {
            $gelukt = verwijderStudentVanAlleGroepen($ch[$i]);
        }
    }

    if($gelukt == true) {
        $typeboodschap = "juist";
        $foutboodschap = 'Alle geselecteerden hebben geen groep meer.';
    }
    else {
        $typeboodschap = "fout";
        $foutboodschap = 'Technisch probleem! Actie is mogelijk niet volledig gelukt! Gelieve manueel te controleren.';
    }
}
else if(isset ($_POST['zoekopgroepStudGroep'])) {
    $link = "studentGroepOntGroep";
    if($_POST['selectgroep'] == 'leeg') {
        header("location: admin.php?pagina=studentGroepOntGroep&foutboodschap=U moet een groep kiezen!");
    }
}
else if(isset ($_POST['verderknopstudentenverwijderenselectie'])) {
    if(count($_POST['checkboxStudenten']) > 0) {
        $checkbox2 = serialize($_POST['checkboxStudenten']);
    }
}
else if(isset ($_POST['voltooienstudentenverwijderknop'])) {
    $ch = unserialize($_POST['checkbox2']);

    $count = count($ch);
    for($i=0; $i < $count; $i++) {
        $gelukt = verwijderStudent($ch[$i]);
    }

    if($gelukt) {
        $typeboodschap = "juist";
        $foutboodschap = 'Alle geselecteerden zijn verwijdert!'; // dit is geen foutboodschap
    }
    else {
        $typeboodschap = "fout";
        $foutboodschap = 'Technisch probleem, actie onderbroken en mogelijk niet volledig uitgevoerd, gelieve manueel te controleren!';
    }
}





$TBS->LoadTemplate('./html/admin/templateAdmin.html');

//verwerkMergeGegevens($TBS); //noodzakelijk om de correcte


if(isset ($_GET['pagina'])) {
    if($_GET['pagina'] == 'studentOverzicht') {
        //dropdown voor alle groepen
        $TBS->MergeBlock('blk3', $db, 'SELECT * FROM hoorcollege_vak GROUP BY naam asc');
        //dropdown opvullen voor filter selectie klas
        $TBS->MergeBlock('blk2', $db, 'SELECT * FROM hoorcollege_groep GROUP BY naam asc');



        if(!isset ($_POST['filteroptiesNaam']) && !isset ($_POST['filteroptiesVak']) && !isset ($_POST['filteroptiesGroep'])) {
            //standaard geladen als men nog geen filteropties heeft gekozen
            $TBS->MergeBlock('blk1', $db, "SELECT * FROM hoorcollege_Gebruiker where idGebruiker = '-1' and actief = '1' GROUP BY naam asc");
        }
        //indien men de filteropties gebruikt van de opties kiest
        else {
            $filteroptiesNaam = false;
            $filteroptiesVak = false;
            $filteroptiesGroep = false;

            if(isset ($_POST['toonFilterRes'])) { //nagaan of men op deze pagina wel via correcte weg is gekomen
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
                                                            WHERE g.niveau = '1' and g.actief = '1' AND vv.Vak_idVak = '$vak' AND g.naam LIKE '%$naamBegintMet%'
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
                                                            WHERE g.niveau = '1' and g.actief = '1' AND vv.Vak_idVak = '$vak' AND g.naam LIKE '%$naamBegintMet%'
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
                                                            WHERE g.niveau = '1' and g.actief = '1' AND vv.Vak_idVak = '$vak' AND g.naam LIKE '%$naamBegintMet%'
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
                                                            WHERE g.niveau = '1' and g.actief = '1' AND vv.Vak_idVak = '$vak' AND g.niveau = '1'
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
                                                            WHERE g.niveau = '1' and g.actief = '1' AND vv.Vak_idVak = '$vak' AND g.niveau = '1'
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
                                                           WHERE gg.Groep_idGroep = '$groep' AND g.naam LIKE '%$naamBegintMet%' AND g.niveau = '1' and g.actief = '1'");
                        $studdata = serialize($data);
                    }
                    else {
                        $data = $TBS->MergeBlock('blk1,*', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                                           FROM hoorcollege_gebruiker g
                                                           WHERE g.naam LIKE '%$naamBegintMet%' AND g.niveau = '1' and g.actief = '1' AND g.idGebruiker
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
                                                       WHERE g.naam LIKE '%$naamBegintMet%' AND g.niveau = '1' and g.actief = '1'");
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
                                              WHERE gr.Groep_idGroep = '$groep' 
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

        if(isset ($_POST['studentVakVerderknop'])) { //nagaan of men op deze pagina wel via correcte weg is gekomen
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
                                                            WHERE g.niveau = '1' and g.actief = '1' AND vv.Vak_idVak = '$vak' AND g.naam LIKE '%$naamBegintMet%'
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
                                                            WHERE g.niveau = '1' and g.actief = '1' AND vv.Vak_idVak = '$vak' AND g.naam LIKE '%$naamBegintMet%'
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
                                                            WHERE g.niveau = '1' and g.actief = '1' AND vv.Vak_idVak = '$vak' AND g.naam LIKE '%$naamBegintMet%'
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
                                                            WHERE g.niveau = '1' and g.actief = '1' AND vv.Vak_idVak = '$vak' AND g.niveau = '1'
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
                                                            WHERE g.niveau = '1' and g.actief = '1' AND vv.Vak_idVak = '$vak' AND g.niveau = '1'
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
                                                           WHERE gg.Groep_idGroep = '$groep' AND g.naam LIKE '%$naamBegintMet%' AND g.niveau = '1' and g.actief = '1'");
                    $studdata = serialize($data);
                }
                else {
                    $data = $TBS->MergeBlock('blk1,*', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                                           FROM hoorcollege_gebruiker g
                                                           WHERE g.naam LIKE '%$naamBegintMet%' AND g.niveau = '1' and g.actief = '1' AND g.idGebruiker
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
                                                       WHERE g.naam LIKE '%$naamBegintMet%' AND g.niveau = '1' and g.actief = '1'");
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
                                              WHERE gr.Groep_idGroep = '$groep' AND g.niveau = '1' and g.actief = '1'
                                              GROUP BY g.naam, g.voornaam ASC");
                    $studdata = serialize($data);
                }
                else {
                    $data = $TBS->MergeBlock('blk1,*', $db, "SELECT * FROM hoorcollege_gebruiker
                                              WHERE niveau = 1 and actief = 1 AND idGebruiker NOT IN
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
    else if($_GET['pagina'] == 'groepenOverzicht') {
        //overzicht alle groepen
        $TBS->MergeBlock('blk1', $db, 'SELECT * FROM hoorcollege_groep GROUP BY naam asc');
    }
    else if($_GET['pagina'] == 'groepVakOnt') {
        //overzicht alle vakken
        $TBS->MergeBlock('blk1', $db, 'SELECT * FROM hoorcollege_vak GROUP BY naam asc');
    }
    else if($_GET['pagina'] == 'groepVakOntSelectie') {
        //overzicht alle groepen die het vak volgen
        $vak = $_POST['selectvak'];
        $groepen = array();
        $i = 0;
        $array = $db->Execute('SELECT * FROM hoorcollege_groep GROUP BY naam asc');
        while(!$array->EOF) {
            if(isGroepToegekentAanVak($array->fields['idGroep'], $vak)) {
                $groepen[$i]['idGroep'] = $array->fields['idGroep'];
                $groepen[$i]['naam'] = $array->fields['naam'];
                $i++;
            }
            $array->MoveNext();
        }

        $TBS->MergeBlock('blk1', $groepen);
    }
    else if($_GET['pagina'] == 'groepVakOntOverzicht') {
        $namen = array();
        $checkbox2 = serialize($_POST['checkbox']);
        $count = count($_POST['checkbox']);
        for($i=0; $i < $count; $i++) {
            $namen[$i] = getGroepNameViaId($_POST['checkbox'][$i]);
        }
        $TBS->MergeBlock('blk1',$namen);
    }
    else if($_GET['pagina'] == 'wijzigVaknaam') {
        //overzicht alle vakken
        $TBS->MergeBlock('blk1', $db, 'SELECT * FROM hoorcollege_vak GROUP BY naam asc');
    }
    else if($_GET['pagina'] == 'verwijderBeheerder') {
        //overzicht alle vakken
        $TBS->MergeBlock('blk1', $db, 'SELECT * FROM hoorcollege_vak GROUP BY naam asc');
    }
    else if($_GET['pagina'] == 'verwijderBeheerderVerder') {
        //alle lectoren die het vak geven
        $vak = $_POST['selectvak'];
        //overzicht om de beheerderders van een vak te tonen
        $TBS->MergeBlock('blk1', $db, "SELECT *
                                               FROM hoorcollege_gebruiker g
                                               LEFT JOIN hoorcollege_gebruiker_beheert_vak bv ON g.idGebruiker = bv.Gebruiker_idGebruiker
                                               WHERE bv.Vak_idVak = '$vak'");

    }
    else if($_GET['pagina'] == 'verwijderenVakken') {
        //overzicht alle vakken
        $TBS->MergeBlock('blk1', $db, 'SELECT * FROM hoorcollege_vak GROUP BY naam asc');
    }
    else if($_GET['pagina'] == 'wijzigenGroepen') {
        //overzicht alle groepen
        $TBS->MergeBlock('blk1', $db, 'SELECT * FROM hoorcollege_groep GROUP BY naam asc');
    }
    else if($_GET['pagina'] == 'verwijderenGroepen') {
        //overzicht alle groepen
        $TBS->MergeBlock('blk1', $db, 'SELECT * FROM hoorcollege_groep GROUP BY naam asc');
    }
    else if($_GET['pagina'] == 'verderverwijderenGroepen') {
        $groep = (int) $_POST['selectgroep'];
        $TBS->MergeBlock('blk1', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                              FROM hoorcollege_gebruiker g
                                              LEFT JOIN hoorcollege_gebruikergroep gr ON g.idGebruiker = gr.Gebruiker_idGebruiker
                                              WHERE gr.Groep_idGroep = '$groep' AND g.niveau = '1' and g.actief = '1'
                                              GROUP BY g.naam, g.voornaam ASC");
    }
    else if($_GET['pagina'] == 'studentGroepVerder') {
        //dropdown voor alle vakken
        $TBS->MergeBlock('blk2', $db, 'SELECT * FROM hoorcollege_groep GROUP BY naam asc');

        $filteroptiesNaam = false;
        $filteroptiesVak = false;
        $filteroptiesGroep = false;

        if(isset ($_POST['studentGroepVerder'])) { //nagaan of men op deze pagina wel via correcte weg is gekomen
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
                                                            WHERE g.niveau = '1' and g.actief = '1' AND vv.Vak_idVak = '$vak' AND g.naam LIKE '%$naamBegintMet%'
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
                                                            WHERE g.niveau = '1' and g.actief = '1' AND vv.Vak_idVak = '$vak' AND g.naam LIKE '%$naamBegintMet%'
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
                                                            WHERE g.niveau = '1' and g.actief = '1' AND vv.Vak_idVak = '$vak' AND g.naam LIKE '%$naamBegintMet%'
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
                                                            WHERE g.niveau = '1' and g.actief = '1' AND vv.Vak_idVak = '$vak' AND g.niveau = '1'
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
                                                            WHERE g.niveau = '1' and g.actief = '1' AND vv.Vak_idVak = '$vak' AND g.niveau = '1'
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
                                                           WHERE gg.Groep_idGroep = '$groep' AND g.naam LIKE '%$naamBegintMet%' AND g.niveau = '1'");
                    $studdata = serialize($data);
                }
                else {
                    $data = $TBS->MergeBlock('blk1,*', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                                           FROM hoorcollege_gebruiker g
                                                           WHERE g.naam LIKE '%$naamBegintMet%' AND g.niveau = '1' and g.actief = '1' AND g.idGebruiker
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
                                                       WHERE g.naam LIKE '%$naamBegintMet%' AND g.niveau = '1' and g.actief = '1'");
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
                                              WHERE gr.Groep_idGroep = '$groep' AND g.niveau = '1' and g.actief = '1'
                                              GROUP BY g.naam, g.voornaam ASC");
                    $studdata = serialize($data);
                }
                else {
                    $data = $TBS->MergeBlock('blk1,*', $db, "SELECT * FROM hoorcollege_gebruiker
                                              WHERE niveau = 1 and actief = 1 AND idGebruiker NOT IN
                                              (SELECT Gebruiker_idGebruiker FROM hoorcollege_gebruikergroep)");
                    $studdata = serialize($data);
                }
            }
        }
        else {  //beveiliging
            if(!isset ($_GET['foutboodschap'])) {
                header("location: index.php");
            }
        }
    }
    else if($_GET['pagina'] == 'studentGroep') {
        //dropdown voor alle vakken
        $TBS->MergeBlock('blk1', $db, 'SELECT * FROM hoorcollege_vak GROUP BY naam asc');
        //dropdown opvullen voor filter selectie klas
        $TBS->MergeBlock('blk2', $db, 'SELECT * FROM hoorcollege_groep GROUP BY naam asc');
    }
    else if($_GET['pagina'] == 'studentGroepVerderOverzicht') {
        $count = count($_POST['checkboxStudenten']);
        for($i=0; $i < $count; $i++) {
            $namen[$i] = getGebruikerNaamViaId($_POST['checkboxStudenten'][$i]);
        }
        $TBS->MergeBlock('blk1',$namen);

    }
    else if($_GET['pagina'] == 'resStudentGroepOntNaam') {
        $naam = $_POST['naam'];
        $TBS->MergeBlock('blk1', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                                 FROM hoorcollege_gebruiker g
                                                 WHERE g.naam LIKE '%$naam%' AND g.niveau = '1' and g.actief = '1'");
    }
    else if($_GET['pagina'] == 'StudentGroepOntNaamOverzicht') {
        $count = count($_POST['checkboxStudenten']);
        for($i=0; $i < $count; $i++) {
            $namen[$i] = getGebruikerNaamViaId($_POST['checkboxStudenten'][$i]);
        }
        $TBS->MergeBlock('blk1',$namen);
    }
    else if($_GET['pagina'] == 'studentGroepOntGroep') {
        //overzicht alle groepen
        $TBS->MergeBlock('blk1', $db, 'SELECT * FROM hoorcollege_groep GROUP BY naam asc');
    }
    else if($_GET['pagina'] == 'resStudentGroepOntGroep') {
        $groep = $_POST['selectgroep'];
        //overzicht van alle studenten van een groep
        $TBS->MergeBlock('blk1', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                               FROM hoorcollege_gebruiker g
                                               LEFT JOIN hoorcollege_gebruikergroep gg ON g.idGebruiker = gg.Gebruiker_idGebruiker
                                               WHERE gg.Groep_idGroep = '$groep'");
    }
    else if($_GET['pagina'] == 'detailsstudent') {
        $id = $_GET['detailsGebruikerId'];
        //overzicht persoonlijke gegevens
        $TBS->MergeBlock('blk1', $db, "SELECT * FROM hoorcollege_gebruiker WHERE idGebruiker = '$id' GROUP BY naam, voornaam asc");
        //overzicht van alle vakken die de student volgt
        $TBS->MergeBlock('blk2', $db, "SELECT v.naam, vv.van
                                               FROM hoorcollege_vak v
                                               LEFT JOIN hoorcollege_gebruiker_volgt_vak vv ON v.idVak = vv.Vak_idVak
                                               WHERE vv.Gebruiker_idGebruiker = '$id'
                                               GROUP BY v.naam ASC");
    }
    else if($_GET['pagina'] == 'lectorDetails') {
        $id = $_GET['detailsLectorId'];
        //overzicht van persoonlijke gegevens
        $TBS->MergeBlock('blk32', $db, "SELECT * FROM hoorcollege_gebruiker WHERE niveau != 1 AND idGebruiker = '$id' GROUP BY naam, voornaam asc");
        //overzicht alle vakken van een lector
        $TBS->MergeBlock('blk31', $db, "SELECT v.naam as naam, v.idVak as id
                                            FROM hoorcollege_vak v
                                            LEFT JOIN hoorcollege_gebruiker_beheert_vak bv ON v.idVak = bv.Vak_idVak
                                            WHERE bv.Gebruiker_idGebruiker = '$id'
                                            GROUP BY v.naam ASC");
    }
    else if($_GET['pagina'] == 'studentVerwijderen') {
        //dropdown voor alle vakken
        $TBS->MergeBlock('blk1', $db, 'SELECT * FROM hoorcollege_vak GROUP BY naam asc');
        //overzicht alle groepen
        $TBS->MergeBlock('blk2', $db, 'SELECT * FROM hoorcollege_groep GROUP BY naam asc');
    }
    else if($_GET['pagina'] == 'studentVerwijderenOverzicht') {
        $count = count($_POST['checkboxStudenten']);
        for($i=0; $i < $count; $i++) {
            $namen[$i] = getGebruikerNaamViaId($_POST['checkboxStudenten'][$i]);
        }
        $TBS->MergeBlock('blk1',$namen);
    }
    else if($_GET['pagina'] == 'studentVerwijderenVerder') {
        $filteroptiesNaam = false;
        $filteroptiesVak = false;
        $filteroptiesGroep = false;

        if(isset ($_POST['studentenverwijderenverder'])) { //nagaan of men op deze pagina wel via correcte weg is gekomen
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
                                                            WHERE g.niveau = '1' and g.actief = '1' AND vv.Vak_idVak = '$vak' AND g.naam LIKE '%$naamBegintMet%'
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
                                                            WHERE g.niveau = '1' and g.actief = '1' AND vv.Vak_idVak = '$vak' AND g.naam LIKE '%$naamBegintMet%'
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
                                                            WHERE g.niveau = '1' and g.actief = '1' AND vv.Vak_idVak = '$vak' AND g.naam LIKE '%$naamBegintMet%'
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
                                                            WHERE g.niveau = '1' and g.actief = '1' AND vv.Vak_idVak = '$vak' AND g.niveau = '1'
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
                                                            WHERE g.niveau = '1' and g.actief = '1' AND vv.Vak_idVak = '$vak' AND g.niveau = '1'
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
                                                           WHERE gg.Groep_idGroep = '$groep' AND g.naam LIKE '%$naamBegintMet%' AND g.niveau = '1' and g.actief = '1'");
                    $studdata = serialize($data);
                }
                else {
                    $data = $TBS->MergeBlock('blk1,*', $db, "SELECT g.idGebruiker, g.naam, g.voornaam
                                                           FROM hoorcollege_gebruiker g
                                                           WHERE g.naam LIKE '%$naamBegintMet%' AND g.niveau = '1' and g.actief = '1' AND g.idGebruiker
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
                                                       WHERE g.naam LIKE '%$naamBegintMet%' AND g.niveau = '1' and g.actief = '1'");
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
                                              WHERE gr.Groep_idGroep = '$groep' AND g.niveau = '1' and g.actief = '1'
                                              GROUP BY g.naam, g.voornaam ASC");
                    $studdata = serialize($data);
                }
                else {
                    $data = $TBS->MergeBlock('blk1,*', $db, "SELECT * FROM hoorcollege_gebruiker
                                              WHERE niveau = 1 and actief = 1 AND idGebruiker NOT IN
                                              (SELECT Gebruiker_idGebruiker FROM hoorcollege_gebruikergroep)");
                    $studdata = serialize($data);
                }
            }
        }
    }
    else if($_GET['pagina'] == 'vakDetails') {
        $id = $_GET['Id'];
        //overzicht om de beheerderders van een vak te tonen
        $TBS->MergeBlock('blk33', $db, "SELECT *
                                            FROM hoorcollege_gebruiker g
                                            LEFT JOIN hoorcollege_gebruiker_beheert_vak bv ON g.idGebruiker = bv.Gebruiker_idGebruiker
                                            WHERE bv.Vak_idVak = '$id'");
        
        //overzicht van alle studenten die het vak volgen
        $TBS->MergeBlock('blk34', $db, "SELECT g.naam, g.voornaam, g.idGebruiker
                                            FROM hoorcollege_gebruiker g
                                            LEFT JOIN hoorcollege_gebruiker_volgt_vak vv ON g.idGebruiker = vv.Gebruiker_idGebruiker
                                            WHERE vv.Vak_idVak = '$id'
                                            GROUP BY g.naam, g.voornaam ASC");
    }
    else if($_GET['pagina'] == 'groepDetails') {
        $id = $_GET['detailsGroepId'];
        //overzicht om de leden
        $TBS->MergeBlock('blk1', $db, "SELECT *
                                       FROM hoorcollege_gebruiker g
                                       LEFT JOIN hoorcollege_gebruikergroep bv ON g.idGebruiker = bv.Gebruiker_idGebruiker
                                       WHERE bv.Groep_idGroep = '$id'");    
    }
}



$TBS->Show() ;
?>
