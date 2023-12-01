<?php
/**
 * BuilderConfirmCommandsDiffForm
 *
 * @version    1.0
 * @author     Lucas Tomasi
 */
class BuilderConfirmCommandsDiffForm extends TPage
{
	private $form;

	private static $formName = 'ConfirmCommandsDiffForm';

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

	        $tstep = new TStep();
			$tstep->addItem('Escolha das bases', false, true);
			$tstep->addItem('Verificação de tabelas', false, true );
			$tstep->addItem('Verificação de colunas', false, true);
			$tstep->addItem('Validação de comandos', false, true);
			$tstep->addItem('Verificação de views', false, true);
			$tstep->addItem('Confirmação comandos', true, false);

			$container = new TVBox;
	        $container->id = "database-merge";
	        $container->style = 'width: 100%';
	        $container->add($this->form);

	        $label = new TLabel('Confirmação de comandos');
	        $label->style = 'font-size: 16px;margin-top: 25px;font-weight: bold';

			$infoBase = "<div style='font-size: 12px; color: #333'>* Base de dados de referência <b class='badge bg-info' style='color: white;font-size: 10px;'>{$databaseMergeSession->databaseName}</b> localizada em <b class='badge bg-info' style='color: white;font-size: 10px;'>{$databaseMergeSession->databaseLocal}</b></div>";

	        $labelSubtitle = new TLabel('Confirme os comandos e execute o merge entre as base de dados' . $infoBase);
	        $labelSubtitle->style = 'font-size: 12px;color: #949ea7;margin-bottom: 15px;';

	        $this->form->addContent([$tstep]);
	        $this->form->addContent([$label]);
	        $this->form->addContent([$labelSubtitle]);

	        $divCommands = new TElement('div');
	        $divCommands->add(TElement::tag('label', 'Log de comandos'));
	        $divCommands->id = 'results';

	        $row = $this->form->addFields([$divCommands]);
	        $row->layout = ['col-sm-12'];
	        $row->style  = 'display: none';

		    $commands = new TText('commands');
	        $commands->setSize('100%', 200);

	        $row = $this->form->addFields([$commands]);
			$row->layout = ['col-sm-12'];

	        $aceite = new TCheckGroup('aceite');
	        $aceite->setChangeFunction('Builder.toogleButtonExecutar(this);');
	        $aceite->addItems([1 => 'Li todos os comandos e desejo executar os mesmos na base de dados do projeto']);

	        $row = $this->form->addFields([new TLabel('&nbsp;'), $aceite]);
	        $row->layout = ['col-sm-12'];

	        $this->form->addAction("Voltar", new TAction(['BuilderViewsDiffForm', 'onLoad']), 'fas:arrow-left red');

	        $action = $this->form->addAction("Executar", new TAction([$this, 'onExecutar']), 'fas:bolt white');
	        $action->disabled = true;
	        $action->addStyleClass('bg-green');

	        parent::add($container);
		}
		catch (Exception $e)
		{
			new TMessage('error', $e->getMessage());
		}
	}

	public static function executar($param)
	{
		$commandsCorrects = [];
		$commandError     = '';

		try
		{
			BuilderPermissionService::checkPermission();

			$databaseMergeSession = TSession::getValue('databaseMergeSession');

			$conn = TTransaction::open($databaseMergeSession->databaseProject);

			$commands = explode(';', $param['commands']);

			foreach($commands as $command)
			{
				$command = trim($command);

				if(! $command)
				{
					continue;
				}

				try
				{
					$conn->query($command);

					$commandsCorrects[] = $command;
				}
				catch (Exception $e)
				{
					$commandError = $command;
					throw new Exception("Houve um erro ao executar o comando:<br><b>{$command}</b><br>{$e->getMessage()}");
				}
			}

			$pdo = TTransaction::get();
			if($pdo->inTransaction())
			{
				TTransaction::close();
			}

			TSession::setValue('databaseMergeSession', NULL);

			new TMessage('info', 'Merge executado com sucesso', new TAction(['BuilderDatabaseDiffForm', 'onLoad']));
		}
		catch (Exception $e)
		{
			$ini  = TTransaction::getDatabaseInfo();

			if(BuilderDatabaseTypeService::MYSQL == BuilderDatabaseTypeService::getType($ini['type']))
			{
				$commandsCorrects = json_encode($commandsCorrects);
				TScript::create("Builder.processErrors({$commandsCorrects}, '{$commandError}');");
			}

			new TMessage('error', $e->getMessage());
			
			TTransaction::rollback();
		}
	}

	public static function onExecutar($param)
	{
		$icon = new TImage('fa:question-circle fa-4x blue');
		$label = new TLabel("Essa ação não poderá ser desfeita e ocasionará na alteração da estrutura base de dados do projeto, deseja continuar? ");
		$commands = new THidden('commands');
		$commands->setValue($param['commands']);

		$form = new BootstrapFormBuilder('executar');
		$row = $form->addFields([$icon],[$label]);
		$row->layout = ['col-sm-2', 'col-sm-10'];

		$row = $form->addFields([$commands]);
		$row->style = 'display: none';
		
		$button = $form->addAction('Executar', new TAction(['BuilderConfirmCommandsDiffForm', 'executar']), 'fas:bolt white');
		$button->addStyleClass('bg-green');

		new TInputDialog('Atenção', $form);
	}

	public function onLoad($param)
	{
		try
		{
			$databaseMergeSession = TSession::getValue('databaseMergeSession');

			$commands = '';

			if(! empty($databaseMergeSession->confirmedSqls))
			{
				$queryes       = trim($databaseMergeSession->confirmedSqls, "\n");
				$arrayCommands = explode(';', $queryes);
				$arrayCommands = array_map(function($query){ return trim($query); }, $arrayCommands);
				$uniqCommands  = array_unique($arrayCommands);
				$commandsUniqs = implode(";", $uniqCommands);
				$commandsUniqs = str_replace(";", ";\n\n", $commandsUniqs);
				$commands     .= $commandsUniqs;
			}

			if(! empty($databaseMergeSession->views))
			{
				$commands .= $databaseMergeSession->views . "\n";
			}

			if(empty($commands))
			{
				TToast::show('info', 'Sem modificações para realizar', 'center');
				TCheckGroup::disableField(self::$formName, 'aceite');
				TText::disableField(self::$formName, 'commands');
			}

			$data = new stdClass;
			$data->commands = $commands;

			TForm::sendData(self::$formName, $data);
		}
		catch (Exception $e)
		{
			new TMessage('error', $e->getMessage());
		}
	}
}