<?php
include_once('./../includes/kern.php');
session_start();

$config["pagina"] = "./FileUpload/Error1Login.html";
$tekstinhoud = "";

if(isset ($_SESSION['gebruiker'])) {
    $gebruiker = $_SESSION['gebruiker'];
    $gebruikerNiv = $gebruiker->getNiveau();
    $gebruikerID = $gebruiker->getIdGebruiker();

    if($gebruikerNiv>= 40) {

        if (preg_match('/^[0-9]+$/iD', $_POST['vakID']) && preg_match('/^[a-z0-9\+\#\ ]+$/iD', $_POST['veld1'])) {
//voeg toe


            $result= $db->Execute("select * from hoorcollege_onderwerp where vak_idVak=".$_POST["vakID"]." AND naam=\"".$_POST['veld1']."\"");
            if($result->fields["naam"]==null) {

                //Als we van maakHoorcollege komen
                if  (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'maakHoorcollege.php') !== false){
                    header("location: maakHoorcollege.php");
                }
                
                $db->Execute("INSERT INTO hoorcollege_onderwerp (idOnderwerp, naam, Vak_idVak) VALUES (NULL, '".$_POST['veld1']."', '".$_POST['vakID']."')");
                $config["pagina"] = "./Lector/Boodschap.html";
                $Titel="Onderwerp toevoegen";
                $tekstinhoud = "Het onderwerp ".'"'.$_POST['veld1'].'"'." werd toegevoegd";
            }
            else {
                $tekstinhoud = "Het onderwerp werd niet toegevoegd omdat er al een onderwerp met deze naam bestaat.";
                $Titel="Foutmelding";
                $config["pagina"] = "./lector/Boodschap.html";
            }


            $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
            $TBS->Show() ;
        }
        else {
            //Geen Speciale Tekens toegestaan
            $Titel="Foutmelding";
            $tekstinhoud = "Het onderwerp werd niet toegevoegd omdat het speciale tekens bevat.";
            $config["pagina"] = "./Lector/Boodschap.html";
            $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
            $TBS->Show() ;
        }
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