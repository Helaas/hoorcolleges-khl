<?php
    include_once('./includes/kern.php');
    $config["pagina"] = "SqlenTBSVoorbeeld.html";

    $q = "select * from hoorcollege_gebruiker";
    $TBS->LoadTemplate('./html/template.html');
    $TBS->MergeBlock("blk1",$db,$q); //$db wordt in kern . php gemaakt
    $TBS->Show();
?>
