<?php

class Issue extends Database {
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
    $stmt->bindValue(':id', $this->id, SQLITE3_INTEGER);
    $stmt->bindValue(':subject', $this->subject, SQLITE3_TEXT);
    $stmt->bindValue(':description', $this->description, SQLITE3_TEXT);
    $stmt->bindValue(':status', $this->status, SQLITE3_INTEGER);
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
