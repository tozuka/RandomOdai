<?php

class Odai extends Database_pdo {
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
    if ($this->id) $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
    $stmt->bindValue(':odai', $this->odai, PDO::PARAM_STR);
    $stmt->bindValue(':is_valid', $this->is_valid, PDO::PARAM_INT);
    $stmt->bindValue(':author_id', $this->author_id, PDO::PARAM_INT);
  }

  protected function getCreateSQL()
  {
    # return 'INSERT INTO odais VALUES (NULL,:odai,:is_valid,:author_id)';
    return 'INSERT INTO odais (odai,is_valid,author_id) VALUES (:odai,:is_valid,:author_id)';
  }

  protected function getUpdateSQL()
  {
    return 'UPDATE odais SET odai=:odai,is_valid=:is_valid,author_id=:author_id WHERE id=:id';
  }

  public static function getRandomOdai()
  {
#    return self::st_fetchOne('SELECT odai FROM odais ORDER BY random() LIMIT 1');
    return self::st_fetch('SELECT id,odai FROM odais WHERE is_valid=1 ORDER BY random() LIMIT 1');
  }

  public static function isKnownOdai($odai)
  {
    $pdo = self::st_getDatabase();
    $stmt = $pdo->prepare('SELECT count(*) FROM odais WHERE odai=?');
    $result = $stmt->execute(array($odai));
    $r = $stmt->fetch(PDO::FETCH_NUM);
    return ('0' === $r[0]) ? false : true;
  }
}
