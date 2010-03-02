<?php
    require("./../includes/kern.php");
    arrTest();

    $nieuweID = 14;

    $config["pagina"] = "./lector/activeerMCOK.html";
    $TBS->LoadTemplate('./../html/lector/templateLector.html');

    $TBS->Show();
?>
