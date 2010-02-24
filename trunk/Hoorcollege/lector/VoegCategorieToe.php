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

        if (preg_match('/^[0-9]+$/iD', $gebruikerID) && preg_match('/^[a-z0-9\+\#\ ]+$/iD', $_POST['veld1'])) {
//voeg toe


            $result= $db->Execute("select * from hoorcollege_bibliotheekcategorie where Gebruiker_idGebruiker=".$gebruikerID." AND naam=\"".$_POST['veld1']."\"");
            if($result->fields["naam"]==null) {
                $db->Execute("INSERT INTO hoorcollege_bibliotheekcategorie (naam, Gebruiker_idGebruiker) VALUES ('".$_POST['veld1']."', '".$gebruikerID."')");
                $config["pagina"] = "./Lector/Boodschap.html";
                $Titel="Categorie toevoegen";
                $tekstinhoud = "De categorie ".'"'.$_POST['veld1'].'"'." werd toegevoegd";
            }
            else {
                $tekstinhoud = "De categorie werd niet toegevoegd omdat er al een categorie met deze naam bestaat.";
                $Titel="Foutmelding";
                $config["pagina"] = "./Lector/Boodschap.html";
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
        $TBS->LoadTemplate('./../html/student/templateStudent.html');
        $TBS->Show() ;
    }else if($_SESSION['gebruiker']->getNiveau() == 99) {
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./../html/admin/templateAdmin.html');
        $TBS->Show() ;
    }

    else {
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./../html/template.html') ;
        $TBS->Show() ;
    }

}
else {
    $config["pagina"] = "./FileUpload/Error1Login.html";
    $TBS->LoadTemplate('./../html/template.html') ;
    $TBS->Show() ;
}
?>
