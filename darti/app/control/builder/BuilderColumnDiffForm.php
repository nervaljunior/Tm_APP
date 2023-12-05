<?php

use Adianti\Database\TTransaction;

/**
 * ColumnDiffForm
 *
 * @version    1.0
 * @author     Lucas Tomasi
 */
class BuilderColumnDiffForm extends TPage
{
	private $form;
	private $warnings;
	private $table;
	private $tablesProject;
	private $databaseBuilder;
	private $databaseProject;
	private $databaseType;

	private static $formName = 'tableDiffForm';

	public function __construct($param)
	{
		try
		{
			parent::__construct();

			BuilderPermissionService::checkPermission();

			$databaseMergeSession = TSession::getValue('databaseMergeSession');

			$this->form = new BootstrapFormBuilder(self::$formName);
	        $this->form->setFormTitle('Database merge');
	        $this->form->setFieldSizes('100%');

	        $isValidacao = ($param['method'] == 'onAnalisar');

	        $actionBack = $isValidacao ? "BuilderColumnDiffForm" : "BuilderTableDiffForm";
	        $labelNext  = $isValidacao ? "Validar comandos" : "Confirmar comandos";
	        $actionNext = $isValidacao ? "onConfirmCommands" : "onAnalisar";

	        $this->form->addAction("Voltar", new TAction([$actionBack, 'onLoad']), 'fas:arrow-left red');
	        $onsave = $this->form->addAction($labelNext, new TAction([$this, $actionNext]), 'fas:arrow-right #ffffff');
	        $onsave->addStyleClass('btn-primary right');

	        $tstep = new TStep();
			$tstep->addItem('Escolha das bases', false, true);
			$tstep->addItem('Verificação de tabelas', false, true );

			if($isValidacao) {
				$tstep->addItem('Verificação de colunas', false, true);
				$tstep->addItem('Validação de comandos', true, false);
			} else {
				$tstep->addItem('Verificação de colunas', true, false);
				$tstep->addItem('Validação de comandos', false, false);
			}

			$tstep->addItem('Verificação de views', false, false);
			$tstep->addItem('Confirmação comandos', false, false);

			$container = new TVBox;
	        $container->id = "database-merge";
	        $container->style = 'width: 100%';
	        $container->add($this->form);

	        $label = new TLabel(($isValidacao ? 'Verificação de colunas' : 'Validação de comandos'));
			$label->style = 'font-size: 16px;margin-top: 25px;font-weight: bold';

			$infoBase = "<div style='font-size: 12px; color: #333'>* Base de dados de referência <b class='badge bg-info' style='color: white;font-size: 10px;'>{$databaseMergeSession->databaseName}</b> localizada em <b class='badge bg-info' style='color: white;font-size: 10px;'>{$databaseMergeSession->databaseLocal}</b></div>";

	        $labelSubtitle = new TLabel('Verifique as alterações que serão realizadas nas colunas das tabelas'.$infoBase);
	        $labelSubtitle->style = 'font-size: 12px;color: #949ea7;';

	        $this->form->addContent([$tstep]);
	        $this->form->addContent([$label]);
	        $this->form->addContent([$labelSubtitle]);

	        $this->table = new TElement('div');
	        $this->warnings = [];

	        parent::add($container);
		}
		catch (Exception $e)
		{
			new TMessage('error', $e->getMessage());
		}
	}

	public static function onChangeComboAdicionada($param)
	{
		try
		{
			BuilderPermissionService::checkPermission();

			$params = explode('+', substr($param['_field_name'],0, -2));
			TScript::create("Builder.defineColumnRename('{$params[2]}');");
		}
		catch (Exception $e)
		{
			new TMessage('error', $e->getMessage());
		}
	}

	private function getColumnRenamed($param)
	{
		$columnsRenamed = [];

		if(! $param)
		{
			return $columnsRenamed;
		}

		TTransaction::open($this->databaseProject);
        $info = TConnection::getDatabaseInfo($this->databaseProject);

        $prescripts = BuilderDatabaseService::getQueries()['prescripts']??[];

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

		foreach ($param as $key => $table)
		{
			if(strpos($key, 'colunas+new+') === FALSE)
			{
				continue;
			}

			foreach ($table as $key => $value)
			{
				$renames = explode('<=>', $value);
				if(count($renames) < 2)
				{
					continue;
				}

				$columns = explode('->', $renames[1]);

				if(count($columns) == 2)
				{
					$columnName = $columns[1];
					$columnRenamed = array_filter(
						BuilderDatabaseSystemService::listColumns(
							$this->databaseProject,
							$renames[0]
						), function($column) use ($columnName) {
							return $column['name'] == $columnName;
						}
					);

					if($columnRenamed)
					{
						$columnsRenamed[$columnName] = array_shift($columnRenamed);
					}
				}
			}
		}

		TTransaction::close();

		return $columnsRenamed;
	}

	public static function onConfirmCommands($param)
	{
		try
		{
			BuilderPermissionService::checkPermission();

			$databaseMergeSession = TSession::getValue('databaseMergeSession');

			$sqls  = json_decode($databaseMergeSession->data_ColumnDiffForm['sqls'], TRUE);
			$param = array_filter($param, function($key) { return strpos($key, 'commands_') !== FALSE; }, ARRAY_FILTER_USE_KEY);
			$param = array_column($param, 0);

			$databaseMergeSession->formColumnDiffs = $param;

			$commandsSqls = BuilderDatabaseService::getCommandsConfirmeds($param, $sqls);

			$databaseMergeSession->confirmedSqls = $commandsSqls;

			TSession::setValue('databaseMergeSession', $databaseMergeSession);

			AdiantiCoreApplication::loadPage('BuilderViewsDiffForm', 'onLoad');
			AdiantiCoreApplication::registerPage('index.php?class=BuilderViewsDiffForm&method=onLoad');
		}
		catch (Exception $e)
		{
			new TMessage('error', $e->getMessage());
		}
	}

