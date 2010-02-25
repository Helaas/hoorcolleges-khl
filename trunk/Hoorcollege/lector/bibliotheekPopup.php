<?php
    require_once('./../includes/kern.php');
    session_start();

    if(isset($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 40){ //lector is ingelogged
        $toegestaneTypes = array();
        $toegestaneTypes[] = "mp3";
        $toegestaneTypes[] = "flv";
        $toegestaneTypes[] = "txt";

        if (isset($_GET["type"]) && in_array($_GET["type"],  $toegestaneTypes)){
            $bibliotheekCategorie=0;
            if (isset($_POST["bibliotheekCategorie"])) $bibliotheekCategorie=(int)$_POST["bibliotheekCategorie"];
            if (isset($_GET["geselecteerd"])){
                $bibliotheekCategorie=(int)getBibliotheekCategorieId($_GET["geselecteerd"]);
            }
            $TBS->LoadTemplate('./../html/lector/bibliotheekPopup.html');
            $TBS->MergeBlock("blk1",$db,"select *, IF(idBibliotheekCategorie= ". $bibliotheekCategorie .", \" selected\", \"\") as selected from hoorcollege_bibliotheekcategorie WHERE Gebruiker_idGebruiker = ".(int)$_SESSION['gebruiker']->getIdGebruiker());

            $TBS->MergeBlock("blk2",$db,"SELECT * FROM hoorcollege_bibliotheekitem
                                         where BibliotheekCategorie_Gebruiker_idGebruiker = ". (int)$_SESSION['gebruiker']->getIdGebruiker() ."
                                         and mimetype = '". $_GET["type"] ."'
                                         and BibliotheekCategorie_idBibliotheekCategorie = ".$bibliotheekCategorie);

                
                

                
        } else {
            $fout["reden"] = "Fout type";
            $fout["inhoud"] = "U heeft een fout type geslecteerd.";
            $config["pagina"] = "./algemeneFout.html";
            $TBS->LoadTemplate('./../html/lector/templateLector.html');
        }

    } else {
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
    }
    $TBS->Show();

?>
