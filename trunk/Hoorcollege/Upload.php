<!-- Php file die instaat voor het uploaden van het bestand -->
<?php
include_once('./includes/TinyButStrong.php');
include_once('./includes/gebruiker.class.php');
$TBS = new clsTinyButStrong;

session_start();

$config["pagina"] = "./FileUpload/FileAdded.html";
$testinhoud = "";

if(isset ($_SESSION['gebruiker'])) {
        $gebruiker = $_SESSION['gebruiker'];
        $gebruikerNiv = $gebruiker->getNiveau();
        $gebruikerID = $gebruiker->getIdGebruiker();



     

        if($gebruikerNiv==40 ){
            //Folder mag geen speciale tekens zoals een punt bevatten, anders zou een vak als .NET bvb een hidden folder aanmaken
         if (preg_match('/^[a-z0-9\+\#\ ]+$/iD', $_POST['cat']) && preg_match('/^[0-9]+$/iD', $_SESSION['gebruiker']->getIdGebruiker()) && preg_match('/^[a-z0-9\+\#\ ]+$/iD', $_POST['bestandsnaam'])) {

    //Kijk of de folder reeds bestaat, zoniet maak hem aan, herhaal dit voor alle subfolders
    if (!is_dir("Bibliotheek/".$gebruikerID."/")){
        mkdir("Bibliotheek/".$gebruikerID, 0777);
    }

    if (!is_dir("Bibliotheek/".$gebruikerID."/".$_POST['cat']."/")){
        mkdir("Bibliotheek/".$gebruikerID."/".$_POST['cat'], 0777);
    }


//Path waar het bestand heen moet
$target_path = "Bibliotheek/".$gebruikerID."/".$_POST['cat']."/";
$target_path = $target_path . basename( $_FILES['uploadedfile']['name']);

//Code voor het verplaatsen/kopiëren van het tijdelijke bestand naar de server+pagina weergeven
if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
   $testinhoud="Het bestand ".'"'.  basename( $_FILES['uploadedfile']['name']).'"'." werd geüpload!";
} else{
    $testinhoud="Er was een probleem bij het uploaden van je bestand, probeer het later opnieuw!  (De maximale bestandsgrootte is ".$_POST['MAX_FILE_SIZE']." bytes)";
}


$TBS->LoadTemplate('./html/lector/templateLector.html') ;
$TBS->Show() ;
        }
else{
    if(!preg_match('/^[a-z0-9\+\#\ ]+$/iD', $_POST['cat'])){
        $Titel="Foutmelding";
        $tekstinhoud="U heeft geen categorie geselecteerd.";

        $config["pagina"] = "./lector/Boodschap.html";
        $TBS->LoadTemplate('./html/lector/templateLector.html') ;
        $TBS->Show() ;
    }
    else{
$config["pagina"] = "./FileUpload/Error2Input.html";
$TBS->LoadTemplate('./html/lector/templateLector.html') ;
$TBS->Show() ;
    }
}
}

//Users met onvoldoende privileges voor deze pagina een foutpagina tonen
    else if($_SESSION['gebruiker']->getNiveau() == 1){
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./html/student/templateStudent.html');
         $TBS->Show() ;
    }else if($_SESSION['gebruiker']->getNiveau() == 99){
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./html/admin/templateAdmin.html');
         $TBS->Show() ;
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
