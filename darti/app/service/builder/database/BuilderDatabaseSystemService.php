<?php

use Adianti\Database\TTransaction;

/**
 * Classe para manipulações com o base de dados do sistema
 * @author Lucas Tomasi <lcstomasi@gmail.com>
 */
class BuilderDatabaseSystemService
{
    private static $structures;
    private static $tables;
    private static $tablesBuilder;
    private static $views;
    private static $columns;

    private static $queries;


    private static function getQueries()
    {
        if(empty(self::$queries))
        {
            self::$queries = BuilderDatabaseService::getQueries();
        }

        return self::$queries;
    }

	/**
	 * Lista as bases de dados do projeto
	 * @return array Base de dados
	 */
	public static function listDatabases() : array
    {
        $list = [];

        foreach (new DirectoryIterator('app/config') as $file)
        {
            $connection = str_replace(['.ini','.php'], ['',''], $file->getFilename());

            if ($file->isFile() && in_array($file->getExtension(), ['ini', 'php']))
            {
                $ini = ($file->getExtension() == 'ini') ? parse_ini_file('app/config/'.$file) : require 'app/config/'.$file;

                $types = ['pgsql','mysql','sqlite'];
                // $types = ['pgsql','mysql','sqlite','ibase','fbird','oracle','mssql','dblib','sqlsrv'];

                if(! empty($ini['type']) AND in_array($ini['type'], $types))
                {
                    $list[ $connection ] = $connection;
                }
            }
        }

        natcasesort($list);
        return $list;
    }

	/**
	 * Lista as estruturas da base de dados passada por parâmetro
	 * @author Lucas Tomasi <lcstomasi@gmail.com>
	 * @param  String  $nameDatabase Nome da base de dados
	 * @return array Estruturas
	 */
	public static function listSturctures(String $nameDatabase) : array
	{
        if(! empty(self::$structures))
        {
            return self::$structures;
        }

		$info = TConnection::getDatabaseInfo($nameDatabase);

		if(! $info)
		{
			throw new Exception("Database not found");
		}

        $queries = self::getQueries()['list_structures']??[];
        $prescripts = self::getQueries()['prescripts']??[];

        if(! empty($queries[$info['type']]))
        {
            TTransaction::open($nameDatabase);
            $conn = TTransaction::get();

            if(! empty($prescripts[$info['type']]))
            {
                $scripts = explode(';', $prescripts[$info['type']]);

                foreach($scripts as $script)
                {
                    if(empty(trim($script)))
                    {
                        continue;
                    }

                    $prepared = $conn->prepare($script);
                    $prepared->execute([]);
                }
            }

            $query = str_replace(':database', $info['name'], $queries[$info['type']]);
            $result = $conn->query($query);

            self::$structures = $result->fetchAll(PDO::FETCH_NUM);
            TTransaction::close();

            return self::$structures;
        }
        else
        {
        	throw new Exception("Database type unsupported");
        }
	}

    /**
     * Lista as views da base de dados passada por parâmetro
     * @author Lucas Tomasi <lcstomasi@gmail.com>
     * @param  String  $nameDatabase Nome da base de dados
     * @return array Vires
     */
    public static function listViews(String $nameDatabase)
    {
        $structures = self::listSturctures($nameDatabase);

        foreach ($structures as $structure)
        {
            if($structure[1] != 'view')
            {
                continue;
            }

            $name = $structure[0];
            self::$views[$name] = ['name' => $name];
        }

        return self::$views;
    }

    /**
     * Busca a tabela da base de dados passada por parâmetro
     * @author Lucas Tomasi <lcstomasi@gmail.com>
     * @param  String  $nameDatabase Nome da base de dados
     * @param  String  $nameTable    Nome da tabela
     * @return array Tabela
     */
    public static function getTable(String $nameDatabase,  String $nameTable) : array
    {
        return self::listTables($nameDatabase)[$nameTable]??[];
    }

