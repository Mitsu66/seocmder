<?php
import("Whois");

$dom = $params->arg[2];

$domain = new Whois($dom);
$whois_answer = $domain->info();
echo $whois_answer;
