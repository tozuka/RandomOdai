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
  }

  protected function bindValues($stmt)
  {
    $stmt->bindValue(':id', $this->id, PDO::PARAM_STR);
    $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
    $stmt->bindValue(':screenname', $this->screenname, PDO::PARAM_STR);
    $stmt->bindValue(':location', $this->location, PDO::PARAM_STR);
    $stmt->bindValue(':description', $this->description, PDO::PARAM_STR);
    $stmt->bindValue(':profile_image_url', $this->profile_image_url, PDO::PARAM_STR);
    $stmt->bindValue(':protected', $this->protected, PDO::PARAM_INT);
    $stmt->bindValue(':is_spam', $this->is_spam, PDO::PARAM_INT);
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
