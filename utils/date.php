<?php

class Date
{
    static public function only_date($timestamp = NULL)
    {
        if ($timestamp === NULL) {
            $timestamp = time();
        }
        $date = getdate($timestamp);

        return mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']);
    }

    static public function close_hour($timestamp = NULL) {
        if ($timestamp === NULL) {
            $timestamp = time();
        }

        $date = getdate($timestamp);
        return mktime($date['hours'], 0, 0, $date['mon'], $date['mday'], $date['year']);
    }
}