<?php

class RoomModel {
    static public function records($id_card) {
        $db = Database::instance();
        $st = $db->query_bind('SELECT Typename as room_type_name, Capacity as room_capacity, Ordertime as order_time, Ordertype as cost_type '.
                        'FROM Reservation, Room, RoomType '.
                        'WHERE Reservation.Roomno = Room.Roomno and Room.Typeno = RoomType.Typeno and LidCard=?',
                        array($id_card));
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
Capacity as person_num
FROM Room, RoomType, (SELECT Roomno, COUNT(*) as order_count  FROM Reservation WHERE Realouttime is null GROUP BY Roomno) as Tmp
WHERE RoomType.Typeno = Room.Typeno and Room.Roomno = Tmp.Roomno)

UNION

(SELECT Room.Roomno as room_name, Typename as type_name, Roomfloor as floor, ROUND(Pricepercent * 10, 1) as discount, 0 as order_count,  RoomType.Typeno as type_id,
Capacity as person_num
FROM Room, RoomType
WHERE RoomType.Typeno = Room.Typeno and
    NOT EXISTS (SELECT * FROM Reservation WHERE Reservation.Roomno = Room.Roomno))

EOF;

        $st = $db->query_bind($sql);
        return $st->fetchAll();
    }
}