	/**
	 * Lista as tabelas da base de dados passada por parâmetro
	 * @author Lucas Tomasi <lcstomasi@gmail.com>
	 * @param  String  $nameDatabase Nome da base de dados
	 * @return array Tabelas
	 */
	public static function listTables(String $nameDatabase) : array
	{
        if(! empty(self::$tables))
        {
            return self::$tables;
        }

		$info = TConnection::getDatabaseInfo($nameDatabase);

		if(! $info)
		{
			throw new Exception("Database not found");
		}

        $structures = self::listSturctures($nameDatabase);

        if(empty($structures))
        {
            return [];
        }

        TTransaction::open($nameDatabase);
        
        $prescripts = self::getQueries()['prescripts']??[];

        if(! empty($prescripts[$info['type']]))
        {
            $conn = TTransaction::get();

            $scripts = explode(';', $prescripts[$info['type']]);

            foreach($scripts as $script)
            {
                if(empty(trim($script)))
                {
                    continue;
                }

                $prepared = $conn->prepare($script);
                $prepared->execute([]);
            }
        }

        foreach ($structures as $structure)
        {
            if($structure[1] != 'table')
            {
                continue;
            }

            $name = $structure[0];
            $references = $structure[2]??NULL;
            self::$tables[$name] = [
                'name'       => $name,
                'references' => $references ? explode(',', $references) : NULL,
                'columns'    => BuilderDatabaseSystemService::listColumns($nameDatabase, $name)
            ];
        }
        TTransaction::close();

        return self::$tables;
	}

    /**
	 * Lista colunas das tabelas da base de dados passada por parâmetro
	 * @author Lucas Tomasi <lcstomasi@gmail.com>
	 * @param  String  $nameDatabase Nome da base de dados
	 * @param  String  $nameTable Nome da tabela
	 * @return array Colunas
	 */
	public static function listColumns(String $nameDatabase, String $nameTable) : array
	{
        if(! empty(self::$columns[$nameTable]))
        {
            return self::$columns[$nameTable];
        }

		$info = TConnection::getDatabaseInfo($nameDatabase);

		if(! $info)
		{
			throw new Exception("Database not found");
		}

        $queries = self::getQueries()['list_columns']??[];
        

        if(! empty($queries[$info['type']]))
        {
            $conn = TTransaction::get();
            $closeTransaction = false;
            if(!$conn)
            {
                $closeTransaction = true;
                TTransaction::open($nameDatabase);
                $conn = TTransaction::get();
            }
            
            $query = str_replace(':database', $info['name'], $queries[$info['type']]);

            $result = $conn->prepare($query);
            $result->execute(['table_name' => $nameTable]);
            $columns = $result->fetchAll(PDO::FETCH_ASSOC);
            
            if($closeTransaction)
            {
                TTransaction::close();
            }

            self::$columns[$nameTable] = $columns;

            return $columns;
        }
        else
        {
        	throw new Exception("Columns not found");
        }

		return NULL;
	}

    /**
     * Busca a tabela do Builder
     * @author Lucas Tomasi <lcstomasi@gmail.com>
     * @param  String  $nameDatabase Nome da base de dados
     * @param  String  $nameTable Nome da tabela
     * @return array Tabela
     */
    public static function getTableBuilder($nameDatabase, $tableBuilder)
    {
        if(empty(self::$tablesBuilder))
        {
            self::$tablesBuilder = BuilderDatabaseService::listTables($nameDatabase);
        }

        $table = array_filter(self::$tablesBuilder, function($table) use ($tableBuilder) {
            return $table['name'] == $tableBuilder;
        });

        return empty($table) ? NULL : array_shift($table);
    }

