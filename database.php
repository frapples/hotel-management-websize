<?php

class Database {
    static private $db = NULL;
    static public function instance()
    {
        if (self::$db == NULL) {
            self::$db = new PDOExt('mysql:host=127.0.0.1;port=3306;dbname=hotel_schema;charset=UTF8;','root','88888888',
                                array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC));
            self::$db->query("SET NAMES utf8;");

            /* 使用异常捕获错误 */
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if (!file_exists("sql_created")) {

                foreach(self::split_statement(Config::created_sql()) as $sql) {
                    self::$db->exec($sql);
                }

                try {
                    self::$db->beginTransaction();
                    foreach(self::split_statement(Config::test_data_sql()) as $sql) {
                        self::$db->exec($sql);
                    }
                    self::$db->commit();

                    file_put_contents("sql_created", "");

                } catch (PDOException $e) {
                    self::$db->rollBack();
                    throw $e;
                }
            }
        }

        return self::$db;
    }

    static public function split_statement($sql) {
        return array_filter(explode(";", $sql),
                             function ($item) {
                                 return trim($item) != '';
                             });
    }

    private function __construct()
    {
    }
}

class PDOExt extends PDO {
    public function query_bind($sql, $binds=array()) {
        $sth = $this->prepare($sql);
        $sth->execute($binds);
        return $sth;
    }
}