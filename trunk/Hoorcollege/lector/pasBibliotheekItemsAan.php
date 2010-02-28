<?php
    require_once('./../includes/kern.php');
    session_start();
    $config["pagina"] = "./lector/pasBibliotheekItemsAan.html";
    $TBS->LoadTemplate('./../html/lector/templateLector.html');
    $TBS->Show();

?>
