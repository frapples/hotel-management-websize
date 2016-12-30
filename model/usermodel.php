<?php

class UserModel {
    static public function check($user_name, $password) {
        $db = Database::instance();

        $pwd = md5($password);
        $sth = $db->query_bind("select LidCard as id_card from Lodger where Lname=? and Lpassword=?",
                            array($user_name, $pwd));

        $res = $sth->fetchAll();
        if (count($res) > 0) {
            return $res[0]["id_card"];
        } else {
            return false;
        }
    }

    static public function exists($id_card) {
        return count(self::user($id_card)) > 0;
    }

    static public function user($id_card) {
        $db = Database::instance();

        $sth = $db->query_bind("SELECT " .
                            "LidCard as id_card, Lname as name, Lage as age, Lsex as sex, Lphone as phone, Lscore as score, ".
                            "Lodger.Vid as vip_id, Vname as vip_name, LregistrationTime as register_time ".
                               "FROM Lodger, Viptype WHERE LidCard=? and Viptype.Vid=Lodger.Vid",
                               array($id_card));

        $res = $sth->fetch();
        if ($res) {
            return $res;
        } else {
            return array();
        }
    }

    static public function add_user($name, $password, $id_card, $age, $sex, $phone)
    {
        $db = Database::instance();

        $pwd = md5($password);
        $sth = $db->query_bind("INSERT INTO Lodger (Lname, Lpassword, LidCard, Lage, Lsex, Lphone, Vid, LregistrationTime) " .
                            "VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                               array($name, $pwd, $id_card, $age, $sex, $phone,
                                     1,
                                     date('Y-m-d')));

        return $sth->rowCount() > 0;
    }
}