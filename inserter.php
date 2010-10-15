<?php
  require_once('twitterOAuth.php');
  include 'config.inc';

  require_once('Database_pdo.class.php');
  require_once('Odai.class.php');

  $thanks_message = 'お題を登録しました。うべー。';
  $known_odai_message = 'そのお題は既に登録されています。';


  $fp = fopen($argv[1],'r');
  while (false !== ($line = fgets($fp))) {
    $odai_text = trim($line);
    if (Odai::isKnownOdai($odai_text))
    {
      $msg = $known_odai_message;
    }
    else
    {
      $odai = new Odai( $odai_text, true );
      $odai->save();
      $msg = $thanks_message;
    }
    printf("%s > %s\n", $msg, $odai_text);
  }

  fclose($fp);