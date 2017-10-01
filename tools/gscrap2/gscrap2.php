<?php

import("simple_html_dom");

$ext = "fr";
if(isset($params->variable["ext"])) $ext= $params->variable["ext"];

$keyword = $params->arg[2];
$keywords = file_get_contents($keyword);
$keywords = explode("\r\n",$keywords);
$output_content = "";



function scrapp($keyword,$ext)
{
	$url="https://www.google.".$ext."/search?q=".str_replace(" ","+",$keyword);
	$curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Chrome/5.0.375.125 Safari/533.4");
    $str = curl_exec($curl);
    curl_close($curl);
	//sleep(1);
	return $str;
}
$k=0;
foreach($keywords as $keyword){
$k++;
$output = $params->arg[3];

echo $k."\t- ".$keyword."\r\n";
$str =  scrapp($keyword,$ext);

// Create a DOM object
    $dom = new simple_html_dom();
    // Load HTML from a string
    $dom->load($str);

	$links = $dom->find('h3 a');

	$get_links = array();

	foreach($links as $link)
	{
		$dom = str_replace("https://","",$link->href);
		$dom = str_replace("http://","",$dom);
		$dom = explode("/",$dom);
		$dom = $dom[0];

		$get_links[] = (object) array(
			"dom" => $dom,
			"href" => $link->href,
		);
	}
$i=0;
foreach($get_links as $data)
{
	$i++;
	if($i==1) $output_content .= $keyword."\t".$data->href."\r\n";
}
}
file_put_contents($output,$output_content);
echo "Done.";
