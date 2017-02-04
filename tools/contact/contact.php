<?php
require('simple_html_dom.php');
require('Whois/Whois.php');

$url = $argv[2];

$dom = str_replace("http://","",$url);
$dom = str_replace("https://","",$dom);
$dom = explode("/",$dom);
$dom = $dom[0];

$protocol = explode(":",$url);
$protocol = $protocol[0];
$protocol = $protocol."://";

function get_links($url)
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


    // Create a DOM object
    $dom = new simple_html_dom();
    // Load HTML from a string
    $dom->load($str);

	$links = $dom->find('a');
	$get_links = array();
	foreach($links as $link)
	{
		$get_links[] = (object) array(
			"anchor" => $link->plaintext,
			"href" => $link->href
		);
	}
	return $get_links;
	
}

function get_form_contact($links){
	global $dom;
	global $protocol;
	
	$contact = "";
	foreach($links as $link)
	{
		if(strstr(strtolower($link->anchor),"contact"))
		{
			if(substr($link->href,0,4) != "http"){
				if(substr($link->href,0,1)!="/")
				{
					$dom_fix = $dom."/";
				} else {
					$dom_fix = $dom;
				}
				$contact = $protocol.$dom_fix.$link->href;
			} else {
				$contact = $link->href;
			}
			break;
		}
	}
	
	if($contact == "")
	{
		foreach($links as $link)
		{
			if(strstr(strtolower($link->anchor),"devis"))
			{
				if(substr($link->href,0,4) != "http"){
					if(substr($link->href,0,1)!="/")
					{
						$dom_fix = $dom."/";
					} else {
						$dom_fix = $dom;
					}
					$contact = $protocol.$dom_fix.$link->href;
				} else {
					$contact = $link->href;
				}
				break;
			}
		}
	}
	return $contact;
	
}

function get_emails_whois($dom)
{
	
	$dom = str_replace("www.","",$dom);
	$domain = new Whois($dom);
	$whois_answer = $domain->info();
	
	$emails = array();
	if(false !== preg_match_all('`\w(?:[-_.]?\w)*@\w(?:[-_.]?\w)*\.(?:[a-z]{2,4})`', $whois_answer, $aEmails)) {
	if(is_array($aEmails[0]) && sizeof($aEmails[0])>0) {
		
		$return_mail = array_unique($aEmails[0]);
		$emails_final = array();
		foreach($return_mail as $email)
		{
			if(!strstr($email,"1and1") && !strstr($email,"ovh") && !strstr($email,"abuse") && !strstr($email,"host") && !strstr($email,"spam") && !strstr($email,"support") && !strstr($email,"bookmyname") && !strstr($email,"gandi.net"))
			{
				$emails_final[] = $email;
			}
		}
		
		$emails = $emails_final;
	}
  }
  return $emails;
  
}

function get_social_profiles($links)
{
	$profiles = array();
	foreach($links as $link)
	{
		if(preg_match("/https?:\/\/w?w?w?\.?twitter\.com\/(.*)/",$link->href))
		{
			$profiles["twitter"] = trim($link->href);
		}
		if(preg_match("/https?:\/\/(.*).?facebook.com\/(.*)/",$link->href))
		{
			$profiles["facebook"] = trim($link->href);
		}
		if(preg_match("/https:\/\/plus\.google.com\/(.*)/",$link->href))
		{
			$profiles["googleplus"] = trim($link->href);
		}
		
		if(preg_match("#http://(.*)linkedin.com/(.*)#",$link->href))
		{
			$profiles["linkedin"] = trim($link->href);
		}
		if(preg_match("#http://fr.viadeo.com/(.*)/profile/(.*)#",$link->href))
		{
			$profiles["viadeo"] = trim($link->href);
		}
		
	}
	return (object) $profiles;
}
function get_mailto($links)
{
	$mailto = "";
	foreach($links as $link)
	{
		if(preg_match("#mailto:(.*)#",$link->href,$match))
		{
			$mailto = $match[1];
		}
		
	}
	return $mailto;
}

$links = get_links($url);
$social = get_social_profiles($links);
$mailto = get_mailto($links);

$contact_form = get_form_contact($links);
$whois_emails = get_emails_whois($dom);

$data = (object) array(
	"contact-form" => $contact_form,
	"social" => $social,
	"mailto" => $mailto,
	"whois-emails" => $whois_emails
);

var_dump($data);

// ADD email from string mailto:
// ADD Twitter Facebook et Googleplus