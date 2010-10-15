<?php

class Tweet extends Database_pdo {
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
    $this->is_mine               = $is_mine ? 1 : 0;
  }

  protected function bindValues($stmt)
  {
    $stmt->bindValue(':id', (string)$this->id, PDO::PARAM_STR);
    $stmt->bindValue(':created_at', $this->created_at, PDO::PARAM_INT);
    $stmt->bindValue(':text', $this->text, PDO::PARAM_STR);
    $stmt->bindValue(':in_reply_to_status_id', $this->in_reply_to_status_id, PDO::PARAM_STR);
    $stmt->bindValue(':in_reply_to_user_id', $this->in_reply_to_user_id, PDO::PARAM_STR);
    $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_STR);
    $stmt->bindValue(':issue_id', $this->issue_id, PDO::PARAM_STR);
    $stmt->bindValue(':status', $this->status, PDO::PARAM_INT);
    $stmt->bindValue(':is_mine', $this->is_mine, PDO::PARAM_INT);
  }

  protected function getCreateSQL()
  {
    return 'INSERT INTO '.$this->getTableName().' (id,created_at,text,in_reply_to_status_id,in_reply_to_user_id,user_id,issue_id,status,is_mine) VALUES (:id,:created_at,:text,:in_reply_to_status_id,:in_reply_to_user_id,:user_id,:issue_id,:status,:is_mine)';
  }

  protected function getUpdateSQL()
  {
    return 'UPDATE '.$this->getTableName().' SET created_at=:created_at,text=:text,in_reply_to_status_id=:in_reply_to_status_id,in_reply_to_user_id=:in_reply_to_user_id,user_id=:user_id,issue_id=:issue_id,status=:status,is_mine=:is_mine WHERE id=:id';
  }

  public function setIssueId($issue_id, $tweet_id=null)
  ## tweet_id > INT_MAX 故に起こる問題を回避するために $tweet_id を別途指定できるようにしている。
  ## $this->id == $tweet_id なはずだが save() 時にINTに丸められてしまっている...
  {
    $this->issue_id = $issue_id;
    if (!$tweet_id) $tweet_id = $this->id;
    $this->execute('UPDATE '.$this->getTableName().' SET issue_id=' . $issue_id . ' WHERE id=' . $tweet_id);
  }

  public static function maxId()
  {
    return self::st_fetchOne('SELECT max(id) FROM '. self::getTableName());
  }

}
