<?php

require_once('Database.class.php');

class Tweet extends Database {
  public $id, $created_at, $text, $in_reply_to_status_id, $in_reply_to_user_id, $user_id, $issue_id, $status, $is_mine;

  protected function getTableName()
  {
    return 'tweets';
  }

  public function __construct($id, $created_at, $text, $in_reply_to_status_id, $in_reply_to_user_id, $user_id, $issue_id=null, $status=null, $is_mine=false)
  {
    $this->id                    = $id;
    $this->created_at            = $created_at;
    $this->text                  = $text;
    $this->in_reply_to_status_id = $in_reply_to_status_id;
    $this->in_reply_to_user_id   = $in_reply_to_user_id;
    $this->user_id               = $user_id;
    $this->issue_id              = $issue_id;
    $this->status                = $status;
    $this->is_mine               = $is_mine;
  }

  protected function bindValues($stmt)
  {
    $stmt->bindValue(':id', (string)$this->id, SQLITE3_TEXT);
    $stmt->bindValue(':created_at', $this->created_at, SQLITE3_INTEGER);
    $stmt->bindValue(':text', $this->text, SQLITE3_TEXT);
    $stmt->bindValue(':in_reply_to_status_id', $this->in_reply_to_status_id, SQLITE3_TEXT);
    $stmt->bindValue(':in_reply_to_user_id', $this->in_reply_to_user_id, SQLITE3_TEXT);
    $stmt->bindValue(':user_id', $this->user_id, SQLITE3_TEXT);
    $stmt->bindValue(':issue_id', $this->issue_id, SQLITE3_TEXT);
    $stmt->bindValue(':status', $this->status, SQLITE3_INTEGER);
    $stmt->bindValue(':is_mine', $this->is_mine, SQLITE3_INTEGER);
  }

  protected function getCreateSQL()
  {
    return 'INSERT INTO tweets (id,created_at,text,in_reply_to_status_id,in_reply_to_user_id,user_id,issue_id,status,is_mine) VALUES (:id,:created_at,:text,:in_reply_to_status_id,:in_reply_to_user_id,:user_id,:issue_id,:status,:is_mine)';
  }

  protected function getUpdateSQL()
  {
    return 'UPDATE tweets SET created_at=:created_at,text=:text,in_reply_to_status_id=:in_reply_to_status_id,in_reply_to_user_id=:in_reply_to_user_id,user_id=:user_id,issue_id=:issue_id,status=:status,is_mine=:is_mine WHERE id=:id';
  }

  public function setIssueId($issue_id, $tweet_id=null)
  ## tweet_id > INT_MAX 故に起こる問題を回避するために $tweet_id を別途指定できるようにしている。
  ## $this->id == $tweet_id なはずだが save() 時にINTに丸められてしまっている...
  {
    $this->issue_id = $issue_id;
    if (!$tweet_id) $tweet_id = $this->id;
#    $this->execute('UPDATE tweets SET issue_id=:issue_id WHERE id=:id');
printf("// %s:%s\n", $tweet_id, $this->id);
    $this->execute('UPDATE tweets SET issue_id=' . $issue_id . ' WHERE id=' . $tweet_id);
  }

  public static function maxId()
  {
    return self::st_fetchOne('SELECT max(id) FROM '. self::getTableName());
  }

}
