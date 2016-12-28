<?php

class Database {
    static private $db = NULL;
    static public function instance()
    {
        if (self::$db == NULL) {
            self::$db = new PDO('mysql:host=127.0.0.1;port=3306;dbname=hotel_schema;charset=UTF8;','root','88888888',
                                array(PDO::ATTR_PERSISTENT=>true,
                                      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC));
            self::$db->query("SET NAMES utf8;");

            /* 使用异常捕获错误 */
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if (!file_exists("sql_created")) {
                self::$db->exec(Config::created_sql());

                try {
                    self::$db->beginTransaction();
                    self::$db->exec(Config::test_data_sql());
                    self::$db->commit();
                    file_put_contents("sql_created", "");

                } catch(PDOException $e) {
                    self::$db->rollBack();
                    throw $e;
                }
            }
        }

        return self::$db;
    }

    private function __construct()
    {
    }
}