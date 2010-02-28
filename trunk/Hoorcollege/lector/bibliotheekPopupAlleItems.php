<?php
require_once('./../includes/kern.php');
session_start();
$gelukt = false;


if(isset($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 40) { //lector is ingelogged
    $toegestaneTypes = array();
    $toegestaneTypes[] = "mp3";
    $toegestaneTypes[] = "flv";
    $toegestaneTypes[] = "txt";

    $TBS->LoadTemplate('./../html/lector/bibliotheekPopupAlleItems.html');


    //Alle item van de gebruiker, gesorteerd op type, categorie en naam
    $TBS->MergeBlock("blk2",$db,"SELECT idBibliotheekItem, bi.naam, mimetype, beschrijving, bc.naam as categorienaam
                                 FROM hoorcollege_bibliotheekitem bi
                                 LEFT OUTER JOIN hoorcollege_bibliotheekcategorie bc
                                 ON BibliotheekCategorie_idBibliotheekCategorie = idBibliotheekCategorie
                                 WHERE BibliotheekCategorie_Gebruiker_idGebruiker =". (int)$_SESSION['gebruiker']->getIdGebruiker()."
                                 ORDER BY mimetype, bc.naam, bi.naam ");

    if($_GET['pagina'] == 'pasaan') {
        $itemId = $_GET['itemId'];
        $itemNaam = getBibliotheekitemNaam($itemId);
        $TBS->LoadTemplate('./../html/lector/pasItemAan.html');
        if (isset($_POST["beschrijvingVeranderen"])) {
                $nieuweBeschrijving = mysql_real_escape_string($_POST['nieuweBeschrijving']);
                $db->Execute("UPDATE hoorcollege_bibliotheekitem SET beschrijving = '".$nieuweBeschrijving."'
                              WHERE idBibliotheekItem=".$itemId);
                $gelukt=true;
        }

    }else if($_GET['pagina'] == 'verwijder') {
        $itemId = $_GET['itemId'];
        $itemNaam = getBibliotheekitemNaam($itemId);

        $TBS->LoadTemplate('./../html/lector/verwijderItem.html');
    }


} else {
    $config["pagina"] = "./FileUpload/Error1Login.html";
    $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
}
$TBS->Show();

?>