    /**
     * Validar se a mudança poderá ser feita sem erros
     * @author Lucas Tomasi <lcstomasi@gmail.com>
     * @param  String $nameDatabase  Nome da base de dados
     * @param  String $table         Nome da tabela
     * @param  array  $columnProject Nome da coluna
     * @param  array  $columnBuilder Coluna do modelo
     * @return bool                  T/F
     */
    public static function validateChanges($databaseBuilder, $databaseProject, $tableProject, $tableBuilder, $columnsDiffs, $tablesRenames)
    {
        $tableBuilder = self::getTableBuilder($databaseBuilder, $tableBuilder);

        $warnings = [];

        foreach ($tableBuilder['columns'] as $column)
        {
            if(! in_array($column['name'], $columnsDiffs))
            {
                continue;
            }

            $warning = [];

            // valida date
            if($column['typeId'] == BuilderColumnTypeService::$DATE AND self::validateChangeToDate($databaseProject, $tableProject, $column))
            {
               $warning[] = 'Valores que não são compatíveis com datas';
            }
            // valida datetime
            elseif($column['typeId'] == BuilderColumnTypeService::$DATETIME AND self::validateChangeToDateTime($databaseProject, $tableProject, $column))
            {
               $warning[] = 'Valores que não são compatíveis com data hora';
            }
            // valida time
            elseif($column['typeId'] == BuilderColumnTypeService::$TIME AND self::validateChangeToTime($databaseProject, $tableProject, $column))
            {
                $warning[] = 'Valores que não são compatíveis com hora';
            }
            // valida double
            elseif($column['typeId'] == BuilderColumnTypeService::$DOUBLE AND self::validateChangeToFloat($databaseProject, $tableProject, $column))
            {
                $warning[] = 'Valores que não são compatíveis com double';
            }
            // valida int
            elseif($column['typeId'] == BuilderColumnTypeService::$INT AND self::validateChangeToInt($databaseProject, $tableProject, $column))
            {
                $warning[] = 'Valores que não são compatíveis com inteiros';
            }

            if($column['foreignKey'] AND self::validateChangeToForeignKey($databaseProject, $tableProject, $column, $tablesRenames))
            {
                $warning[] = 'Valores que não estão na tabela referenciada';
            }

            // valida not null
            if($column['notNull'] AND self::validateChangeToNotNull($databaseProject, $tableProject, $column))
            {
                $warning[] = 'Valores são nulos';
            }

            // valida tamanho
            if($column['columnSize'] AND in_array($column['typeId'], [BuilderColumnTypeService::$VARCHAR, BuilderColumnTypeService::$CHAR]) AND self::validateChangeToLength($databaseProject, $tableProject, $column))
            {
                $warning[] = 'Valores que são muito grandes';
            }

            // valida unique
            if($column['unique'] AND self::validateChangeToUnique($databaseProject, $tableProject, $column))
            {
                $warning[] = 'Valores que são iguais';
            }

            if(! empty($warning))
            {
                $warnings[$column['name']] = $warning;
            }
        }

        return $warnings;
    }

    /**
     * Validar se as chaves estrangeiras estão ok
     * @author Lucas Tomasi <lcstomasi@gmail.com>
     * @param  String $nameDatabase  Nome da base de dados
     * @param  String $table         Nome da tabela
     * @param  String $column        Nome da coluna
     * @param  array  $columnBuilder Coluna do modelo
     * @return bool                  T/F
     */
    private static function validateChangeToForeignKey($nameDatabase, $table, $columnBuilder, $tableRenames)
    {
        $info = TConnection::getDatabaseInfo($nameDatabase);

        $queries = self::getQueries()['validates']['fks']??[];

        if(! empty($queries[$info['type']]))
        {
            list($refTabela, $refColuna) = explode('::', $columnBuilder['foreignKey']);

            $refTabela = empty($tableRenames[$refTabela]) ? $refTabela : $tableRenames[$refTabela];

            $query = $queries[$info['type']];
            $query = str_replace(':tabela', $table, $query);
            $query = str_replace(':refTabela', $refTabela, $query);
            $query = str_replace(':refColuna', $refColuna, $query);
            $query = str_replace(':coluna', $columnBuilder['name'], $query);

            try {
                $conn = TTransaction::open($nameDatabase);
                $result = $conn->prepare($query);
                $result->execute();
                $result = $result->fetchAll(PDO::FETCH_ASSOC);
                TTransaction::close();
            } catch(Exception $e) {
                TTransaction::rollback();
                return false;
            }

            return ! empty($result);
        }

        return FALSE;
    }

    /**
     * Validar se o tipo da coluna pode ser alterada para int
     * @author Lucas Tomasi <lcstomasi@gmail.com>
     * @param  String $nameDatabase  Nome da base de dados
     * @param  String $table         Nome da tabela
     * @param  String $column        Nome da coluna
     * @param  array  $columnBuilder Coluna do modelo
     * @return bool                  T/F
     */
    private static function validateChangeToInt($nameDatabase, $table, $columnBuilder)
    {
        $info = TConnection::getDatabaseInfo($nameDatabase);

        $queries = self::getQueries()['validates']['int']??[];

        if(! empty($queries[$info['type']]))
        {
            $query = $queries[$info['type']];
            $query = str_replace(':tabela', $table, $query);
            $query = str_replace(':coluna', $columnBuilder['name'], $query);

            try {
                $conn = TTransaction::open($nameDatabase);
                $result = $conn->prepare($query);
                $result->execute();
                $result = $result->fetchAll(PDO::FETCH_ASSOC);
                TTransaction::close();
            } catch(Exception $e) {
                TTransaction::rollback();
                return false;
            }

            return ! empty($result);
        }

        return FALSE;
    }

