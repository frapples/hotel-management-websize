insert into Viptype values(1,'一级VIP',0.90,0.50,0.50);
insert into Viptype values(2,'二级VIP',0.80,0.60,0.60);
insert into Viptype values(3,'三级VIP',0.75,0.70,0.70);
insert into Viptype values(4,'四级VIP',0.70,0.75,0.75);
insert into PrimissionType values(1,'一级权限',0,0,1,1);
insert into PrimissionType values(2,'二级权限',0,1,0,1);
insert into PrimissionType values(3,'三级权限',0,1,1,1);
insert into PrimissionType values(4,'四级权限',1,1,1,1);
insert into Employee values(1,'张晶',18,'女','8DDCFF3A80F4189CA1C9D4D902C3C909',1,'北京王府井','13765738567','2016-5-17');
insert into Employee values(2,'王云',18,'女','8DDCFF3A80F4189CA1C9D4D902C3C909',1,'安徽宣城宣州区','13766858557','2017-5-30');
insert into Employee values(3,'张猛',22,'男','8DDCFF3A80F4189CA1C9D4D902C3C909',2,'内蒙宁城县','13565638987','2014-7-19');
insert into Employee values(4,'王峰',25,'男','8DDCFF3A80F4189CA1C9D4D902C3C909',2,'北京丰台区','13765938567','2014-3-17');
insert into Employee values(5,'马超',25,'男','8DDCFF3A80F4189CA1C9D4D902C3C909',3,'安徽合肥市王府井','13765679047','2017-5-17');
insert into Employee values(6,'孙梅',30,'女','8DDCFF3A80F4189CA1C9D4D902C3C909',3,'浙江杭州','13765739468','2016-5-17');
insert into Employee values(7,'王怡',27,'女','8DDCFF3A80F4189CA1C9D4D902C3C909',4,'安徽宣城','18775639629','2013-8-22');
insert into Lodger values('678432578052507538','张英',23,'男','8DDCFF3A80F4189CA1C9D4D902C3C909','13698753570',0,1,'2016-1-27');
insert into Lodger values('678432507350687558','张云',54,'男','8DDCFF3A80F4189CA1C9D4D902C3C909','13678523470',200,2,'2017-1-27');
insert into Lodger values('678432578052572056','李琦',23,'女','8DDCFF3A80F4189CA1C9D4D902C3C909','13698738648',300,2,'2016-5-26');
insert into Lodger values('678438346582945628','赵云',26,'男','8DDCFF3A80F4189CA1C9D4D902C3C909','13896983687',100,1,'2017-8-27');
insert into Lodger values('123412128052507535','王娜',21,'女','8DDCFF3A80F4189CA1C9D4D902C3C909','16937484758',0,1,'2016-7-17');
insert into Lodger values('192641782238207538','欧阳一丹',23,'女','8DDCFF3A80F4189CA1C9D4D902C3C909','18679740769',700,3,'2014-6-16');
insert into Lodger values('852372738274623530','李美琪',35,'女','8DDCFF3A80F4189CA1C9D4D902C3C909','18875628324',1000,4,'2013-2-18');
insert into Lodger values('235434248052507533','孙一海',46,'男','8DDCFF3A80F4189CA1C9D4D902C3C909','13276588763',500,3,'2014-1-1');


INSERT INTO RoomType(Typename,Clockprice,Dayprice,Area,Capacity) VALUES('单人小间房',10,70,10,1);
INSERT INTO RoomType(Typename,Clockprice,Dayprice,Area,Capacity) VALUES('标准单人房',15,90,25,1);
INSERT INTO RoomType(Typename,Clockprice,Dayprice,Area,Capacity) VALUES('双人大床房',20,120,40,2);
INSERT INTO RoomType(Typename,Clockprice,Dayprice,Area,Capacity) VALUES('标准双人房',20,130,60,2);
INSERT INTO RoomType(Typename,Clockprice,Dayprice,Area,Capacity) VALUES('情侣温馨房',30,150,45,2);
INSERT INTO RoomType(Typename,Clockprice,Dayprice,Area,Capacity) VALUES('古风典韵套房',50,300,100,4);
INSERT INTO RoomType(Typename,Clockprice,Dayprice,Area,Capacity) VALUES('欧式豪华套房',50,300,120,6);
INSERT INTO RoomType(Typename,Clockprice,Dayprice,Area,Capacity) VALUES('总统套房',80,500,220,10);

