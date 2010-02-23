<?php
include_once('./includes/kern.php');
session_start();

$TBS = new clsTinyButStrong;


if(isset ($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 1) {
    $config["pagina"] = "./student/commentaar.html";
    $TBS->LoadTemplate('./html/student/templateStudent.html') ;
    $gebruikerID = $_SESSION['gebruiker']->getIdGebruiker();
    //Nog even standaard hoorcollegeID
    $hoorcollegeID = 1;


    if($_GET){
    //Inhoud commentaar moet ik nog controleren

    $commentaar = $_GET['commentaar'];
    echo $commentaar;
    voegCommentaarToe($gebruikerID, $hoorcollegeID, $commentaar);

    $alleCommentarenVanHoorcollege = $db->Execute("select * from hoorcollege_reactie where hoorcollege_idHoorcollege=".$hoorcollegeID);

    //xml file aanmaken
    $xml_file  = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
    $xml_file .= "<root>";

    while (!$alleCommentarenVanHoorcollege->EOF) {
        $gebruiker = getGebruikerNaamViaId($alleCommentarenVanHoorcollege->fields["Gebruiker_idGebruiker"]);
        $xml_file .= "<div>";
        $xml_file .= "<Gebruiker>".$gebruiker."</Gebruiker>";
        $xml_file .= "<Tekst>".$alleCommentarenVanHoorcollege->fields["inhoud"]."</Tekst>";
        $xml_file .= "</div>";
        $alleCommentarenVanHoorcollege->MoveNext();
    }

    $xml_file .= "</root>";

    echo $xml_file;

    }



}else if(!isset ($_SESSION['gebruiker'])) {
    header("location: login.php");
}else {
    $config["pagina"] = "./FileUpload/Error1Login.html";
    $TBS->LoadTemplate('./html/template.html') ;
}
$TBS->Show();
?>
