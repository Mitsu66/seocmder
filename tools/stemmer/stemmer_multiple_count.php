<?php
require('French.php');
require('scrapp.php');
$urls = array();

$urls_file = $params->arg[3];
$export_file = $params->arg[4];
$urls_tab = explode("\r\n",file_get_contents($urls_file));
$i = 0; 
foreach($urls_tab as $url)
{
	$i++;
	$urls[] = scrappe($url);
	echo "SCRAPP : $i\r\n";
}

$str = "";
$count = "url\tlemmes";
$i = 0;
foreach($urls as $url)
{
	$i++;
	$str.= implode(" ",$url);
	$stemmer = new French();

	$words = cleaner($str);
	$words = array_unique($words);
	
	$nstr =array();
	foreach ($words as $word)
	{
		if(strlen($word)>2){
			$nstr[] = $stemmer->stem($word);
		}
	}
	$data = $str."\r\n";
	

	$nstr = array_unique($nstr);
	$count .= "\r\n";
	$count .= $i."\t".count($nstr);
	
	$str2 = explode(" ",$str);
	$str2 = array_unique($str2);
	$str = implode(" ",$str2);
	
	echo "TRAITEE : $i\r\n";

}
file_put_contents($export_file,$count);