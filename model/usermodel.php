<?php

class UserModel {
    static public function check($user_name, $password) {
        $db = Database::instance();

        $pwd = md5($password);
        $sth = $db->prepare("select LidCard as id_card from Lodger where Lname=? and Lpassword=?");
        $sth->execute(array($user_name, $pwd));

        if ($sth->rowCount() > 0) {
            return $sth->fetch()["id_card"];
        } else {
            return false;
        }
    }

    static public function exists($id_card) {
        return count(self::user($id_card)) > 0;
    }

    static public function user($id_card) {
        $db = Database::instance();

        $sth = $db->prepare("SELECT " .
                            "LidCard as id_card, Lname as name, Lage as age, Lsex as sex, Lphone as phone, Lscore as score, ".
                            "Lodger.Vid as vip_id, Vname as vip_name, LregistrationTime as register_time ".
                            "FROM Lodger, Viptype WHERE LidCard=?");
        $sth->execute(array($id_card));

        $res = $sth->fetch();
        if ($res) {
            return $res;
        } else {
            return array();
        }
    }
}