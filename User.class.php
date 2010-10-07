<?php

require_once('Database.class.php');

class User extends Database {
  public $id, $name, $screenname, $location, $description, $profile_image_url, $protected, $is_spam;

  protected function getTableName()
  {
    return 'users';
  }

  public function __construct($id, $name, $screenname, $location, $description, $profile_image_url, $protected, $is_spam=false)
  {
    $this->id                = $id;
    $this->name              = $name;
	$this->screenname        = $screenname;
	$this->location          = $location;
	$this->description       = $description;
	$this->profile_image_url = $profile_image_url;
	$this->protected         = $protected;
	$this->is_spam           = $is_spam;
  }

  protected function bindValues($stmt)
  {
    $stmt->bindValue(':id', $this->id, SQLITE3_INTEGER);
    $stmt->bindValue(':name', $this->name, SQLITE3_TEXT);
    $stmt->bindValue(':screenname', $this->screenname, SQLITE3_TEXT);
    $stmt->bindValue(':location', $this->location, SQLITE3_TEXT);
    $stmt->bindValue(':description', $this->description, SQLITE3_TEXT);
    $stmt->bindValue(':profile_image_url', $this->profile_image_url, SQLITE3_TEXT);
    $stmt->bindValue(':protected', $this->protected, SQLITE3_INTEGER);
    $stmt->bindValue(':is_spam', $this->is_spam, SQLITE3_INTEGER);
  }

  protected function getCreateSQL()
  {
    return 'INSERT INTO users VALUES (:id,:name,:screenname,:location,:description,:profile_image_url,:protected,:is_spam)';
  }

  protected function getUpdateSQL()
  {
    return 'UPDATE users SET name=:name,screenname=:screenname,location=:location,description=:description,profile_image_url=:profile_image_url,protected=:protected,is_spam=:is_spam WHERE id=:id';
  }

}
