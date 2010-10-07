<?php

class Odai extends Database {
  public $id, $odai, $is_valid, $author_id;

  protected function getTableName()
  {
    return 'odais';
  }

  public function __construct($odai, $is_valid=true, $author_id=null)
  {
    $this->id        = null;
	$this->odai      = $odai;
	$this->is_valid  = $is_valid ? 1 : 0;
	$this->author_id = $author_id;
  }

  protected function bindValues($stmt)
  {
    $stmt->bindValue(':id', $this->id, SQLITE3_INTEGER);
    $stmt->bindValue(':odai', $this->odai, SQLITE3_TEXT);
    $stmt->bindValue(':is_valid', $this->is_valid, SQLITE3_INTEGER);
    $stmt->bindValue(':author_id', $this->author_id, SQLITE3_TEXT);
  }

  protected function getCreateSQL()
  {
    return 'INSERT INTO odais VALUES (NULL,:odai,:is_valid,:author_id)';
  }

  protected function getUpdateSQL()
  {
    return 'UPDATE odais SET odai=:odai,is_valid=:is_valid,author_id=:author_id WHERE id=:id';
  }

  public static function getRandomOdai()
  {
    return self::st_fetchOne('SELECT odai FROM odais ORDER BY random() LIMIT 1');
  }
}
