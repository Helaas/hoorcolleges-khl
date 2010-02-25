<?php
    require_once('./../includes/kern.php');
    session_start();

    if(isset($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 40){ //lector is ingelogged
        $bibliotheekCategorie=0;
        if (isset($_POST["bibliotheekCategorie"])) $bibliotheekCategorie=(int)$_POST["bibliotheekCategorie"];
        
        $TBS->LoadTemplate('./../html/lector/bibliotheekPopup.html');
        $TBS->MergeBlock("blk1",$db,"select *, IF(idBibliotheekCategorie= ". $bibliotheekCategorie .", \" selected\", \"\") as selected from hoorcollege_bibliotheekcategorie WHERE Gebruiker_idGebruiker = ".(int)$_SESSION['gebruiker']->getIdGebruiker());

         if (isset($_POST["bibliotheekCategorie"])) {

             echo "lolol";
         }

    } else {
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
    }
    $TBS->Show();

?>
