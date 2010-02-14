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



        if (preg_match('/^[a-z0-9\+\#]+$/iD', $_POST['vak']) && preg_match('/^[a-z0-9\+\#]+$/iD', $_POST['onderwerp'])) {

        if($gebruikerNiv==40){

    //Kijk of de folder reeds bestaat, zoniet maak hem aan, herhaal dit voor alle subfolders
    if (!is_dir("Bibliotheek/".$gebruikerID."/")){
        mkdir("Bibliotheek/".$gebruikerID, 0777);
    }

    if (!is_dir("Bibliotheek/".$gebruikerID."/".$_POST['vak']."/")){
        mkdir("Bibliotheek/".$gebruikerID."/".$_POST['vak'], 0777);
    }

        if (!is_dir("Bibliotheek/".$gebruikerID."/".$_POST['vak']."/".$_POST['onderwerp']."/")){
        mkdir("Bibliotheek/".$gebruikerID."/".$_POST['vak']."/".$_POST['onderwerp'], 0777);
    }


//Path waar het bestand heen moet
$target_path = "Bibliotheek/".$gebruikerID."/".$_POST['vak']."/".$_POST['onderwerp']."/";
$target_path = $target_path . basename( $_FILES['uploadedfile']['name']);

//Code voor het verplaatsen/kopiëren van het tijdelijke bestand naar de server+pagina weergeven
if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
   $testinhoud="Het bestand ".'"'.  basename( $_FILES['uploadedfile']['name']).'"'." werd geüpload!";
} else{
    $testinhoud="Er was een probleem bij het uploaden van je bestand, probeer het later opnieuw!  (De maximale bestandsgrootte is ".$_POST['MAX_FILE_SIZE']." bytes)";
}


$TBS->LoadTemplate('./html/Lector/templateLector.html') ;
$TBS->Show() ;
        }
}
else{
$config["pagina"] = "./FileUpload/Error2Input.html";
$TBS->LoadTemplate('./html/Lector/templateLector.html') ;
$TBS->Show() ;
}
}
?>
