<?php
import("simple_html_dom");

$url = $params->arg[2];
$urls = file_get_contents($url);
$urls = explode("\r\n",$urls);
$html = "result";
foreach($urls as $url)
{
$html.="\r\n".$url;
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
	echo $xpath; exit;
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
	if($i>0) { $html.="\t"; }
	$i++;
	$j=0;
	foreach($val as $elem)
	{
		if($j>0) { $html.="\t"; }
		$j++;
		$html.=$elem;
	}

}


//echo $data;

}

file_put_contents($file_export,$html);
