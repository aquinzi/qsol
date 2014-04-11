<!DOCTYPE html>
<html>
<meta charset="utf-8">
<body>

<?php 

include "src/config.php";

$json_file = "search_json/content.json";
$str_data = file_get_contents($json_file);
$jsoned = json_decode($str_data, true); // to array

printListing($jsoned);

?>


</body>
</html>