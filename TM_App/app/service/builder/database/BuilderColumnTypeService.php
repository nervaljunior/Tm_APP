
<?php
/**
 * Classe de colunas do builder
 */
class BuilderColumnTypeService
{
    static $INT       = 1;
    static $TEXT      = 2;
	static $VARCHAR   = 3;
    static $DATE      = 4;
    static $DATETIME  = 5;
    static $TIMESTAMP = 6;
    static $CHAR      = 7;
    static $BOOLEAN   = 8;
    static $DOUBLE    = 9;
    static $TIME      = 10;
    static $CUSTOM    = 11;
    static $BIGINT    = 12;

    const NO_SIZE = [5, 4, 2, 8];

	public static function getColumnTypeForDatabase($column_type_id, $database_type_id)
	{
	    $columns = [];
	    $columns[BuilderDatabaseTypeService::MYSQL][self::$INT]       = 'int';
	    $columns[BuilderDatabaseTypeService::MYSQL][self::$CHAR]      = 'char';
	    $columns[BuilderDatabaseTypeService::MYSQL][self::$VARCHAR]   = 'varchar';
	    $columns[BuilderDatabaseTypeService::MYSQL][self::$TEXT]      = 'text';
	    $columns[BuilderDatabaseTypeService::MYSQL][self::$DATE]      = 'date';
	    $columns[BuilderDatabaseTypeService::MYSQL][self::$DATETIME]  = 'datetime';
	    $columns[BuilderDatabaseTypeService::MYSQL][self::$DOUBLE]    = 'double';
	    $columns[BuilderDatabaseTypeService::MYSQL][self::$BOOLEAN]   = 'boolean';
	    $columns[BuilderDatabaseTypeService::MYSQL][self::$TIME]      = 'time';
	    $columns[BuilderDatabaseTypeService::MYSQL][self::$CUSTOM]    = 'custom';
	    $columns[BuilderDatabaseTypeService::MYSQL][self::$BIGINT]    = 'bigint';

	    $columns[BuilderDatabaseTypeService::PGSQL][self::$INT]       = 'integer';
	    $columns[BuilderDatabaseTypeService::PGSQL][self::$CHAR]      = 'char';
	    $columns[BuilderDatabaseTypeService::PGSQL][self::$VARCHAR]   = 'varchar';
	    $columns[BuilderDatabaseTypeService::PGSQL][self::$TEXT]      = 'text';
	    $columns[BuilderDatabaseTypeService::PGSQL][self::$DATE]      = 'date';
	    $columns[BuilderDatabaseTypeService::PGSQL][self::$DATETIME]  = 'timestamp';
	    $columns[BuilderDatabaseTypeService::PGSQL][self::$DOUBLE]    = 'float';
	    $columns[BuilderDatabaseTypeService::PGSQL][self::$BOOLEAN]   = 'boolean';
	    $columns[BuilderDatabaseTypeService::PGSQL][self::$TIME]      = 'time';
		$columns[BuilderDatabaseTypeService::PGSQL][self::$CUSTOM]    = 'custom';
		$columns[BuilderDatabaseTypeService::PGSQL][self::$BIGINT]    = 'bigint';

	    $columns[BuilderDatabaseTypeService::MSSQL][self::$INT]       = 'int';
	    $columns[BuilderDatabaseTypeService::MSSQL][self::$VARCHAR]   = 'varchar';
	    $columns[BuilderDatabaseTypeService::MSSQL][self::$TEXT]      = 'nvarchar(max)';
	    $columns[BuilderDatabaseTypeService::MSSQL][self::$TEXT]      = 'nvarchar';
	    $columns[BuilderDatabaseTypeService::MSSQL][self::$DATE]      = 'date';
	    $columns[BuilderDatabaseTypeService::MSSQL][self::$DATETIME]  = 'datetime2';
	    $columns[BuilderDatabaseTypeService::MSSQL][self::$CHAR]      = 'char';
	    $columns[BuilderDatabaseTypeService::MSSQL][self::$DOUBLE]    = 'float';
	    $columns[BuilderDatabaseTypeService::MSSQL][self::$BOOLEAN]   = 'bit';
	    $columns[BuilderDatabaseTypeService::MSSQL][self::$TIME]      = 'time';
		$columns[BuilderDatabaseTypeService::MSSQL][self::$CUSTOM]    = 'custom';
		$columns[BuilderDatabaseTypeService::MSSQL][self::$BIGINT]    = 'bigint';

	    $columns[BuilderDatabaseTypeService::ORACLE][self::$INT]       = 'number(10)';
	    $columns[BuilderDatabaseTypeService::ORACLE][self::$VARCHAR]   = 'varchar';
	    $columns[BuilderDatabaseTypeService::ORACLE][self::$TEXT]      = 'CLOB';
	    $columns[BuilderDatabaseTypeService::ORACLE][self::$DATE]      = 'date';
	    $columns[BuilderDatabaseTypeService::ORACLE][self::$CHAR]      = 'char';
	    $columns[BuilderDatabaseTypeService::ORACLE][self::$DATETIME]  = 'timestamp(0)';
	    $columns[BuilderDatabaseTypeService::ORACLE][self::$DOUBLE]    = 'binary_double';
	    $columns[BuilderDatabaseTypeService::ORACLE][self::$BOOLEAN]   = 'char(1)';
	    $columns[BuilderDatabaseTypeService::ORACLE][self::$TIME]      = 'time';
		$columns[BuilderDatabaseTypeService::ORACLE][self::$CUSTOM]    = 'custom';
		$columns[BuilderDatabaseTypeService::ORACLE][self::$BIGINT]    = 'bigint';

	    $columns[BuilderDatabaseTypeService::SQLITE][self::$INT]       = 'int';
	    $columns[BuilderDatabaseTypeService::SQLITE][self::$CHAR]      = 'char';
	    $columns[BuilderDatabaseTypeService::SQLITE][self::$VARCHAR]   = 'varchar';
	    $columns[BuilderDatabaseTypeService::SQLITE][self::$TEXT]      = 'text';
	    $columns[BuilderDatabaseTypeService::SQLITE][self::$DATE]      = 'date';
	    $columns[BuilderDatabaseTypeService::SQLITE][self::$DATETIME]  = 'datetime';
	    $columns[BuilderDatabaseTypeService::SQLITE][self::$DOUBLE]    = 'double';
	    $columns[BuilderDatabaseTypeService::SQLITE][self::$BOOLEAN]   = 'text';
	    $columns[BuilderDatabaseTypeService::SQLITE][self::$TIME]      = 'text';
		$columns[BuilderDatabaseTypeService::SQLITE][self::$CUSTOM]    = 'custom';
		$columns[BuilderDatabaseTypeService::SQLITE][self::$BIGINT]    = 'bigint';

	    $columns[BuilderDatabaseTypeService::FIREBIRD][self::$INT]       = 'integer';
	    $columns[BuilderDatabaseTypeService::FIREBIRD][self::$CHAR]      = 'char';
	    $columns[BuilderDatabaseTypeService::FIREBIRD][self::$VARCHAR]   = 'varchar';
	    $columns[BuilderDatabaseTypeService::FIREBIRD][self::$TEXT]      = 'blob sub_type 1';
	    $columns[BuilderDatabaseTypeService::FIREBIRD][self::$DATE]      = 'date';
	    $columns[BuilderDatabaseTypeService::FIREBIRD][self::$DATETIME]  = 'timestamp';
	    $columns[BuilderDatabaseTypeService::FIREBIRD][self::$DOUBLE]    = 'float';
	    $columns[BuilderDatabaseTypeService::FIREBIRD][self::$BOOLEAN]   = 'char(1)';
	    $columns[BuilderDatabaseTypeService::FIREBIRD][self::$TIME]      = 'time';
		$columns[BuilderDatabaseTypeService::FIREBIRD][self::$CUSTOM]    = 'custom';
		$columns[BuilderDatabaseTypeService::FIREBIRD][self::$BIGINT]    = 'bigint';

	    return $columns[$database_type_id][$column_type_id];
	}

