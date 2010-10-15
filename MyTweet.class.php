<?php
require_once('Tweet.class.php');

class MyTweet extends Tweet {
  public $odai_id;

  protected function getTableName()
  {
    return 'my_tweets';
  }

  public function __construct($id, $created_at, $text, $in_reply_to_status_id, $in_reply_to_user_id, $user_id, $issue_id=null, $status=null, $is_mine=false, $odai_id)
  {
    parent::__construct($id, $created_at, $text, $in_reply_to_status_id, $in_reply_to_user_id, $user_id, $issue_id, $status, $is_mine);
#    $this->id                    = $id;
#    $this->created_at            = $created_at;
#    $this->text                  = $text;
#    $this->in_reply_to_status_id = $in_reply_to_status_id;
#    $this->in_reply_to_user_id   = $in_reply_to_user_id;
#    $this->user_id               = $user_id;
#    $this->issue_id              = $issue_id;
#    $this->status                = $status;
#    $this->is_mine               = $is_mine ? 1 : 0;
    $this->odai_id               = $odai_id;
  }

  protected function bindValues($stmt)
  {
    parent::bindValues($stmt);
    $stmt->bindValue(':odai_id', $this->odai_id, PDO::PARAM_INT);
  }

  protected function getCreateSQL()
  {
    return 'INSERT INTO '.$this->getTableName().' (id,created_at,text,in_reply_to_status_id,in_reply_to_user_id,user_id,issue_id,status,is_mine,odai_id) VALUES (:id,:created_at,:text,:in_reply_to_status_id,:in_reply_to_user_id,:user_id,:issue_id,:status,:is_mine,:odai_id)';
  }

  protected function getUpdateSQL()
  {
    return 'UPDATE '.$this->getTableName().' SET created_at=:created_at,text=:text,in_reply_to_status_id=:in_reply_to_status_id,in_reply_to_user_id=:in_reply_to_user_id,user_id=:user_id,issue_id=:issue_id,status=:status,is_mine=:is_mine,odai_id=:odai_id WHERE id=:id';
  }

  public static function increment_ninki($id, $delta=1)
  {
    if ($delta > 0) { $delta = 1; $incr = '++'; }
    elseif ($delta < 0) { $delta = -1; $incr = '--'; }
    else return null;
 
    $odai_id = self::st_fetchOne('SELECT odai_id FROM my_tweets WHERE id='.$id);
    if (!$odai_id) return null;

    self::st_execute('UPDATE odais SET ninki=ninki+'.$delta.' WHERE id='.$odai_id);

    $odai = self::st_fetchOne('SELECT odai FROM odais WHERE id='.$odai_id);
    if (!$odai) return null;

    return sprintf('(%s)%s', $odai, $incr);
  }
}
