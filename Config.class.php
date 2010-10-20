<?php

require_once 'Database_pdo.class.php';

class Config extends Database_pdo {
  public $id, $name, $value;

  protected function getTableName()
  {
    return 'configs';
  }

  public function __construct($name, $value=null)
  {
    $this->id    = null;
    $this->name  = $name;
    $this->value = $value;
  }

  protected function bindValues($stmt)
  {
    if ($this->id) $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
    $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
    $stmt->bindValue(':value', $this->value, PDO::PARAM_STR);
  }

  protected function getCreateSQL()
  {
    return 'INSERT INTO configs (name,value) VALUES (:name,:value)';
  }

  protected function getUpdateSQL()
  {
    return 'UPDATE configs SET value=:value WHERE name=:name';
  }

  public static function getValue($name, $default=null)
  {
    $pdo = self::st_getDatabase();
    $stmt = $pdo->prepare('SELECT value FROM configs WHERE name=?');
    $stmt->execute(array($name));
    $r = $stmt->fetch(PDO::FETCH_NUM);
    return $r ? $r[0] : $default; 
  }

  public static function resetValue($name)
  {
    $pdo = self::st_getDatabase();
    $stmt = $pdo->prepare('DELETE FROM configs WHERE name=?');
    $stmt->execute(array($name));
  } 

  public static function setValue($name, $value)
  {
    $pdo = self::st_getDatabase();
    # $stmt = $pdo->prepare('REPLACE INTO configs (name,value) VALUES (?,?)'); # これだと意図した通りにならない
    $stmt = $pdo->prepare('INSERT OR IGNORE INTO configs (name) VALUES (?)');
    $stmt->execute(array($name));
    $stmt = $pdo->prepare('UPDATE configs SET value=? WHERE name=?');
    $stmt->execute(array($value,$name));
  }

}
