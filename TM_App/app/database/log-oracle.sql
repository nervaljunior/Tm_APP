CREATE TABLE system_change_log (
    id INTEGER PRIMARY KEY NOT NULL,
    logdate timestamp(0),
    login varchar(255),
    tablename varchar(255),
    primarykey varchar(255),
    pkvalue varchar(255),
    operation varchar(255),
    columnname varchar(255),
    oldvalue CLOB,
    newvalue CLOB,
    access_ip varchar(255),
    transaction_id varchar(255), 
    log_trace clob,
    session_id varchar(255),
    class_name varchar(255),
    php_sapi varchar(255),
    log_year varchar(4),
    log_month varchar(2),
    log_day varchar(2)
);
CREATE TABLE system_sql_log (
    id INTEGER PRIMARY KEY NOT NULL,
    logdate timestamp(0),
    login varchar(255),
    database_name varchar(255),
    sql_command CLOB,
    statement_type varchar(255),
    access_ip varchar(45),
    transaction_id varchar(255),
    log_trace clob,
    session_id varchar(255),
    class_name varchar(255),
    php_sapi varchar(255),
    request_id varchar(255),
    log_year varchar(4),
    log_month varchar(2),
    log_day varchar(2)
);
CREATE TABLE system_access_log (
    id INTEGER PRIMARY KEY NOT NULL,
    sessionid varchar(255),
    login varchar(255),
    login_time timestamp(0),
    login_year varchar(4),
    login_month varchar(2),
    login_day varchar(2),
    logout_time timestamp(0),
    impersonated char(1),
    access_ip varchar(45),
    impersonated_by varchar(200)
);
CREATE TABLE system_request_log (
    id INTEGER PRIMARY KEY NOT NULL,
    endpoint varchar(255),
    logdate varchar(255),
    log_year varchar(4),
    log_month varchar(2),
    log_day varchar(2),
    session_id varchar(255),
    login varchar(255),
    access_ip varchar(255),
    class_name varchar(1000),
    http_host varchar(1000),
    server_port varchar(1000),
    request_uri varchar(1000),
    request_method varchar(1000),
    query_string CLOB,
    request_headers CLOB,
    request_body CLOB,
    request_duration INTEGER
);

CREATE TABLE system_access_notification_log (
    id INTEGER PRIMARY KEY NOT NULL,
    login varchar(255),
    email varchar(255),
    ip_address varchar(255),
    login_time varchar(255)
);