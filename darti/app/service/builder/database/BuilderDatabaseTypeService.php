<?php
/**
 * Classe de databases do builder
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class BuilderDatabaseTypeService
{
    const MYSQL  = 1;
    const PGSQL  = 2;
    const ORACLE = 3;
    const MSSQL  = 4;
    const SQLITE = 5;
    const FIREBIRD  = 6;

	public static function getType($type)
	{
		if($type == 'pgsql')
		{
			$typeId = self::PGSQL;
		}
		else if($type == 'mysql')
		{
			$typeId = self::MYSQL;
		}
		else if($type == 'sqlite')
		{
			$typeId = self::SQLITE;
		}
		else if(in_array($type, ['ibase', 'fbird']))
		{
			$typeId = self::FIREBIRD;
		}
		else if($type == 'oracle')
		{
			$typeId = self::ORACLE;
		}
		else if(in_array($type, ['mssql','dblib', 'sqlsrv']))
		{
			$typeId = self::MSSQL;
		}
		else
		{
			throw new Exception("Database type not supported");
		}

		return $typeId;
	}
}