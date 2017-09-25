<?php

require('simple_html_dom.php');

function scrappe($url)
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

    //$str = file_get_contents($url);

    // Create a DOM object
    $dom = new simple_html_dom();
    // Load HTML from a string
    $test = $dom->load($str);
	$text = $test->plaintext; 
	
	$text = str_replace("’"," ",$text);
	$text = str_replace(","," ",$text);
	$text = str_replace("!"," ",$text);
	$text = str_replace("-"," ",$text);
	$text = str_replace("'"," ",$text);
	$text = str_replace('"'," ",$text);
	$text = str_replace('#'," ",$text);
	$text = str_replace('-'," ",$text);
	$text = str_replace('/'," ",$text);
	$text = str_replace('.'," ",$text);
	$text = str_replace(')'," ",$text);
	$text = str_replace('('," ",$text);
	$text = str_replace('='," ",$text);
	
	$words = explode(" ",$text);
	$return = array();
	
	foreach($words as $word)
	{
		if(!empty($word))
		{
			$return[]=$word;
		}
	}
	return $return;
	
}