CREATE TABLE system_change_log (
    id INTEGER PRIMARY KEY NOT NULL,
    logdate timestamp,
    login blob sub_type 1,
    tablename blob sub_type 1,
    primarykey blob sub_type 1,
    pkvalue blob sub_type 1,
    operation blob sub_type 1,
    columnname blob sub_type 1,
    oldvalue blob sub_type 1,
    newvalue blob sub_type 1,
    access_ip blob sub_type 1,
    transaction_id blob sub_type 1, 
    log_trace blob sub_type 1,
    session_id blob sub_type 1,
    class_name blob sub_type 1,
    php_sapi blob sub_type 1,
    log_year varchar(4),
    log_month varchar(2),
    log_day varchar(2)
);
CREATE TABLE system_sql_log (
    id INTEGER PRIMARY KEY NOT NULL,
    logdate timestamp,
    login blob sub_type 1,
    database_name blob sub_type 1,
    sql_command blob sub_type 1,
    statement_type blob sub_type 1,
    access_ip varchar(45),
    transaction_id blob sub_type 1,
    log_trace blob sub_type 1,
    session_id blob sub_type 1,
    class_name blob sub_type 1,
    php_sapi blob sub_type 1,
    request_id blob sub_type 1,
    log_year varchar(4),
    log_month varchar(2),
    log_day varchar(2)
);
CREATE TABLE system_access_log (
    id INTEGER PRIMARY KEY NOT NULL,
    sessionid blob sub_type 1,
    login blob sub_type 1,
    login_time timestamp,
    login_year varchar(4),
    login_month varchar(2),
    login_day varchar(2),
    logout_time timestamp,
    impersonated char(1),
    access_ip varchar(45),
    impersonated_by varchar(200)
);
CREATE TABLE system_request_log (
    id INTEGER PRIMARY KEY NOT NULL,
    endpoint blob sub_type 1,
    logdate blob sub_type 1,
    log_year varchar(4),
    log_month varchar(2),
    log_day varchar(2),
    session_id blob sub_type 1,
    login blob sub_type 1,
    access_ip blob sub_type 1,
    class_name blob sub_type 1,
    http_host blob sub_type 1,
    server_port blob sub_type 1,
    request_uri blob sub_type 1,
    request_method blob sub_type 1,
    query_string blob sub_type 1,
    request_headers blob sub_type 1,
    request_body blob sub_type 1,
    request_duration INT
);


CREATE TABLE system_access_notification_log (
    id INTEGER PRIMARY KEY NOT NULL,
    login blob sub_type 1,
    email blob sub_type 1,
    ip_address blob sub_type 1,
    login_time blob sub_type 1
);