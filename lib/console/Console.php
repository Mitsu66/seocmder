<?php

class Console{

private $colors = array(
  "Red" => 31,
  "Green" => 32,
  "Cyan" => 36,
  "Default" => 0
);

  public function write($text,$color="Default"){
    echo "\033[1;".$this->colors[$color]."m".$text;
    echo "\033[1;0m";
  }

}

$Console = new Console();
