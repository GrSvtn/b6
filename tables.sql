CREATE TABLE user_pass (
    user_id int(10) unsigned NOT NULL,
    login varchar(16) NOT NULL,
    hash_pass varchar(16) NOT NULL,
    FOREIGN KEY (user_id)  REFERENCES form(id) ON DELETE CASCADE,
    PRIMARY KEY (user_id)
);

CREATE TABLE form(
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(32) NOT NULL DEFAULT '',
  email varchar(32) NOT NULL DEFAULT '',
  birthday DATE NOT NULL DEFAULT 0,
  sex VARCHAR(4) NOT NULL DEFAULT '',
  limbs int(1) NOT NULL DEFAULT 0,
  bio varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
);


CREATE TABLE form_power (
  form_id int(10) unsigned NOT NULL,
  power_id int(10) unsigned NOT NULL,
  FOREIGN KEY (form_id)  REFERENCES form (id) ON DELETE CASCADE,
  FOREIGN KEY (power_id) REFERENCES powers(id)
);


CREATE TABLE Admin (
    id int(10) unsigned NOT NULL AUTO_INCREMENT,
    login varchar(5) NOT NULL,
    hash_pass varchar(32) NOT NULL,
    PRIMARY KEY (id)
);
INSERT INTO Admin (login, hash_pass) VALUES ('admin','21232f297a57a5a743894a0e4a801fc3');