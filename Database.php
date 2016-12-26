<?php

class Database {
    static private $db = NULL;
    static public function instance()
    {
        if ($this.db != NULL) {
            return $db;
        } else {
            $this->db = new PDO('mysql:host=127.0.0.1;port=3306;dbname=anexis_new;charset=UTF8;','root','password',
                                array(PDO::ATTR_PERSISTENT=>true));
            $this->db->query("SET NAMES utf8;");

            $this->db->exec(Config::created_sql());
        }
    }

    private function __construct()
    {
    }
}