CREATE TABLE system_message
(
    id INTEGER PRIMARY KEY NOT NULL,
    system_user_id INT,
    system_user_to_id INT,
    subject nvarchar(max),
    message nvarchar(max),
    dt_message nvarchar(max),
    checked char(1)
);

CREATE TABLE system_notification
(
    id INTEGER PRIMARY KEY NOT NULL,
    system_user_id INT,
    system_user_to_id INT,
    subject nvarchar(max),
    message nvarchar(max),
    dt_message nvarchar(max),
    action_url nvarchar(max),
    action_label nvarchar(max),
    icon nvarchar(max),
    checked char(1)
);

CREATE TABLE system_document_category
(
    id INTEGER PRIMARY KEY NOT NULL,
    name nvarchar(max)
);
INSERT INTO system_document_category VALUES(1,'Documentação');

CREATE TABLE system_document
(
    id INTEGER PRIMARY KEY NOT NULL,
    system_user_id INTEGER,
    title nvarchar(max),
    description nvarchar(max),
    category_id INTEGER references system_document_category(id),
    submission_date DATE,
    archive_date DATE,
    filename nvarchar(max)
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
