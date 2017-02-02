<?php
require('simple_html_dom.php');
$url = $params->arg[2];
$file_export = $params->arg[4];
$xpaths = explode(",",$params->arg[3]);
$result = array();
$export = array();

$dom = new DOMDocument();
@$dom->loadHTMLFile($url);
$x = new DOMXPath($dom);

$i=0;
foreach($xpaths as $xpath)
{
	$nodeList = $x->query($xpath);
	
	foreach ($nodeList as $node) {
		$result[$i][] = utf8_decode(urldecode($node->nodeValue));
	}
	$i++;
}

foreach($result[0] as $key=>$value)
{
	$data = array();
	$j=0;
	foreach($xpaths as $xpath)
	{
		$data[] = $result[$j][$key]; 
		$j++;
	}
	$export[$key] = $data;
}

$data = "";
$i=0;
foreach($export as $val)
{
	if($i>0) { $data.="\r\n"; }
	$i++;
	$j=0;
	foreach($val as $elem)
	{
		if($j>0) { $data.="\t"; }
		$j++;
		$data.=$elem;
	}
	
}

file_put_contents($file_export,$data);

echo $data;