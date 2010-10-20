<?php
  require_once('twitterOAuth.php');
  include 'config.inc';

  require_once('Database_pdo.class.php');
  require_once('Tweet.class.php');
  require_once('MyTweet.class.php');
  require_once('User.class.php');
  require_once('Issue.class.php');
  require_once('Odai.class.php');
  require_once('Config.class.php');

  $thanks_message = 'お題を登録しました。うべー。';
  $known_odai_message = 'そのお題は既に登録されています。';

  $my_user_id = 199688815;
  $my_screenname = 'randomodai';

  $to = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);

  $params = array();

  $since_id = Tweet::maxId();
  if ($since_id) $params['since_id'] = $since_id;
# echo 'params: '.print_r($params,true)."\n";

  $friends = array((string)$my_user_id => true); // 自分は登録済み
  $response = $to->oAuthRequest('https://api.twitter.com/1/friends/ids.xml', 'GET', $params);
  $xml = simplexml_load_string($response);
  foreach ($xml->id as $id) $friends[(string)$id] = true;

  $response = $to->oAuthRequest('https://api.twitter.com/statuses/mentions.xml', 'GET', $params);
  $xml = simplexml_load_string($response);

  foreach ($xml->status as $status)
  {
    $twuser = $status->user;
    if ($my_user_id === $twuser->id) continue;
    if (!isset($friends[(string)$twuser->id])) continue;

    // 自分の場合ループしないように...
    if (FALSE !== strpos($status->text, $thanks_message)) continue;
    if (FALSE !== strpos($status->text, $known_odai_message)) continue;

    $user = new User((string)$twuser->id,
                     (string)$twuser->name,
                     (string)$twuser->screen_name,
		             (string)$twuser->location,
                     (string)$twuser->description,
		             (string)$twuser->profile_image_url,
                     (string)$twuser->protected);
    $user->save();

    $content = (string)trim( preg_replace('/@'.$my_screenname.' */', '', $status->text) );
    $created_at = time(); // $status->created_at;

    $tweet = new Tweet((string)$status->id,
                       (string)$created_at,
                       (string)$content,
                       (string)$status->in_reply_to_status_id,
                       (string)$status->in_reply_to_user_id,
                       (string)$status->user->id);
    $tweet->save();

    printf("%s (%s) > { '%s' %s %s %s } %s\n", $user->name, $user->screenname,
                                       $status->created_at,
                                       $status->id, $status->in_reply_to_user_id, $status->in_reply_to_status_id,
                                       $content);

    $freq = null;
    if (FALSE !== strpos($content, 'a tempo')) {
      $freq = 3;
    } elseif (FALSE !== strpos($content, 'faster')) {
      $freq = 2;
    } elseif (FALSE !== strpos($content, 'fastest')) {
      $freq = 1;
    } elseif (FALSE !== strpos($content, 'slower')) {
      $freq = 4;
    } elseif (FALSE !== strpos($content, 'frequency')) {
      $freq = Config::getValue('frequency', 3);
    } elseif (FALSE !== strpos($content, 'login')) {
      $freq = 2;
    } elseif (FALSE !== strpos($content, 'logout')) {
      $freq = 3;
    }
    if ($freq) {
      Config::setValue('frequency', $freq);
      switch ($freq) {
        case 1: $freq_msg = 'every minute'; break;
        case 2: $freq_msg = 'every other minute'; break;
        default: $freq_msg = sprintf('every %d minutes', $freq); break;
      }
      $content = $to->oAuthRequest('https://api.twitter.com/1/statuses/update.xml', 'POST',
  				   array('status' => sprintf('@%s %s', $user->screenname, $freq_msg),
				   'in_reply_to_status_id' => $status->id) );
      continue;
    }

    if ($my_user_id == $status->in_reply_to_user_id && $status->in_reply_to_status_id) {
      $delta = (int)$content;
      if (!$delta) {
        if (FALSE !== strpos($content, 'イイネ')) $delta = 1;
        elseif (FALSE !== strpos($content, 'ひどい')) $delta = -1;
      }
      if ($delta) {
        $msg = MyTweet::increment_ninki($status->in_reply_to_status_id, $delta);
        if ($msg) {
          $content = $to->oAuthRequest('https://api.twitter.com/1/statuses/update.xml', 'POST',
                                       array('status' => sprintf('@%s %s', $user->screenname, $msg),
                                       'in_reply_to_status_id' => $status->id) );
        }
      }
      continue;
    }

    if (Odai::isKnownOdai($content))
    {
      $msg = $known_odai_message;
    }
    else
    {
      $odai = new Odai( $content, true, $user->id );
      $odai->save();
      $msg = $thanks_message;
    }
    printf("@%s %s > %s\n", $user->screenname, $msg, $content);

    $content = $to->oAuthRequest('https://api.twitter.com/1/statuses/update.xml', 'POST',
				 array('status' => sprintf('@%s %s > %s', $user->screenname, $msg, $content),
				 'in_reply_to_status_id' => $status->id) );
  }
