<?php

class RoomModel {
    static public function records($id_card) {
        $db = Database::instance();
        $sql = <<<EOF
SELECT Typename as room_type_name, Capacity as room_capacity, Ordertime as order_time, Ordertype as cost_type
FROM Reservation, Room, RoomType
WHERE Reservation.Roomno = Room.Roomno and Room.Typeno = RoomType.Typeno and LidCard=?
ORDER BY Ordertime DESC
EOF;
        $st = $db->query_bind($sql, array($id_card));
        $results = $st->fetchAll();

        foreach ($results as &$result) {
            $result['cost_type'] = $result['cost_type'] == 0 ? '钟点房' : '普通房';
        }

        return $results;
    }

    /* 当前预定但是没退房的订单 */
    static public function current_records() {
        $db = Database::instance();
        $st = $db->query_bind('SELECT  '.
                              'Lodger.LidCard as id_card, Lname as name, Lage as age, Lsex as sex, Lphone as phone, Lscore as score, '.
                              // "LregistrationTime as register_time ".
                              'Room.Roomno as room_name, Typename as room_type_name, Capacity as room_capacity, Ordertime as order_time, Ordertype as cost_type, '.
                              "date_format(Maybeintime, '%Y-%m-%d %H:%i') as in_time, date_format(Maybeouttime, '%Y-%m-%d %H:%i') as out_time, " .
                              'Cashpledge as cashpledge, (Maybeouttime < now()) as is_timeout '.
                              'FROM Lodger, Reservation, Room, RoomType '.
                              'WHERE Reservation.Roomno = Room.Roomno and Room.Typeno = RoomType.Typeno and Lodger.LidCard = Reservation.LidCard ' .
                              'and Realouttime IS NULL ORDER BY room_name, Maybeintime');
        $results = $st->fetchAll();

        foreach ($results as &$result) {
            $result['cost_type'] = $result['cost_type'] == 0 ? '钟点房' : '普通房';
        }

        return $results;
    }

    static public function vacate($id_card, $room_name, $order_time)
    {
        $db = Database::instance();
        $st = $db->query_bind('UPDATE Reservation SET Realouttime = now() ' .
                              'WHERE LidCard = ? and Roomno = ? and Ordertime = ?',
                              array($id_card, $room_name, $order_time));

        return $st->rowCount() > 0;
    }


    static public function room_types()
    {
        $db = Database::instance();
        $sql = <<<EOF
(SELECT Typename as type_name, Clockprice as price_per_hour, Dayprice as price_per_day, Area as area, Capacity as person_num, room_count
FROM RoomType, (SELECT Typeno, COUNT(*) as room_count FROM Room GROUP BY Typeno) as Tmp
WHERE RoomType.Typeno = Tmp.Typeno)

UNION

(SELECT Typename as type_name, Clockprice as price_per_hour, Dayprice as price_per_day, Area as area, Capacity as person_num, 0 as room_count
FROM RoomType
WHERE NOT EXISTS (SELECT * FROM Room WHERE Room.Typeno = RoomType.Typeno))


EOF;

        $st = $db->query_bind($sql);
        return $st->fetchAll();
    }

    static public function rooms()
    {
        $db = Database::instance();
        $sql = <<<EOF
(SELECT Room.Roomno as room_name, Typename as type_name, Roomfloor as floor, ROUND(Pricepercent * 10, 1) as discount, order_count, RoomType.Typeno as type_id,
Capacity as person_num, Clockprice as price_per_hour, Dayprice as price_per_day, Area as area,
Dayprice * Pricepercent as real_price_per_day, Clockprice * Pricepercent as real_price_per_hour
FROM Room, RoomType, (SELECT Roomno, COUNT(*) as order_count  FROM Reservation WHERE Realouttime is null GROUP BY Roomno) as Tmp
WHERE RoomType.Typeno = Room.Typeno and Room.Roomno = Tmp.Roomno)

UNION

(SELECT Room.Roomno as room_name, Typename as type_name, Roomfloor as floor, ROUND(Pricepercent * 10, 1) as discount, 0 as order_count,  RoomType.Typeno as type_id,
Capacity as person_num, Clockprice as price_per_hour, Dayprice as price_per_day, Area as area,
ROUND(Dayprice * Pricepercent, 1) as real_price_per_day, ROUND(Clockprice * Pricepercent, 1) as real_price_per_hour
FROM Room, RoomType
WHERE RoomType.Typeno = Room.Typeno and
    NOT EXISTS (SELECT * FROM Reservation WHERE Reservation.Roomno = Room.Roomno))

EOF;

        $st = $db->query_bind($sql);
        return $st->fetchAll();
    }

