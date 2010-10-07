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

  protected function create($auto_increment=true)
  {
    $this->execute($this->getCreateSQL());

    if ($auto_increment) $this->id = $this->getDatabase()->lastInsertRowID();
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
    if (!self::$db)
    {
      self::$db = new SQLite3('tweets.db');
    }
    return self::$db;
  }

  public static function st_execute($sql) // no bindValues
  {
    self::st_getDatabase();
    return self::$db->prepare($sql)->execute();
  }

  protected static function st_fetchOne($sql)
  {
    $result = self::st_execute($sql);
    $rs = $result->fetchArray();
    return $rs[0];
  }

#  public static function maxId()
#  {
#    return self::st_fetchOne('SELECT max(id) FROM '. self::getTableName());
#  }

}
