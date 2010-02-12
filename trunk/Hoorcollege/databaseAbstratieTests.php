<?php
    include_once('./includes/kern.php');

    $resultaat = $db->Execute("select * from hoorcollege_gebruiker");
    while (!$resultaat->EOF) {
        print_r($resultaat->fields);
    $resultaat->MoveNext();
}


?>
