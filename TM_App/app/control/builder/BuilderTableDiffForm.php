<?php
/**
 * BuilderTableDiffForm
 *
 * @version    1.0
 * @author     Lucas Tomasi
 */
class BuilderTableDiffForm extends TPage
{
	private $form;
	private $tablesBuilder;
	private $tablesProject;

	private $table;

	private static $formName = 'tableDiffForm';

	private static $NEW    	   = 1;
	private static $EQUAL  	   = 2;
	private static $RENAME 	   = 3;
	private static $DROP   	   = 4;
	private static $NAME_EQUAL = 5;
	private static $NOT_FOUND  = 6;

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

	        $this->table = new TTable;
	        $this->table->id    = 'table-diffs';
	        $this->table->class = 'table table-condensed table-bordered table-striped';

	        $row = new TTableRow('thead');
			$cell = $row->addCell(new TLabel("Ações"));
			$cell->style = 'width: 100px';
			$cell = $row->addCell(new TLabel("Tabelas da base de dados"));
			$cell = $row->addCell(new TLabel("Tabelas do Builder"));
			$cell = $row->addCell(new TLabel("Situação"));
			$cell->style = 'width: 300px';

	        $header = $this->table->addSection('thead');
	        $header->add($row);

	        $row = $this->form->addFields([new THidden('db_builder'), new THidden('db_project')]);
	        $row->style = 'display: none';

			$this->form->addAction("Voltar", new TAction(['BuilderDatabaseDiffForm', 'onLoad']), 'fas:arrow-left red');

	        $onsave = $this->form->addAction("Analisar colunas", new TAction([$this, 'onAnalisar']), 'fas:arrow-right #ffffff');
	        $onsave->addStyleClass('btn-primary right');

			$onview = $this->form->addAction("Analisar views", new TAction(['BuilderViewsDiffForm', 'onLoad']), 'fas:arrow-right #ffffff');
	        $onview->addStyleClass('btn-primary right');

	        $tstep = new TStep();
			$tstep->addItem('Escolha das bases', false, true);
			$tstep->addItem('Verificação de tabelas', true, false);
			$tstep->addItem('Verificação de colunas', false, false);
			$tstep->addItem('Validação de comandos', false, false);
			$tstep->addItem('Verificação de views', false, false);
			$tstep->addItem('Confirmação comandos', false, false);

			$container = new TVBox;
	        $container->id = "database-merge";
	        $container->style = 'width: 100%';
	        $container->add($this->form);

			$label = new TLabel('Verificação de tabelas');
	        $label->style = 'font-size: 16px;margin-top: 25px;font-weight: bold';

			$infoBase = "<div style='font-size: 12px; color: #333'>* Base de dados de referência <b class='badge bg-info' style='color: white;font-size: 10px;'>{$databaseMergeSession->databaseName}</b> localizada em <b class='badge bg-info' style='color: white;font-size: 10px;'>{$databaseMergeSession->databaseLocal}</b></div>";
	        $labelSubtitle = new TLabel("Verifique as alterações em relação as tabelas{$infoBase}");
	        $labelSubtitle->style = 'font-size: 12px;color: #949ea7;margin-bottom: 15px;';

	        $this->form->addContent([$tstep]);
	        $this->form->addContent([$label]);
	        $this->form->addContent([$labelSubtitle]);
	        $this->form->addContent([$this->table]);

