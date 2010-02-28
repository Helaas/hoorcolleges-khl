<?php
    require_once('./../includes/kern.php');
    session_start();
    $script = false;
    
    if(isset($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 40){ //lector is ingelogged
        if (isset($_POST["verzenden"])){
            print_r($_POST);
            $foutboodschap = "";

            if (!isset($_POST['vak']) || !$_POST['vak'] >0 ) $foutboodschap .= "- U moet een vak selecteren\n";
            if (!isset($_POST['Ond']) || !$_POST['Ond'] >0 ) $foutboodschap .= "- U moet een onderwerp selecteren\n";
            @$som = $_POST["keuze_flv"] +  $_POST["keuze_mp3"] + $_POST["keuze_txt"];
            if ($som == -3) $foutboodschap .= "- Uw hoorcollege moet minstens één component bevatten\n";
            if (!isset($_POST["studentGeselecteerd"]) || count($_POST["studentGeselecteerd"])<=0) $foutboodschap .= "- Er moet minstens één student toegewezen worden aan dit hoorcollege\n";

            echo $foutboodschap;

            if (strlen($foutboodschap)>0){ //foutboodschap weergeven enzo
                $fout = true;
                $script = true;
                $config["pagina"] = "./lector/maakHoorcollege.html";
                $q = "select *,IF(idVak= ". (int)$_POST['vak'] .", \" selected\", \"\") as selected from hoorcollege_vak where idVak in(SELECT Vak_idVak FROM hoorcollege_gebruiker_beheert_vak WHERE gebruiker_idgebruiker =".$_SESSION['gebruiker']->getIdGebruiker().")";
                $TBS->LoadTemplate('./../html/lector/templateLector.html');
                $TBS->MergeBlock("blk1",$db,$q);
            } else { //alles OK, hoorcollege maken
                echo "ok";
            }

        } else {
            $config["pagina"] = "./lector/maakHoorcollege.html";
            $q = "select * from hoorcollege_vak where idVak in(SELECT Vak_idVak FROM hoorcollege_gebruiker_beheert_vak WHERE gebruiker_idgebruiker =".$_SESSION['gebruiker']->getIdGebruiker().")";
            $TBS->LoadTemplate('./../html/lector/templateLector.html');
            $TBS->MergeBlock("blk1",$db,$q);
        }
    } else {
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
    }
    $TBS->Show();
?>
