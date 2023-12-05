<?php

class ProjetoDartCardList extends TPage
{
    private $form; // form
    private $cardView; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'darti_db';
    private static $activeRecord = 'ProjetoDart';
    private static $primaryKey = 'id';
    private static $formName = 'form_ProjetoDartCardList';
    private $showMethods = ['onReload', 'onSearch'];

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);

        // define the form title
        $this->form->setFormTitle("Home");

        $nome_projeto = new TEntry('nome_projeto');

        $nome_projeto->setSize('100%');
        $nome_projeto->setMaxLength(255);

        $row1 = $this->form->addFields([new TLabel("Nome:", null, '14px', null, '100%'),$nome_projeto]);
        $row1->layout = [' col-sm-12'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

        $this->cardView = new TCardView;

        $this->cardView->setContentHeight(170);
        $this->cardView->setTitleTemplate('{nome_projeto}');
        $this->cardView->setColorAttribute('cor');
        $this->cardView->setItemTemplate("{users_permitidos} <br>
{descricao}  ");

        $this->cardView->setItemDatabase(self::$database);

        $this->filter_criteria = new TCriteria;

        $filterVar = TSession::getValue("userid");
        $this->filter_criteria->add(new TFilter('id', 'in', "(SELECT projeto_dart_id FROM user_permission_projeto WHERE user = '{$filterVar}')"));

        $action_ProjetoDartForm_onVisualizar = new TAction(['ProjetoDartForm', 'onVisualizar'], ['key'=> '{id}']);

        $this->cardView->addAction($action_ProjetoDartForm_onVisualizar, "Ação", 'fas:book-reader #3F51B5', null, "Ação", false); 

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));

        $panel = new TPanelGroup;
        $panel->add($this->cardView);

        $panel->addFooter($this->pageNavigation);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(TBreadCrumb::create(["Home","Home"]));
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);

    }

    /**
     * Register the filter in the session
     */
    public function onSearch($param = null)
    {
        // get the search form data
        $data = $this->form->getData();
        $filters = [];

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        if (isset($data->nome_projeto) AND ( (is_scalar($data->nome_projeto) AND $data->nome_projeto !== '') OR (is_array($data->nome_projeto) AND (!empty($data->nome_projeto)) )) )
        {

            $filters[] = new TFilter('nome_projeto', 'like', "%{$data->nome_projeto}%");// create the filter 
        }

        $param = array();
        $param['offset']     = 0;
        $param['first_page'] = 1;

        // fill the form with data again
        $this->form->setData($data);

        // keep the search data in the session
        TSession::setValue(__CLASS__.'_filter_data', $data);
        TSession::setValue(__CLASS__.'_filters', $filters);

        $this->onReload($param);
    }

    public function onReload($param = NULL)
    {
        try
        {

            // open a transaction with database 'darti_db'
            TTransaction::open(self::$database);

            // creates a repository for ProjetoDart
            $repository = new TRepository(self::$activeRecord);
            $limit = 20;

            $criteria = clone $this->filter_criteria;

            if (empty($param['order']))
            {
                $param['order'] = 'id';    
            }

            if (empty($param['direction']))
            {
                $param['direction'] = 'desc';
            }

            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);

            if($filters = TSession::getValue(__CLASS__.'_filters'))
            {
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       
                }
            }

            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

            $this->cardView->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {

                    $object->users_permitidos = call_user_func(function($value, $object, $row)
                    {

                            // Código gerado pelo snippet: "Conexão com banco de dados"
                            TTransaction::open('darti_db');

                                    $array_ids_produtos = Configuracao::get_ids_produtos_by_projeto($object->id);

                                    $count_tarefas = Configuracao::get_quant_tarefas_by_produtos($array_ids_produtos);

                            TTransaction::close();

                            $html = '<ul style="font-size: 18px;">
                                        <li><strong>Total de Aplicações:</strong>&nbsp;&nbsp;<span style=" color:green;">'.count($array_ids_produtos).'</span></li>
                                        <li><strong>Total de Tarefas:</strong>&nbsp;&nbsp;<span style=" color:green;">'.$count_tarefas.'</span></li>
                                    </ul>';

                        return $html;

                    }, $object->users_permitidos, $object, null);

                    $this->cardView->addItem($object);

                }
            }

            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);

            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit

            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }

    public function onShow($param = null)
    {

    }

    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  $this->showMethods))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }

}

