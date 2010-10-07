<?php
  require_once('twitterOAuth.php');
  include 'config.inc';

  require_once('Database.class.php');
  require_once('Odai.class.php');

  $odai = Odai::getRandomOdai() . "\n";
  echo $odai."\n";

  $to = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);
  $content = $to->oAuthRequest('https://api.twitter.com/1/statuses/update.xml', 'POST', array('status' => $odai));

