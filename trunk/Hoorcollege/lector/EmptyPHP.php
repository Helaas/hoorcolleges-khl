<?php
    require("./../includes/kern.php");

    $nieuweID = 14;

    $config["pagina"] = "./lector/hoorcollegeGemaakt.html";
    $TBS->LoadTemplate('./../html/lector/templateLector.html');

    $TBS->Show();
?>