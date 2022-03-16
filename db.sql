PRAGMA foreign_keys = ON;
CREATE TABLE IF NOT EXISTS verified_users (
  username TEXT NOT NULL UNIQUE ,
  email TEXT NOT NULL UNIQUE,
  userpwd TEXT NOT NULL,
  PRIMARY KEY (rowid));

  CREATE TABLE IF NOT EXISTS pending_users(
  username TEXT NOT NULL UNIQUE,
  email TEXT NOT NULL UNIQUE,
  creation_date datetime DEFAULT NULL,
  activation_code   varchar(255) NOT NULL,
  activation_expiry datetime     NOT NULL,
  PRIMARY KEY (rowid));