	public function onAnalisar($param)
	{
		$databaseMergeSession = TSession::getValue('databaseMergeSession');

		$this->onLoad(['showConfirm' => true]);

		if( empty($param['processed']) )
		{
			$sqls = BuilderDatabaseService::generateComands(
				$databaseMergeSession->databaseBuilder,
				$databaseMergeSession->table_drops,
				$databaseMergeSession->table_renames,
				$databaseMergeSession->table_news,
				$this->getColumnRenamed($param)??[],
				$databaseMergeSession->tableDiffs,
				$databaseMergeSession->databaseType,
				$this->tablesProject,
				$param
			);

			$param['sqls'] = json_encode($sqls);

			$databaseMergeSession->data_ColumnDiffForm = $param;

			TSession::setValue('databaseMergeSession', $databaseMergeSession);

			if(empty($sqls))
			{
				$this->table->add(new TLabel('<i class="fa fa-exclamation-triangle orange" style="margin-right: 10px;"></i>Sem alterações nas tabelas, base de dados do projeto está igual ao modelo de dados.'));
			}

			AdiantiCoreApplication::registerPage('index.php?class=BuilderColumnDiffForm&method=onAnalisar&processed=1');
		}
		else
		{
			$databaseMergeSession = TSession::getValue('databaseMergeSession');
			$param = $databaseMergeSession->data_ColumnDiffForm;
		}

		$this->validateChanges();

		$warnings = json_encode($this->warnings);

		$dataJson = json_encode($param);

		TScript::create("
			Builder.comandsSqls = {$param['sqls']};
			setTimeout(Builder.setSqlCommands, 500);
			Builder.warningTables = {$warnings};
			Builder.setCustonData({$dataJson});
		");
	}

	public function onLoad($param)
	{
		try
		{
			$databaseMergeSession = TSession::getValue('databaseMergeSession');

			$table_news        = $databaseMergeSession->table_news;
			$table_drops       = $databaseMergeSession->table_drops;
			$table_equals      = $databaseMergeSession->table_equals;
			$table_renames     = $databaseMergeSession->table_renames;
			$table_name_equals = $databaseMergeSession->table_name_equals;

			$this->databaseBuilder = $databaseMergeSession->databaseBuilder;
			$this->databaseProject = $databaseMergeSession->databaseProject;
			$this->databaseType    = $databaseMergeSession->databaseType;
			$this->tablesProject   = BuilderDatabaseSystemService::listTables($this->databaseProject);

			if(empty($this->databaseBuilder) OR empty($this->databaseProject))
			{
				throw new Exception("Verifique as bases de dados");
			}

			if(empty($table_news) AND empty($table_drops) AND empty($table_equals) AND empty($table_renames) AND empty($table_name_equals))
			{
				throw new Exception("Verifique as tabelas");
			}

			$result = BuilderDatabaseService::makeColumnComponentDiff(
				$table_name_equals,
				$table_renames,
				$table_equals,
				$table_drops,
				$table_news,
				! empty($param['showConfirm']),
				$this->databaseBuilder,
				$this->databaseProject,
				$this->databaseType,
				$this->tablesProject
			);

			if(! empty($result->tableFormFieldColumnDiff))
			{
				foreach($result->tableFormFieldColumnDiff as $field)
				{
					$this->form->addField($field);
				}
			}

			if(! empty($result->tablesContainer))
			{
				$this->table->add($result->tablesContainer);
			}

			$row = $this->form->addFields([$this->table]);
			$row->layout = ['col-sm-12'];

			$databaseMergeSession->tableDiffs = $result->tableDiffs??[];
			TSession::setValue('databaseMergeSession', $databaseMergeSession);

			$data = json_encode($databaseMergeSession->formColumnDiffs??[]);

			if(isset($databaseMergeSession->formColumnDiffs))
			{
				TScript::create("Builder.setDataColumnDiff({$data});");
			}
		}
		catch (Exception $e)
		{
			new TMessage('error', $e->getMessage());
		}
	}

	public function validateChanges()
	{
		$databaseMergeSession = TSession::getValue('databaseMergeSession');
		$columnsChanged 	  = array_map(function($item){ return array_keys($item); }, $databaseMergeSession->tableDiffs);
		$tableRenames         = [];

		if(empty($columnsChanged))
		{
			return;
		}

		if(! empty($databaseMergeSession->table_renames))
		{
			foreach ($databaseMergeSession->table_renames as $tables)
			{
				$tables = explode('->', $tables);
				$tableRenames[$tables[1]] = $tables[0];
			}
		}

		foreach($columnsChanged as $table => $columns)
		{
			$tableProject = $tableRenames[$table]??$table;

			$diffs = BuilderDatabaseSystemService::validateChanges($databaseMergeSession->databaseBuilder, $databaseMergeSession->databaseProject, $tableProject, $table, $columns, $tableRenames);

			if(! empty($diffs))
			{
				$this->warnings[$table] = $diffs;
			}
		}
	}

	public function show()
	{
		parent::show();
		TScript::create("$('.table-column-diff-name').click(
			function(evt){
				if( $(this).next().is(':visible') ) {
					$(this).find('i').css('transform', 'rotate(-90deg)');
				} else {
					$(this).find('i').css('transform', 'rotate(0deg)');
				}
				$(this).next().toggle();
		});");
	}
}