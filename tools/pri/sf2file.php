<?php
$export = '';

$file = $argv[3];
if(!file_exists($file)) { echo "$file doesn't exist"; exit;}
if(!isset($argv[4])) { echo "You have to define output file"; exit;}
$file_export = $argv[4];


function extract_dom($url){
	$dom = str_replace("http://","",$url);
	$dom = str_replace("https://","",$dom);
	$dom = explode("/",$dom);
	$dom=$dom[0];
	return $dom;
}

$handle = fopen($file, 'r');
/*Si on a réussi à ouvrir le fichier*/
if ($handle)
{
	/*Tant que l'on est pas à la fin du fichier*/
	$i=0;
	while (!feof($handle))
	{
		/*On lit la ligne courante*/
		$line = fgets($handle);
		/*On l'affiche*/
		$data = explode(",",$line);

		if($data[0]=='"HREF"') { 
			if($i>0) { $export.="\r\n"; }
			$i++;
			if($data[7] =='"true"') { $type = "NF"; } else { $type = "DF"; }
			$src = str_replace('"','',$data[1]);
			$target = str_replace('"','',$data[2]);
			$srcdom = extract_dom($src);
			$targetdom = extract_dom($target);
			if($targetdom != $srcdom)
			{
				$target = 'EXTERNAL';
			}
			
			$export.=$src."\t".$target."\t".$type;
		}
		
	}
	/*On ferme le fichier*/
	fclose($handle);
}
file_put_contents($file_export,$export);