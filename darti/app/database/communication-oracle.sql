CREATE TABLE system_message
(
    id INTEGER PRIMARY KEY NOT NULL,
    system_user_id INT,
    system_user_to_id INT,
    subject varchar(1000),
    message varchar(1000),
    dt_message varchar(1000),
    checked char(1)
);

CREATE TABLE system_notification
(
    id INTEGER PRIMARY KEY NOT NULL,
    system_user_id INT,
    system_user_to_id INT,
    subject varchar(1000),
    message varchar(1000),
    dt_message varchar(1000),
    action_url varchar(1000),
    action_label varchar(1000),
    icon varchar(1000),
    checked char(1)
);

CREATE TABLE system_document_category
(
    id INTEGER PRIMARY KEY NOT NULL,
    name varchar(1000)
);
INSERT INTO system_document_category VALUES(1,'Documentação');

CREATE TABLE system_document
(
    id INTEGER PRIMARY KEY NOT NULL,
    system_user_id INTEGER,
    title varchar(1000),
    description varchar(1000),
    category_id INTEGER references system_document_category(id),
    submission_date DATE,
    archive_date DATE,
    filename varchar(1000)
);

CREATE TABLE system_document_user
(
    id INTEGER PRIMARY KEY NOT NULL,
    document_id INTEGER references system_document(id),
    system_user_id INTEGER
);

CREATE TABLE system_document_group
(
    id INTEGER PRIMARY KEY NOT NULL,
    document_id INTEGER references system_document(id),
    system_group_id INTEGER
);
