CREATE TABLE IF NOT EXISTS errorlog
(
errorid INT NOT NULL AUTO_INCREMENT,
errormsg VARCHAR(200),
moreinfo VARCHAR(1000),
filename VARCHAR(200),
functionname VARCHAR(50),
linenum INT(11),
errordate DATETIME,
sessioninfo VARCHAR(1000),
PRIMARY KEY(errorid)
);

CREATE TABLE userdetails
(
userid INT NOT NULL AUTO_INCREMENT,
username VARCHAR(100),
userpass VARCHAR(100),
firstname VARCHAR(100),
surname VARCHAR(100),
dateregistered DATETIME,
lastlogin DATETIME,
lastaction DATETIME,
customerid INT,
cellnumber VARCHAR(20),
telnumber VARCHAR(20),
inactive INT DEFAULT 0,
PRIMARY KEY(userid)
);

INSERT INTO userdetails(username, userpass, firstname, surname, dateregistered, lastlogin, lastaction) VALUES ("test@example.com", "$2y$14$jSV1IXR2tEsE2W4Zlf6B9OYWKABZuUjKcVNrU.lIeEHVYja6G8Eq.", "Test", "Example", NOW(), NOW(), NOW());

CREATE TABLE customerdetails
(
customerid INT NOT NULL AUTO_INCREMENT,
customername VARCHAR(100),
customersurname VARCHAR(100),
idnumber VARCHAR(30),
email1 VARCHAR(100),
email2 VARCHAR(100),
cell1 VARCHAR(30),
cell2 VARCHAR(30),
tel1 VARCHAR(30),
tel2 VARCHAR(30),
dateregistered DATETIME,
customernumber VARCHAR(10),
userpass VARCHAR(100),
refererid INT,
customerbalance DECIMAL(10,2),
PRIMARY KEY(customerid)
);

CREATE TABLE customerunits
(
unitid INT NOT NULL AUTO_INCREMENT,
customerid INT NOT NULL,
complexid INT NOT NULL,
unitnumber VARCHAR(10),
unitowner INT DEFAULT 0,
packageid INT NOT NULL,
tponly INT,
PRIMARY KEY(unitid)
);

CREATE TABLE customerbilling
(
billingid INT NOT NULL AUTO_INCREMENT,
customerid INT NOT NULL,
billingname VARCHAR(100),
billingcontact VARCHAR(100),
billingemail VARCHAR(100),
billingcell VARCHAR(30),
unitnumber VARCHAR(10),
streetaddress1 VARCHAR(100),
streetaddress2 VARCHAR(100),
streetaddress3 VARCHAR(100),
streetaddress4 VARCHAR(100),
streetaddress5 VARCHAR(100),
PRIMARY KEY(billingid)
);

CREATE TABLE unitstatusses
(
statusid INT NOT NULL AUTO_INCREMENT,
statusname VARCHAR(50),
parentid INT DEFAULT 0,
nextstatusid INT DEFAULT 0,
PRIMARY KEY(statusid)
);

INSERT INTO unitstatusses(statusname, parentid) VALUES ("Complex Completion", 0);
SET @UnitStatus1 := LAST_INSERT_ID();
INSERT INTO unitstatusses(statusname, parentid) VALUES ("Awaiting Installation", 0);
SET @UnitStatus2 := LAST_INSERT_ID();
INSERT INTO unitstatusses(statusname, parentid) VALUES ("Installation Completed", 0);
SET @UnitStatus3 := LAST_INSERT_ID();
INSERT INTO unitstatusses(statusname, parentid) VALUES ("Live", 0);
SET @UnitStatus4 := LAST_INSERT_ID();

UPDATE unitstatusses SET nextstatusid = @UnitStatus2 WHERE statusid = @UnitStatus1;
UPDATE unitstatusses SET nextstatusid = @UnitStatus3 WHERE statusid = @UnitStatus2;
UPDATE unitstatusses SET nextstatusid = @UnitStatus4 WHERE statusid = @UnitStatus3;

CREATE TABLE unitstatus
(
unitstatusid INT NOT NULL AUTO_INCREMENT,
unitid INT NOT NULL,
statusid INT NOT NULL,
datechanged DATETIME,
historyserial INT DEFAULT 0,
userid INT NOT NULL,
PRIMARY KEY(unitstatusid)
);

CREATE TABLE unitstatuscomments
(
commentaryid INT NOT NULL AUTO_INCREMENT,
unitstatusid INT NOT NULL,
userid INT NOT NULL,
commentary VARCHAR(1000),
datechanged DATETIME,
PRIMARY KEY(commentaryid)
);

