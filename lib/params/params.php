<?php

$params = array();
$params["var"] = array();
$params["arg"] = array();

foreach($argv as $param)
{
	if(preg_match("#^-([A-Za-z0-9-_]+)=(.*)$#", trim($param), $matches))
	{
		$params["variable"][$matches[1]] = $matches[2];
	} else {
		$params["arg"][] = $param;
	}
}
$params = (object) $params;
unset($params->arg[0]);