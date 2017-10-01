<?php
import("simple_html_dom");

$ext = "fr";
if(isset($params->variable["ext"])) $ext= $params->variable["ext"];

$keyword = $params->arg[2];
$output = $params->arg[3];


function scrapp($keyword,$ext)
{
	$url="https://www.google.".$ext."/search?q=".str_replace(" ","+",$keyword)."&num=100";
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
$output_content = "pos\tdom\turl";
$i=0;
foreach($get_links as $data)
{
	$i++;
	$output_content .= "\r\n#".$i."\t".$data->dom."\t".$data->href;
}
file_put_contents($output,$output_content);
echo "Done.";
