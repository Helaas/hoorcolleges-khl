<?php
include_once('./includes/kern.php');
session_start();

$TBS = new clsTinyButStrong;


if(isset ($_SESSION['gebruiker']) && $_SESSION['gebruiker']->getNiveau() == 1){
    $config["pagina"] = "./student/student.html";
    $TBS->LoadTemplate('./html/student/templateStudent.html') ;
    $gebruikerID = $_SESSION['gebruiker']->getIdGebruiker();

    $nogTeBekijken = array();
    $nogTeBekijkenTabel = array();
    $i = 0;

    //Query voor overzicht hoorcolleges nog te bekijken
    $queryNogTeBekijken = $db->Execute('SELECT idHoorcollege, naam, Onderwerp_Vak_idVak, Onderwerp_idOnderwerp
                            FROM (hoorcollege_hoorcollege h LEFT OUTER JOIN hoorcollege_gebruikerhoorcollege gh
                            ON gh.Hoorcollege_idHoorcollege = h.idHoorcollege)
                            LEFT OUTER JOIN hoorcollege_onderwerphoorcollege oh ON h.idHoorcollege = oh.Hoorcollege_idHoorcollege
                            WHERE Gebruiker_idGebruiker ='.$gebruikerID.' AND reedsBekeken = false
                            ORDER BY Onderwerp_Vak_idVak, Onderwerp_idOnderwerp, idHoorcollege');
    while(!$queryNogTeBekijken->EOF){
        $vak = $db->Execute('SELECT naam FROM hoorcollege_vak WHERE idVak ='.$queryNogTeBekijken->fields["Onderwerp_Vak_idVak"]);
        $onderwerp = $db->Execute('SELECT naam FROM hoorcollege_onderwerp WHERE idOnderwerp ='.$queryNogTeBekijken->fields["Onderwerp_idOnderwerp"]);
        $nogTeBekijken["vak"] = $vak->fields["naam"];
        $nogTeBekijken["onderwerp"] = $onderwerp->fields["naam"];
        $nogTeBekijken["idHoorcollege"] = $queryNogTeBekijken->fields["idHoorcollege"];
        $nogTeBekijken["naam"] = $queryNogTeBekijken->fields["naam"];
        $nogTeBekijkenTabel[$i] = $nogTeBekijken;
        $i = $i+1;
        $queryNogTeBekijken->MoveNext();
    }

    $alBekeken = array();
    $alBekekenTabel = array();
    $i = 0;

    //Query voor overzicht hoorcolleges reeds bekeken
    $queryReedsBekeken = $db->Execute('SELECT idHoorcollege, naam, Onderwerp_Vak_idVak, Onderwerp_idOnderwerp
                            FROM (hoorcollege_hoorcollege h LEFT OUTER JOIN hoorcollege_gebruikerhoorcollege gh
                            ON gh.Hoorcollege_idHoorcollege = h.idHoorcollege)
                            LEFT OUTER JOIN hoorcollege_onderwerphoorcollege oh ON h.idHoorcollege = oh.Hoorcollege_idHoorcollege
                            WHERE Gebruiker_idGebruiker ='.$gebruikerID.' AND reedsBekeken = true
                            ORDER BY Onderwerp_Vak_idVak, Onderwerp_idOnderwerp, idHoorcollege');

    while(!$queryReedsBekeken->EOF){
        $vak = $db->Execute('SELECT naam FROM hoorcollege_vak WHERE idVak ='.$queryReedsBekeken->fields["Onderwerp_Vak_idVak"]);
        $onderwerp = $db->Execute('SELECT naam FROM hoorcollege_onderwerp WHERE idOnderwerp ='.$queryReedsBekeken->fields["Onderwerp_idOnderwerp"]);
        $alBekeken["vak"] = $vak->fields["naam"];
        $alBekeken["onderwerp"] = $onderwerp->fields["naam"];
        $alBekeken["idHoorcollege"] = $queryReedsBekeken->fields["idHoorcollege"];
        $alBekeken["naam"] = $queryReedsBekeken->fields["naam"];
        if(heeftGebruikerVragenGemaakt($gebruikerID, $queryReedsBekeken->fields["idHoorcollege"]) || !heeftHoorcollegeVragen($queryReedsBekeken->fields["idHoorcollege"])){
            $alBekeken["gemaakt"] = true;
        }else{
            $alBekeken["gemaakt"] = false;
        }
        $alBekekenTabel[$i] = $alBekeken;
        $i = $i+1;
        $queryReedsBekeken->MoveNext();
    }
    //Tabel nog te bekijken hoorcolleges opvullen (nog aan te passen)
    $TBS->MergeBlock('blk1', $nogTeBekijkenTabel);

    //Tabel reeds bekeken hoorcolleges opvullen (nog aan te passen)
    $TBS->MergeBlock('blk2', $alBekekenTabel);

}else if(!isset ($_SESSION['gebruiker'])){
    header("location: login.php");
}else{
    $config["pagina"] = "./FileUpload/Error1Login.html";
    $TBS->LoadTemplate('./html/template.html') ;
}



$TBS->Show() ;

?>