    static public function room($room_no)
    {
        $db = Database::instance();
        $sql = <<<EOF
SELECT Room.Roomno as room_name, Typename as type_name, Roomfloor as floor, ROUND(Pricepercent * 10, 1) as discount, RoomType.Typeno as type_id,
Capacity as person_num, Clockprice as price_per_hour, Dayprice as price_per_day, Area as area,
Dayprice * Pricepercent as real_price_per_day, Clockprice * Pricepercent as real_price_per_hour
FROM Room, RoomType
WHERE RoomType.Typeno = Room.Typeno and Roomno=?
EOF;

        $st = $db->query_bind($sql, array($room_no));
        $res = $st->fetchAll();
        if (!$res) {
            return array();
        } else {
            return $res[0];
        }
    }

    static public function order_room($id_card, $room_name, $in_time, $out_time, $type)
    {
        $db = Database::instance();

        $price = self::price($id_card, $room_name, $in_time, $out_time, $type);

        if ($type != 'clock') {
            $in_time =  self::dayroom_intime_std($in_time);
            $out_time = self::dayroom_outtime_std($out_time);
        }

        $in_time = date("Y-m-d H:i:s", $in_time);
        $out_time = date("Y-m-d H:i:s", $out_time);

        $sql = <<<EOF
        INSERT INTO Reservation(Roomno,LidCard,Ordertime,Maybeintime,Maybeouttime,Realouttime,Ordertype,Cashpledge)
            VALUES(?, ?, now(), ?, ?, null, ?, ?);
EOF;

        $st = $db->query_bind($sql, array(
            $room_name, $id_card,
            $in_time, $out_time,
            ($type == 'clock' ? 0 : 1),
            self::cal_cashpledge($price)
        ));

        return $st->rowCount() > 0;
    }

    /* 查询是否有冲突 */
    static public function is_order_conflict($room_name, $in_time, $out_time, $type)
    {
        $db = Database::instance();
        $sql = <<<EOF
SELECT * FROM Reservation
WHERE Roomno=? and Ordertype=? and Maybeintime >= ? and Maybeouttime <= ? and Realouttime is null
EOF;

        $st = $db->query_bind($sql, array(
            $room_name, ($type == 'clock' ? 0 : 1), date("Y-m-d H:i:s", $in_time), date("Y-m-d H:i:s", $out_time)));

        return count($st->fetchAll()) > 0;
    }

    static public function dayroom_intime_std($intime)
    {
        $intime = Date::only_date($intime);
        return $intime + 11 * 60 * 60;
    }

    static public function dayroom_outtime_std($outtime)
    {
        $outtime = Date::only_date($outtime);
        return $outtime + 15 * 60 * 60;
    }

    /* 真正支付的价格　*/
    static public function price($id_card, $room_name, $in_time, $out_time, $type)
    {
        $db = Database::instance();
        $sql = <<<EOF
SELECT Pricepercent as discount, Clockprice as price_per_hour, Dayprice as price_per_day
FROM Room, RoomType
WHERE RoomType.Typeno = Room.Typeno and Roomno = ?
EOF;
        $st = $db->query_bind($sql, array($room_name));
        $res = $st->fetchAll();
        if (!$res) {
            return PHP_INT_MAX;
        } else {
            $res = $res[0];
            if ($type == 'clock') {

                return $res['price_per_hour'] * (($out_time - $in_time) / (60 * 60)) * $res['discount'];

            } else {

                $in_time = Date::only_date($in_time);
                $out_time = Date::only_date($out_time);
                return $res['price_per_day'] * (($out_time - $in_time) / (24 * 60 * 60)) * $res['discount'];
            }
        }
    }


    /* 押金是按照房钱计算的 */
    static public function cal_cashpledge($price)
    {
        return $price < 50 ? 50 : $price;
    }
}