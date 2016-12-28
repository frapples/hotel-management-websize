create table Viptype(/*VIP类型表*/
Vid int primary key,
Vname char(20) not null,
Vdiscount numeric(3,2) not null,
VscoreMultiplier numeric(3,2) not null,
VdepositDiscount numeric(3,2) not null
);
create table PrimissionType(/*权限表*/
Pid int primary key,
Pname char(20) not null,
PchangeRoome tinyint check(PchangeRoome in(0,1)),
PchangePrice tinyint check(PchangePrice in(0,1)),
PmanageRoome tinyint check(PmanageRoome in(0,1)),
PcontactLodger tinyint
);
create table Lodger(/*住客表*/
LidCard char(18) primary key,
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
Eno int primary key,
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
