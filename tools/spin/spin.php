<?php

$spun = array();

$spin = file_get_contents($params->arg[2]);
 
 function test( $item1, $item2) {
	similar_text($item1,$item2,$similartext);
	$item1 = str_replace(","," ",$item1);
	$item1 = str_replace("."," ",$item1);
	$item1 = str_replace("'"," ",$item1);
	$item1 = str_replace(":"," ",$item1);
	$item1 = str_replace("‘"," ",$item1);
	$item2 = str_replace(","," ",$item2);
	$item2 = str_replace("."," ",$item2);
	$item2 = str_replace("'"," ",$item2);
	$item2 = str_replace(":"," ",$item2);
	$item2 = str_replace("‘"," ",$item2);
	$separator =" ";
	$item1 = explode( $separator, $item1 );
	$item2 = explode( $separator, $item2 );
	$arr_intersection = array_intersect( $item1, $item2 );
	$arr_union = array_merge( $item1, $item2 );
	$coefficient = count( $arr_intersection ) / count( $arr_union );
	return ($coefficient*100)*2;
}


	function spin($str)
	{
		$pattern = '#\{([^{}]*)\}#msi';
		$test = preg_match_all($pattern, $str, $out);
		if(!$test)
		return $str;

		$find = array();
		$replace = array();

		foreach($out[0] as $id => $match)
		{
			$select = explode("|", $out[1][$id]);
			$find[] = $match;
			$replace[] = $select[rand(0, count($select)-1)];
		}
		$reponse = str_replace($find, $replace, $str);
		$reponse = str_replace("\r\n", " ", $reponse);
		$reponse = str_replace("  ", " ", $reponse);
		$reponse = str_replace("  ", " ", $reponse);

		return spin($reponse);
	}	
	
	$tries = 0;
	$limit = 65;
	$quantity = 0;
	if(isset($params->variable["limit"])) $limit = $params->variable["limit"];
	if(isset($params->variable["quantity"])) $quantity = $params->variable["quantity"];
	echo "LIMIT = ".$limit."\r\n";
	echo "Quantity = ".$quantity."\r\n";

	$try = 100;
	$stop = 100;		
	function spining($spin)
	{
		global $quantity;
		global $try;
		global $tries;
		global $spun;
		global $limit;
		global $stop;
		
		
		$text = spin($spin);
		if(count($spun)==0)
		{
			$spun[] = $text;
			spining($spin);
		}
		else 
		{
			if($quantity!=0)
			{
				if(count($spun)<$quantity ) { $spun[] = $text; spining($spin); }
				
			} else {
				
				$valid = true;
				foreach($spun as $compare)
				{
					if(test($text,$compare)>$limit)
					{
						$valid = false;
						$tries++;
						if($tries<$try)
						{
							spining($spin);
						}
						
					}
				}
				if($valid==true)
				{
					$spun[] = $text;
					if(count($spun)==$stop)
					{
						var_dump($spun); exit;
					}
					$tries=0;
					spining($spin);
				}
				
			}
		}
		
	}
	
	spining($spin);
	file_put_contents($params->arg[3],implode("\r\n",$spun));
	echo count($spun)." spin";

?>