<?php
  require_once('twitterOAuth.php');
  include 'config.inc';

  require_once('Database_pdo.class.php');
  require_once('Tweet.class.php');
  require_once('MyTweet.class.php');
  require_once('User.class.php');
  require_once('Issue.class.php');
  require_once('Odai.class.php');

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

    $odai_text = (string)trim( preg_replace('/@'.$my_screenname.' */', '', $status->text) );
    printf("%s (%s) > { '%s' %s %s %s } %s\n", $user->name, $user->screenname,
                                       $status->created_at,
                                       $status->id, $status->in_reply_to_user_id, $status->in_reply_to_status_id,
                                       $odai_text);

    if ($my_user_id == $status->in_reply_to_user_id && $status->in_reply_to_status_id) {
      $delta = (int)$odai_text;
      if (!$delta) {
        if (FALSE !== strpos($odai_text, 'イイネ')) $delta = 1;
        elseif (FALSE !== strpos($odai_text, 'ひどい')) $delta = -1;
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

    $created_at = time(); // $status->created_at;
    $tweet = new Tweet((string)$status->id,
                       (string)$created_at,
                       (string)$odai_text,
                       (string)$status->in_reply_to_status_id,
                       (string)$status->in_reply_to_user_id,
                       (string)$status->user->id);
    $tweet->save();

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
