<?php

class Config
{
    static public function created_sql() {
        return file_get_contents("create.sql");
    }
};