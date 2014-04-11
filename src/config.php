<?php 

$json_file = "content.json";


/*
Does convertion to match $name to the (html) filename

$name str 
 */
function linkFilename($name){
	return str_replace(" ", "_", strtolower($name));
}

/*
Prints the list from $data as unordered list with info in dl

$data arr: array as:
	Array ( 
	    [Nombre] => Array ( 
	       [difficulty] => Muy Facil 
	       [decks] => 1 
	       )
	    ...
$extraName str: extra info to put next to the name

 */
function printListing($data, $extraName=""){
	echo "<ul>";
	foreach ($data as $solitaire => $info) {
	  echo "<li><a href='html/".linkFilename($solitaire).".html'>".$solitaire."</a> <span>$extraName</span>";
	  echo "<dl>";
	  echo "<dt>Mazos </dt><dd>$info[decks]</dd>";
	  echo "<dt>Dificultad </dt><dd>$info[difficulty]</dd>";

	  echo "</dl>";
	}
	echo "</ul>";
}

/*
	Search if $thisval is between $min and $max (including those numbers)
 */
function in_range($thisval, $min, $max){
	if ($thisval >= $min && $thisval <= $max) return true;
	return false;
}



?>