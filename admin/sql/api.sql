CREATE TABLE apiSystem
(
id INT NOT NULL AUTO_INCREMENT,
systemname VARCHAR(50),
code1 VARCHAR(16),
code2 VARCHAR(16),
code3 VARCHAR(16),
PRIMARY KEY(id)
);

INSERT INTO apiSystem(systemname, code1, code2, code3) VALUES ("Fiona", "ASDASDFASDF", "2345dsfgWER345", "gs234SFDGvsdfd43");

CREATE TABLE apiComplexIDs
(
systemid INT NOT NULL,
localid INT NOT NULL,
remoteid INT NOT NULL
);

CREATE TABLE apiUnitIDs
(
systemid INT NOT NULL,
localid INT NOT NULL,
remoteid INT NOT NULL
);