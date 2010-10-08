<?php

function getMyDatabase()
{
  try {
    $pdo = new PDO('sqlite:/home/naoyat/randomodai/tweets.db');
    # $pdo = new PDO('sqlite:tweets.db');
  } catch (PDOException $e) {
    print('Error: '.$e->getMessage());
    die();
  }
  return $pdo;
}

abstract class Database_pdo {
  protected static $pdo = null;
  protected $properties = array();
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
    return self::st_fetchOne('SELECT count(*) FROM '.$this->getTableName().' WHERE id='.$this->properties[':id']);
  }

  protected function bindValue($key, $value, $type)
  {
    $this->properties[$key] = $value;
  }
  // abstract protected function bindValues($stmt); // for SQLite3

  abstract protected function getUpdateSQL();
  abstract protected function getCreateSQL();

  protected function execute($sql)
  {
    $stmt = $this->getDatabase()->prepare($sql);
    $result = $stmt->execute( $this->properties );

    $error_info = $this->getDatabase()->errorInfo();
    printf("execute('%s')... => %s\n", $sql, $error_info[2]);

    return $stmt->fetchAll();
  }
  protected function fetchOne($sql)
  {
    $stmt = $this->getDatabase()->prepare($sql);
    $result = $stmt->execute( $this->properties );
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
      $this->bindValue(':id', $this->id, SQLITE3_TEXT);
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
    $pdo = self::st_getDatabase();
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute();
    return $stmt->fetchAll();
  }

  protected static function st_fetchOne($sql)
  {
    $pdo = self::st_getDatabase();
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute();
    $r = $stmt->fetch(PDO::FETCH_NUM);
    return $r[0]; 
  }

#  public static function maxId()
#  {
#    return self::st_fetchOne('SELECT max(id) FROM '. self::getTableName());
#  }


}
