<?php
/**
 * BuilderViewsDiffForm
 *
 * @version    1.0
 * @author     Lucas Tomasi
 */
class BuilderViewsDiffForm extends TPage
{
	private $form;
	private $views;
	private $viewsBuilder;

	private static $formName = 'viewsDiffForm';

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

	        $this->form->addAction("Voltar", new TAction(['BuilderColumnDiffForm', 'onAnalisar'], ['processed' => 1]), 'fas:arrow-left red');
	        $this->form->addAction("Voltar para analise de tabelas", new TAction(['BuilderTableDiffForm', 'onLoad']), 'fas:arrow-left red');

	        $onsave = $this->form->addAction('Confirmar comandos', new TAction([$this, 'onConfirmar']), 'fas:arrow-right #ffffff');
	        $onsave->addStyleClass('btn-primary right');

	        $tstep = new TStep();
			$tstep->addItem('Escolha das bases', false, true);
			$tstep->addItem('Verificação de tabelas', false, true );
			$tstep->addItem('Verificação de colunas', false, true);
			$tstep->addItem('Validação de comandos', false, true);
			$tstep->addItem('Verificação de views', true, false);
			$tstep->addItem('Confirmação comandos', false, false);

			$container = new TVBox;
	        $container->id = "database-merge";
	        $container->style = 'width: 100%';
	        $container->add($this->form);

	        $label = new TLabel('Verificação de views');
	        $label->style = 'font-size: 16px;margin-top: 25px;font-weight: bold';

			$infoBase = "<div style='font-size: 12px; color: #333'>* Base de dados de referência <b class='badge bg-info' style='color: white;font-size: 10px;'>{$databaseMergeSession->databaseName}</b> localizada em <b class='badge bg-info' style='color: white;font-size: 10px;'>{$databaseMergeSession->databaseLocal}</b></div>";

	        $labelSubtitle = new TLabel('Verifique as views que serão criadas ou recriadas'.$infoBase);
	        $labelSubtitle->style = 'font-size: 12px;color: #949ea7;margin-bottom: 15px;';

	        $this->views = new TElement('div');

	        $this->form->addContent([$tstep]);
	        $this->form->addContent([$label]);
	        $this->form->addContent([$labelSubtitle]);
	        $this->form->addContent([$this->views]);

	        parent::add($container);
		}
		catch (Exception $e)
		{
			new TMessage('error', $e->getMessage());
		}
	}

	public static function onConfirmar($param)
	{
		try
		{
			BuilderPermissionService::checkPermission();

			$databaseMergeSession = TSession::getValue('databaseMergeSession');
			$databaseMergeSession->views = BuilderDatabaseService::getCommandsViews(
				$databaseMergeSession->databaseBuilder,
				array_column($param['views_new']??[], 0),
				array_column($param['views_equal']??[], 0),
				array_column($param['views_drop']??[], 0)
			);

			TSession::setValue('databaseMergeSession', $databaseMergeSession);

			AdiantiCoreApplication::loadPage('BuilderConfirmCommandsDiffForm', 'onLoad');
		}
		catch (Exception $e)
		{
			new TMessage('error', $e->getMessage());
		}
	}

	private function makeContainer($view, $type)
	{
		$check = new TCheckGroup("views_{$type}[]");
		$check->setLayout('horizontal');
		$check->addItems([$view => '']);
        $check->checkAll();

		$spanCheck = new TElement('span');
		$spanCheck->style = 'float: left;';
		$spanCheck->add($check);

		$div = new TElement('div');
		$div->style = 'display: flex;margin: 10px; width: unset';
		$div->class = 'container-table-diff '.$type;

		$span = new TElement('span');
		$span->style = 'float: left;';
		$span->add($view);

		$divHelp = new TElement('div');
		$divHelp->style = 'color: grey; font-size: 12px';

		if($type == 'new')
		{
			$divHelp->add('Nova view');
		}
		elseif($type == 'drop')
		{
			$divHelp->add('View não encontrada no Adianti Builde e será removida');
		}
		else
		{
			$divHelp->add('View existente e será recriada');
		}

		$container = new TElement('div');
		$container->style = 'margin: 10px; width: unset';
		$container->add($divHelp);
		$container->add($spanCheck);
		$container->add($span);


		$div->add($container);

		return $div;
	}

	public function onLoad($param)
	{
		try
		{
			$databaseMergeSession = TSession::getValue('databaseMergeSession');

			$diffs = BuilderDatabaseService::getDifferencesViews(
				BuilderDatabaseService::listViews($databaseMergeSession->databaseBuilder),
				BuilderDatabaseSystemService::listViews($databaseMergeSession->databaseProject)
			);

			$itens = [];

			if(!empty($diffs['news']))
			{
				foreach($diffs['news'] as $view)
				{
					$this->views->add($this->makeContainer($view, 'new'));
				}
			}

			if(!empty($diffs['drops']))
			{
				foreach($diffs['drops'] as $view)
				{
					$this->views->add($this->makeContainer($view, 'drop'));
				}
			}

			if(!empty($diffs['equals']))
			{
				foreach($diffs['equals'] as $view)
				{
					$this->views->add($this->makeContainer($view, 'equal'));
				}
			}

			if(empty($diffs))
			{
				$this->views->add(new TLabel('<i class="fa fa-exclamation-triangle orange" style="margin-right: 10px;"></i>Não há views no seu projeto e nem no seu modelo de dados para validações'));
			}

			if($databaseMergeSession->tablesEquals) {
				TScript::create("$('#tbutton_btn_voltar').hide();");
			} else {
				TScript::create("$('#tbutton_btn_voltar_para_analise_de_tabelas').hide();");
			}
		}
		catch (Exception $e)
		{
			new TMessage('error', $e->getMessage());
		}
	}
}