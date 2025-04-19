CREATE TABLE tx_feuerwehren_domain_model_rolle (
    uid INT AUTO_INCREMENT PRIMARY KEY,
    pid INT DEFAULT 0,
    tstamp INT DEFAULT 0,
    crdate INT DEFAULT 0,
    cruser_id INT DEFAULT 0,
    deleted TINYINT DEFAULT 0,
    hidden TINYINT DEFAULT 0,
    title VARCHAR(255) DEFAULT ''
);

CREATE TABLE tx_feuerwehren_domain_model_person (
    uid INT AUTO_INCREMENT PRIMARY KEY,
    pid INT DEFAULT 0,
    tstamp INT DEFAULT 0,
    crdate INT DEFAULT 0,
    cruser_id INT DEFAULT 0,
    deleted TINYINT DEFAULT 0,
    hidden TINYINT DEFAULT 0,
    title VARCHAR(255) DEFAULT '',
    slug VARCHAR(255) DEFAULT '',
    fe_user INT DEFAULT 0,
    rolle INT DEFAULT 0,
    gemeinde INT DEFAULT 0
);

CREATE TABLE tx_feuerwehren_person_person_mm (
    uid_local INT DEFAULT 0,
    uid_foreign INT DEFAULT 0,
    sorting INT DEFAULT 0
);

CREATE TABLE tx_feuerwehren_domain_model_gemeinde (
    uid INT AUTO_INCREMENT PRIMARY KEY,
    pid INT DEFAULT 0,
    tstamp INT DEFAULT 0,
    crdate INT DEFAULT 0,
    cruser_id INT DEFAULT 0,
    deleted TINYINT DEFAULT 0,
    hidden TINYINT DEFAULT 0,
    name VARCHAR(255) DEFAULT '',
    slug VARCHAR(255) DEFAULT '',
    logo VARCHAR(255) DEFAULT '',
    gemeindegebiet TEXT
);

CREATE TABLE tx_feuerwehren_gemeinde_feuerwehr_mm (
    uid_local INT DEFAULT 0,
    uid_foreign INT DEFAULT 0,
    sorting INT DEFAULT 0
);

CREATE TABLE tx_feuerwehren_domain_model_feuerwehr (
    uid INT AUTO_INCREMENT PRIMARY KEY,
    pid INT DEFAULT 0,
    tstamp INT DEFAULT 0,
    crdate INT DEFAULT 0,
    cruser_id INT DEFAULT 0,
    deleted TINYINT DEFAULT 0,
    hidden TINYINT DEFAULT 0,
    name VARCHAR(255) DEFAULT '',
    slug VARCHAR(255) DEFAULT '',
    strasse VARCHAR(255) DEFAULT '',
    plz VARCHAR(10) DEFAULT '',
    ort VARCHAR(255) DEFAULT '',
    latitude DOUBLE DEFAULT 0,
    longitude DOUBLE DEFAULT 0,
    gruendungsdatum INT DEFAULT 0,
    kommandant INT DEFAULT 0,
    stellv_kommandant INT DEFAULT 0,
    jugendwart INT DEFAULT 0,
    stellv_jugendwart INT DEFAULT 0,
    kinderwart INT DEFAULT 0,
    stellv_kinderwart INT DEFAULT 0
);

CREATE TABLE tx_feuerwehren_feuerwehr_fahrzeugkategorie_mm (
   uid_local INT DEFAULT 0,
   uid_foreign INT DEFAULT 0,
   sorting INT DEFAULT 0
);

CREATE TABLE tx_feuerwehren_domain_model_fahrzeugkategorie (
    uid INT AUTO_INCREMENT PRIMARY KEY,
    pid INT DEFAULT 0,
    tstamp INT DEFAULT 0,
    crdate INT DEFAULT 0,
    cruser_id INT DEFAULT 0,
    deleted TINYINT DEFAULT 0,
    hidden TINYINT DEFAULT 0,
    title VARCHAR(255) DEFAULT ''
);

CREATE TABLE tx_feuerwehren_domain_model_jubilaeum (
    uid INT AUTO_INCREMENT PRIMARY KEY,
    pid INT DEFAULT 0,
    tstamp INT DEFAULT 0,
    crdate INT DEFAULT 0,
    cruser_id INT DEFAULT 0,
    deleted TINYINT DEFAULT 0,
    hidden TINYINT DEFAULT 0,
    feuerwehr INT DEFAULT 0,
    jahr INT DEFAULT 0,
    titel VARCHAR(255) DEFAULT '',
    beschreibung TEXT
);