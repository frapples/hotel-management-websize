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

    static public function hotel_list()
    {
        $api = array(
            'is_login' => Session::get('is_login', false),
            'is_admin' => Session::get('is_admin', false),
            'rooms' => RoomModel::rooms(),
            'dayroom_start_date' => Date::only_date(),
            'id_card' => Session::get('id_card', ''),
        );

        Tpl::load("hotel_list", $api);
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
                    "reason"=> "不存在的api"
                );
        }

        return call_user_func(array('ApiController', $api_name));
    }

    static public function login()
    {
        $id_card = UserModel::check($_POST['username'], $_POST['password']);
        $employee_id =  EmployeeModel::check($_POST['username'], $_POST['password']);

        if ($id_card != false) {
            Session::clear();
            Session::set('is_login', true);
            Session::set('username', $_POST['username']);
            Session::set('id_card', $id_card);

            return array(
                "success" => true,
                "identity" => 'user'
            );

        } else if ($employee_id != false) {
            Session::clear();
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
            Session::clear();
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
        if (!Session::get("is_admin", false)) {
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

    static public function check_room_order() {
        if (!((Session::get("is_login", false) && Session::get("id_card", "") == $_POST['id_card']) ||
              (Session::get("is_admin", false)))) {
            return array(
                "success" => false,
                "reason" => "permission"
            );
        }

        /* 数据有效性检查　*/
        if (!($_POST['in_time'] && $_POST['out_time'] &&
            $_POST['in_time'] < $_POST['out_time'])) {
            /* 实际上这里应该还需要检查时间是否整点 */
            return array(
                'success' => false,
                'reason' => 'error_time'
            );
        }

        $success = !RoomModel::is_order_conflict($_POST['room_name'], $_POST['in_time'], $_POST['out_time'], $_POST['type']);

        $res = array();
        $res['success'] = $success;

        if ($success) {
            $res['price'] = RoomModel::price($_POST['id_card'], $_POST['room_name'], $_POST['in_time'], $_POST['out_time'], $_POST['type']);
            $res['cashpledge'] = RoomModel::cal_cashpledge($res['price']);
            $res['room_type'] = RoomModel::room($_POST['room_name'])['type_name'];
            $res['room_name'] = $_POST['room_name'];
            if ($_POST['type'] == 'clock') {
                $res['in_time'] = $_POST['in_time'];
                $res['out_time'] = $_POST['out_time'];
            } else {
                $res['in_time'] = RoomModel::dayroom_intime_std($_POST['in_time']);
                $res['out_time'] = RoomModel::dayroom_outtime_std($_POST['out_time']);
            }
            $res['formated_in_time'] = date('Y年m月d日 H时i分', $res['in_time']);
            $res['formated_out_time'] = date('Y年m月d日 H时i分', $res['out_time']);
        } else {
            $res['reason'] = 'time_conflict';
        }

        return $res;
    }

    static public function order_room() {
        if (!((Session::get("is_login", false) && Session::get("id_card", "") == $_POST['id_card']) ||
              (Session::get("is_admin", false)))) {
            return array(
                "success" => false,
                "reason" => "permission"
            );
        }

        /* 其它检查，暂时没写 */
        $success = RoomModel::order_room($_POST['id_card'], $_POST['room_name'], $_POST['in_time'], $_POST['out_time'], $_POST['type']);

        return array(
            'success' => $success
        );
    }

    static public function add_room_type() {

        if(!Session::get("is_admin", false)) {
            return array(
                "success" => false,
                "reason" => "permission"
            );
        }

        $success = RoomModel::add_room_type($_POST['type_name'], $_POST['area'], $_POST['person_num'], $_POST['price_per_day'], $_POST['price_per_hour']);

        return array(
            'success' => $success
        );
    }

    static public function del_room_type() {
        if(!Session::get("is_admin", false)) {
            return array(
                "success" => false,
                "reason" => "permission"
            );
        }

        $success = RoomModel::del_room_type($_POST['type_id']);
        return array(
            'success' => $success
        );
    }



    static public function add_room() {

        if(!Session::get("is_admin", false)) {
            return array(
                "success" => false,
                "reason" => "permission"
            );
        }

        $success = RoomModel::add_room($_POST['room_name'], $_POST['type_name'], $_POST['floor'], $_POST['discount']);

        return array(
            'success' => $success
        );
    }

    static public function del_room() {
        if(!Session::get("is_admin", false)) {
            return array(
                "success" => false,
                "reason" => "permission"
            );
        }

        $success = RoomModel::del_room($_POST['room_name']);
        return array(
            'success' => $success
        );
    }

}