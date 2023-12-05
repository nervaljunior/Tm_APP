CREATE TABLE system_message
(
    id INTEGER PRIMARY KEY NOT NULL,
    system_user_id INT,
    system_user_to_id INT,
    subject blob sub_type 1,
    message blob sub_type 1,
    dt_message blob sub_type 1,
    checked char(1)
);

CREATE TABLE system_notification
(
    id INTEGER PRIMARY KEY NOT NULL,
    system_user_id INT,
    system_user_to_id INT,
    subject blob sub_type 1,
    message blob sub_type 1,
    dt_message blob sub_type 1,
    action_url blob sub_type 1,
    action_label blob sub_type 1,
    icon blob sub_type 1,
    checked char(1)
);

CREATE TABLE system_document_category
(
    id INTEGER PRIMARY KEY NOT NULL,
    name blob sub_type 1
);
INSERT INTO system_document_category VALUES(1,'Documentação');

CREATE TABLE system_document
(
    id INTEGER PRIMARY KEY NOT NULL,
    system_user_id INTEGER,
    title blob sub_type 1,
    description blob sub_type 1,
    category_id INTEGER references system_document_category(id),
    submission_date DATE,
    archive_date DATE,
    filename blob sub_type 1
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
