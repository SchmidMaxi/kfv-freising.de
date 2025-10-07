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
    rolle VARCHAR(20) DEFAULT '',
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
    gemeindegebiet MEDIUMTEXT,
    kbm_area int DEFAULT 0 NOT NULL
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
    longitude DECIMAL(11, 8) DEFAULT 0.0 NOT NULL,
    latitude DECIMAL(10, 8) DEFAULT 0.0 NOT NULL,
    gruendungsdatum DATE DEFAULT NULL
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
    date DATE DEFAULT NULL,
    titel INT DEFAULT 0,
    beschreibung TEXT
);

-- Optional member MM tables
CREATE TABLE tx_feuerwehren_feuerwehr_aktiv_feusers_mm (
    uid_local INT DEFAULT 0,
    uid_foreign INT DEFAULT 0,
    sorting INT DEFAULT 0
);
CREATE TABLE tx_feuerwehren_feuerwehr_jugend_feusers_mm (
    uid_local INT DEFAULT 0,
    uid_foreign INT DEFAULT 0,
    sorting INT DEFAULT 0
);
CREATE TABLE tx_feuerwehren_feuerwehr_kinder_feusers_mm (
    uid_local INT DEFAULT 0,
    uid_foreign INT DEFAULT 0,
    sorting INT DEFAULT 0
);
CREATE TABLE tx_feuerwehren_person_gemeinde_mm (
    uid_local INT DEFAULT 0,
    uid_foreign INT DEFAULT 0,
    sorting INT DEFAULT 0
);
