create table Viptype(/*VIP类型表*/
Vid int primary key AUTO_INCREMENT,
Vname char(20) not null,
Vdiscount numeric(3,2) not null,
VscoreMultiplier numeric(3,2) not null,
VdepositDiscount numeric(3,2) not null
);
create table PrimissionType(/*权限表*/
Pid int primary key AUTO_INCREMENT,
Pname char(20) not null,
PchangeRoome tinyint check(PchangeRoome in(0,1)),
PchangePrice tinyint check(PchangePrice in(0,1)),
PmanageRoome tinyint check(PmanageRoome in(0,1)),
PcontactLodger tinyint
);
create table Lodger(/*住客表*/
LidCard char(18) primary key ,
Lname char(20),
Lage smallint not null,
Lsex char(2) not null,
Lpassword char(32),
Lphone char(16) not null,
Lscore int default 0,
Vid int,
LregistrationTime date,
foreign key(Vid)references Viptype(Vid)
);
create table Employee(/*员工表*/
Eno int primary key AUTO_INCREMENT,
Ename char(20) not null,
Eage smallint not null,
Esex char(2) not null,
Epassword char(32),
Pid int not null,
Eaddr varchar(50) not null,
Ephone char(16) not null,
EhireDate date,
foreign key(Pid)references PrimissionType(Pid)
);

CREATE TABLE RoomType										/*房间类型表*/
(
  Typeno int AUTO_INCREMENT,								/*类型编号(自增)*/
  Typename char(10) not null,								/*类型名称*/
	Clockprice float not null check(Clockprice>=0),			/*人民币每小时*/
	Dayprice float not null check(Dayprice>=0),				/*人民币每天*/
  Area float not null check(Area>=0),						/*面积*/
  Capacity tinyint check(Capacity>=0),			/*容纳人数*/
	PRIMARY KEY(Typeno)
);
CREATE TABLE Room														/*房间信息表*/
(
	Roomno char(5),														/*房间编号*/
	Typeno int,															/*类型编号*/
	Roomfloor int check(Roomfloor>=1),									/*房间所在层*/
	Pricepercent float  default 1 check(Pricepercent BETWEEN 0 AND 1),				/*优惠折扣*/
	PRIMARY KEY (Roomno),
	FOREIGN KEY(Typeno)REFERENCES RoomType(Typeno)
);
CREATE TABLE Reservation													/*预约信息表*/
(
	Roomno char(5),															/*房间编号*/
	LidCard char(18),														/*身份证号*/
	Ordertime  DATETIME not null,												/*订房时间*/
	Maybeintime DATETIME not null,											/*预计入住时间*/
	Maybeouttime DATETIME not null,											/*预计退房时间*/
	Realouttime DATETIME,														/*实际退房时间*/
	Ordertype tinyint check(Ordertype in (0,1)),												/*预定房间类型*/
  Cost float not null, /* 费用 */
	Cashpledge float  default 0 check(Cashpledge>=0),				/*已交押金*/
	PRIMARY KEY(Roomno,LidCard,Ordertime),
	FOREIGN KEY(Roomno)REFERENCES Room(Roomno),
	FOREIGN KEY(LidCard)REFERENCES Lodger(LidCard)
);

CREATE TABLE RoomAllReservationCount(
  Roomno char(5),
  AllCount int not null default 0,
	FOREIGN KEY(Roomno)REFERENCES Room(Roomno)
);

/* 当前服务订单 */
CREATE VIEW CurrentReservation AS
SELECT Roomno, LidCard, Ordertime, Maybeintime, Maybeouttime, Ordertype, Cost, Cashpledge
FROM Reservation
WHERE Realouttime is null;

/* 历史订单记录 */
CREATE VIEW HistoryReservation AS
SELECT Roomno, LidCard, Ordertime, Maybeintime, Maybeouttime, Realouttime, Ordertype, Cost
FROM Reservation
WHERE Realouttime is not null;




/* 每个房间的当前服务订单统计 */
CREATE VIEW RoomReservationCount AS
(SELECT Room.Roomno, COUNT(CurrentReservation.Roomno) as CurrentCount
FROM Room LEFT JOIN CurrentReservation ON Room.Roomno = CurrentReservation.Roomno
GROUP BY Roomno);


/*每种房型的房间数统计 */
CREATE VIEW RoomTypeCount AS
(SELECT RoomType.Typeno, COUNT(Room.Typeno) as Count
FROM RoomType LEFT JOIN Room ON RoomType.Typeno = Room.Typeno
GROUP BY Typeno);


CREATE TRIGGER ReservationTrigger
AFTER  INSERT
ON Reservation
FOR EACH ROW
    UPDATE RoomAllReservationCount
    SET AllCount = AllCount + 1
    WHERE Roomno = NEW.Roomno;

CREATE TRIGGER RoomTriggerInsert
AFTER  INSERT
ON Room
FOR EACH ROW
INSERT INTO RoomAllReservationCount(Roomno) VALUES (NEW.Roomno);


CREATE TRIGGER RoomTriggerDelete
AFTER  DELETE
ON Room
FOR EACH ROW
DELETE FROM RoomAllReservationCount WHERE RoomAllReservationCount.Roomno = OLD.Roomno;
