CREATE TABLE customervoip
(
id INT NOT NULL AUTO_INCREMENT,
customerid INT NOT NULL,
telnumber VARCHAR(20),
voipstatus INT DEFAULT 0,
PRIMARY KEY(id)
);