CREATE TABLE complexdetails
(
complexid INT NOT NULL AUTO_INCREMENT,
complexname VARCHAR(100),
complexcode VARCHAR(10),
complextype INT NOT NULL,
latitude DECIMAL(9,6),
longitude DECIMAL(9,6),
unitnumber VARCHAR(10),
streetaddress1 VARCHAR(100),
streetaddress2 VARCHAR(100),
streetaddress3 VARCHAR(100),
streetaddress4 VARCHAR(100),
streetaddress5 VARCHAR(100),
precinctid INT,
suburbid INT,
areaid INT,
cityid INT,
provinceid INT,
countryid INT,
numunits INT,
dateregistered DATETIME,
vendorid INT,
agentid INT,
secagentid INT,
maid INT,
macontact VARCHAR(100),
macell VARCHAR(100),
maemail VARCHAR(100),
seccompid INT,
seccontact VARCHAR(100),
seccell VARCHAR(100),
secemail VARCHAR(100),
customerid INT,
groupid INT DEFAULT 0,
showinresults INT DEFAULT 0,
kickoff DATETIME,
subdomain VARCHAR(100),
PRIMARY KEY(complexid)
);

CREATE TABLE bodycorpcontacts
(
contactid INT NOT NULL AUTO_INCREMENT, 
complexid INT NOT NULL,
contactname VARCHAR(100),
contactsurname VARCHAR(100),
contactemail VARCHAR(100),
contactcell VARCHAR(30),
contacttel VARCHAR(30),
addedby INT,
addedwhen DATETIME,
unitnum VARCHAR(30),
designation VARCHAR(50),
PRIMARY KEY(contactid)
);

CREATE TABLE unitorders
(
orderid INT NOT NULL AUTO_INCREMENT, 
unitid INT NOT NULL, 
orderdate DATETIME, 
customerid INT NOT NULL, 
orderstatus INT NOT NULL DEFAULT 0, 
PRIMARY KEY(orderid)
);

CREATE TABLE unitorderhistory
(
historyid INT NOT NULL AUTO_INCREMENT,
orderid INT NOT NULL,
eventdescr VARCHAR(30),
eventcomment VARCHAR(250),
userid INT NOT NULL,
eventdate DATETIME,
PRIMARY KEY(historyid)
);

CREATE TABLE unitorderdetails
(
orderid INT NOT NULL AUTO_INCREMENT,
unitid INT NOT NULL, 
customerid INT NOT NULL,
orderdate DATETIME, 
packageid INT NOT NULL,
termnum INT NOT NULL,
speedid INT NOT NULL,
ontid INT NOT NULL,
vendorid INT NOT NULL,
ontcost DECIMAL(10,2),
installcost DECIMAL(10,2),
connectcost DECIMAL(10,2),
monthlycost DECIMAL(10,2),
historyserial INT NOT NULL DEFAULT 0,
activedate DATETIME,
canceldate DATETIME,
orderstatus int default 0,
po_id INT DEFAULT NULL,
networkconnectcost DECIMAL(10,2),
networkontcost DECIMAL(10,2),
networkinstallcost DECIMAL(10,2),
prevatsalesprice DECIMAL(10,2),
prevatontcost DECIMAL(10,2),
prevatconnectcost DECIMAL(10,2),
prevatmonthlycost DECIMAL(10,2),
networkmonthlycost DECIMAL(10,2),
prevatinstallcost DECIMAL(10,2),
PRIMARY KEY(orderid)
);

CREATE TABLE unitorderstatusses
(
statusid INT NOT NULL AUTO_INCREMENT,
statusname VARCHAR(50),
PRIMARY KEY(statusid)
);

INSERT INTO unitorderstatusses(statusname) VALUES ("Ordered");
INSERT INTO unitorderstatusses(statusname) VALUES ("Order PO Submitted");
INSERT INTO unitorderstatusses(statusname) VALUES ("Activated");
INSERT INTO unitorderstatusses(statusname) VALUES ("Upgrade Requested");
INSERT INTO unitorderstatusses(statusname) VALUES ("Upgrade PO Submitted");
INSERT INTO unitorderstatusses(statusname) VALUES ("Downgrade Requested");
INSERT INTO unitorderstatusses(statusname) VALUES ("Downgrade PO Submitted");
INSERT INTO unitorderstatusses(statusname) VALUES ("Cancellation Requested");
INSERT INTO unitorderstatusses(statusname) VALUES ("Cancellation PO Submitted");
INSERT INTO unitorderstatusses(statusname) VALUES ("Cancelled");

CREATE TABLE complextypes
(
typeid INT NOT NULL AUTO_INCREMENT,
typename VARCHAR(50),
PRIMARY KEY(typeid)
);

CREATE TABLE sitestatusses
(
statusid INT NOT NULL AUTO_INCREMENT,
statusname VARCHAR(50),
parentid INT DEFAULT 0,
nextstatusid INT DEFAULT 0,
PRIMARY KEY(statusid)
);

