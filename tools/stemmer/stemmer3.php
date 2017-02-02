<?php
require('French.php');
require('scrapp.php');
$urls = array();

$urls = scrappe($argv[2]);
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
$data = $str."\r\n";

$data.= implode(",",$nstr);
file_put_contents("test.txt",$data);

$nstr = array_unique($nstr);

echo count($nstr).",";





//var_dump($test);