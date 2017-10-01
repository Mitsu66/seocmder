<?php
import("Separator");

$file = $params->arg[2];
$data = file_get_contents($file);
$data = explode("\n",$data);

echo "le séparateur de ce fichier est haha '".detect_separator($data)."'";
