CREATE USER 'dba'@'localhost' IDENTIFIED BY 'YupThisIsDaNewDBP4$$';
#CREATE USER 'find-dba'@'localhost' IDENTIFIED BY 'YupThisIsDaFindP4$$';
GRANT SELECT,INSERT,UPDATE,DELETE on cachet.* to 'dba'@'localhost';
#GRANT SELECT on cachet.users to 'find-dba'@'localhost';
FLUSH PRIVILEGES;

