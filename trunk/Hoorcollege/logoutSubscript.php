<?php

global $x;
global $path;

if (isset($this)) {
    $TBS =& $this;
} else {
    include_once('./includes/kern.php');
    $TBS = new clsTinyButStrong;
}

if(isset ($_SESSION['gebruiker'])){
    $x = $_SESSION['gebruiker']->getVoornaam().' '.$_SESSION['gebruiker']->getNaam();
    $path = "";
    if(file_exists('./html/logoutSubscript.html')){
            $TBS->LoadTemplate('./html/logoutSubscript.html');
    }
    else{
        $path = "./../";
        $TBS->LoadTemplate('./../html/logoutSubscript.html');
    }
}
$TBS->Show() ;


?>
