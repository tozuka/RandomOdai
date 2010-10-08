<?php

class User extends Database_pdo {
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
    $this->protected         = ('true' === $protected)? true : false;
    $this->is_spam           = $is_spam;

    $this->bindValues();
  }

  protected function bindValues()
  {
    $this->bindValue(':id', $this->id, SQLITE3_TEXT);
    $this->bindValue(':name', $this->name, SQLITE3_TEXT);
    $this->bindValue(':screenname', $this->screenname, SQLITE3_TEXT);
    $this->bindValue(':location', $this->location, SQLITE3_TEXT);
    $this->bindValue(':description', $this->description, SQLITE3_TEXT);
    $this->bindValue(':profile_image_url', $this->profile_image_url, SQLITE3_TEXT);
    $this->bindValue(':protected', $this->protected, SQLITE3_INTEGER);
    $this->bindValue(':is_spam', $this->is_spam, SQLITE3_INTEGER);
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