    /**
     * Validar se o tipo da coluna pode ser alterada para float/double
     * @author Lucas Tomasi <lcstomasi@gmail.com>
     * @param  String $nameDatabase  Nome da base de dados
     * @param  String $table         Nome da tabela
     * @param  String $column        Nome da coluna
     * @param  array  $columnBuilder Coluna do modelo
     * @return bool                  T/F
     */
    private static function validateChangeToFloat($nameDatabase, $table, $columnBuilder)
    {
        $info = TConnection::getDatabaseInfo($nameDatabase);

        $queries = self::getQueries()['validates']['float']??[];

        if(! empty($queries[$info['type']]))
        {
            $query = $queries[$info['type']];
            $query = str_replace(':tabela', $table, $query);
            $query = str_replace(':coluna', $columnBuilder['name'], $query);

            try {
                $conn = TTransaction::open($nameDatabase);
                $result = $conn->prepare($query);
                $result->execute();
                $result = $result->fetchAll(PDO::FETCH_ASSOC);
                TTransaction::close();
            } catch(Exception $e) {
                TTransaction::rollback();
                return false;
            }

            return ! empty($result);
        }

        return FALSE;
    }

    /**
     * Validar se o tipo da coluna pode ser alterada para o tipo com o tamanho
     * @author Lucas Tomasi <lcstomasi@gmail.com>
     * @param  String $nameDatabase  Nome da base de dados
     * @param  String $table         Nome da tabela
     * @param  String $column        Nome da coluna
     * @param  array  $columnBuilder Coluna do modelo
     * @return bool                  T/F
     */
    private static function validateChangeToLength($nameDatabase, $table, $columnBuilder)
    {
        $info = TConnection::getDatabaseInfo($nameDatabase);

        $queries = self::getQueries()['validates']['length']??[];

        if(! empty($queries[$info['type']]))
        {
            $query = $queries[$info['type']];
            $query = str_replace(':tabela', $table, $query);
            $query = str_replace(':coluna', $columnBuilder['name'], $query);
            $query = str_replace(':limite', $columnBuilder['columnSize'], $query);

            try {
                $conn = TTransaction::open($nameDatabase);
                $result = $conn->prepare($query);
                $result->execute();
                $result = $result->fetchAll(PDO::FETCH_ASSOC);
                TTransaction::close();
            } catch(Exception $e) {
                TTransaction::rollback();
                return false;
            }

            return ! empty($result);
        }

        return FALSE;
    }

    /**
     * Validar se o tipo da coluna pode ser alterada para not null
     * @author Lucas Tomasi <lcstomasi@gmail.com>
     * @param  String $nameDatabase  Nome da base de dados
     * @param  String $table         Nome da tabela
     * @param  String $column        Nome da coluna
     * @param  array  $columnBuilder Coluna do modelo
     * @return bool                  T/F
     */
    private static function validateChangeToNotNull($nameDatabase, $table, $columnBuilder)
    {
        $info = TConnection::getDatabaseInfo($nameDatabase);

        $queries = self::getQueries()['validates']['notnull']??[];

        if(! empty($queries[$info['type']]))
        {
            $query = $queries[$info['type']];
            $query = str_replace(':tabela', $table, $query);
            $query = str_replace(':coluna', $columnBuilder['name'], $query);

            try {
                $conn = TTransaction::open($nameDatabase);
                $result = $conn->prepare($query);
                $result->execute();
                $result = $result->fetchAll(PDO::FETCH_ASSOC);
                TTransaction::close();
            } catch(Exception $e) {
                TTransaction::rollback();
                return false;
            }

            return ! empty($result);
        }

        return FALSE;
    }

