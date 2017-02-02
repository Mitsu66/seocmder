<?php
$time = time();
if($argv[2]=='sf-outlinks')
{
	include('sf2file.php');
	exit;
}

$links_file = $argv[2];
$iteration = 30;
$d = 0.85;
/*
for($i=4;$i<20;$i++)
{
	if(isset($argv[$i]))
	{
		if(preg_match("#^-DumpingFactor=(0\.[0-9]+)$#", trim($argv[$i]), $matches))
		{
			$d = $matches[1];
		}
		if(preg_match("#^-Iterations=([0-9]+)$#", trim($argv[$i]), $matches))
		{
			$iteration = $matches[1];
		}
	}
}
*/
$d = $params->variable["DumpingFactor"];
$iteration = $params->variable["Iterations"];

echo "Iterations : $iteration\r\nDumpingFactor : $d";
echo "\r\n";

$pages = array();
$backlinks = array();

$data = file_get_contents($links_file);
$lines = explode("\n",$data);

foreach($lines as $line)
{
	$line = str_replace('"','',$line);
	$col = explode("\t",$line);
	if(isset($col[2])){
		$col[0] = trim($col[0]);
		$col[1] = trim($col[1]);
		$col[2] = trim($col[2]);

		if(!isset($backlinks[$col[0]])) { $backlinks[$col[0]] = array();}
		if(!isset($backlinks[$col[1]])) { $backlinks[$col[1]] = array();}

		if($col[2] == "DF") { $backlinks[$col[1]][] = $col[0]; }

		if(!isset($pages[$col[1]]))
		{
			$pages[$col[1]]["url"] = $col[1];
			$pages[$col[1]]["links"]=0;
		}

		if(isset($pages[$col[0]]))
		{
			$pages[$col[0]]["links"]++;
			if($col[2] == "T") { $pages[$col[0]]["links"] = 0; }
		}
		else {
			$pages[$col[0]]["url"] = $col[0];
			$pages[$col[0]]["links"] = 1;
			if($col[2] == "T") { $pages[$col[0]]["links"] = 0; }
		}
	}
}

$pages_count = count($pages);
$bonus = (1-$d)/$pages_count;

$pages_n = $pages;

foreach($pages as $url=>$val)
{
	$pages[$url]["PR"] = (1/$pages_count);
}

function check_bonus(){

	global $pages; 
	global $pages_count;
 	global $pages;
 	global $d;

 	$bonus = 0;

	foreach($pages as $url => $page)
	{
		if($page["links"] == 0) {  $bonus += $pages[$url]["PR"]; }
	}
	$bonus = $d*($bonus/$pages_count);

	return $bonus;


}

function pagerank($iteration){
	global $pages_n;
	global $pages;
	global $pages_count;
	global $bonus;
	global $backlinks;
	global $d;
	$b2 = check_bonus();
	$pagenumber=0;
	foreach($pages as $page)
	{
		$pagenumber++;
		$pr=0;
		if(isset($backlinks[$page["url"]]))
		{
			foreach($backlinks[$page["url"]] as $bl)
			{
				$pr += ($pages[$bl]["PR"]/$pages[$bl]["links"]);
			}
		}
		$pr = $pr*$d+$bonus;
		$pr += $b2;

		$pages_n[$page["url"]]["PR"] = $pr;

	}
	$pages = $pages_n;
}

function check_redirect()
{
	global $lines;
	global $pages;

	foreach($lines as $line)
	{
		$col = explode("\t",$line);
		$col[0] = trim($col[0]);
		$col[1] = trim($col[1]);
		$col[2] = trim($col[2]);

		if($col[2] == "T")
		{
			$pages[$col[1]]["PR"] += $pages[$col[0]]["PR"];
			unset($pages[$col[0]]);
		}
	}
}

$export = "";
function display($val){
	echo "ECRITURE DES PRI \r\n";
	global $export;
	$total = 0;

	foreach($val as $data)
	{
		$total += $data["PR"];
		$export.=$data["url"]."\t".sprintf('%f', $data["PR"])."\r\n";
	}
	$perte = 1-$total;
	echo "\r\n\r\nTotal : ".$total;
	echo "\r\nPerte : ".$perte;

}

for($i=0;$i<$iteration;$i++) { echo "IT : ".($i+1)."\r\n"; pagerank($i); }
check_redirect();
display($pages);
file_put_contents($argv[3],$export);

echo "fait en ".(time()-$time)." secondes";