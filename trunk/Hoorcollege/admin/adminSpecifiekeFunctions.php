<?php
    //require_once('./includes/kern.php');    //kern is essentieel voor DB en heeft includes ivm met functions, etc

    
    //indien men niet ingelogd is als admin zal deze methode redirecten naar index.php (gebruikt in admin.php)
    function verwerkLogin() {
        if(!isset ($_SESSION['gebruiker']) || !$_SESSION['gebruiker']->getNiveau() == "99") {
            header("location: index.php");
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
        if(isset ($_GET['pagina'])) {
            if($_GET['pagina'] == 'studentOverzicht') {
                $TBS->MergeBlock('blk16', $db, "SELECT * FROM hoorcollege_gebruiker
                                                WHERE actief = 1 AND niveau = 1 AND (naam LIKE 'A%' OR naam LIKE 'B%')
                                                GROUP BY naam, voornaam asc");
                //dropdown opvullen voor filter selectie klas
                $TBS->MergeBlock('blk17', $db, 'SELECT * FROM hoorcollege_groep GROUP BY naam asc');
            }
        }
    }
?>
