<?php
    include_once('./includes/kern.php');

 //if (heeftHoorcollegeVragen(1))

    $config["pagina"] = "hoorcollegeVragenBeantwoorden.html";

    $vragen = array();
     $resultaat = $db->Execute('SELECT * FROM hoorcollege_vraag');
     while (!$resultaat->EOF) {
        $vragen[$resultaat->fields["idVraag"]]["vraagstelling"] =  $resultaat->fields["vraagstelling"];
        $vragen[$resultaat->fields["idVraag"]]["id"] =  $resultaat->fields["idVraag"];
        $resultaat->MoveNext();
    }

     $resultaat = $db->Execute('SELECT * FROM hoorcollege_mogelijkantwoord');
     while (!$resultaat->EOF) {
        $vragen[$resultaat->fields["Vraag_idVraag"]]["mogelijkantwoorden"][] = array ("antwoord" => $resultaat->fields["antwoord"],
           "id" => $resultaat->fields["idMogelijkAntwoord"] );
        $resultaat->MoveNext();
    }

/**
    $vragen[1]["mogelijkantwoorden"][] = "mogelijkantwoord1";
    $vragen[1]["mogelijkantwoorden"][] = "mogelijkantwoord2";

        $vragen[2]["mogelijkantwoorden"][] = "mogelijkantwoord1";
    $vragen[2]["mogelijkantwoorden"][] = "mogelijkantwoord2";**/
   ?>
    <pre>
<?php print_r($vragen); ?>
    </pre>
<?php
    $TBS->LoadTemplate('./html/template.html');
    $TBS->MergeBlock("blk1",$vragen);
    $TBS->Show();

 //hoorcollegeVragenBeantwoorden

?>
