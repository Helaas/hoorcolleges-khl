<?php

include_once('./includes/kern.php');
session_start();

$TBS = new clsTinyButStrong;

$config["pagina"] = 'admin/excelfileInvoegen.html';
$TBS->LoadTemplate('./html/admin/templateAdmin.html');

$data = array();

function add_data( $groep, $gebruikerId) {
    global $data;
    $data []= array('groep' => $groep,'gebruikerId' => $gebruikerId);
}

/*
 * Wanneer er een file wordt toegevoegd
*/
if (isset($_POST["fileUploaden"])) {
    if ($_FILES['file']['tmp_name'] ) {
        $dom = DOMDocument::load( $_FILES['file']['tmp_name'] );
        $rows = $dom->getElementsByTagName('Row');
        $first_row = true;  //Zodat eerste row als header wordt gezien
        foreach ($rows as $row) {
            if ( !$first_row ) {
                $groep = "";
                $gebruikerId = "";
                $index = 1;
                $cells = $row->getElementsByTagName( 'Cell' );
                foreach( $cells as $cell ) {
                    $ind = $cell->getAttribute( 'ss:Index' );
                    if ( $ind != null ) $index = $ind;
                    if ( $index == 1 ) $groep = $cell->nodeValue;
                    if ( $index == 2 ) $gebruikerId = $cell->nodeValue;
                    $index += 1;
                }
                add_data( $groep, $gebruikerId);
            }
            $first_row = false;
        }
        /*
     * Voor elke rij in de file
        */
        foreach( $data as $row ) {
            $gebruikerId = $row['gebruikerId'];
            if(validateNumber($gebruikerId) && strlen($row['groep']) < 8) {
                if(!bestaatGroep($row['groep'])) {
                    voegGroepToe($groep);
                }
                $groepId = getGroepId($row['groep']);
                //Ervoor zorgen dat een student geen 2keer aan dezelfde groep wordt toegevoegd
                if(!isStudentToegekentAanGroep2($gebruikerId, $groepId)) {
                    kenStudentToeAanGroep($gebruikerId, $groepId);
                }
            }
        }
    }
}
$TBS->Show() ;

?>
