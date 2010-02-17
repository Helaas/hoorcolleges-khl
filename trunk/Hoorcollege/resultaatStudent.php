<?php
    require_once('./includes/kern.php');
    session_start();

    $TBS = new clsTinyButStrong;

    if(isset ($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 1){
        $config["pagina"] = "./student/resultaatStudent.html";
        $TBS->LoadTemplate('./html/student/templateStudent.html');
        $gebruikerID = $_SESSION['gebruiker']->getIdGebruiker();
        $hoorcollegeID = $_GET['hoorcollege'];
        echo $hoorcollegeID;

    }else if(!isset ($_SESSION['gebruiker'])){
        header("location: login.php");
    }else{
        $config["pagina"] = "./FileUpload/Error1Login.html";
    }

    $TBS->Show();
?>
