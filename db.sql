

CREATE TABLE IF NOT EXISTS login (
    rowid,
    username TEXT NOT NULL,
    email TEXT NOT NULL,
    userpwd TEXT NOT NULL,
    PRIMARY KEY ( username, email )
    );
