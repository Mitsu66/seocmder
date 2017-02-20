<?php

require('simple_html_dom.php');

function scrapp($url)
{

	$curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Chrome/5.0.375.125 Safari/533.4");
    $str = curl_exec($curl);
    curl_close($curl);
	
	return $str;
	
}
$url = $params->arg[2];
$output = $params->arg[3];
$str = scrapp($url);
$sitemap = array();
$urls = array();

function get_urls($str)
{
	global $sitemap;
	global $urls;
	$str = str_replace("\r\n"," ",$str);
	preg_match_all("#<loc>([^<]+)</loc>#",$str,$matches);

	if(strstr(strtolower($str),"<sitemap>"))
	{
		foreach($matches[1] as $url) { $sitemap[] = $url; }
		foreach($sitemap as $url) { 
			$content = scrapp($url);
			get_urls($content); 
		}
	} else 
	{
		foreach($matches[1] as $url) { $urls[] = trim($url); }
	}
}

get_urls($str);
$data = implode("\r\n",$urls);
file_put_contents($output,$data);