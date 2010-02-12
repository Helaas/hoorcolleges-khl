<?php    
    include_once('./includes/TinyButStrong.php');
    include_once('./includes/gebruiker.class.php');
    $TBS = new clsTinyButStrong;

    session_start();
   
    if(isset ($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == "99" && isset ($_GET['actie'])) {
        //nagaan naar welke pagina er moet genavigeerd worden
        $config["pagina"] = $_GET['actie'] . ".html";
       
        if($_GET['actie'] == 'student') {
            //table aanmaken voor de studentenoverzicht op student.html
            //$TBS->MergeBlock('blk1', 'web_k_be', 'SELECT * FROM ');
        }
        $gebruiker = $_SESSION['gebruiker'];
        $gebruikerID = $gebruiker->getIdGebruiker();
        $TBS->LoadTemplate('./html/template.html') ;
        $TBS->Show() ;
    }
    else if(isset ($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == "99" ) {
        $config["pagina"] = "admin.html";
        
        $gebruiker = $_SESSION['gebruiker'];
        $gebruikerID = $gebruiker->getIdGebruiker();
        $TBS->LoadTemplate('./html/template.html') ;
        $TBS->Show() ;
    }
    else {
        echo 'Niet ingelogd';
    }
    
?>
