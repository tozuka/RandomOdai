<?php

abstract class Database {
  protected static $db = null;
  protected $has_record = false;

  abstract protected function getTableName();

  protected function getDatabase()
  {
    if (!self::$db)
    {
      self::$db = new SQLite3('tweets.db');
    }
    return self::$db;
  }

  private function checkExistence()
  {
    return $this->fetchOne('SELECT count(*) FROM '.$this->getTableName().' WHERE id=:id');
  }

  abstract protected function bindValues($stmt);
  abstract protected function getUpdateSQL();
  abstract protected function getCreateSQL();

  protected function execute($sql)
  {
    $stmt = $this->getDatabase()->prepare($sql);
    $this->bindValues($stmt);
    $result = $stmt->execute();
    printf("execute('%s')... => %s\n", $sql, $this->getDatabase()->lastErrorMsg());
    return $result;
  }
  protected function fetchOne($sql)
  {
    $result = $this->execute($sql);
    $rs = $result->fetchArray();
    return $rs[0];
  }

  protected function update()
  {
    $this->execute($this->getUpdateSQL());
  }

  protected function create()
  {
    $this->execute($this->getCreateSQL());

    if (null === $this->id) $this->id = $this->getDatabase()->lastInsertRowID();
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
echo "c";
    if (!self::$db)
    {
echo "d";
      self::$db = new SQLite3('tweets.db');
      return new SQLite3('tweets.db');
    }
echo "e";
    return self::$db;
  }

  public static function st_execute($sql) // no bindValues
  {
echo 3;
    $db = self::st_getDatabase();
echo 4;
    return $db->prepare($sql)->execute();
  }

  protected static function st_fetchOne($sql)
  {
echo "st_fetchOne($sql)\n";
    $result = self::st_execute($sql);
echo "result = ".print_r($result,true);
    $rs = $result->fetchArray();
    return $rs[0];
  }

#  public static function maxId()
#  {
#    return self::st_fetchOne('SELECT max(id) FROM '. self::getTableName());
#  }


}
