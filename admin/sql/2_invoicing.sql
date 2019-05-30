CREATE TABLE invoices
(
invoiceid INT NOT NULL AUTO_INCREMENT,
customerid INT NOT NULL,
orderid INT,
invoicedate DATETIME,
vattotal DECIMAL(10,2),
nonvattotal DECIMAL(10,2),
datestart DATE,
dateend DATE,
invoicecnt INT NOT NULL DEFAULT 1,
invstatus INT DEFAULT 0 COMMENT 'Links to invoicestatusses',
invtype INT DEFAULT 0 COMMENT '0 = fibre, 1 = other',
filepath VARCHAR(255),
outstanding DECIMAL(10,2),
PRIMARY KEY(invoiceid)
);

CREATE TABLE invoiceitems
(
itemid INT NOT NULL AUTO_INCREMENT,
invoiceid INT NOT NULL,
unitid INT,
vattotal DECIMAL(10,2),
nonvatotal DECIMAL(10,2),
itemqty INT NOT NULL DEFAULT 1,
itemdesc VARCHAR(30),
PRIMARY KEY(itemid)
);

CREATE TABLE invoicestatusses
(
statusid INT NOT NULL AUTO_INCREMENT,
statusname VARCHAR(20),
PRIMARY KEY(statusid)
);

INSERT INTO invoicestatusses(statusname) VALUES ('Generated');
INSERT INTO invoicestatusses(statusname) VALUES ('Awaiting send');
INSERT INTO invoicestatusses(statusname) VALUES ('Unpaid');
INSERT INTO invoicestatusses(statusname) VALUES ('Partially Paid');
INSERT INTO invoicestatusses(statusname) VALUES ('Paid');
INSERT INTO invoicestatusses(statusname) VALUES ('Cancelled');
INSERT INTO invoicestatusses(statusname) VALUES ('Queried');

CREATE TABLE customerbalances
(
customerid INT NOT NULL,
owingamount DECIMAL(10,2),
referamount DECIMAL(10,2)
);

CREATE TABLE creditnotes
(
creditid INT NOT NULL AUTO_INCREMENT,
customerid INT NOT NULL,
creditby INT NOT NULL,
creditdate DATETIME,
creditamount DECIMAL(10,2),
creditstatus INT DEFAULT 0,
creditdescription VARCHAR(1000),
PRIMARY KEY(creditid)
);

CREATE TABLE fundaccounts
(
accountid INT NOT NULL AUTO_INCREMENT,
accountname VARCHAR(30),
accountbalance DECIMAL(10,2) DEFAULT 0,
PRIMARY KEY(accountid)
);

CREATE TABLE fundaccounttransactions
(
id INT NOT NULL AUTO_INCREMENT,
transactiondate DATETIME,
accountid INT NOT NULL,
transactionamount DECIMAL(10,2) DEFAULT 0,
customerid INT NOT NULL,
supplierid INT NOT NULL,
PRIMARY KEY(id)
);

CREATE TABLE suppliers
(
supplierid INT NOT NULL AUTO_INCREMENT,
suppliername VARCHAR(100),
suppliervatnum VARCHAR(20),
supplierregnum VARCHAR(20),
supplieraddress VARCHAR(200),
supplieremail VARCHAR(50),
suppliertel VARCHAR(20),
supplierbalance DECIMAL(10,2) DEFAULT 0,
PRIMARY KEY(supplierid)
);
