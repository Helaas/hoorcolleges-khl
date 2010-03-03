<?php
include_once('./../includes/kern.php');
$config["pagina"] = "lector/commentarenVerwijderen.html";
session_start();
$TBS->LoadTemplate('./../html/lector/templateLector.html') ;

if(isset($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 40 && isset($_GET["hoorcollege"])) {
    //$hoorcollege moet nog veranderen naar = $_GET["hoorcollege"];
    $idHoorcollege = $_GET["hoorcollege"];
    $hoorcollegeInfo = $db->GetRow('select naam from hoorcollege_hoorcollege where idHoorcollege='.$idHoorcollege);
    $naamHoorcollege = $hoorcollegeInfo['naam'];
    $alleCommentarenQuery = $db->Execute('SELECT idReactie, voornaam, naam, inhoud, datum
                                     FROM hoorcollege_reactie LEFT OUTER JOIN hoorcollege_gebruiker
                                     ON Gebruiker_idGebruiker = idGebruiker
                                     WHERE Hoorcollege_idHoorcollege ='.$idHoorcollege);
    
    //Noodzakelijk voor het weglaten van de slahes in de inhoud
    $alleCommentarenTabel = array();
    $i = 0;
    while(!$alleCommentarenQuery->EOF){
          $alleCommentarenTabel[$i]["idReactie"] = $alleCommentarenQuery->fields["idReactie"];
          $alleCommentarenTabel[$i]["voornaam"] = $alleCommentarenQuery->fields["voornaam"];
          $alleCommentarenTabel[$i]["naam"] = $alleCommentarenQuery->fields["naam"];
          $alleCommentarenTabel[$i]["inhoud"] = stripslashes($alleCommentarenQuery->fields["inhoud"]);
          $alleCommentarenTabel[$i]["datum"] = $alleCommentarenQuery->fields["datum"];
          $i++;
          $alleCommentarenQuery->MoveNext();
     }

    $TBS->MergeBlock('blk1', $alleCommentarenTabel);


    

} else { //geen lector / niet ingelogged
        $config["pagina"] = "./FileUpload/Error1Login.html";
        $TBS->LoadTemplate('./../html/lector/templateLector.html') ;
}

    $TBS->Show() ;
?>
