<?php

class Issue extends Database_pdo {
  public $id, $subject, $description, $status;

  protected function getTableName()
  {
    return 'issues';
  }

  public function __construct($id, $subject, $description, $status)
  {
    $this->id          = $id;
    $this->subject     = $subject;
    $this->description = $description;
    $this->status      = $status;
  }

  protected function bindValues($stmt)
  {
    $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
    $stmt->bindValue(':subject', $this->subject, PDO::PARAM_STR);
    $stmt->bindValue(':description', $this->description, PDO::PARAM_STR);
    $stmt->bindValue(':status', $this->status, PDO::PARAM_INT);
  }

  protected function getCreateSQL()
  {
    return 'INSERT INTO issues VALUES (:id,:subject,:description,:status)';
  }

  protected function getUpdateSQL()
  {
    return 'UPDATE issues SET subject=:subject,description=:description,status=:status WHERE id=:id';
  }

  public function getURL()
  {
    return 'http://demo.redmine.org/issues/'.$this->id;
  }
}
