<?php
/**
 * Classe para manipulações com o base de dados do Adianti Builder
 * @author Lucas Tomasi <lcstomasi@gmail.com>
 */
class BuilderDatabaseService
{
	const BASE_URL = 'https://manager.adiantibuilder.com.br/ws.php?version=2&token=';

    public static function makeColumnComponentDiff($table_name_equals, $table_renames, $table_equals, $table_drops, $table_news, $showConfirm, $databaseBuilder, $databaseProject, $databaseType, $tablesProject)
    {
        $param = [
            'method' => 'makeColumnComponentDiff',
            'table_name_equals' => $table_name_equals,
            'table_renames' => $table_renames,
            'table_equals' => $table_equals,
            'table_drops' => $table_drops,
            'table_news' => $table_news,
            'showConfirm' => $showConfirm,
            'databaseBuilder' => $databaseBuilder,
            'databaseProject' => $databaseProject,
            'databaseType' => $databaseType,
            'tablesProject' => $tablesProject
        ];

        return unserialize(
            self::post($param)
        );
    }

    /**
     * Busca sql para serem executadas no banco de dados do projeto
     * - list_structures
     * - list_column
     * - validates
     *   -  int
     *   -  float
     *   -  length
     *   -  notnull
     *   -  unique
     *   -  date
     *   -  datetime
     *   -  time
     * @return array sqls
     */
    public static function getQueries()
    {
        return self::post(['method' => 'getQueries']);
    }

    /**
     * Gera os comandos para views
     * @param  String $database     Base de dados
     * @param  array  $newsViews    Views
     * @param  array  $equalsViews  Views
     * @param  array  $dropsViews   Views
     * @return String               Comandos
     */
    public static function getCommandsViews($database, $newsViews, $equalsViews, $dropsViews)
    {
        return self::post([
            'method' => 'getCommandsViews',
            'database' => $database,
            'newsViews' => $newsViews,
            'equalsViews' => $equalsViews,
            'dropsViews' => $dropsViews,
        ]);
    }

    /**
     * Busca as diferenças de views das bases de dados
     * @param  array $viewsBuilder   Views do modelo no builder
     * @param  array $viewsProject   Views existente no projeto
     * @return array                 Diferenças [news, equals, drops]
     */
    public static function getDifferencesViews($viewsBuilder, $viewsProject)
    {
        return self::post([
            'method' => 'getDifferencesViews',
            'viewsBuilder' => $viewsBuilder,
            'viewsProject' => $viewsProject,
        ]);
    }

    /**
     * Filtra os comandos confirmados
     * @param  array $confirmeds Comfirmados
     * @param  array $sqls       sqls
     * @return array             Sqls confirmados
     */
    public static function getCommandsConfirmeds($confirmeds, $sqls)
    {
        return self::post([
            'method' => 'getCommandsConfirmeds',
            'confirmeds' => $confirmeds,
            'sqls' => $sqls,
        ]);
    }

    /**
     * Retorna um obj com o componente mostrando as differenças assim com as diferenças em um array
     * @param  String $databaseBuilder Nome da base do builder
     * @param  String $databaseProject Nome da base do projeto
     * @param  String $table           Nome da tabela do builder
     * @param  String $tableProject    Nome da tabela do projeto
     * @param  int    $dbType          Tipo do banco
     * @param  String $type            Tipo do mudança da tabela
     * @param  array  $tableDiffs      Diferenças separados por tabela e colunas
     * @param  bool   $showConfirm     Tipo do mudança da tabela
     * @return String                  Objeto serilizado
     */
    public static function makeComponentContainerDiff($databaseBuilder, $databaseProject, $table, $tableProject, $dbType, $type, $showConfirm)
    {
        return self::post([
            'method' => 'makeComponentContainerDiff',
            'databaseBuilder' => $databaseBuilder,
            'databaseProject' => $databaseProject,
            'table' => $table,
            'tableProject' => $tableProject,
            'dbType' => $dbType,
            'type' => $type,
            'showConfirm' => $showConfirm,
        ]);
    }

