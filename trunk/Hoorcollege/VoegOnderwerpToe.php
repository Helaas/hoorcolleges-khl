<?php
include_once('./includes/TinyButStrong.php');
include_once('./includes/gebruiker.class.php');
include_once('./includes/kern.php');
$TBS = new clsTinyButStrong;

session_start();

$config["pagina"] = "./FileUpload/Error1Login.html";
$tekstinhoud = "";

if(isset ($_SESSION['gebruiker'])) {
        $gebruiker = $_SESSION['gebruiker'];
        $gebruikerNiv = $gebruiker->getNiveau();
        $gebruikerID = $gebruiker->getIdGebruiker();



        if($gebruikerNiv==40){
            
 if (preg_match('/^[0-9]+$/iD', $_POST['vakID']) && preg_match('/^[a-z0-9\+\#\ ]+$/iD', $_POST['onderwerp'])) {
//voeg toe
    $tekstinhoud = "Het onderwerp ".$_POST['onderwerp'].$_POST['vakID']." werd toegevoegd";
    $db->Execute("INSERT INTO `hoorcolleges`.`hoorcollege_onderwerp` (`idOnderwerp`, `naam`, `Vak_idVak`) VALUES (NULL, '".$_POST['onderwerp']."', '".$_POST['vakID']."')");
     $config["pagina"] = "./Lector/OndToegevoegd.html";


$TBS->LoadTemplate('./html/lector/templateLector.html') ;
$TBS->Show() ;
        }
else{
    //Geen Speciale Tekens

$config["pagina"] = "./FileUpload/Error2Input.html";
$TBS->LoadTemplate('./html/lector/templateLector.html') ;
$TBS->Show() ;
}
}
    else {
        $config["pagina"] = "./FileUpload/Error1Login.html";
         $TBS->LoadTemplate('./html/template.html') ;
        $TBS->Show() ;
    }

    }
    else {
        $config["pagina"] = "./FileUpload/Error1Login.html";
         $TBS->LoadTemplate('./html/template.html') ;
        $TBS->Show() ;
    }
?>