<?php

$output = array();
$returnvar;
if(function_exists('exec')) {
    system("echo \"it works\""); 
    exec('/usr/bin/ffmpeg -i /home/birgit/ffmpeg/borstvergroting.wmv -ar 22050 -ab 32 -f flv -s 320x240 /home/birgit/ffmpeg/video.flv',$output,$returnval);
    echo $returnvar . "</br>";
    print_r($output);
    system("echo \"it works\"");
}

?>
