<?php
  require_once('twitterOAuth.php');
  include 'config.inc';

  require_once('Tweet.class.php');
  require_once('User.class.php');
  require_once('Issue.class.php');
  require_once('Odai.class.php');

  $thanks_message = 'お題を登録しました。うべー。';


  $to = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);

  $params = array();

  $since_id = Tweet::maxId();
  if ($since_id) $params['since_id'] = $since_id;
// echo 'params: '.print_r($params)."\n";

  $response = $to->oAuthRequest('https://api.twitter.com/statuses/mentions.xml', 'GET', $params);
  $xml = simplexml_load_string($response);
  foreach ($xml->status as $status) # ほんとは逆順にしたい
  {
#print_r($status);
    $twuser = $status->user;
    $user = new User($twuser->id,
                     $twuser->name,
                     $twuser->screen_name,
					 $twuser->location,
                     $twuser->description,
					 $twuser->profile_image_url,
                     $twuser->protected);
#print_r($user);
	$user->save();

    // 自分の場合ループしないように...
    if (FALSE !== strpos($status->text, $thanks_message)) continue;

	$created_at = time(); // $status->created_at;
    $tweet = new Tweet($status->id,
                       $created_at,
                       $status->text,
                       $status->in_reply_to_status_id,
                       $status->in_reply_to_user_id,
                       $status->user->id);
    $tweet->save();

    printf("%s (%s) > { %s %s:%s } %s\n",
							  $user->name, $user->screenname,
							  $created_at,
                              $status->id, $tweet->id,
							  $tweet->text);

    $odai = new Odai( preg_replace('/@randomodai */', '', $tweet->text),
                      true,
                      $user->id );
    $odai->save();

    $content = $to->oAuthRequest('https://api.twitter.com/1/statuses/update.xml',
								 'POST',
								 array('status' => sprintf('@%s %s', $user->screenname, $thanks_message),
							     'in_reply_to_status_id' => $status->id) );
  }
