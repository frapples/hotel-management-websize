<?php

class Controller {
    static public function dispath($con_name, $sub_querys)
    {
        if ($con_name == "api") {
            $result = ApiController::dispath($sub_querys[0]);
            echo json_encode($result);
            return;
        }

        if (!method_exists('Controller', $con_name)) {
            echo "404错误:" . $con_name;
            return;
        }

        call_user_func(array('Controller', $con_name), $sub_querys);
    }

    static public function user_login()
    {
        $api = array(
            "located_url" => Path::url(array(), "user_space")
        );
        Tpl::load("user_login", $api);
    }

    static public function user_space()
    {
        if (!Session::get('is_login', false)) {
            Path::locate(Path::url(array(), "user_login"));
        } else {
            $api = array(
                "user_info" => UserModel::user(Session::get('id_card', ''))
            );
            Tpl::load("user_space", $api);
        }
    }

    static public function test()
    {
        UserModel::user("678432578052507538");
    }

}

class ApiController {
    static public function dispath($api_name)
    {
        if (!method_exists('ApiController', $api_name)) {
            return array(
                    "success"=> false,
                    "msg"=> "不存在的api"
                );
        }

        return call_user_func(array('ApiController', $api_name));
    }

    static public function user_login()
    {
        $id_card = UserModel::check($_POST['username'], $_POST['password']);

        if ($id_card != false) {
            Session::set('is_login', true);
            Session::set('username', $_POST['username']);
            Session::set('id_card', $id_card);
        }

        return array(
            "success" => $id_card != false
        );
    }

    static public function user_logoff()
    {
        Session::clear();
        return array(
            "success" => true
        );
    }

    static public function user_register()
    {
        if (UserModel::exists($_POST["id_card"])) {
            return array(
                "success" => false,
                "reason" => "user_existed"
            );
        }
    }
}