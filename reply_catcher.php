<?php
  require_once('twitterOAuth.php');
  include 'config.inc';

  require_once('Database_pdo.class.php');
  require_once('Tweet.class.php');
  require_once('User.class.php');
  require_once('Issue.class.php');
  require_once('Odai.class.php');

  $thanks_message = 'お題を登録しました。うべー。';
  $known_odai_message = 'そのお題は既に登録されています。';

  $to = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);

  $params = array();

  $since_id = Tweet::maxId();
  if ($since_id) $params['since_id'] = $since_id;
# echo 'params: '.print_r($params,true)."\n";

  $friends = array('199688815' => true); // 自分は登録済み
  $response = $to->oAuthRequest('https://api.twitter.com/1/friends/ids.xml', 'GET', $params);
  $xml = simplexml_load_string($response);
  foreach ($xml->id as $id) $friends[(string)$id] = true;

  $response = $to->oAuthRequest('https://api.twitter.com/statuses/mentions.xml', 'GET', $params);
  $xml = simplexml_load_string($response);

  foreach ($xml->status as $status)
  {
    $twuser = $status->user;
    if (!isset($friends[(string)$twuser->id])) continue;

    // 自分の場合ループしないように...
    if (FALSE !== strpos($status->text, $thanks_message)) continue;
    if (FALSE !== strpos($status->text, $known_odai_message)) continue;

    printf("%s (%s) > { %s %s } %s\n",
	$twuser->name, $twuser->screen_name,
        $status->created_at, $status->id, $status->text);

    $user = new User((string)$twuser->id,
                     (string)$twuser->name,
                     (string)$twuser->screen_name,
		     (string)$twuser->location,
                     (string)$twuser->description,
		     (string)$twuser->profile_image_url,
                     (string)$twuser->protected);
    $user->save();

    $created_at = time(); // $status->created_at;
    $tweet = new Tweet((string)$status->id,
                       (string)$created_at,
                       (string)$status->text,
                       (string)$status->in_reply_to_status_id,
                       (string)$status->in_reply_to_user_id,
                       (string)$status->user->id);
    $tweet->save();

    $odai_text = trim( preg_replace('/@randomodai */', '', $tweet->text) );
    if (Odai::isKnownOdai($odai_text))
    {
      $msg = $known_odai_message;
    }
    else
    {
      $odai = new Odai( $odai_text, true, $user->id );
      $odai->save();
      $msg = $thanks_message;
    }
    printf("@%s %s > %s\n", $user->screenname, $msg, $odai_text);

    $content = $to->oAuthRequest('https://api.twitter.com/1/statuses/update.xml', 'POST',
				 array('status' => sprintf('@%s %s > %s', $user->screenname, $msg, $odai_text),
				 'in_reply_to_status_id' => $status->id) );
  }
