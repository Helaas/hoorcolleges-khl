<?php
require("./../includes/kern.php");

if (isset($_GET['f']) &&  $_GET['f'] == "bibitem" && isset($_GET['bibid'])){
    header("Content-type: text");
    die(getBibliotheekitemNaam($_GET['bibid']));
}


header("Content-type: text/xml");
echo "<?xml version=\"1.0\"?>";
echo "<root>";

if(isset($_GET['f']) &&  $_GET['f'] == "dropdown" && isset($_GET['id'])){
    $items = getGroepenVoorvak($_GET['id']);
    foreach ($items as $sleutel => $waarde) {
        echo "<vak>";
        echo "<id>".$sleutel."</id>";
        echo "<naam>". $waarde."</naam>";
        echo "</vak>";
    }

}

if(isset($_GET['f']) &&  $_GET['f'] == "studenten" && isset($_GET['vakid'])&& isset($_GET['groepid'])){
    $items = getStudentenVoorvak($_GET['vakid'], $_GET['groepid']);
    foreach ($items as $sleutel => $waarde) {
        echo "<student>";
        echo "<id>".$sleutel."</id>";
        echo "<naam>". $waarde."</naam>";
        echo "</student>";
    }

}

if(isset($_GET['f']) &&  $_GET['f'] == "studentenAlles" && isset($_GET['vakid'])){
    $items = getStudentenVoorvakAlles($_GET['vakid']);
    foreach ($items as $sleutel => $waarde) {
        echo "<student>";
        echo "<id>".$sleutel."</id>";
        echo "<naam>". $waarde."</naam>";
        echo "</student>";
    }

}

if(isset($_GET['f']) &&  $_GET['f'] == "studentenZonderGroep" && isset($_GET['vakid'])){
    $items = getStudentenZonderGroep($_GET['vakid']);
    foreach ($items as $sleutel => $waarde) {
        echo "<student>";
        echo "<id>".$sleutel."</id>";
        echo "<naam>". $waarde."</naam>";
        echo "</student>";
    }

}

if(isset($_GET['f']) &&  $_GET['f'] == "studentenVanIds" && isset($_GET['studenten'])){
    $ids = explode(',', $_GET['studenten']);
    $items = getStudenten($ids);
    foreach ($items as $sleutel => $waarde) {
        echo "<student>";
        echo "<id>".$sleutel."</id>";
        echo "<naam>". $waarde."</naam>";
        echo "</student>";
    }

}

echo "</root>";
?>
