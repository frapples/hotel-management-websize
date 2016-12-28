<?php

class Config
{
    static public function created_sql() {
        return file_get_contents("create.sql");
    }

    static public function test_data_sql() {
        return file_get_contents("test_data.sql");
    }
};