	        parent::add($container);
		}
		catch (Exception $e)
		{
			new TMessage('error', $e->getMessage());
		}
	}

	private function getStatus($tableBuilder, $tablesProject, $status)
	{
		if(self::$NAME_EQUAL == $status)
		{
			$label = new TElement('label');
			$label->class = 'label';

			$span = new TElement('span');
			$span->add('colunas modificadas');
			$span->class = 'badge bg-brown';

			$label->add("A tabela <b>{$tableBuilder}</b> terá suas ");
			$label->add($span);
		}
		elseif(self::$NOT_FOUND == $status)
		{
			$label = new TElement('label');
			$label->class = 'label';

			$span = new TElement('span');
			$span->class = 'badge bg-info';
			$span->add("não foi encontrada");

			$label->add("A tabela <b>{$tableBuilder}</b>  ");
			$label->add($span);
			$label->add(" na base de dados");
		}
		else
		{
			$label = new TElement('label');
			$label->class = 'label';

			$span = new TElement('span');
			$span->class = 'badge bg-red';
			$span->add("não foi encontrada");

			$label->add("A tabela <b>{$tablesProject}</b>  ");
			$label->add($span);
			$label->add(" no modelo do Builder");
		}

		$container = new TElement('div');
		$container->class = 'div-status';
		$container->add($label);
		$container->style = 'display: flex;width: 100%';

		return $container;
	}

	private function getItemsComboTable($tables)
	{
		$items = [];
		$items[''] = 'Selecione a tabela da base de dados';
		foreach ($tables as $table)
		{
			$items[$table] = $table;
		}

		return $items;
	}

	private static function getRenameTables($param)
	{
		$renamesTables = array_filter($param, function($val , $key){ return $val && strpos($key, 'news_') === 0; }, ARRAY_FILTER_USE_BOTH);

		$renames = [];

		if(empty($renamesTables))
		{
			return $renames;
		}

		foreach ($renamesTables as $key => $value)
		{
			$key = substr($key, 5);
			$renames[] = "{$value}->{$key}";
		}

		return $renames;
	}

	public static function onAnalisar($param)
	{
		try
		{
			BuilderPermissionService::checkPermission();
			$databaseMergeSession = TSession::getValue('databaseMergeSession');

			$param['renames'] = self::getRenameTables($param);
			$param['drops']   = $param['drops'] ?? [];

			if(! empty($databaseMergeSession->diffsTables['not_found']) AND (empty($param['news']) || count($param['news']) < count($databaseMergeSession->diffsTables['not_found'])))
			{
				throw new Exception("Você precisa definir <b>todas</b> as ações das tabelas que <span style='color:white;' class='badge bg-info'>não foram encontradas</span> na base de dados", 1);
			}

			if((! empty($databaseMergeSession->diffsTables['drops']) AND empty($param['drops'])) AND (count($param['drops']) + count($param['renames'])) < count($databaseMergeSession->diffsTables['drops']))
			{
				throw new Exception("Você precisa definir <b>todas</b> as ações das tabelas que <span  style='color:white;' class='badge bg-red'>não foram encontradas</span> no modelo de dados do Builder", 1);
			}

			if (! empty($databaseMergeSession->diffsTables['name_equals']) AND count($param['name_equals']??[]) !=  count($databaseMergeSession->diffsTables['name_equals']))
			{
				throw new Exception("Você precisa definir <b>todas</b> as ações das tabelas que possuem <span  style='color:white;' class='badge bg-brown'>colunas modificadas</span>", 1);
			}

			$news        = empty($param['news'])        ? [] : array_column($param['news'], 0);
			$drops       = empty($param['drops'])       ? [] : array_column($param['drops'], 0);
			$nameEquals  = empty($param['name_equals']) ? [] : array_column($param['name_equals'], 0);

			$markRenames = empty($param['news'])  ? 0  : array_filter($news, function($value){ return $value === ''; });
			$tableNews   = empty($param['news'])  ? 0  : array_filter($news, function($value){ return $value !== ''; });
			$tablesDrops = empty($param['drops']) ? [] : array_filter($drops, function($value){ return $value !== ''; });
			$tablesNameEquals = empty($param['name_equals']) ? [] : array_filter($nameEquals, function($value){ return $value !== ''; });

			if($markRenames AND count($param['renames']) < count($markRenames) )
			{
				throw new Exception("Você precisa escolher <b>todas</b> as tabelas da base de dados que serão alteradas", 1);
			}

			$databaseMergeSession->data_TableDiffForm = $param;
			$databaseMergeSession->table_news         = $tableNews??[];
			$databaseMergeSession->table_drops        = $tablesDrops??[];
			$databaseMergeSession->table_renames      = $param['renames']??[];
			$databaseMergeSession->table_equals       = $param['equals']??[];
			$databaseMergeSession->table_name_equals  = $tablesNameEquals??[];

			TSession::setValue('databaseMergeSession', $databaseMergeSession);
			
			if(
				empty($databaseMergeSession->table_news) AND
				empty($databaseMergeSession->table_name_equals) AND
				empty($databaseMergeSession->table_drops) AND
				empty($databaseMergeSession->table_renames)
			) {
				$databaseMergeSession->tablesEquals = true;
				AdiantiCoreApplication::loadPage('BuilderViewsDiffForm', 'onLoad');
			} else {
				$databaseMergeSession->tablesEquals = false;
				AdiantiCoreApplication::loadPage('BuilderColumnDiffForm', 'onLoad');
			}
		}
		catch (Exception $e)
		{
			new TMessage('error', $e->getMessage());
		}
	}

	public function onLoad($param = null)
	{
		try
		{
			$databaseMergeSession = TSession::getValue('databaseMergeSession');

			if(empty($databaseMergeSession->databaseBuilder) OR empty($databaseMergeSession->databaseProject))
			{
				throw new Exception("Selecione as bases do Builder e do projeto");
			}

			$tablesBuilder = BuilderDatabaseService::listTables($databaseMergeSession->databaseBuilder);
			$tablesProject = BuilderDatabaseSystemService::listTables($databaseMergeSession->databaseProject);

			$diffs = BuilderDatabaseService::getDifferencesTables($tablesBuilder, $tablesProject, $databaseMergeSession->databaseType);

			$databaseMergeSession->diffsTables = $diffs;

			$itemsProject = $this->getItemsComboTable($diffs['drops']??[]);

			$body = $this->table->addSection('tbody');

			if(! empty($diffs['name_equals']))
			{
				foreach ($diffs['name_equals'] as $table)
				{
					$labelBuilder = new TLabel($table);
					$labelProject = new TLabel($table);

					$alterar = new TElement('div');
					$alterar->add('Alterar');
					$alterar->title = "Ajustar as colunas da tabela {$table}";

					$manter = new TElement('div');
					$manter->add('Manter');
					$manter->title = "Manter a tabela {$table} sem alterações";

					$checkEqual = new TCheckGroup('name_equals[]');
					$checkEqual->setLayout('horizontal');
					$checkEqual->addItems(['' => $manter, $table => $alterar]);
					$checkEqual->setUseButton();

					if(! isset($databaseMergeSession->table_name_equals))
					{
						$checkEqual->setValue([$table]);
					}

					$labels = $checkEqual->getLabels();
					array_walk($labels, function($label) use ($table) { return $label->{'data-reference'} = $table; });

					$row = new TTableRow();
					$row->addCell($checkEqual);
					$row->addCell($labelProject);
					$row->addCell($labelBuilder);
					$row->addCell($this->getStatus($table, $table, self::$NAME_EQUAL));
					$body->add($row);

					$this->form->addField($checkEqual);
				}
			}

			if(! empty($diffs['not_found']))
			{
				foreach ($diffs['not_found'] as $table)
				{
					$label = new TLabel($table);

					$comboNew = new TCombo("news_{$table}");
					$comboNew->setSize('100%');
					$comboNew->setChangeFunction('Builder.setRenameTable(this);');
					$comboNew->addItems($itemsProject);
					$comboNew->setDefaultOption(false);
					$comboNew->setEditable(!empty($itemsProject));
					$comboNew->style = 'display: none';

					$alterar = new TElement('div');
					$alterar->add('Alterar');
					$alterar->title = "Renomar a tabela {$table} e ajustar as colunas";

					$criar = new TElement('div');
					$criar->add('Criar');
					$criar->title = "Adicionar a tabela {$table} na base de dados";

					$checkNotFound = new TCheckGroup('news[]');
					$checkNotFound->setLayout('horizontal');
					$checkNotFound->addItems([''=> $alterar, $table=> $criar]);
					$checkNotFound->setUseButton();

					$labels = $checkNotFound->getLabels();
					array_walk($labels, function($label) use ($table) { return $label->{'data-reference'} = $table; });

					$row = new TTableRow();
					$row->addCell($checkNotFound);
					$row->addCell($comboNew);
					$row->addCell($label);
					$row->addCell($this->getStatus($table, null, self::$NOT_FOUND));
					$body->add($row);

					$this->form->addField($comboNew);
				}
			}

			if(! empty($diffs['drops']))
			{
				foreach ($diffs['drops'] as $table)
				{
					$label = new TLabel($table);

					$manter = new TElement('div');
					$manter->add('Manter');
					$manter->title = "Manter a tabela {$table} na base de dados";

					$deletar = new TElement('div');
					$deletar->add('Apagar');
					$deletar->title = "Apagar a tabela {$table} e as suas chaves estrangeiras na base de dados";

					$checkDrop = new TCheckGroup('drops[]');
					$checkDrop->setLayout('horizontal');
					$checkDrop->addItems(['' => $manter, $table => $deletar]);
					$checkDrop->setUseButton();

					$labels = $checkDrop->getLabels();
					array_walk($labels, function($label) use ($table) { return $label->{'data-reference'} = $table; });

					$row = new TTableRow();
					$row->addCell($checkDrop);
					$row->addCell($label);
					$row->addCell('');
					$row->addCell($this->getStatus(null, $table, self::$DROP));
					$body->add($row);

					$this->form->addField($checkDrop);
				}
			}

			if(empty($diffs['name_equals']) AND empty($diffs['not_found']) AND empty($diffs['drops']))
			{
				$row = new TTableRow();
				$row->addCell("<br/><i class='fas fa-exclamation-triangle orange'></i><br/>Nenhuma modificação a ser realizada.<br/>A base de dados do projeto está igual ao modelo de dados do Builder<br/><br/>")->colspan = 4;
				$body->add($row);
				$databaseMergeSession->tablesEquals = true;
				TScript::create("$('#tbutton_btn_analisar_colunas').hide();");
			}
			else
			{
				$databaseMergeSession->tablesEquals = false;
				TScript::create("$('#tbutton_btn_analisar_views').hide();");
			}

			TSession::setValue('databaseMergeSession', $databaseMergeSession);

			TScript::create('Builder.adjustChecksTablesDiff();');

			$this->fireEvents();
		}
		catch (Exception $e)
		{
			new TMessage('error', $e->getMessage());
		}
	}

	public function fireEvents()
	{
		$databaseMergeSession = TSession::getValue('databaseMergeSession');

		if(empty($databaseMergeSession->data_TableDiffForm))
		{
			return;
		}

		$data = new stdClass;
		$checkeds = [];

		foreach ($databaseMergeSession->data_TableDiffForm as $key => $value)
		{
			if( strpos($key, 'news_') === 0 && $value)
			{
				$data->{$key} = $value;
				$checkeds[] = $key;
			}
		}

		$table_equals = $databaseMergeSession->table_name_equals??[];
		$table_news = $databaseMergeSession->table_news??[];
		$table_drops = $databaseMergeSession->table_drops??[];

		sort($table_equals);
		sort($table_drops);
		sort($table_news);


		$nameEquals = json_encode($table_equals);
		$news = json_encode($table_news);
		$drops = json_encode($table_drops);
		$checkeds = json_encode($checkeds);

		TForm::sendData(self::$formName, $data, FALSE, FALSE);

		TScript::create("Builder.setDataNewTable($news);");
		TScript::create("Builder.setDataDropTable($drops);");
		TScript::create("Builder.setDataTableNameEquals($nameEquals);");
		TScript::create("Builder.setDataRenameTable($checkeds);");
	}
}