INSERT INTO sitestatusses(statusname, parentid) VALUES ("Far Off-Net", 0); -- 1 -> 2
INSERT INTO sitestatusses(statusname, parentid) VALUES ("Off-Net", 0); -- 2 -> 3
INSERT INTO sitestatusses(statusname, parentid) VALUES ("Monitoring", 0); -- 3 -> 4
INSERT INTO sitestatusses(statusname, parentid) VALUES ("Developing", 0); -- 4 -> 5
SET @SiteStatus4 := LAST_INSERT_ID();
INSERT INTO sitestatusses(statusname, parentid) VALUES ("Body Corporate / HOA Engaged", @SiteStatus4); -- 5 -> 6
INSERT INTO sitestatusses(statusname, parentid) VALUES ("Intro Meeting Held", @SiteStatus4); -- 6 -> 7
INSERT INTO sitestatusses(statusname, parentid) VALUES ("LOE Submnitted", @SiteStatus4); -- 7 -> 8
INSERT INTO sitestatusses(statusname, parentid) VALUES ("Planning", 0); -- 8 -> 9
SET @SiteStatus8 := LAST_INSERT_ID();
INSERT INTO sitestatusses(statusname, parentid) VALUES ("LOE Approved", @SiteStatus8); -- 9 -> 10
INSERT INTO sitestatusses(statusname, parentid) VALUES ("Technical Site Survey", @SiteStatus8); -- 11 -> 12
INSERT INTO sitestatusses(statusname, parentid) VALUES ("Technical Proposal Submitted", @SiteStatus8); -- 12 -> 13
INSERT INTO sitestatusses(statusname, parentid) VALUES ("MOU Submitted", @SiteStatus8); -- 13 -> 14
INSERT INTO sitestatusses(statusname, parentid) VALUES ("Technical Proposal Approved", @SiteStatus8); -- 13 -> 14
INSERT INTO sitestatusses(statusname, parentid) VALUES ("MOU Approved", @SiteStatus8); -- 14 -> 15
INSERT INTO sitestatusses(statusname, parentid) VALUES ("Business Case Approval", @SiteStatus8); -- 15 -> 16
INSERT INTO sitestatusses(statusname, parentid) VALUES ("Pending", 0); -- 16 -> 17
INSERT INTO sitestatusses(statusname, parentid) VALUES ("Building", 0); -- 17 -> 18
SET @SiteStatus17 := LAST_INSERT_ID();
INSERT INTO sitestatusses(statusname, parentid) VALUES ("Project Kick-off Meeting Held", @SiteStatus17); -- 18 -> 19
INSERT INTO sitestatusses(statusname, parentid) VALUES ("Project Start Date", @SiteStatus17); -- 19 -> 20
INSERT INTO sitestatusses(statusname, parentid) VALUES ("In progress", @SiteStatus17); -- 20 -> 22
SET @SiteStatus20 := LAST_INSERT_ID();
INSERT INTO sitestatusses(statusname, parentid) VALUES ("Live", 0); -- 21 -> 22
INSERT INTO sitestatusses(statusname, parentid) VALUES ("Project Completed", @SiteStatus20); -- 22 -> 23
INSERT INTO sitestatusses(statusname, parentid) VALUES ("Project Handed Over", @SiteStatus20);
INSERT INTO sitestatusses(statusname, parentid, nextstatusid) VALUES ("Customer Registered", 0, 1);

UPDATE sitestatusses SET nextstatusid = @SiteStatus2 WHERE statusid = @SiteStatus1;
UPDATE sitestatusses SET nextstatusid = @SiteStatus3 WHERE statusid = @SiteStatus2;
UPDATE sitestatusses SET nextstatusid = @SiteStatus4 WHERE statusid = @SiteStatus3;
UPDATE sitestatusses SET nextstatusid = @SiteStatus5 WHERE statusid = @SiteStatus4;
UPDATE sitestatusses SET nextstatusid = @SiteStatus6 WHERE statusid = @SiteStatus5;
UPDATE sitestatusses SET nextstatusid = @SiteStatus7 WHERE statusid = @SiteStatus6;
UPDATE sitestatusses SET nextstatusid = @SiteStatus8 WHERE statusid = @SiteStatus7;
UPDATE sitestatusses SET nextstatusid = @SiteStatus9 WHERE statusid = @SiteStatus8;
UPDATE sitestatusses SET nextstatusid = @SiteStatus10 WHERE statusid = @SiteStatus9;
UPDATE sitestatusses SET nextstatusid = @SiteStatus11 WHERE statusid = @SiteStatus10;
UPDATE sitestatusses SET nextstatusid = @SiteStatus12 WHERE statusid = @SiteStatus11;
UPDATE sitestatusses SET nextstatusid = @SiteStatus13 WHERE statusid = @SiteStatus12;
UPDATE sitestatusses SET nextstatusid = @SiteStatus14 WHERE statusid = @SiteStatus13;
UPDATE sitestatusses SET nextstatusid = @SiteStatus15 WHERE statusid = @SiteStatus14;
UPDATE sitestatusses SET nextstatusid = @SiteStatus16 WHERE statusid = @SiteStatus15;
UPDATE sitestatusses SET nextstatusid = @SiteStatus17 WHERE statusid = @SiteStatus16;
UPDATE sitestatusses SET nextstatusid = @SiteStatus18 WHERE statusid = @SiteStatus17;
UPDATE sitestatusses SET nextstatusid = @SiteStatus19 WHERE statusid = @SiteStatus18;
UPDATE sitestatusses SET nextstatusid = @SiteStatus20 WHERE statusid = @SiteStatus19;
UPDATE sitestatusses SET nextstatusid = @SiteStatus22 WHERE statusid = @SiteStatus20;
UPDATE sitestatusses SET nextstatusid = @SiteStatus22 WHERE statusid = @SiteStatus21;
UPDATE sitestatusses SET nextstatusid = @SiteStatus23 WHERE statusid = @SiteStatus22;

