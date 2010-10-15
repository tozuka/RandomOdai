DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id                 INTEGER PRIMARY KEY, /* twitter の user idをそのまま */
  name               TEXT,
  screenname         TEXT,
  location           TEXT,
  description        TEXT,
  profile_image_url  BLOB,
  protected          INTEGER DEFAULT 0,
  is_spam            INTEGER DEFAULT 0
);

DROP TABLE IF EXISTS tweets;
CREATE TABLE tweets (
  id                     INTEGER PRIMARY KEY, /* twitterのtweet idをそのまま */
  created_at             INTEGER DEFAULT NULL, /* unix time */
  text                   TEXT NOT NULL,
  in_reply_to_status_id  INTEGER DEFAULT NULL,
  in_reply_to_user_id    INTEGER DEFAULT NULL,
  user_id                INTEGER DEFAULT NULL,
  issue_id               INTEGER DEFAULT NULL,
  status                 INTEGER DEFAULT 0, /* 要返答とか */
  is_mine                INTEGER DEFAULT 0  /* bool */
);

DROP TABLE IF EXISTS issues;
CREATE TABLE issues (
  id                INTEGER PRIMARY KEY,
  subject           TEXT,
  description       TEXT,
  status            INTEGER DEFAULT 0
);

DROP TABLE IF EXISTS odais;
CREATE TABLE odais (
  id         INTEGER PRIMARY KEY,
  odai       TEXT NOT NULL,
  is_valid   INTEGER DEFAULT 1,
  author_id  INTEGER DEFAULT NULL,
  ninki      INTEGER DEFAULT 0
);

DROP TABLE IF EXISTS my_tweets;
CREATE TABLE my_tweets (
  id                     INTEGER PRIMARY KEY, /* twitterのtweet idをそのまま */
  created_at             INTEGER DEFAULT NULL, /* unix time */
  text                   TEXT NOT NULL,
  in_reply_to_status_id  INTEGER DEFAULT NULL,
  in_reply_to_user_id    INTEGER DEFAULT NULL,
  user_id                INTEGER DEFAULT NULL,
  issue_id               INTEGER DEFAULT NULL,
  status                 INTEGER DEFAULT 0, /* 要返答とか */
  is_mine                INTEGER DEFAULT 0, /* bool */
  odai_id                INTEGER
);

