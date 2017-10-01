<?php

function detect_separator($data)
{
    $separators = array(
      "\t",
      ",",
      "/",
      "!",
      "µ",
      "|",
      "-",
      ".",
      ";"
    );

  $separator = ""; // Right separator
  $bettergood=0; // Max good answers

  foreach($separators as $sep)
  {
    $testcount=0;
    $good=0;
    $i=0;
    foreach($data as $line)
    {
      $i++;
      if($i>100) { break; }
      $test = explode($sep,$line);
      if($testcount == count($test) && count($test) > 1) { $good++;}
      $testcount = count($test);
    }
    if($good>$bettergood) { $bettergood = $good; $separator = $sep;}
  }
  return $separator;
}
