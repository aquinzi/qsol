<?php 
/*
MySQL to YAML. Individual yaml files for each row.
Only one table.Very hardcoded.
*/

$DBHOST = 'localhost';
$DBUSER = '';
$DBPASS = '';
$DBNAME = '';
$TABLE = '';
$SAVETO = 'solitaires_yaml'; //must exist

$db = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);

if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}

if(!$result = $db->query("SELECT * from $TABLE")){
    die('There was an error running the query [' . $db->error . ']');
}

while($row = $result->fetch_assoc()){
	// clean/order rows
	$reorg = array();
	$reorg['caracs']['redeals']   = "";
	$reorg['caracs']['type']      = "";
	$reorg['caracs']['game_time'] = "";
	$reorg['aka'] = "";

	foreach ($row as $key => $value) {
		switch ($key) {
			case 'nombre':
				$reorg['name'] = $value;
				break;
			case 'caracs_decks':
				$reorg['caracs']['decks'] = $value;
				break;
			case 'caracs_dificultad':
				$reorg['caracs']['difficulty'] = $value;
				break;
			case 'caracs_habilidad':
				$reorg['caracs']['skill'] = $value;
				break;
			case 'objetivo':
				$reorg['object'] = $value;
				break;
			case 'setup_fundations':
				$reorg['setup']['fundations'] = $value;
				break;
			case 'setup_tableau':
				$reorg['setup']['tableau'] = $value;
				break;
			case 'setup_reserve':
				$reorg['setup']['reserve'] = $value;
				break;
			case 'setup_deck':
				$reorg['setup']['deck'] = $value;
				break;
			case 'setup_imglay':
				$reorg['setup']['imglay'] = $value;
				break;
			case 'similares':
				$reorg['similar'] = $value;
				break;
			case 'juego':
				$reorg['object'] .= ". " .$value;
				break;
		}
	}

	// now print the yaml
	$thisorder = array('name', 'aka', array('type', 'game_time','decks', 'difficulty', 
		'skill', 'redeals'), 'object', array('fundations', 'tableau', 'reserve', 
		'deck', 'imglay'), 'similar');
	
	ob_start();
	echo "---\n";
	$subarrays = 0; //1 = caracs, 2 = setup
	foreach ($thisorder as $order) {
		if (!is_array($order)){
			if ($order != 'similar' && $order != 'object'){
				echo "$order: $reorg[$order]\n";
			} 
			else{
				if ($order == 'object'){
					echo "$order: \"$reorg[$order]\"\n";
				}
				if ($order == 'similar'){
					// break it into a list
						$tmp_list = explode(", ", $reorg[$order]);
	
					echo "similars: \n";
					foreach ($tmp_list as $key => $value) {
						echo "  - $value \n";
					}
				}
			}
		}
		else{
			$subarrays += 1;
			$subtitle = "";
			if ($subarrays == 1){ $subtitle = "caracs"; }
			if ($subarrays == 2){ $subtitle = "setup"; }
			echo "$subtitle : \n";
			foreach ($order as $subkey => $subvalue) {
				if (($subarrays != 2) or ($subarrays == 2 and $subvalue == "imglay")){
					echo "  {$subvalue}: {$reorg[$subtitle][$subvalue]}\n";
				}
				else{
					//in case it has line breaks
					echo "  {$subvalue}: |\n";
					echo "    \"{$reorg[$subtitle][$subvalue]}\"\n";
				}
			}
		}
	}
	$filename = strtolower($reorg['name']);
	$filename = str_replace(' ', '_', $filename);
	$content = iconv("CP1257","UTF-8", ob_get_contents());
	file_put_contents($SAVETO.'/'.$filename.'.yaml', $content);
	ob_end_flush();

}
///echo "...\n"; //not nec if per row per file
?>