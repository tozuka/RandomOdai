<?php

require_once(dirname(__FILE__).'/../Config.class.php');


$assert_count = $assert_fail = 0;

function assertEqual($expected, $actual, $msg=null)
{
  global $assert_count, $assert_fail;

  printf("assert(%s === %s)", print_r($expected,true), print_r($actual,true));
  $bool = ($expected === $actual);

  $assert_count++;
  if (!$bool) $assert_fail++;

  printf(" => %s // %s\n", $bool?'OK':'FAIL', $msg);
  #assert($expected === $actual);
}

function assertSummary()
{
  global $assert_count, $assert_fail;
  printf("%d success + %d fail\n", $assert_count-$assert_fail, $assert_fail);
}


$key1 = 'ebi'.time();
$key2 = 'kani'.time();

assertEqual(null, Config::getValue($key1), '最初はkey1もkey2も空であること');
assertEqual(null, Config::getValue($key2), '');

Config::setValue($key1,'ebi');
assertEqual('ebi', Config::getValue($key1), 'configに登録できるかどうか');
assertEqual(null, Config::getValue($key2), 'key1に登録してもkey2は空のままであること');

Config::setValue($key1,'EBI');
assertEqual('EBI', Config::getValue($key1), '大文字と小文字が区別されるかどうか');
assertEqual(null, Config::getValue($key2), '');

Config::setValue($key2,'kani');
assertEqual('EBI', Config::getValue($key1), 'key2の変更がkey1に影響しないこと');
assertEqual('kani', Config::getValue($key2), '勿論key2にちゃんと登録できていること');

Config::resetValue($key1);
assertEqual(null, Config::getValue($key1), 'resetできること');
assertEqual('kani', Config::getValue($key2), 'key2には影響がないこと');

assertSummary();

