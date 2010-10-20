<?php

require_once('Config.class.php');

function assertEqual($expected, $actual)
{
  printf("assert(%s === %s)", print_r($expected,true), print_r($actual,true));
  printf(" => %s\n", ($expected === $actual)? 'true':'false');
  #assert($expected === $actual);
}


$key = 'ebi'.time();

assertEqual(null, Config::getValue($key));

Config::setValue($key,'ebi');
assertEqual('ebi', Config::getValue($key));

Config::setValue($key,'kani');
assertEqual('kani', Config::getValue($key));

Config::setValue($key,'EBI');
assertEqual('EBI', Config::getValue($key));

Config::setValue($key,'EBIC');
assertEqual('EBIC', Config::getValue($key));

Config::resetValue($key);
assertEqual(null, Config::getValue($key));

