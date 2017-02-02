<?php

if(trim($params->arg[2]) == "multiple-count")
{
	include("stemmer_multiple_count.php");
	exit;
}

require('French.php');
require('scrapp.php');
$urls = array();

$url = scrappe($params->arg[2]);
$str = "";


$str.= implode(" ",$url);
$stemmer = new French();

$words = cleaner($str);
$nstr =array();
foreach ($words as $word)
{
    if(strlen($word)>2){
        $nstr[] = $stemmer->stem($word);
    }
}

$export = "Lemme\tOccurences";

$nstr = array_count_values($nstr);

echo "Lemmes : ".count($nstr);
echo "\r\n";
echo "\r\n";
foreach($nstr as $word=>$val)
{
    echo $word."($val) \r\n";
    $export.="\r\n".$word."\t".$val;
}

file_put_contents($params->arg[3], $export);