CREATE TABLE complexstatus
(
complexstatusid INT NOT NULL AUTO_INCREMENT,
complexid INT NOT NULL,
statusid INT NOT NULL,
datechanged DATETIME,
historyserial INT DEFAULT 0,
userid INT NOT NULL,
PRIMARY KEY(complexstatusid)
);

CREATE TABLE complexstatuscomments
(
commentaryid INT NOT NULL AUTO_INCREMENT,
complexstatusid INT NOT NULL,
userid INT NOT NULL,
commentary VARCHAR(1000),
datechanged DATETIME,
PRIMARY KEY(commentaryid)
);

CREATE TABLE complexnotes
(
noteid INT NOT NULL AUTO_INCREMENT,
complexid INT NOT NULL,
userid INT NOT NULL,
commentary VARCHAR(1000),
datechanged DATETIME,
PRIMARY KEY(noteid)
);

INSERT INTO complextypes(typename) VALUES ("Complex");
SET @ComplexTypeID := LAST_INSERT_ID();
INSERT INTO complextypes(typename) VALUES ("Estate");
SET @EstateTypeID := LAST_INSERT_ID();
INSERT INTO complextypes(typename) VALUES ("Cluster");
SET @ClusterTypeID := LAST_INSERT_ID();
INSERT INTO complextypes(typename) VALUES ("Free Standing House");
SET @FSHTypeID := LAST_INSERT_ID();
INSERT INTO complextypes(typename) VALUES ("Street");
INSERT INTO complextypes(typename) VALUES ("Single & Multi Dwelling");
INSERT INTO complextypes(typename) VALUES ("Shopping Centre");
INSERT INTO complextypes(typename) VALUES ("Business Park");
INSERT INTO complextypes(typename) VALUES ("Office Park");
INSERT INTO complextypes(typename) VALUES ("Office Block");
INSERT INTO complextypes(typename) VALUES ("Mixed");
INSERT INTO complextypes(typename) VALUES ("Townhouses");
INSERT INTO complextypes(typename) VALUES ("Stack Units");
INSERT INTO complextypes(typename) VALUES ("Multi-dwelling units");
INSERT INTO complextypes(typename) VALUES ("Precinct");

CREATE TABLE precinctdetails
(
precinctid INT NOT NULL AUTO_INCREMENT,
precinctname VARCHAR(100),
precinctcode VARCHAR(10),
suburbid INT,
areaid INT,
cityid INT,
provinceid INT,
countryid INT,
PRIMARY KEY(precinctid)
);

CREATE TABLE suburbdetails
(
suburbid INT NOT NULL AUTO_INCREMENT,
suburbname VARCHAR(100),
suburbcode VARCHAR(10),
areaid INT,
cityid INT,
provinceid INT,
countryid INT,
PRIMARY KEY(suburbid)
);

CREATE TABLE areadetails
(
areaid INT NOT NULL AUTO_INCREMENT,
areaname VARCHAR(100),
areacode VARCHAR(10),
cityid INT,
provinceid INT,
countryid INT,
PRIMARY KEY(areaid)
);

CREATE TABLE citydetails
(
cityid INT NOT NULL AUTO_INCREMENT,
cityname VARCHAR(100),
citycode VARCHAR(10),
provinceid INT,
countryid INT,
PRIMARY KEY(cityid)
);

CREATE TABLE provincedetails
(
provinceid INT NOT NULL AUTO_INCREMENT,
provincename VARCHAR(100),
provincecode VARCHAR(10),
countryid INT,
PRIMARY KEY(provinceid)
);

CREATE TABLE countrydetails
(
countryid INT NOT NULL AUTO_INCREMENT,
countryname VARCHAR(100),
countrycode VARCHAR(10),
PRIMARY KEY(countryid)
);

