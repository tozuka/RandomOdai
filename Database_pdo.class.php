<?php

function getMyDatabase()
{
  $dbpath = dirname(__FILE__).'/tweets.db';
  try {
    $pdo = new PDO('sqlite:'.$dbpath);
  } catch (PDOException $e) {
    print('Error: '.$e->getMessage());
    die();
  }
  return $pdo;
}

abstract class Database_pdo {
  protected static $pdo = null;
  protected $has_record = false;

  abstract protected function getTableName();

  protected function getDatabase()
  {
    if (!self::$pdo)
    {
      self::$pdo = getMyDatabase();
    }
    return self::$pdo;
  }

  private function checkExistence()
  {
    if (!$this->id) return false;

    return self::st_fetchOne('SELECT count(*) FROM '.$this->getTableName().' WHERE id='.$this->id);
  }

  abstract protected function bindValues($stmt); // for SQLite3
  abstract protected function getUpdateSQL();
  abstract protected function getCreateSQL();

  protected function execute($sql)
  {
    $stmt = $this->getDatabase()->prepare($sql);
    if (!$stmt) {
      $error_info = $this->getDatabase()->errorInfo();
      //printf("execute('%s')... => %s\n", $sql, $error_info[2]);
      return null;
    }

    $this->bindValues($stmt);
    $result = $stmt->execute();

    $error_info = $this->getDatabase()->errorInfo();
    //printf("execute('%s')... => %s\n", $sql, $error_info[2]);

    return $stmt->fetchAll();
  }
  protected function fetchOne($sql)
  {
    $stmt = $this->getDatabase()->prepare($sql);
    $this->bindValues($stmt);
    $result = $stmt->execute();
    $r = $stmt->fetch(PDO::FETCH_NUM);
    return $r[0];
  }

  protected function update()
  {
    $this->execute($this->getUpdateSQL());
  }

  protected function create()
  {
    $this->execute($this->getCreateSQL());

    if (null === $this->id)
    {
      $this->id = $this->getDatabase()->lastInsertId();
    }
    $this->has_record = true;
  }

  public function save()
  {
    if ($this->has_record || $this->checkExistence())
    {
      $this->update();
    }
    else
    {
      $this->create();
    }
    $this->has_record = true;
  }

  public function truncate()
  {
    $this->execute('DELETE FROM '.$this->getTableName());
  }

  // static version
  protected static function st_getDatabase()
  {
    if (!self::$pdo)
    {
      self::$pdo = getMyDatabase();
    }
    return self::$pdo;
  }

  public static function st_execute($sql) // no bindValues
  {
    $stmt = self::st_getDatabase()->prepare($sql);
    $result = $stmt->execute();
    return $stmt->fetchAll();
  }

  protected static function st_fetchOne($sql)
  {
    $stmt = self::st_getDatabase()->prepare($sql);
    $result = $stmt->execute();
    $r = $stmt->fetch(PDO::FETCH_NUM);
    return $r[0]; 
  }

#  public static function maxId()
#  {
#    return self::st_fetchOne('SELECT max(id) FROM '. self::getTableName());
#  }


}
