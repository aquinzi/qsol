<?php

$dir    = 'src/solitaires';
$files1 = scandir($dir);

foreach ($files1 as $key => $value) {
    if ($value != "." && $value != ".."){
        echo "$value";
        echo "<hr>";
    }
}

?>
