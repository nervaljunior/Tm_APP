CREATE TABLE system_change_log (
    id INTEGER PRIMARY KEY NOT NULL,
    logdate datetime2,
    login nvarchar(max),
    tablename nvarchar(max),
    primarykey nvarchar(max),
    pkvalue nvarchar(max),
    operation nvarchar(max),
    columnname nvarchar(max),
    oldvalue nvarchar(max),
    newvalue nvarchar(max),
    access_ip nvarchar(max),
    transaction_id nvarchar(max), 
    log_trace nvarchar(max),
    session_id nvarchar(max),
    class_name nvarchar(max),
    php_sapi nvarchar(max),
    log_year varchar(4),
    log_month varchar(2),
    log_day varchar(2)
);
CREATE TABLE system_sql_log (
    id INTEGER PRIMARY KEY NOT NULL,
    logdate datetime2,
    login nvarchar(max),
    database_name nvarchar(max),
    sql_command nvarchar(max),
    statement_type nvarchar(max),
    access_ip varchar(45),
    transaction_id nvarchar(max),
    log_trace nvarchar(max),
    session_id nvarchar(max),
    class_name nvarchar(max),
    php_sapi nvarchar(max),
    request_id nvarchar(max),
    log_year varchar(4),
    log_month varchar(2),
    log_day varchar(2)
);
CREATE TABLE system_access_log (
    id INTEGER PRIMARY KEY NOT NULL,
    sessionid nvarchar(max),
    login nvarchar(max),
    login_time datetime2,
    login_year varchar(4),
    login_month varchar(2),
    login_day varchar(2),
    logout_time datetime2,
    impersonated char(1),
    access_ip varchar(45),
    impersonated_by varchar(200)
);
CREATE TABLE system_request_log (
    id INTEGER PRIMARY KEY NOT NULL,
    endpoint nvarchar(max),
    logdate nvarchar(max),
    log_year varchar(4),
    log_month varchar(2),
    log_day varchar(2),
    session_id nvarchar(max),
    login nvarchar(max),
    access_ip nvarchar(max),
    class_name nvarchar(max),
    http_host nvarchar(max),
    server_port nvarchar(max),
    request_uri nvarchar(max),
    request_method nvarchar(max),
    query_string nvarchar(max),
    request_headers nvarchar(max),
    request_body nvarchar(max),
    request_duration int
);

CREATE TABLE system_access_notification_log (
    id INTEGER PRIMARY KEY NOT NULL,
    login nvarchar(max),
    email nvarchar(max),
    ip_address nvarchar(max),
    login_time nvarchar(max)
);