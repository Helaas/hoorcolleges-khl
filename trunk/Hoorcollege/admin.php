<?php    
    include_once('./includes/TinyButStrong.php');
    include_once('gebruiker.class.php'); 
    $TBS = new clsTinyButStrong;

    session_start();

    $config["pagina"] = "admin.html";    

    if(isset ($_SESSION['gebruiker'])) {
        $gebruiker = $_SESSION['gebruiker'];
        $gebruikerID = $gebruiker->getIdGebruiker();
        $TBS->LoadTemplate('./html/template.html') ;
        $TBS->Show() ;
    }
    else {
        echo 'Niet ingelogd';
    }
    
?>
