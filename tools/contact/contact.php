<?php
require('Whois/Whois.php');
require('simple_html_dom.php');
require(__DIR__.'/../../lib/Crimp/Crimp.php');



$time = time();

function get_html($url)
{
	$curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,5);
	curl_setopt($curl, CURLOPT_TIMEOUT, 5); //timeout in seconds

    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Chrome/5.0.375.125 Safari/533.4");
    $str = curl_exec($curl);
    curl_close($curl);
	return $str;
}
function get_tel($str)
{
	preg_match("#\+[0-9]{2,3} \(?[0-9]?\)?[0-9]* [0-9]* [0-9]* [0-9]* [0-9]*#",$str,$tels);
	if(count($tels) == 0)
	{
		preg_match("#\+?[0-9]+ [0-9]* [0-9]* [0-9]* [0-9]*#",$str,$tels);
	}
	if(isset($tels[0])) { $tel = $tels[0]; }
	else { $tel = ""; }
	return $tel;

}
function get_links($str)
{
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
function get_form_contact($links,$dom,$protocol){

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
			if(!strstr($email,"1and1") && !strstr($email,"ovh") && !strstr($email,"abuse") && !strstr($email,"host") && !strstr($email,"spam") && !strstr($email,"support") && !strstr($email,"bookmyname") && !strstr($email,"gandi.net") && !strstr($email,"o-w-o") && !strstr($email,"nic.fr") && !strstr($email,"admin") && !strstr($email,"regist") && !strstr($email,"dns") && !strstr($email,"domain") && !strstr($email,"whois") && !strstr($email,"privacy") && !strstr($email,"notify") && !strstr($email,"nic@") && !strstr($email,"postmaster") && !strstr($email,"private") && !strstr($email,"tld") && !strstr($email,"provid") && !strstr($email,"markmonitor"))
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
if(substr($params->arg[2],0,7) == "http://" || substr($params->arg[2],0,7) == "https:/")
{
	$url = $params->arg[2];

  $dom = str_replace("http://","",$url);
	$dom = str_replace("https://","",$dom);
	$dom = explode("/",$dom);
	$dom = $dom[0];

  //$Console->write($count." (","Green");
  //$Console->write($dom);
  //$Console->write(")\r\n","Green");

	$protocol = explode(":",$url);
	$protocol = $protocol[0];
	$protocol = $protocol."://";

  $html = get_html($url);
	$tel = get_tel($html);
	$links = get_links($html);
	$social = get_social_profiles($links);
	$mailto = get_mailto($links);

	$contact_form = get_form_contact($links,$dom,$protocol);
	$whois_emails = get_emails_whois($dom);
  $Console->write("_____________________________________________________\r\n\r\n","Cyan");
	if(!empty($tel)) {$Console->write("Tel : ","Green"); echo $tel."\r\n"; }

	if(!empty($contact_form)) { $Console->write("Contact form : ","Green"); echo $contact_form."\r\n"; }

  $Console->write("\r\nSocial Network :\r\n","Cyan");
	if(isset($social->facebook)) {$Console->write("- Facebook : ","Green"); echo $social->facebook."\r\n"; }
	if(isset($social->twitter)) {$Console->write("- Twitter : ","Green"); echo $social->twitter."\r\n"; }
	if(isset($social->googleplus)) {$Console->write("- Googleplus : ","Green"); echo $social->googleplus."\r\n"; }
	if(isset($social->linkedin)) {$Console->write("- Linkedin : ","Green"); echo $social->linkedin."\r\n"; }
	if(isset($social->viadeo)) {$Console->write("- Viadeo : ","Green"); echo $social->viadeo."\r\n"; }


	$Console->write("\r\nEmails :\r\n","Cyan");
	if(!empty($mailto)) echo "- Mailto : ".$mailto."\r\n";
	if(isset($whois_emails[0])) { $Console->write("- Whois : ","Green"); echo $whois_emails[0]."\r\n"; }
  if(isset($whois_emails[1])) { $Console->write("- Whois : ","Green"); echo $whois_emails[1]."\r\n"; }
  if(isset($whois_emails[2])) { $Console->write("- Whois : ","Green"); echo $whois_emails[2]."\r\n"; }
  $Console->write("\r\n_____________________________________________________\r\n","Cyan");
	//var_dump($data);
	exit;
}



$urls_file = $params->arg[2];
$export_file = $params->arg[3];
$urls = file_get_contents($urls_file);
$urls = explode("\r\n",$urls);
$count = 0;


$Crimp = new Crimp( 'CrimpCallback' );
$Crimp->Urls = $urls;

$Crimp->CurlOptions[ CURLOPT_FOLLOWLOCATION ] = False;
$Crimp->CurlOptions[ CURLOPT_HEADER ] = FALSE;
$Crimp->CurlOptions[ CURLOPT_SSL_VERIFYPEER ] = FALSE;
$Crimp->CurlOptions[ CURLOPT_CONNECTTIMEOUT ] = 2;
$Crimp->CurlOptions[ CURLOPT_TIMEOUT ] = 2;
$Crimp->CurlOptions[ CURLOPT_USERAGENT ] = "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Chrome/5.0.375.125 Safari/533.4";
$Crimp->Go();
$data = array();

$export_data = "";


function CrimpCallback( $Handle, $html )
{
	global $data;
  global $count;
  global $Console;
  global $export_data;
  global $export_file;
  global $urls;

  $count++;
  $url = curl_getinfo( $Handle, CURLINFO_EFFECTIVE_URL );

	$dom = str_replace("http://","",$url);
	$dom = str_replace("https://","",$dom);
	$dom = explode("/",$dom);
	$dom = $dom[0];

  $Console->write($count." (","Green");
  $Console->write($dom);
  $Console->write(")\r\n","Green");

	$protocol = explode(":",$url);
	$protocol = $protocol[0];
	$protocol = $protocol."://";

	$tel = get_tel($html);
	$links = get_links($html);
	$social = get_social_profiles($links);
	$mailto = get_mailto($links);

	$contact_form = get_form_contact($links,$dom,$protocol);
	$whois_emails = get_emails_whois($dom);

	$dom = (object) array(
		"dom" => $dom,
		"url" => $url,
		"contactForm" => $contact_form,
		"social" => $social,
		"mailto" => $mailto,
		"whoisEmails" => $whois_emails,
		"tel" => $tel
	);

  $export_data .= "\r\n";
	$export_data .= $dom->dom;
	$export_data .= "\t";
	$export_data .= $dom->url;
	$export_data .= "\t";
	$export_data .= $dom->tel;
	$export_data .= "\t";
	$export_data .= $dom->contactForm;
	$export_data .= "\t";
	$export_data .= $dom->mailto;
	$export_data .= "\t";
	if(isset($dom->social->twitter)) { $export_data .= $dom->social->twitter; }
	$export_data .= "\t";
	if(isset($dom->social->facebook)) { $export_data .= $dom->social->facebook; }
	$export_data .= "\t";
	if(isset($dom->social->googleplus)) { $export_data .= $dom->social->googleplus; }
	$export_data .= "\t";
	if(isset($dom->social->linkedin)) { $export_data .= $dom->social->linkedin; }
	$export_data .= "\t";
	if(isset($dom->social->viadeo)) { $export_data .= $dom->social->viadeo; }
	$export_data .= "\t";
	if(isset($dom->whoisEmails[0])) { $export_data .= $dom->whoisEmails[0]; }
	$export_data .= "\t";
	if(isset($dom->whoisEmails[1])) { $export_data .= $dom->whoisEmails[1]; }
	$export_data .= "\t";
	if(isset($dom->whoisEmails[2])) { $export_data .= $dom->whoisEmails[2]; }
  if($count == count($urls)) {
    $export_data = "dom\turl\ttel\tcontact-form\tmailto\ttwitter\tfacebook\tgoogleplus\tlinkedin\tviadeo\twhois1\twhois2\twhois3".$export_data;
    file_put_contents($export_file,$export_data);
	}

}

$time2 = time()-$time;
echo "\r\n".$time2." secondes.";
