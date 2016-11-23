DROP DATABASE IF EXISTS cachet;
CREATE DATABASE cachet;
USE cachet;
CREATE TABLE users (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	username varchar(64) NOT NULL,
	password char(128) NOT NULL,
	pin char(128) NOT NULL,
	pubkey varchar(10240) NOT NULL,
	admin INT NOT NULL DEFAULT 0
);

CREATE TABLE messages (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	message varchar(10240),
	toID INT NOT NULL,
	fromID INT NOT NULL,
	subject varchar(64) NOT NULL,
	timesent timestamp DEFAULT CURRENT_TIMESTAMP,
	msg_read TINYINT(1) NOT NULL DEFAULT 0,
	display TINYINT(1) NOT NULL DEFAULT 1,
	FOREIGN KEY (toID) REFERENCES users(id),
	FOREIGN KEY (fromID) REFERENCES users(id)
);

CREATE TABLE logins (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	success TINYINT(1) NOT NULL,
	username varchar(10240),
	password varchar(10240),
	remote_ip varchar(200),
	remote_port varchar(16),
	user_agent varchar(1024),
	cookies varchar(10240),
	timesubmitted timestamp
);

CREATE TABLE pubkey_fails (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	userid INT NOT NULL,
	remote_ip varchar(200),
	remote_port varchar(16),
	user_agent varchar(1024),
	cookies varchar(10240),
	post_vars varchar(20000),
	get_vars varchar(10240),
	timesubmitted timestamp
);

