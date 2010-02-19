<?php

if (isset($this)) {
    $TBS =& $this;
} else {
    include_once('./includes/kern.php');
    $TBS = new clsTinyButStrong;
}

if(!isset ($_SESSION['gebruiker'])){
    $TBS->LoadTemplate('loginSubscript.html') ;
}
$TBS->Show() ; 

?>
