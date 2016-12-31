<?php

class EmployeeModel {
    static public function check($user_name, $password) {
        $db = Database::instance();

        $pwd = md5($password);
        $sth = $db->query_bind("select Eno as employee_id from Employee where Ename=? and Epassword=?",
                               array($user_name, $pwd));

        $res = $sth->fetchAll();
        if (count($res) > 0) {
            return $res[0]["employee_id"];
        } else {
            return false;
        }
    }

    static public function employees() {
        $db = Database::instance();
        $sth = $db->query_bind("SELECT Eno as admin_id, Ename as name, Eage as age, Esex as sex, Ephone as phone, Pname as job_name, Eaddr as address " .
                               "FROM Employee, PrimissionType WHERE Employee.Pid = PrimissionType.Pid");
        return $sth->fetchAll();
    }


}