    /**
     * Gera os comandos SQL
     * @param  String $dbName        Nome da base de dados do builder
     * @param  array  $drops         Tabelas para remover
     * @param  array  $renames       Tabelas para renomear
     * @param  array  $news          Tabelas para adicionar
     * @param  array  $columnsRename Colunas para renomeação
     * @param  array  $diffs         Diff das tabelas
     * @param  array  $dbType        Tipo do banco
     * @param  array  $tablesProject Tabelas do projeto
     * @param  array  $columnsForm   Parametros fo formulario
     * @return array sql
     */
    public static function generateComands($dbName, $drops, $renames, $news, $columnsRename, $diffs, $dbType, $tablesProject, $columnsForm)
    {
        return self::post([
            'method' => 'generateComands',
            'dbName' => $dbName,
            'drops' => $drops,
            'renames' => $renames,
            'news' => $news,
            'columnsRename' => $columnsRename,
            'diffs' => $diffs,
            'dbType' => $dbType,
            'tablesProject' => $tablesProject,
            'columnsForm' => $columnsForm,
        ]);
    }

    /**
     * Lista as tabelas da base de dados passada por parâmetro
     * @author Lucas Tomasi <lcstomasi@gmail.com>
     * @param  String  $nameDatabase Nome da base de dados
     * @return array Tabelas
     */
    public static function listTables($nameDatabase)
    {
        return self::get([
            'method' => 'listTables',
            'nameDatabase' => $nameDatabase
        ]);
    }

    /**
     * Lista as tabelas da base de dados passada por parâmetro
     * @author Lucas Tomasi <lcstomasi@gmail.com>
     * @param  String  $nameDatabase Nome da base de dados
     * @return array Tabelas
     */
    public static function listViews($nameDatabase)
    {
        return self::get([
            'method' => 'listViews',
            'nameDatabase' => $nameDatabase
        ]);
    }

    /**
     * Busca as diferenças dde tabelas das bases de dados
     * @param  array $tablesBuilder  Tabelas do modelo no builder
     * @param  array $tablesProject  Tabelas existente no projeto
     * @param  int   $dbType         Tipo do banco de dados
     * @return array                 Diferenças [news, equals, renames, drops]
     */
    public static function getDifferencesTables($tablesBuilder, $tablesProject, $dbType)
    {
        return self::post([
            'method' => 'getDifferencesTables',
            'tablesBuilder' => $tablesBuilder,
            'tablesProject' => $tablesProject,
            'dbType' => $dbType
        ]);
    }

    /**
     * Lista as bases de dados do projeto no Adianti Builder
     * @author Lucas Tomasi <lcstomasi@gmail.com>
     *
     * @return array Base de dados
     */
    public static function listDatabases()
    {
        return self::get([
            'method' => 'listDatabases'
        ]);
    }

	/**
	 * Executa uma chamada rest para a url BASE
	 * @author Lucas Tomasi <lcstomasi@gmail.com>
	 *
	 * @param  string $method Método a ser exeecutado
	 * @param  array  $params parâmetros
	 * @return array          Retorno
	 */
	private static function get($param)
	{
		$ini = AdiantiApplicationConfig::get();

		$ch = curl_init();
		$defaults = [
    		CURLOPT_URL            => self::BASE_URL.$ini['general']['token']. '&' .http_build_query($param),
    		CURLOPT_CUSTOMREQUEST  => 'GET',
    		CURLOPT_RETURNTRANSFER => TRUE,
    		CURLOPT_SSL_VERIFYPEER => FALSE,
    		CURLOPT_CONNECTTIMEOUT => 10
		];

		curl_setopt_array($ch, $defaults);

		$output = curl_exec ($ch);

        curl_close ($ch);

        $return = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE)
        {
            throw new Exception('Return is not JSON. Check the URL'.$output);
        }

        if ($return['status'] == 'error')
        {
            throw new Exception($return['data']);
        }

		return $return['data'];
	}

    /**
     * Executa uma chamada rest para a url BASE
     * @author Lucas Tomasi <lcstomasi@gmail.com>
     *
     * @param  string $method Método a ser exeecutado
     * @param  array  $params parâmetros
     * @return array          Retorno
     */
    private static function post($param)
    {
        $ini = AdiantiApplicationConfig::get();

        $ch = curl_init();
        $defaults = [
            CURLOPT_URL            => self::BASE_URL.$ini['general']['token'],
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_HEADER         => false,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_POSTFIELDS     => json_encode($param)
        ];

        curl_setopt_array($ch, $defaults);

        $output = curl_exec ($ch);
        
        curl_close ($ch);
        
        $return = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE)
        {
            throw new Exception('Return is not JSON. Check the URL'. $output);
        }

        if ($return['status'] == 'error')
        {
            throw new Exception($return['data']);
        }

        return $return['data'];
    }
}