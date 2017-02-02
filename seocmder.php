<?php
require('lib/params/params.php');

//var_dump($params);
//exit;

if(!isset($params->arg[1]))
{
	echo "Please type seocmder --help to see how to use seocmder";
	exit;
}

$tool = $params->arg[1];
$script = dirname($_SERVER["PHP_SELF"]).'/tools/'.$tool.'/'.$tool.'.php';
$helper = dirname($_SERVER["PHP_SELF"]).'/tools/'.$tool.'/help.txt';


if($params->arg[1]=="--help")
{
	$dir = scandir(dirname($_SERVER["PHP_SELF"]).'/tools');
	foreach($dir as $cmd)
	{
		if($cmd!="." && $cmd!="..")
		{
			echo "[+] ".$cmd."\r\n";
			echo "\t ".file_get_contents(dirname($_SERVER["PHP_SELF"]).'/tools/'.$cmd.'/desc.txt');
			echo "\r\n";
			echo "\t => type seocmder $cmd --help for more informations";
			echo "\r\n";
			echo "----------------------------------------";
			echo "\r\n";
			echo "\r\n";
		}
	}
	
	exit;
}

if(isset($params->arg[2]) && $params->arg[2]=="--help")
{
	if(is_file($script))
	{
		echo file_get_contents($helper);
		exit;
	} else {
		echo "$tool not exist, please type seocmder --help to see";
		exit;
	}
}


if(is_file($script))
{
	include($script);
} else {
	echo "$tool not exist, please type seocmder --help to see";
	exit;
}