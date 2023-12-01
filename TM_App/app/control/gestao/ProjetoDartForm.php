<?php

class ProjetoDartForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'darti_db';
    private static $activeRecord = 'ProjetoDart';
    private static $primaryKey = 'id';
    private static $formName = 'form_ProjetoDartForm';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Cadastro de projeto");


        $nome_projeto = new TEntry('nome_projeto');
        $cor = new TColor('cor');
        $id = new THidden('id');
        $descricao = new THtmlEditor('descricao');
        $bpage = new BPageContainer();
        $bpage_grantt = new BPageContainer();
        $bpage_grafico = new BPageContainer();
        $bpage_kanban = new BPageContainer();
        $users = new TDBCheckGroup('users', 'permission', 'SystemUsers', 'id', '{name}','name asc'  );

        $nome_projeto->addValidation("Nome", new TRequiredValidator()); 

        $nome_projeto->setMaxLength(255);
        $users->setLayout('horizontal');
        $users->setValue([TSession::getValue("userid")]);
        $users->setBreakItems(4);
        $bpage->setAction(new TAction(['ProdutosDartList', 'onShow'], $param));
        $bpage_kanban->setAction(new TAction(['TarefasDartKanbanView', 'onShow']));
        $bpage_grafico->setAction(new TAction(['ProdutoTarefasDartForm', 'onShow']));
        $bpage_grantt->setAction(new TAction(['TarefasDartGanttFormView', 'onShow']));

        $bpage->setId('b6509bd1072472');
        $bpage_grantt->setId('bpage_grantt');
        $bpage_kanban->setId('bpage_kanban');
        $bpage_grafico->setId('bpage_grafico');

        $bpage->hide();
        $bpage_grantt->hide();
        $bpage_kanban->hide();
        $bpage_grafico->hide();

        $id->setSize(200);
        $cor->setSize('100%');
        $bpage->setSize('100%');
        $users->setSize('100%');
        $nome_projeto->setSize('100%');
        $bpage_grantt->setSize('100%');
        $bpage_kanban->setSize('100%');
        $bpage_grafico->setSize('100%');
        $descricao->setSize('100%', 150);

        $loadingContainer = new TElement('div');
        $loadingContainer->style = 'text-align:center; padding:50px';

        $icon = new TElement('i');
        $icon->class = 'fas fa-spinner fa-spin fa-3x';

        $loadingContainer->add($icon);
        $loadingContainer->add('<br>Carregando');

        $bpage->add($loadingContainer);
        $loadingContainer = new TElement('div');
        $loadingContainer->style = 'text-align:center; padding:50px';

        $icon = new TElement('i');
        $icon->class = 'fas fa-spinner fa-spin fa-3x';

        $loadingContainer->add($icon);
        $loadingContainer->add('<br>Carregando');

        $bpage_grantt->add($loadingContainer);
        $loadingContainer = new TElement('div');
        $loadingContainer->style = 'text-align:center; padding:50px';

        $icon = new TElement('i');
        $icon->class = 'fas fa-spinner fa-spin fa-3x';

        $loadingContainer->add($icon);
        $loadingContainer->add('<br>Carregando');

        $bpage_grafico->add($loadingContainer);
        $loadingContainer = new TElement('div');
        $loadingContainer->style = 'text-align:center; padding:50px';

        $icon = new TElement('i');
        $icon->class = 'fas fa-spinner fa-spin fa-3x';

        $loadingContainer->add($icon);
        $loadingContainer->add('<br>Carregando');

        $bpage_kanban->add($loadingContainer);

        $this->bpage = $bpage;
        $this->bpage_grantt = $bpage_grantt;
        $this->bpage_grafico = $bpage_grafico;
        $this->bpage_kanban = $bpage_kanban;

        $this->form->appendPage("Dados");

        $this->form->addFields([new THidden('current_tab')]);
        $this->form->setTabFunction("$('[name=current_tab]').val($(this).attr('data-current_page'));");

        $row1 = $this->form->addFields([new TLabel("Nome:", '#000000', '14px', null, '100%'),$nome_projeto],[new TLabel("Cor:", null, '14px', null, '100%'),$cor]);
        $row1->layout = [' col-sm-8',' col-sm-4'];

        $row2 = $this->form->addFields([$id,new TLabel("Descrição:", null, '14px', null, '100%'),$descricao]);
        $row2->layout = [' col-sm-12'];

        $this->form->appendPage("Produtos");
        $row3 = $this->form->addFields([$bpage]);
        $row3->layout = [' col-sm-12'];

        $this->form->appendPage("Tarefas");
        $row4 = $this->form->addFields([$bpage_grantt],[$bpage_grafico]);
        $row4->layout = [' col-sm-8',' col-sm-4'];

        $this->form->appendPage("Kanban Tarefas");
        $row5 = $this->form->addFields([$bpage_kanban]);
        $row5->layout = [' col-sm-12'];

        $this->form->appendPage("Usuários com permissão");
        $row6 = $this->form->addFields([$users]);
        $row6->layout = [' col-sm-12'];

        // create the form actions
        $btn_onshow = $this->form->addAction("Voltar", new TAction(['ProjetoDartList', 'onShow']), 'fas:arrow-left #000000');
        $this->btn_onshow = $btn_onshow;

        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Gestão","Cadastro de projeto"]));
        }
        $container->add($this->form);

        $btn_onsave->setId("tbutton_btn_salvar");

        parent::add($container);

    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new ProjetoDart(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 

            $repository = UserPermissionProjeto::where('projeto_dart_id', '=', $object->id);
            $repository->delete(); 

            if ($data->users) 
            {
                foreach ($data->users as $users_value) 
                {
                    $user_permission_projeto = new UserPermissionProjeto;

                    $user_permission_projeto->user = $users_value;
                    $user_permission_projeto->projeto_dart_id = $object->id;
                    $user_permission_projeto->store();
                }
            }

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle'); 

            $loadPageParam['key'] = $object->id;

            TApplication::loadPage('ProjetoDartList', 'onEdit', $loadPageParam);

        }
        catch (Exception $e) // in case of exception
        {
            //</catchAutoCode> 

            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open(self::$database); // open a transaction

                $object = new ProjetoDart($key); // instantiates the Active Record 

                TSession::setValue("projeto_id", $object->id );

                $this->form->setCurrentPage(1);

                                $this->bpage->unhide();
                $this->bpage->setParameter('projeto_id', $object->id);
                $this->bpage_grantt->unhide();
                $this->bpage_grafico->unhide();
                $this->bpage_kanban->unhide();

                $object->users = UserPermissionProjeto::where('projeto_dart_id', '=', $object->id)->getIndexedArray('user', 'user');

                $this->form->setData($object); // fill the form 

                TTransaction::close(); // close the transaction 
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(true);

    }

    public function onShow($param = null)
    {

        $users_id = Configuracao::get_users_permitidos();

        if(!in_array(TSession::getValue('userid'),$users_id)){

                    TApplication::loadPage('ProjetoDartList', 'onShow');
        }

    } 

    public  function onVisualizar($param = null) 
    {
        try 
        {
            TScript::create('document.getElementById("tbutton_btn_salvar").style.display = "none";'); // OCULTA O BOTÃO SALVAR 
    	    TScript::create('$(`.nav-item a:contains(Usuários com permissão)`).closest("li").hide();');

            $this->onEdit($param); // CARREGA OS DADOS NO FORMULÁRIO

    	    $this->form->setEditable(false); # DEFINE OS CAMPOS DO FORMULÁRIO COMO NÃO EDITÁVEIS

        }
        catch (Exception $e) 
        {
           new TMessage('error', $e->getMessage()); 
            TTransaction::rollback();   
        }
    }

}

