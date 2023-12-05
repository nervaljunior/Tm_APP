<?php

class ProdutoTarefasDartForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'darti_db';
    private static $activeRecord = 'ProdutoTarefasDart';
    private static $primaryKey = 'id';
    private static $formName = 'form_ProdutoTarefasDartForm';

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
        $this->form->setFormTitle("");

        $criteria_id = new TCriteria();

        $filterVar = 1;
        $criteria_id->add(new TFilter('produto_tarefas_dart.id', '>=', $filterVar)); 

        TTransaction::open('darti_db');

            $filterVar = Configuracao::get_ids_produtos_by_projeto(TSession::getValue("projeto_id"));
            $criteria_id->add(new TFilter('produto_tarefas_dart.id', 'in', $filterVar)); 

        TTransaction::close();
        // -----

        $id = new BPieChart('id');


        $id->setDatabase('darti_db');
        $id->setFieldValue("produto_tarefas_dart.id_tarefas_dart");
        $id->setFieldColor("produto_tarefas_dart.color");
        $id->setFieldGroup("produto_tarefas_dart.descricao");
        $id->setModel('ProdutoTarefasDart');
        $id->setTitle("TEste");
        $id->setTotal('count');
        $id->showLegend(true);
        $id->enableOrderByValue('asc');
        $id->setCriteria($criteria_id);
        $id->setSize('100%', 280);
        $id->disableZoom();


        $row1 = $this->form->addFields([$id]);
        $row1->layout = [' col-sm-12'];

        // create the form actions

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Gestão","Grafico"]));
        }
        $container->add($this->form);

        parent::add($container);

    }

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open(self::$database); // open a transaction

                $object = new ProdutoTarefasDart($key); // instantiates the Active Record 

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

    } 

    public static function getFormName()
    {
        return self::$formName;
    }

}

