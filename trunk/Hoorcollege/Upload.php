<?php
include_once('./includes/TinyButStrong.php');
$TBS = new clsTinyButStrong;
$config["pagina"] = "FileAdded.html";
$testinhoud = "";



$target_path = "TestBibliotheek/";

$target_path = $target_path . basename( $_FILES['uploadedfile']['name']);

if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
   $testinhoud="Het bestand ".'"'.  basename( $_FILES['uploadedfile']['name']).'"'." werd geüpload!";
} else{
    $testinhoud="Er was een probleem bij het uploaden van je bestand, probeer het later opnieuw!  (De maximale bestandsgrootte is ".$_POST['MAX_FILE_SIZE']." bytes)";
}

$TBS->LoadTemplate('./html/template.html') ;
$TBS->Show() ;
?>