    /**
     * Validar se o tipo da coluna pode ser alterada para unica
     * @author Lucas Tomasi <lcstomasi@gmail.com>
     * @param  String $nameDatabase  Nome da base de dados
     * @param  String $table         Nome da tabela
     * @param  String $column        Nome da coluna
     * @param  array  $columnBuilder Coluna do modelo
     * @return bool                  T/F
     */
    private static function validateChangeToUnique($nameDatabase, $table, $columnBuilder)
    {
        $info = TConnection::getDatabaseInfo($nameDatabase);

        $queries = self::getQueries()['validates']['unique']??[];

        if(! empty($queries[$info['type']]))
        {
            $query = $queries[$info['type']];
            $query = str_replace(':tabela', $table, $query);
            $query = str_replace(':coluna', $columnBuilder['name'], $query);

            try {
                $conn = TTransaction::open($nameDatabase);
                $result = $conn->prepare($query);
                $result->execute();
                $result = $result->fetchAll(PDO::FETCH_ASSOC);
                TTransaction::close();
            } catch(Exception $e) {
                TTransaction::rollback();
                return false;
            }

            return ! empty($result);
        }

        return FALSE;
    }

    /**
     * Validar se o tipo da coluna pode ser alterada para date
     * @author Lucas Tomasi <lcstomasi@gmail.com>
     * @param  String $nameDatabase  Nome da base de dados
     * @param  String $table         Nome da tabela
     * @param  String $column        Nome da coluna
     * @param  array  $columnBuilder Coluna do modelo
     * @return bool                  T/F
     */
    private static function validateChangeToDate($nameDatabase, $table, $columnBuilder)
    {
        $info = TConnection::getDatabaseInfo($nameDatabase);

        $queries = self::getQueries()['validates']['date']??[];

        if(! empty($queries[$info['type']]))
        {
            $query = $queries[$info['type']];
            $query = str_replace(':tabela', $table, $query);
            $query = str_replace(':coluna', $columnBuilder['name'], $query);

            try {
                $conn = TTransaction::open($nameDatabase);
                $result = $conn->prepare($query);
                $result->execute();
                $result = $result->fetchAll(PDO::FETCH_ASSOC);
                TTransaction::close();
            } catch(Exception $e) {
                TTransaction::rollback();
                return false;
            }

            return ! empty($result);
        }

        return FALSE;
    }

    /**
     * Validar se o tipo da coluna pode ser alterada para datetime
     * @author Lucas Tomasi <lcstomasi@gmail.com>
     * @param  String $nameDatabase  Nome da base de dados
     * @param  String $table         Nome da tabela
     * @param  String $column        Nome da coluna
     * @param  array  $columnBuilder Coluna do modelo
     * @return bool                  T/F
     */
    private static function validateChangeToDateTime($nameDatabase, $table, $columnBuilder)
    {
        $info = TConnection::getDatabaseInfo($nameDatabase);

        $queries = self::getQueries()['validates']['datetime']??[];

        if(! empty($queries[$info['type']]))
        {
            $query = $queries[$info['type']];
            $query = str_replace(':tabela', $table, $query);
            $query = str_replace(':coluna', $columnBuilder['name'], $query);

            try {
                $conn = TTransaction::open($nameDatabase);
                $result = $conn->prepare($query);
                $result->execute();
                $result = $result->fetchAll(PDO::FETCH_ASSOC);
                TTransaction::close();
            } catch(Exception $e) {
                TTransaction::rollback();
                return false;
            }

            return ! empty($result);
        }

        return FALSE;
    }

    /**
     * Validar se o tipo da coluna pode ser alterada para time
     * @author Lucas Tomasi <lcstomasi@gmail.com>
     * @param  String $nameDatabase  Nome da base de dados
     * @param  String $table         Nome da tabela
     * @param  String $column        Nome da coluna
     * @param  array  $columnBuilder Coluna do modelo
     * @return bool                  T/F
     */
    private static function validateChangeToTime($nameDatabase, $table, $columnBuilder)
    {
        $info = TConnection::getDatabaseInfo($nameDatabase);

        $queries = self::getQueries()['validates']['time']??[];

        if(! empty($queries[$info['type']]))
        {
            $query = $queries[$info['type']];
            $query = str_replace(':tabela', $table, $query);
            $query = str_replace(':coluna', $columnBuilder['name'], $query);

            try {
                $conn = TTransaction::open($nameDatabase);
                $result = $conn->prepare($query);
                $result->execute();
                $result = $result->fetchAll(PDO::FETCH_ASSOC);
                TTransaction::close();
            } catch(Exception $e) {
                TTransaction::rollback();
                return false;
            }

            return ! empty($result);
        }

        return FALSE;
    }
}