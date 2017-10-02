<?php
import('Detect-cms');
$domain = $keyword = $params->arg[2];
$cms = new \DetectCMS\DetectCMS($domain);
if($cms->getResult()) {
    echo "Detected CMS: ".$cms->getResult();
} else {
    echo "CMS couldn't be detected";
}
