<?php


$source = file_get_contents($argv[1]);

$lines = explode("\n", $source);

require('parse_sentance.php');
$parts = [];
foreach ($lines as $line)
{
  $parts[] = parse_line($line);
}

print_r($parts);
