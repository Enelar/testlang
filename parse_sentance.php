<?php

function parse_line($line)
{
  var_dump($line);
  $res = parse_block($line);

  if (is_null($res))
    throw new Exception("Unrecognized line. Skipping {$line}");

  return $res;
}

function parse_block($line)
{
  $valid_constructions =
  [
    'empty' => 'TryEmpty',
    'assign' => 'TryAssign',
    'func' => 'TryFunc',
    'math' => 'TryMath',
    'value' => 'TryValue',
    'name' => 'TryName',
  ];

  foreach ($valid_constructions as $type => $func)
  {
    $try = $func($line);
    if (is_null($try))
      continue;

    $ret =
    [
      'type' => $type,
      'blocks' => $try,
    ];

    return $ret;
  }

  return null;
}

function TryEmpty($line)
{
  if (empty($line))
    return "";

  if (preg_match('/"\/\/"/', $line))
    return "";

  return null;
}

function TryAssign($line)
{
  $ret = explode('=', $line, 2);

  if (count($ret) == 1)
    return null;

  foreach ($ret as &$t)
    $t = trim($t);

  $test = parse_block($ret[0]);
  if (is_null($test) || $test['type'] != 'name')
    throw new Exception("Left operand of assigment expected to be a name {{$ret[0]}}");
  else
    $ret[0] = $test;

  $test = parse_block($ret[1]);
  if (!is_null($test))
    $ret[1] = $test;

  return $ret;
}

function TryFunc($line)
{
  return null;
}

function TryMath($line)
{
  return null;
}

function TryValue($line)
{
  $matched = preg_match('/"(.*)"/', $line, $match);
  if ($matched)
  {
    if ("\"{$match[1]}\"" != $line)
      throw new Exception("Tried match const value, matched {\"{$match[1]}\"} in {{$line}}. (Should be equal)");

    return
    [
      'type' => 'string',
      'blocks' => $match[1],
    ];
  }

  $matched = preg_match('/(\d*.?\d+)/', $line, $match);
  if ($matched)
  {
    if ($match[1] != $line)
      throw new Exception("Tried match const value, matched {{$match[1]}} in {{$line}}. (Should be equal)");

    return
    [
      'type' => 'number',
      'blocks' => $match[1],
    ];
  }

  return null;
}

function TryName($line)
{
  $matched = preg_match('/([\w\d_]+)/', $line, $match);
  if ($matched)
  {
    if ($match[1] != $line)
      throw new Exception("Tried match name, matched {{$match[1]}} in {{$line}}. (Should be equal)");

    return $match[1];
  }

  return null;
}