	/**
	 * Busca o id do tipo da coluna
	 * @param  String $column_type    Nome do tipo
	 * @param  int $database_type_id  Tipo da base
	 * @return int                    Tipo
	 */
	public static function getTypeId($column_type, $database_type_id)
	{
		$columns = [];
	    $columns[BuilderDatabaseTypeService::MYSQL]['int'] = self::$INT;
	    $columns[BuilderDatabaseTypeService::MYSQL]['char'] = self::$CHAR;
	    $columns[BuilderDatabaseTypeService::MYSQL]['varchar'] = self::$VARCHAR;
	    $columns[BuilderDatabaseTypeService::MYSQL]['text'] = self::$TEXT;
	    $columns[BuilderDatabaseTypeService::MYSQL]['date'] = self::$DATE;
	    $columns[BuilderDatabaseTypeService::MYSQL]['datetime'] = self::$DATETIME;
	    $columns[BuilderDatabaseTypeService::MYSQL]['timestamp'] = self::$TIMESTAMP;
	    $columns[BuilderDatabaseTypeService::MYSQL]['double'] = self::$DOUBLE;
	    $columns[BuilderDatabaseTypeService::MYSQL]['boolean'] = self::$BOOLEAN;
	    $columns[BuilderDatabaseTypeService::MYSQL]['tinyint'] = self::$BOOLEAN;
	    $columns[BuilderDatabaseTypeService::MYSQL]['time'] = self::$TIME;
	    $columns[BuilderDatabaseTypeService::MYSQL]['custom'] = self::$CUSTOM;
		$columns[BuilderDatabaseTypeService::MYSQL]['bigint'] = self::$BIGINT;

	    $columns[BuilderDatabaseTypeService::PGSQL]['integer'] = self::$INT;
	    $columns[BuilderDatabaseTypeService::PGSQL]['int'] = self::$INT;
	    $columns[BuilderDatabaseTypeService::PGSQL]['char'] = self::$CHAR;
	    $columns[BuilderDatabaseTypeService::PGSQL]['character'] = self::$CHAR;
	    $columns[BuilderDatabaseTypeService::PGSQL]['character varying'] = self::$VARCHAR;
	    $columns[BuilderDatabaseTypeService::PGSQL]['varchar'] = self::$VARCHAR;
	    $columns[BuilderDatabaseTypeService::PGSQL]['text'] = self::$TEXT;
	    $columns[BuilderDatabaseTypeService::PGSQL]['date'] = self::$DATE;
	    $columns[BuilderDatabaseTypeService::PGSQL]['timestamp'] = self::$DATETIME;
	    $columns[BuilderDatabaseTypeService::PGSQL]['datetime'] = self::$DATETIME;
	    $columns[BuilderDatabaseTypeService::PGSQL]['float'] = self::$DOUBLE;
	    $columns[BuilderDatabaseTypeService::PGSQL]['double'] = self::$DOUBLE;
	    $columns[BuilderDatabaseTypeService::PGSQL]['boolean'] = self::$BOOLEAN;
	    $columns[BuilderDatabaseTypeService::PGSQL]['time'] = self::$TIME;
		$columns[BuilderDatabaseTypeService::PGSQL]['custom'] = self::$CUSTOM;
		$columns[BuilderDatabaseTypeService::PGSQL]['bigint'] = self::$BIGINT;

	    $columns[BuilderDatabaseTypeService::MSSQL]['int'] = self::$INT;
	    $columns[BuilderDatabaseTypeService::MSSQL]['varchar'] = self::$VARCHAR;
	    $columns[BuilderDatabaseTypeService::MSSQL]['nvarchar(max)'] = self::$TEXT;
	    $columns[BuilderDatabaseTypeService::MSSQL]['nvarchar'] = self::$TEXT;
	    $columns[BuilderDatabaseTypeService::MSSQL]['date'] = self::$DATE;
	    $columns[BuilderDatabaseTypeService::MSSQL]['datetime2'] = self::$DATETIME;
	    $columns[BuilderDatabaseTypeService::MSSQL]['char'] = self::$CHAR;
	    $columns[BuilderDatabaseTypeService::MSSQL]['float'] = self::$DOUBLE;
	    $columns[BuilderDatabaseTypeService::MSSQL]['bit'] = self::$BOOLEAN;
	    $columns[BuilderDatabaseTypeService::MSSQL]['time'] = self::$TIME;
		$columns[BuilderDatabaseTypeService::MSSQL]['custom'] = self::$CUSTOM;
		$columns[BuilderDatabaseTypeService::MSSQL]['bigint'] = self::$BIGINT;

	    $columns[BuilderDatabaseTypeService::ORACLE]['number(10)'] = self::$INT;
	    $columns[BuilderDatabaseTypeService::ORACLE]['varchar'] = self::$VARCHAR;
	    $columns[BuilderDatabaseTypeService::ORACLE]['CLOB'] = self::$TEXT;
	    $columns[BuilderDatabaseTypeService::ORACLE]['date'] = self::$DATE;
	    $columns[BuilderDatabaseTypeService::ORACLE]['char'] = self::$CHAR;
	    $columns[BuilderDatabaseTypeService::ORACLE]['timestamp(0)'] = self::$DATETIME;
	    $columns[BuilderDatabaseTypeService::ORACLE]['binary_double'] = self::$DOUBLE;
	    $columns[BuilderDatabaseTypeService::ORACLE]['char(1)'] = self::$BOOLEAN;
	    $columns[BuilderDatabaseTypeService::ORACLE]['time'] = self::$TIME;
		$columns[BuilderDatabaseTypeService::ORACLE]['custom'] = self::$CUSTOM;
		$columns[BuilderDatabaseTypeService::ORACLE]['bigint'] = self::$BIGINT;

	    $columns[BuilderDatabaseTypeService::SQLITE]['int'] = self::$INT;
	    $columns[BuilderDatabaseTypeService::SQLITE]['integer'] = self::$INT;
	    $columns[BuilderDatabaseTypeService::SQLITE]['char'] = self::$CHAR;
	    $columns[BuilderDatabaseTypeService::SQLITE]['varchar'] = self::$VARCHAR;
	    $columns[BuilderDatabaseTypeService::SQLITE]['text'] = self::$TEXT;
	    $columns[BuilderDatabaseTypeService::SQLITE]['date'] = self::$DATE;
	    $columns[BuilderDatabaseTypeService::SQLITE]['datetime'] = self::$DATETIME;
	    $columns[BuilderDatabaseTypeService::SQLITE]['double'] = self::$DOUBLE;
	    $columns[BuilderDatabaseTypeService::SQLITE]['boolean'] = self::$BOOLEAN;
	    $columns[BuilderDatabaseTypeService::SQLITE]['text'] = self::$TIME;
		$columns[BuilderDatabaseTypeService::SQLITE]['custom'] = self::$CUSTOM;
		$columns[BuilderDatabaseTypeService::SQLITE]['bigint'] = self::$BIGINT;

	    $columns[BuilderDatabaseTypeService::FIREBIRD]['integer'] = self::$INT;
	    $columns[BuilderDatabaseTypeService::FIREBIRD]['char'] = self::$CHAR;
	    $columns[BuilderDatabaseTypeService::FIREBIRD]['varchar'] = self::$VARCHAR;
	    $columns[BuilderDatabaseTypeService::FIREBIRD]['blob sub_type 1'] = self::$TEXT;
	    $columns[BuilderDatabaseTypeService::FIREBIRD]['date'] = self::$DATE;
	    $columns[BuilderDatabaseTypeService::FIREBIRD]['timestamp'] = self::$DATETIME;
	    $columns[BuilderDatabaseTypeService::FIREBIRD]['float'] = self::$DOUBLE;
	    $columns[BuilderDatabaseTypeService::FIREBIRD]['char(1)'] = self::$BOOLEAN;
	    $columns[BuilderDatabaseTypeService::FIREBIRD]['time'] = self::$TIME;
		$columns[BuilderDatabaseTypeService::FIREBIRD]['custom'] = self::$CUSTOM;
		$columns[BuilderDatabaseTypeService::FIREBIRD]['bigint'] = self::$BIGINT;

	    return $columns[$database_type_id][$column_type];
	}
}