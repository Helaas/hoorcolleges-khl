<?php
include_once('./../includes/kern.php');
session_start();

$config["pagina"] = "./FileUpload/Error1Login.html";
$tekstinhoud = "";

if(isset ($_SESSION['gebruiker'])) {
    $gebruiker = $_SESSION['gebruiker'];
    $gebruikerNiv = $gebruiker->getNiveau();
    $gebruikerID = $gebruiker->getIdGebruiker();

    if($gebruikerNiv==40) {
      $ondid=$_GET['gevraagdond'];
      $ondnaam=$_GET['gevraagdondnaam'];


            $config["pagina"] = "./Lector/editOnderwerp.html";
            $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
            $TBS->Show() ;
       
    }  //Users met onvoldoende privileges voor deze pagina een foutpagina tonen
    else if($_SESSION['gebruiker']->getNiveau() == 1) {
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./../html/lector/templateLector.html');
        $TBS->Show() ;
    }else if($_SESSION['gebruiker']->getNiveau() == 99) {
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./../html/lector/templateLector.html');
        $TBS->Show() ;
    }

    else {
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
        $TBS->Show() ;
    }
}


else {
    $config["pagina"] = "./FileUpload/Error1Login.html";
    $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
    $TBS->Show() ;
}
?>
