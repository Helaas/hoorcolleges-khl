<?php

global $x;

if (isset($this)) {
    $TBS =& $this;
} else {
    include_once('./includes/kern.php');
    $TBS = new clsTinyButStrong;
}

if(isset ($_SESSION['gebruiker'])){
    $x = $_SESSION['gebruiker']->getVoornaam().' '.$_SESSION['gebruiker']->getNaam();
    $TBS->LoadTemplate('./html/logoutSubscript.html');
}
$TBS->Show() ;


?>
