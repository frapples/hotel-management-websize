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
            "located_url" => array(
                'user' => Path::url(array(), "user_space"),
                'admin' => Path::url(array(), "admin")
            )
        );
        Tpl::load("user_login", $api);
    }

    static public function user_space()
    {
        if (!Session::get('is_login', false)) {
            Path::locate(Path::url(array(), "user_login"));
        } else {
            $api = array(
                "user_info" => UserModel::user(Session::get('id_card', '')),
                "order_records" => RoomModel::records(Session::get('id_card', '')),
            );
            Tpl::load("user_space", $api);
        }
    }

    static public function admin()
    {
        if (!Session::get('is_admin', false)) {
            Path::locate(Path::url(array(), "user_login"));
        } else {
            $api = array(
            );
            Tpl::load("admin", $api);
        }
    }

    static public function admin_sub($sub_querys)
    {
        if (!Session::get('is_admin', false)) {
            return;
        }

        if ($sub_querys[0] == 'admin_info') {
            $api = array(
                'admins' => EmployeeModel::employees(),
                'self_admin' => array(),
            );
            foreach($api['admins'] as $admin) {
                if ($admin['admin_id'] == Session::get("employee_id", "")) {
                    $api['self_admin'] = $admin;
                }
            }
            Tpl::load("admin.admin_info", $api);
        } else if ($sub_querys[0] == 'manage_room') {
            $api = array(
                'room_types' => RoomModel::room_types(),
                'rooms' => RoomModel::rooms()
            );
            Tpl::load("admin.room", $api);

        } else if ($sub_querys[0] == 'manage_user') {
            $api = array(
                'users' => UserModel::users(),
            );
            Tpl::load("admin.user", $api);

        } else if ($sub_querys[0] == 'manage_order') {
            $api = array(
                'records' => RoomModel::current_records(),
            );
            Tpl::load("admin.order", $api);
        }
    }

    static public function test()
    {
        if ($_POST["pwd"] == "12345678") {
            Database::instance();
            // UserModel::user("678432578052507538");
        }
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

    static public function login()
    {
        $id_card = UserModel::check($_POST['username'], $_POST['password']);
        $employee_id =  EmployeeModel::check($_POST['username'], $_POST['password']);

        if ($id_card != false) {
            Session::set('is_login', true);
            Session::set('username', $_POST['username']);
            Session::set('id_card', $id_card);

            return array(
                "success" => true,
                "identity" => 'user'
            );

        } else if ($employee_id != false) {
            Session::set('is_admin', true);
            Session::set('username', $_POST['username']);
            Session::set('employee_id', $employee_id);

            return array(
                "success" => true,
                "identity" => 'admin'
            );

        } else {
            return array(
                "success" => false,
                "identity" => ''
            );
        }
    }

    static public function logoff()
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

        $success = UserModel::add_user($_POST['username'], $_POST['password'], $_POST['id_card'], $_POST['age'],
                                       $_POST['sex'] == "male" ? "男" : "女",
                                       $_POST['telephone']);


        if ($success) {
            Session::set('is_login', true);
            Session::set('username', $_POST['username']);
            Session::set('id_card', $_POST['id_card']);
        }
        return array(
            "success" => $success,
            "reason" => ""
        );
    }

    static public function vacate_room()
    {
        if (!((Session::get("is_login", false) && Session::get("id_card", "") != $_POST['id_card']) ||
             (Session::get("is_admin", false)))) {
            return array(
                "success" => false,
                "reason" => "permission"
            );
        }

        $success = RoomModel::vacate($_POST['id_card'], $_POST['room_name'], $_POST['order_time']);
        return array(
            'success' => $success,
            'reason' => ""
        );
    }
}