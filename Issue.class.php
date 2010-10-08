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

  protected function bindValues()
  {
    $this->bindValue(':id', $this->id, SQLITE3_INTEGER);
    $this->bindValue(':subject', $this->subject, SQLITE3_TEXT);
    $this->bindValue(':description', $this->description, SQLITE3_TEXT);
    $this->bindValue(':status', $this->status, SQLITE3_INTEGER);
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
