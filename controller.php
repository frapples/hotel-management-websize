<?php

class Controller {
    static public function dispath($con_name)
    {
        if (!method_exists('Controller', $con_name)) {
            $con_name = "user_login";
        }

        call_user_func(array('Controller', $con_name));
    }

    static public function user_login()
    {
        Tpl::load("user_login", array());
    }
}