INSERT INTO Room(Roomno,Typeno,Roomfloor,Pricepercent) VALUES('101',1,1,0.97);
INSERT INTO Room(Roomno,Typeno,Roomfloor,Pricepercent) VALUES('102',2,1,0.96);
INSERT INTO Room(Roomno,Typeno,Roomfloor,Pricepercent) VALUES('103',3,1,1);
INSERT INTO Room(Roomno,Typeno,Roomfloor,Pricepercent) VALUES('104',4,1,0.95);
INSERT INTO Room(Roomno,Typeno,Roomfloor,Pricepercent) VALUES('105',5,1,1);
INSERT INTO Room(Roomno,Typeno,Roomfloor,Pricepercent) VALUES('106',6,1,1);
INSERT INTO Room(Roomno,Typeno,Roomfloor,Pricepercent) VALUES('107',7,1,1);
INSERT INTO Room(Roomno,Typeno,Roomfloor,Pricepercent) VALUES('108',1,1,0.95);
INSERT INTO Room(Roomno,Typeno,Roomfloor,Pricepercent) VALUES('109',2,1,1);
INSERT INTO Room(Roomno,Typeno,Roomfloor,Pricepercent) VALUES('110',3,1,1);
INSERT INTO Room(Roomno,Typeno,Roomfloor,Pricepercent) VALUES('201',4,2,0.8);
INSERT INTO Room(Roomno,Typeno,Roomfloor,Pricepercent) VALUES('302',5,3,1);
INSERT INTO Room(Roomno,Typeno,Roomfloor,Pricepercent) VALUES('403',6,4,1);


INSERT INTO Reservation(Roomno,LidCard,Ordertime,Maybeintime,Maybeouttime,Realouttime,Ordertype,Cashpledge, Cost)
VALUES('101','678432578052507538','2017-01-04 09:11:00','2017-01-04 11:00:00','2017-01-06 15:00:00',null, 1, 100, 200);
INSERT INTO Reservation(Roomno,LidCard,Ordertime,Maybeintime,Maybeouttime,Realouttime,Ordertype,Cashpledge, Cost)
VALUES('102','678432507350687558','2017-01-04 09:12:00','2017-01-04 11:00:00','2017-01-06 15:00:00',null,1, 0, 150);
INSERT INTO Reservation(Roomno,LidCard,Ordertime,Maybeintime,Maybeouttime,Realouttime,Ordertype,Cashpledge, Cost)
VALUES('103','678432578052572056','2017-01-03 12:20:00','2017-01-04 11:00:00','2017-01-05 15:00:00',null,1,100, 140);
INSERT INTO Reservation(Roomno,LidCard,Ordertime,Maybeintime,Maybeouttime,Realouttime,Ordertype,Cashpledge, Cost)
VALUES('104','678438346582945628','2017-01-05 01:20:00','2017-01-05 11:30:00','2017-01-05 16:00:00',null,0,100, 160);
INSERT INTO Reservation(Roomno,LidCard,Ordertime,Maybeintime,Maybeouttime,Realouttime,Ordertype,Cashpledge, Cost)
VALUES('110','123412128052507535','2017-01-01 02:20:00','2017-01-02 10:20:00','2017-01-04 10:20:00',null,1,0, 300);
INSERT INTO Reservation(Roomno,LidCard,Ordertime,Maybeintime,Maybeouttime,Realouttime,Ordertype,Cashpledge, Cost)
VALUES('201','192641782238207538','2017-01-01 03:20:00','2017-01-02 10:20:00','2017-01-04 10:20:00', '2017-01-01 10:20:00', 1,1000, 310);
INSERT INTO Reservation(Roomno,LidCard,Ordertime,Maybeintime,Maybeouttime,Realouttime,Ordertype,Cashpledge, Cost)
VALUES('403','852372738274623530','2017-01-01 04:20:00','2017-01-04 11:00:00','2017-01-05 15:00:00',null,1,5000, 45);
INSERT INTO Reservation(Roomno,LidCard,Ordertime,Maybeintime,Maybeouttime,Realouttime,Ordertype,Cashpledge, Cost)
VALUES('102','678438346582945628','2017-01-01 05:20:00','2017-01-02 10:20:00','2017-01-04 10:20:00',null,0,100, 52);
INSERT INTO Reservation(Roomno,LidCard,Ordertime,Maybeintime,Maybeouttime,Realouttime,Ordertype,Cashpledge, Cost)
VALUES('103','678438346582945628','2017-01-01 06:20:00','2017-01-02 10:20:00','2017-01-04 10:20:00',null,0,100, 128);
INSERT INTO Reservation(Roomno,LidCard,Ordertime,Maybeintime,Maybeouttime,Realouttime,Ordertype,Cashpledge, Cost)
VALUES('110','678438346582945628','2017-01-01 07:20:00','2017-01-02 10:20:00','2017-01-04 10:20:00',null,0,100, 256);
INSERT INTO Reservation(Roomno,LidCard,Ordertime,Maybeintime,Maybeouttime,Realouttime,Ordertype,Cashpledge, Cost)
VALUES('101','678438346582945628','2016-01-01 08:20:00','2017-01-02 10:20:00','2017-01-04 10:20:00',null,0,100, 123);



