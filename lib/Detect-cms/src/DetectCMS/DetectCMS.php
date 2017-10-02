<?php
namespace DetectCMS;

require_once __DIR__.'/Thirdparty/simple_html_dom.php';

class DetectCMS {

	public $systems = array(
    "Drupal",
    "Wordpress",
    "Joomla",
    "Liferay",
    "vBulletin",
    "Magento",
    "ExpressionEngine",
    "Sitecore",
    'Bohemiasoft',
    'EshopRychle',
    'Fastcentrik',
    'Shopsys',
    'Shoptet',
    'Webgarden',
    'Webnode',
    "Moodle",
    "Modx"
  );

	private $common_methods = array("generator_header","generator_meta");

	public $home_html = "";

	public $home_headers = array();

	public $url = "";
	
	public $result = FALSE;

	function __construct($url) {
		$this->url = $url;
		list($this->home_headers, $this->home_html) = $this->fetchBodyAndHeaders();
		$this->result = $this->check($url);
	}

	public function check() {

		/*
		 * Common, easy way first: check for Generator metatags or Generator headers
		 */
				
		foreach($this->systems as $system_name) {

      $system_class = 'DetectCMS\\Systems\\'.$system_name;

			$system = new $system_class($this->home_html, $this->home_headers, $this->url);

			foreach($this->common_methods as $method) {

				if(method_exists($system,$method)) {

					if($system->$method()) {

            return $system_name;

          }

				}

			}

		}

		/*
		 * Didn't find it yet, let's just use regular tricks
		 */

		foreach($this->systems as $system_name) {

      $system_class = 'DetectCMS\\Systems\\'.$system_name;

			$system = new $system_class($this->home_html, $this->home_headers, $this->url);

			foreach($system->methods as $method) {

				if(!in_array($method,$this->common_methods)) {

					if($system->$method()) {
						return $system_name;

					}

				}

			}

		}

		return FALSE;

	}

	public function getResult() {

		return $this->result;

	}

	protected function fetch($url=null) {

		$ch = curl_init();

		if($url==null) {
			$url = $this->url;
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);

		$return = curl_exec($ch);

		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if($httpCode == 404) {
			curl_close($ch);
			return FALSE;
		}

		curl_close($ch);
	
		return $return;

	}

	protected function fetchBodyAndHeaders() {

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_HEADER, 1);

		$response = curl_exec($ch);

		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if($httpCode == 404) {
	    curl_close($ch);
	    return FALSE;
		}

		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($response, 0, $header_size);
		$body = substr($response, $header_size);

		$header_array = array();

		foreach (explode("\r\n", $header) as $line) {
			if($line == '')
      {
          continue;
      }

      $array = explode(': ', $line);
      if(array_key_exists(1,$array))
      {
        list ($key, $value) = $array;
        $header_array[$key] = $value;
        continue;
      }

			$header_array['http_code'] = $line;
		}

		curl_close($ch);

		return array($header_array, $body);		

	}

}
