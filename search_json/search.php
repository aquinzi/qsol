<?php

include "../src/config.php";


// add dummy so we dont have problems with the 0 == none 
// for cool search, we remove the spaces
$difficulty_levels_en = array('dummy', 'very easy', 'easy', 'medium', 'hard', 'very hard');
$difficulty_levels_es = array('dummy', 'muy facil', 'facil', 'medio', 'dificil', 'muy dificil');



/*
  edit json array. 
  ex: "translate" difficulty levels (words) to numbers
 */
function edit_json($json){
	$tmp_json = $json;
	foreach ($tmp_json as $name => $value) {
		$tmp_json[$name]['difficulty'] = change_difficulty($value['difficulty']);
	}

	return $tmp_json;
}

/*
  Changes level from word to number and viceversa.
  $level: word or integer 
  It switches automatically (like a toggle)
 */
function change_difficulty($level, $lang='en'){
	//defaults to english
	//TODO remember multilanguage
	global $difficulty_levels_en, $difficulty_levels_es;

	if (is_string($level)){
		$level = strtolower($level);
		if ($lang='es') return array_search($level, $difficulty_levels_es);
		return array_search($level, $difficulty_levels_en);
	}
	else{
		if ($lang='es') return $difficulty_levels_es[$level];
		return $difficulty_levels_en[$level];
	}
}

/*
  Determines the kind of search, if simple (searching title) or "cool"/"advanced" one.
  Returns true for the "cool" search; False for title search.

  example cool search
  d:1-2  r:easy t:klondike     (also without : for d or r (if numbers))
  where d -> deck. One number or range
        r -> difficulty. one word or range. Could be number. 1 very easy, 2 easy...
        t -> type. one word. Prob accept abbr for common ones.
 */
function is_cool_search($query){
	if (stripos($query, ":") !== false) return true;
	if (preg_match("/(d|r)\d(-\d)?/", $query)) return true;

	return false;
}

function find_title($thisTitle){
	global $jsoned;

	$tmpBox        = array();
	$tmpBoxSimilar = array();

	foreach ($jsoned as $key => $value) {
		// search in titles
		if (stripos($key, $thisTitle) !== false) {
			$tmpBox[$key] = $value;
		}
		else{
			// search in similars
			if (!isset($value['similars'])){continue; }
			$tmp_similars = explode(",", $value['similars']);

			if (in_array($thisTitle, $tmp_similars)){
				$tmpBoxSimilar[$key] = $value;
			}
		}
	}

	if(count($tmpBox) == 0 && count($tmpBoxSimilar) == 0) {echo "nothing found";}
	else {
		printListing($tmpBox);
		printListing($tmpBoxSimilar, $extraName=" (similar)");
	}

}

function find_advanced($queryValues){
	global $jsoned;

	$tmpBox = array();

	foreach ($jsoned as $name => $info) {

		foreach ($queryValues as $key => $value) {
			if (!isset($value) or $value === false) continue; 

			if (!is_array($value) || (count($value) == 1)){
				//normal
				
				// pasamos el array de 1 a variable normal
				if (is_array($value)) $value = $value[0];
				
				if ($info[$key] == $value) $tmpBox[$name] = $info;
				else break;
			}
			else{
				//ranges
				if (in_range($info[$key], $value[0], $value[1])){
					$tmpBox[$name] = $info;
				}
				else break;
			}
		}
	}

	if (count($tmpBox) == 0){echo "nothing found";}
	else printListing($tmpBox);

}


$searchme = array();
$searchme['title'] = false; // str
$searchme['decks'] = false; // or array(min, max)
$searchme['difficulty'] = false; // or array(min, max)
$searchme['type'] = false; // str 
// all false = all


// Search config

$whatsearch = "" ;
if (isset($_GET["searchthis"])) $whatsearch = $_GET["searchthis"];

if ($whatsearch == ""){
	// advanced search form
	
	// decks
	$tmp1 = $_GET["dm"];
	$tmp2 = $_GET["dx"];

	switch ($tmp1) {
		case ($tmp1 == 1 && $tmp2 == 4):
			$searchme['decks'] = false;
			break;
		case ($tmp1 == $tmp2):
			$searchme['decks'] = array($tmp1);
			break;		
		default:
			$searchme['decks'] = array($tmp1, $tmp2);
			break;
	}

	// difficulty
	$tmp1 = $_GET["rm"];
	$tmp2 = $_GET["rx"];

	switch ($tmp1) {
		case ($tmp1 == 'all'):
			$searchme['difficulty'] = false;
			break;
		case ($tmp2 == 'none'):
		case ($tmp1 == $tmp2):
			$searchme['difficulty'] = array($tmp1);
			break;
		default:
			$searchme['difficulty'] = array($tmp1, $tmp2);
			break;
	}

	// type
	$tmp1 = $_GET["t"];
	if ($tmp1 == 'all') $searchme['type'] = false;
	else $searchme['type'] = $_GET["t"];
}
else{
	// advanced search 
	
	if (!is_cool_search($whatsearch)){
		$searchme['title'] = $whatsearch;
	}
	else {
		// get pieces
		$query_pieces = explode(" ", $whatsearch);

		foreach ($query_pieces as $key => $value) {

			$subpiece       = substr($value, 0, 1);
			$subpiece_value = "";

			if (substr($value, 1, 1) == ":") $subpiece_value = substr($value, 2);
			else $subpiece_value = substr($value, 1);

			$subpiece_value = explode("-", $subpiece_value);

			switch ($subpiece) {
				case 'd':
					foreach ($subpiece_value as $index => $number) {
						if (!in_range($number, 1, 4)){
							if ($index == 0) $subpiece_value[0] = 1;
							if ($index == 1) $subpiece_value[1] = 4;
						}
					}
					$searchme['decks'] = $subpiece_value;
					break;

				case 'r':
					foreach ($subpiece_value as $index => $number) {
						if (is_numeric($number)){
							if (!in_range($number, 1, 5)){
								if ($index == 0) $subpiece_value[0] = 1;
								if ($index == 1) $subpiece_value[1] = 5;
							}
						}
						else{
							// remove spaces
							$tmp = $difficulty_levels_es;
							foreach ($tmp as $tkey => $tvalue) {
								$tvalue = str_replace(" ", "", $tvalue);
							}

							$value = (in_array($value, $tmp)) ? change_difficulty($value) : false;
						}
					}

					$searchme['difficulty'] = $subpiece_value;
					break;

				case 't':
					$searchme['type'] = $subpiece_value[0];
					break;
			}
		}
	}
}

$str_data = file_get_contents($json_file);
$jsoned = json_decode($str_data, true); // to array
$jsoned = edit_json($jsoned);

// do the searching 
if ($searchme['title']) find_title($searchme['title']);
else find_advanced($searchme);


?>