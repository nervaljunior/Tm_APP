<?php
/**
 * BuilderDatabaseDiffForm
 *
 * @version    1.0
 * @author     Lucas Tomasi
 */
class BuilderDatabaseDiffForm extends TPage
{
	private $form;
	private static $formName = 'databaseDiffForm';

	public function __construct()
	{
		try
		{
			parent::__construct();

			BuilderPermissionService::checkPermission();

			$this->form = new BootstrapFormBuilder(self::$formName);
	        $this->form->setFormTitle('Database merge');
	        $this->form->setFieldSizes('100%');

	        $databaseBuilder = new TCombo('databaseBuilder');
			$databaseBuilder->addItems(BuilderDatabaseService::listDatabases());

			$databaseProject = new TCombo('databaseProject');
			$databaseProject->setChangeAction(new TAction([$this, 'onSelectBase']));
	        $databaseProject->addItems(BuilderDatabaseSystemService::listDatabases());

	        $databaseBuilder->addValidation('Base de dados do Builder', new TRequiredValidator);
	        $databaseProject->addValidation('Base de dados do projeto', new TRequiredValidator);

	        $tstep = new TStep();
			$tstep->addItem('Escolha das bases', true, false);
			$tstep->addItem('Verificação de tabelas', false, false);
			$tstep->addItem('Verificação de colunas', false, false);
			$tstep->addItem('Validação de comandos', false, false);
			$tstep->addItem('Verificação de views', false, false);
			$tstep->addItem('Confirmação comandos', false, false);

	        $label = new TLabel('Escolha de base de dados');
	        $label->style = 'font-size: 16px;margin-top: 25px;font-weight: bold';

	        $labelSubtitle = new TLabel('Escolha a base de dados modelada no Adianti Builder e a base existente no seu projeto.<br>Atualmente as bases suportadas são do tipo PostgreSQL, MariaDB e MySQL<div id="infobd"></div>');
	        $labelSubtitle->style = 'font-size: 12px;color: #949ea7;margin-bottom: 15px;';

	        $this->form->addContent([$tstep]);
	        $this->form->addContent([$label]);
	        $this->form->addContent([$labelSubtitle]);

			$container = new TVBox;
			$container->id = "database-merge";
	        $container->style = 'width: 100%';
	        $container->add($this->form);

	        $row = $this->form->addFields([new TLabel('Base de dados do Builder'), $databaseBuilder], [new TLabel('Base de dados do projeto'), $databaseProject]);
	        $row->layout = ['col-sm-6', 'col-sm-6'];

	        $onsave = $this->form->addAction("Analisar tabelas", new TAction([$this, 'onAnalisar']), 'fas:arrow-right #ffffff');
	        $onsave->addStyleClass('btn-primary');

	        parent::add($container);
		}
		catch (Exception $e)
		{
			new TMessage('error', $e->getMessage());
		}
	}

	public static function onSelectBase($param)
	{
		try
		{
			if(empty($param['databaseProject']))
			{
				TScript::create("$('#infobd').html(\"\");");
				return;
			}

			TTransaction::open($param['databaseProject']);
			$ini  = TTransaction::getDatabaseInfo();
			$name = $ini['name'];
			$host = $ini['host'];
			$infoBase = "<div style='font-size: 12px; color: #333'>* Base de dados de referência <b class='badge bg-info' style='color: white;font-size: 10px;'>{$name}</b> localizada em <b class='badge bg-info' style='color: white;font-size: 10px;'>{$host}</b></div>";
			TTransaction::close();

			TScript::create("$('#infobd').html(\"{$infoBase}\");");
		}
		catch(Exception $e)
		{
			TScript::create("$('#infobd').html(\"\");");
			TTransaction::close();
		}
	}

	public function onAnalisar()
	{
		try
		{
			$this->form->validate();
			$data = $this->form->getData();

			TTransaction::open($data->databaseProject);
			$ini  = TTransaction::getDatabaseInfo();
			$type = BuilderDatabaseTypeService::getType($ini['type']);
			TTransaction::close();

			$databaseMergeSession = new stdClass;
			$databaseMergeSession->databaseBuilder = $data->databaseBuilder;
			$databaseMergeSession->databaseProject = $data->databaseProject;
			$databaseMergeSession->databaseType    = $type;
			$databaseMergeSession->databaseName    = $ini['name'];
			$databaseMergeSession->databaseLocal   = $ini['host'];

			TSession::setValue('databaseMergeSession', $databaseMergeSession);

			AdiantiCoreApplication::loadPage('BuilderTableDiffForm', 'onLoad');
		}
		catch (Exception $e)
		{
			new TMessage('error', $e->getMessage());
		}
	}

	public function onLoad($param = null)
	{
		$databaseMergeSession = TSession::getValue('databaseMergeSession');

		if($databaseMergeSession)
		{
			$databaseBuilder = $databaseMergeSession->databaseBuilder;
			$databaseProject = $databaseMergeSession->databaseProject;

			$data = new stdClass;
			$data->databaseBuilder = $databaseBuilder;
			$data->databaseProject = $databaseProject;

			TForm::sendData(self::$formName, $data);
		}

		TSession::setValue('databaseMergeSession', NULL);
	}
}