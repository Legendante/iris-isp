CREATE TABLE purchaseorders
(
po_id INT NOT NULL AUTO_INCREMENT,
po_type INT NOT NULL,
po_created DATETIME,
po_status INT NOT NULL DEFAULT 0,
PRIMARY KEY(po_id)
);

CREATE TABLE purchaseorderorders
(
po_id INT NOT NULL,
orderid INT NOT NULL,
created DATETIME
);

CREATE TABLE purchaseordertypes
(
typeid INT NOT NULL AUTO_INCREMENT,
typename VARCHAR(100),
PRIMARY KEY(typeid)
);

INSERT INTO purchaseordertypes(typename) VALUES ("New Order");
INSERT INTO purchaseordertypes(typename) VALUES ("Cancellation");
INSERT INTO purchaseordertypes(typename) VALUES ("Upgrades");
INSERT INTO purchaseordertypes(typename) VALUES ("Downgrades");