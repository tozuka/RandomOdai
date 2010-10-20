<?php
  require_once('twitterOAuth.php');
  include 'config.inc';

  require_once('Database_pdo.class.php');
  require_once('Odai.class.php');
  require_once('MyTweet.class.php');
  require_once('Config.class.php');

  $frequency = Config::getValue('frequency', 3);
  if ($frequency < 1) $frequency = 1;
  elseif ($frequency > 10) $frequency = 10;

  $now = idate('i');
  if ($now % $frequency) exit;

  // $frequency分に1度だけ起動

  $odai = Odai::getRandomOdai();
  $odai_id = $odai[0]; $odai_text = $odai[1];

  $to = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);
  $response = $to->oAuthRequest('https://api.twitter.com/1/statuses/update.xml', 'POST', array('status' => $odai_text));
  $xml = simplexml_load_string($response);

  $tweet = new MyTweet($xml->id, time(), $xml->text, $xml->in_reply_to_status_id, $xml->in_reply_to_user_id, $xml->user->id, null, null, true, $odai_id);
  $tweet->save();

