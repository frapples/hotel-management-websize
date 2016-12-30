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
}