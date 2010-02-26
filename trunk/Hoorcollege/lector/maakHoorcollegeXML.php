<?php
header("Content-type: text/xml");

require("./../includes/kern.php");
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
echo "</root>";
?>