INSERT INTO countrydetails(countryname, countrycode) VALUES ("South Africa", "ZA");
SET @CountryID := LAST_INSERT_ID();
INSERT INTO provincedetails(provincename, countryid) VALUES ("Gauteng", @CountryID);
SET @ProvinceID := LAST_INSERT_ID();
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("Brakpan", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("Brits", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("Centurion", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("Hartebeespoort", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("Heidelberg", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("Johannesburg", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();
INSERT INTO areadetails(areaname, cityid, provinceid, countryid) VALUES ("Fourways", @CityID, @ProvinceID, @CountryID);
SET @AreaID := LAST_INSERT_ID();
INSERT INTO suburbdetails(suburbname, areaid, cityid, provinceid, countryid) VALUES ("Bryanston", @AreaID, @CityID, @ProvinceID, @CountryID);
INSERT INTO suburbdetails(suburbname, areaid, cityid, provinceid, countryid) VALUES ("Douglasdale", @AreaID, @CityID, @ProvinceID, @CountryID);
SET @SuburbID := LAST_INSERT_ID();
INSERT INTO suburbdetails(suburbname, areaid, cityid, provinceid, countryid) VALUES ("Lonehill", @AreaID, @CityID, @ProvinceID, @CountryID);
INSERT INTO suburbdetails(suburbname, areaid, cityid, provinceid, countryid) VALUES ("Olivedale", @AreaID, @CityID, @ProvinceID, @CountryID);
INSERT INTO areadetails(areaname, cityid, provinceid, countryid) VALUES ("Sandton", @CityID, @ProvinceID, @CountryID);
SET @AreaID := LAST_INSERT_ID();
INSERT INTO suburbdetails(suburbname, areaid, cityid, provinceid, countryid) VALUES ("Morningside", @AreaID, @CityID, @ProvinceID, @CountryID);
INSERT INTO suburbdetails(suburbname, areaid, cityid, provinceid, countryid) VALUES ("Lonehill", @AreaID, @CityID, @ProvinceID, @CountryID);
INSERT INTO suburbdetails(suburbname, areaid, cityid, provinceid, countryid) VALUES ("Bryanston", @AreaID, @CityID, @ProvinceID, @CountryID);
INSERT INTO areadetails(areaname, cityid, provinceid, countryid) VALUES ("Sandton", @CityID, @ProvinceID, @CountryID);
SET @AreaID := LAST_INSERT_ID();
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("Krugersdorp", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("Meyerton", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("Pretoria", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("Randfontein", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("Vanderbijlpark", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();
INSERT INTO provincedetails(provincename, countryid) VALUES ("Western Cape", @CountryID);
SET @ProvinceID := LAST_INSERT_ID();
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("Bellville", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("Cape Town", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("Gordon's Bay", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("Melkbosstrand", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("Somerset West", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("Stellenbosch", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();
INSERT INTO provincedetails(provincename, countryid) VALUES ("Orange Free State", @CountryID);
SET @ProvinceID := LAST_INSERT_ID();
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("Bloemfontein", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();
INSERT INTO provincedetails(provincename, countryid) VALUES ("Mpumalanga", @CountryID);
SET @ProvinceID := LAST_INSERT_ID();
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("Nelspruit", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("Secunda", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("Witbank", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();
INSERT INTO provincedetails(provincename, countryid) VALUES ("Northern Cape", @CountryID);
SET @ProvinceID := LAST_INSERT_ID();
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("Kimberley", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();
INSERT INTO provincedetails(provincename, countryid) VALUES ("Eastern Cape", @CountryID);
SET @ProvinceID := LAST_INSERT_ID();
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("East London", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("Port Elizabeth", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();
INSERT INTO provincedetails(provincename, countryid) VALUES ("Limpopo", @CountryID);
SET @ProvinceID := LAST_INSERT_ID();
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("Nirvana", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("Polokwane", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();
INSERT INTO provincedetails(provincename, countryid) VALUES ("Kwazulu Natal", @CountryID);
SET @ProvinceID := LAST_INSERT_ID();
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("Durban", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();
INSERT INTO areadetails(areaname, cityid, provinceid, countryid) VALUES ("Amanzimtoti", @CityID, @ProvinceID, @CountryID);
INSERT INTO areadetails(areaname, cityid, provinceid, countryid) VALUES ("Berea", @CityID, @ProvinceID, @CountryID);
INSERT INTO areadetails(areaname, cityid, provinceid, countryid) VALUES ("Kloof", @CityID, @ProvinceID, @CountryID);
INSERT INTO areadetails(areaname, cityid, provinceid, countryid) VALUES ("New Germany", @CityID, @ProvinceID, @CountryID);
INSERT INTO areadetails(areaname, cityid, provinceid, countryid) VALUES ("Sherwood", @CityID, @ProvinceID, @CountryID);
INSERT INTO areadetails(areaname, cityid, provinceid, countryid) VALUES ("Umhlanga Ridge", @CityID, @ProvinceID, @CountryID);
INSERT INTO citydetails(cityname, provinceid, countryid) VALUES ("Pinetown", @ProvinceID, @CountryID);
SET @CityID := LAST_INSERT_ID();

CREATE TABLE vendordetails
(
vendorid INT NOT NULL AUTO_INCREMENT,
vendorname VARCHAR(100),
PRIMARY KEY(vendorid)
);

INSERT INTO vendordetails(vendorname) VALUES ("Metro Fibre");
INSERT INTO vendordetails(vendorname) VALUES ("Link Africa");
INSERT INTO vendordetails(vendorname) VALUES ("SADV");

CREATE TABLE managingagentdetails
(
maid INT NOT NULL AUTO_INCREMENT,
agentname VARCHAR(100),
PRIMARY KEY(maid)
);

CREATE TABLE securitycompanydetails
(
secid INT NOT NULL AUTO_INCREMENT,
secname VARCHAR(100),
PRIMARY KEY(secid)
);

CREATE TABLE packagetypes
(
packagetypeid INT NOT NULL AUTO_INCREMENT,
packagetypename VARCHAR(50),
PRIMARY KEY(packagetypeid)
);

INSERT INTO packagetypes(packagetypename) VALUES ("TP Only");
INSERT INTO packagetypes(packagetypename) VALUES ("Lite");
INSERT INTO packagetypes(packagetypename) VALUES ("Swift");
INSERT INTO packagetypes(packagetypename) VALUES ("Zoom");
INSERT INTO packagetypes(packagetypename) VALUES ("Supersonic");
INSERT INTO packagetypes(packagetypename) VALUES ("Express");
INSERT INTO packagetypes(packagetypename) VALUES ("Hustle");
INSERT INTO packagetypes(packagetypename) VALUES ("Pace");
INSERT INTO packagetypes(packagetypename) VALUES ("Velocity");

CREATE TABLE packagespeeds
(
speedid INT NOT NULL AUTO_INCREMENT,
speedname VARCHAR(50),
thespeed VARCHAR(50),
PRIMARY KEY(speedid)
);

INSERT INTO packagespeeds(speedname) VALUES ("10Mbps");
INSERT INTO packagespeeds(speedname) VALUES ("25Mbps");
INSERT INTO packagespeeds(speedname) VALUES ("50Mbps");
INSERT INTO packagespeeds(speedname) VALUES ("100Mbps");
INSERT INTO packagespeeds(speedname) VALUES ("200Mbps");

CREATE TABLE onttypes
(
ontid INT NOT NULL AUTO_INCREMENT,
ontname VARCHAR(50),
ontcost DECIMAL(10,2),
PRIMARY KEY(ontid)
);

INSERT INTO onttypes(ontname, ontcost) VALUES ("Midrange", "1000");
INSERT INTO onttypes(ontname, ontcost) VALUES ("GigaCentre", "2000");
INSERT INTO onttypes(ontname, ontcost) VALUES ("Basic", "500");
INSERT INTO onttypes(ontname, ontcost) VALUES ("Home Basic A", "500");
INSERT INTO onttypes(ontname, ontcost) VALUES ("Home Basic B", "750");
INSERT INTO onttypes(ontname, ontcost) VALUES ("Business Basic A", "500");
INSERT INTO onttypes(ontname, ontcost) VALUES ("Business Basic B", "750");
INSERT INTO onttypes(ontname, ontcost) VALUES ("Gigahub", "500");

CREATE TABLE packagegroups
(
packagegroupid INT NOT NULL AUTO_INCREMENT,
packagegroupname VARCHAR(50),
vendorspecific INT DEFAULT 0,
complextypespecific INT DEFAULT 0,
showontid INT DEFAULT 0,
showmonthsterm INT DEFAULT 0,
showspeed INT DEFAULT 0,
showinstallcost INT DEFAULT 0,
showdevicefee INT DEFAULT 0,
showconnectcost INT DEFAULT 0,
showmonthlycost INT DEFAULT 0,
PRIMARY KEY(packagegroupid)
);

INSERT INTO packagegroups(packagegroupname, vendorspecific, complextypespecific, showmonthsterm, showspeed, showinstallcost, showdevicefee, showconnectcost, showmonthlycost) VALUES ("Connectivity", 1, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO packagegroups(packagegroupname, vendorspecific, complextypespecific, showmonthsterm, showspeed, showinstallcost, showdevicefee, showconnectcost, showmonthlycost) VALUES ("VOIP", 0, 0, 0, 0, 1, 0, 0, 1);
INSERT INTO packagegroups(packagegroupname, vendorspecific, complextypespecific, showmonthsterm, showspeed, showinstallcost, showdevicefee, showconnectcost, showmonthlycost) VALUES ("Static IP", 0, 0, 0, 0, 1, 0, 0, 1);

CREATE TABLE packagedetails
(
packageid INT NOT NULL AUTO_INCREMENT,
packagegroupid INT NOT NULL,
packagename VARCHAR(50),
vendorid INT NOT NULL,
complextype INT NOT NULL,
ontid INT,
monthsterm INT,
speedid INT,
packagetype INT DEFAULT 0,
ontcost DECIMAL(10,2),
installcost DECIMAL(10,2),
devicefee DECIMAL(10,2),
connectcost DECIMAL(10,2),
monthlycost DECIMAL(10,2),
costprice DECIMAL(10,2),
isinactive INT DEFAULT 0,
PRIMARY KEY(packageid)
);

INSERT INTO packagedetails(packagename, packagegroupid, vendorid, complextype, ontid, monthsterm, speedid, ontcost, installcost, monthlycost, costprice) VALUES ("TP Only", 1, 1, 2, 0, 0, 0, "", "", "", "");

CREATE TABLE unitpackagedetails
(
unitpackageid INT NOT NULL AUTO_INCREMENT,
packageid INT NOT NULL, 
packagegroupid INT NOT NULL, 
packagename VARCHAR(50), 
packagetype INT DEFAULT 0, 
vendorid INT NOT NULL, 
isinactive INT DEFAULT 0, 
unitid INT NOT NULL, 
registerdate DATETIME, 
tpoption INT DEFAULT 0, 
orderid INT NOT NULL, 
historyserial INT NOT NULL DEFAULT 0,
PRIMARY KEY(unitpackageid)
);

CREATE TABLE unitpackagepieces
(
unitpieceid INT NOT NULL AUTO_INCREMENT,
unitpackageid INT NOT NULL,
pieceid INT NOT NULL,
packageid INT NOT NULL,
speedid INT,
ontid INT,
extraid INT,
piecesnummonths INT,
piecescost DECIMAL(10,2),
endcontinues INT DEFAULT 0,
piecescomms DECIMAL(10,2),
PRIMARY KEY(unitpieceid)
);


CREATE TABLE packagelineextras
(
extraid INT NOT NULL AUTO_INCREMENT,
extraname VARCHAR(30),
costprice DECIMAL(10,2),
PRIMARY KEY(extraid)
);

CREATE TABLE usermailbox
(
mailid INT NOT NULL AUTO_INCREMENT,
receiverid INT NOT NULL,
senderid INT NOT NULL,
sentwhen DATETIME,
openedwhen DATETIME,
subject VARCHAR(255),
priority INT DEFAULT 0,
msgbody TEXT,
threadid INT,
msgstatus INT,
PRIMARY KEY(mailid)
);

CREATE TABLE complexgroups
(
groupid INT NOT NULL AUTO_INCREMENT,
groupname VARCHAR(50),
PRIMARY KEY(groupid)
);

CREATE TABLE systemprivileges
(
privilegeid INT NOT NULL AUTO_INCREMENT,
privilegename VARCHAR(50),
PRIMARY KEY(privilegeid)
);

CREATE TABLE userprivileges
(
userid INT NOT NULL,
privilegeid INT NOT NULL
);

CREATE TABLE complexunitmap
(
mapid INT NOT NULL AUTO_INCREMENT,
complexid INT NOT NULL,
unitid INT NOT NULL,
customerid INT NOT NULL,
unitdesc VARCHAR(50),
hoaunit INT NOT NULL,
PRIMARY KEY(mapid)
);

CREATE TABLE vendorspeeds
(
speedid INT NOT NULL,
vendorid INT NOT NULL,
costprice DECIMAL(10,2),
isinactive INT DEFAULT 0,
historyserial INT DEFAULT 0
);

CREATE TABLE vendoronts
(
ontid INT NOT NULL,
vendorid INT NOT NULL,
costprice DECIMAL(10,2),
isinactive INT NOT NULL DEFAULT 0,
historyserial INT DEFAULT 0
);

CREATE TABLE packagepieces
(
pieceid INT NOT NULL AUTO_INCREMENT,
packageid INT NOT NULL,
speedid INT,
ontid INT,
extraid INT,
piecesnummonths INT,
piecescost DECIMAL(10,2),
endcontinues INT DEFAULT 0,
piecescomms DECIMAL(10,2),
PRIMARY KEY(pieceid)
);

CREATE TABLE complexdocuments
(
documentid INT NOT NULL AUTO_INCREMENT,
complexid INT NOT NULL,
userid INT NOT NULL,
uploadtime DATETIME,
filename VARCHAR(30),
filepath VARCHAR(255),
doctype INT,
PRIMARY KEY(documentid)
);

CREATE TABLE registercustomers
(
registerid INT NOT NULL AUTO_INCREMENT,
customername VARCHAR(100),
customercell VARCHAR(100),
customeremail VARCHAR(100),
complexid INT NOT NULL,
PRIMARY KEY(registerid)
);

CREATE TABLE salesoperationsprocess
(
stepid INT NOT NULL AUTO_INCREMENT,
stepname VARCHAR(100),
steporder INT,
PRIMARY KEY(stepid)
);

INSERT INTO salesoperationsprocess(stepname, steporder) VALUES ("Lead", 1);
INSERT INTO salesoperationsprocess(stepname, steporder) VALUES ("Contact made", 2);
INSERT INTO salesoperationsprocess(stepname, steporder) VALUES ("Bodycorp meeting", 3);
INSERT INTO salesoperationsprocess(stepname, steporder) VALUES ("Bodycorp meeting feedback", 4);
INSERT INTO salesoperationsprocess(stepname, steporder) VALUES ("Engagement letter returned", 5);
INSERT INTO salesoperationsprocess(stepname, steporder) VALUES ("Site survey", 6);
INSERT INTO salesoperationsprocess(stepname, steporder) VALUES ("Site survey feedback", 7);
INSERT INTO salesoperationsprocess(stepname, steporder) VALUES ("Site plan returned", 8);
INSERT INTO salesoperationsprocess(stepname, steporder) VALUES ("MOU returned", 9);
INSERT INTO salesoperationsprocess(stepname, steporder) VALUES ("Gathering Interest", 10);
INSERT INTO salesoperationsprocess(stepname, steporder) VALUES ("Bus. Case Approved", 11);
INSERT INTO salesoperationsprocess(stepname, steporder) VALUES ("Build dates set", 12);
INSERT INTO salesoperationsprocess(stepname, steporder) VALUES ("Kick Off", 13);

CREATE TABLE salesoperationscomplex
(
complexid INT NOT NULL,
stepid INT NOT NULL,
datecompleted DATETIME
);

CREATE TABLE meetingtypes
(
typeid INT NOT NULL AUTO_INCREMENT,
typename VARCHAR(100),
PRIMARY KEY(typeid)
);

INSERT INTO meetingtypes(typename) VALUES ("Bodycorp Meeting");
INSERT INTO meetingtypes(typename) VALUES ("Site Survey");
INSERT INTO meetingtypes(typename) VALUES ("Marketing Drive");
INSERT INTO meetingtypes(typename) VALUES ("Support Meeting");
INSERT INTO meetingtypes(typename) VALUES ("Sales Meeting");

CREATE TABLE meetingdetails
(
meetingid INT NOT NULL AUTO_INCREMENT,
complexid INT,
customerid INT,
meetingtypeid INT NOT NULL,
completed INT NOT NULL DEFAULT 0,
starttime DATETIME,
endtime DATETIME,
setupuser INT NOT NULL,
completeduser INT,
PRIMARY KEY(meetingid)
);

CREATE TABLE meetingdiary
(
diaryid INT NOT NULL AUTO_INCREMENT,
meetingid INT NOT NULL,
userid INT NOT NULL,
attendance INT DEFAULT 0,
PRIMARY KEY(diaryid)
);

CREATE TABLE fibrepackages
(
packageid INT NOT NULL AUTO_INCREMENT,
packagename VARCHAR(30),
termnum INT NOT NULL,
speedid INT NOT NULL,
ontid INT NOT NULL,
vendorid INT NOT NULL,
ontcost DECIMAL(10,2),
connectcost DECIMAL(10,2),
monthlycost DECIMAL(10,2),
costprice DECIMAL(10,2),
networkconnectcost DECIMAL(10,2),
networkontcost DECIMAL(10,2),
networkinstallcost DECIMAL(10,2),
prevatontcost DECIMAL(10,2),
prevatconnectcost DECIMAL(10,2),
prevatmonthlycost DECIMAL(10,2),
networkmonthlycost DECIMAL(10,2),
prevatinstallcost DECIMAL(10,2),
installcost DECIMAL(10,2),
PRIMARY KEY(packageid)
);

INSERT INTO fibrepackages(termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, packagename) VALUES (0, 0,0,1,"0", "0","0", "TP Only");
INSERT INTO fibrepackages(termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, packagename) VALUES (1, 1,2,1,"570", "999","665", "Surf 10");
INSERT INTO fibrepackages(termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, packagename) VALUES (1, 1,8,1,"1950","999","665", "Surf 10");
INSERT INTO fibrepackages(termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, packagename) VALUES (12,1,2,1,"570", "999","631", "Surf 10");
INSERT INTO fibrepackages(termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, packagename) VALUES (12,1,8,1,"1950","999","631", "Surf 10");
INSERT INTO fibrepackages(termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, packagename) VALUES (1, 2,2,1,"570", "999","960", "Flix 25");
INSERT INTO fibrepackages(termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, packagename) VALUES (1, 2,8,1,"1950","999","960", "Flix 25");
INSERT INTO fibrepackages(termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, packagename) VALUES (12,2,2,1,"0",   "999","912", "Flix 25");
INSERT INTO fibrepackages(termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, packagename) VALUES (12,2,8,1,"0",   "999","912", "Flix 25");
INSERT INTO fibrepackages(termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, packagename) VALUES (1, 3,2,1,"570", "999","1250", "Gamer 50");
INSERT INTO fibrepackages(termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, packagename) VALUES (1, 3,8,1,"1950","999","1250", "Gamer 50");
INSERT INTO fibrepackages(termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, packagename) VALUES (12,3,2,1,"0",   "999","1187", "Gamer 50");
INSERT INTO fibrepackages(termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, packagename) VALUES (12,3,8,1,"0",   "999","1187", "Gamer 50");
INSERT INTO fibrepackages(termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, packagename) VALUES (1, 4,2,1,"570", "999","1800", "Family 100");
INSERT INTO fibrepackages(termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, packagename) VALUES (1, 4,8,1,"1950","999","1800", "Family 100");
INSERT INTO fibrepackages(termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, packagename) VALUES (12,4,2,1,"0",   "999","1710", "Family 100");
INSERT INTO fibrepackages(termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, packagename) VALUES (12,4,8,1,"0",   "999","1710", "Family 100");
INSERT INTO fibrepackages(termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, packagename) VALUES (1, 5,2,1,"570", "999","3400", "Ultra 200");
INSERT INTO fibrepackages(termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, packagename) VALUES (1, 5,8,1,"1950","999","3400", "Ultra 200");
INSERT INTO fibrepackages(termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, packagename) VALUES (12,5,2,1,"0",   "999","3230", "Ultra 200");
INSERT INTO fibrepackages(termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, packagename) VALUES (12,5,8,1,"0",   "999","3230", "Ultra 200");

CREATE TABLE unitduplicates
(
curunitid INT NOT NULL, 
newunitid INT NOT NULL, 
curcustomerid INT NOT NULL, 
newcustomerid INT NOT NULL, 
clashwhen DATETIME
);

CREATE TABLE passwordresets
(
resetid INT NOT NULL AUTO_INCREMENT,
customerid INT NOT NULL,
requested DATETIME,
randkey INT,
timekey INT,
PRIMARY KEY(resetid)
);