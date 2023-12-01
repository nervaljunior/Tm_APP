<?php

class TarefasDartGanttForm extends TWindow
{
    protected $form;
    private $formFields = [];
    private static $database = 'darti_db';
    private static $activeRecord = 'TarefasDart';
    private static $primaryKey = 'id';
    private static $formName = 'form_TarefasDartGanttForm';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        parent::setSize(0.8, null);
        parent::setTitle("Tarefa");
        parent::setProperty('class', 'window_modal');

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Tarefa");

        $criteria_produtos_dart_id = new TCriteria();

        $filterVar = TSession::getValue("projeto_id");
        $criteria_produtos_dart_id->add(new TFilter('projeto_id', '=', $filterVar)); 

        $produtos_dart_id = new TDBCombo('produtos_dart_id', 'darti_db', 'ProdutosDart', 'id', '{nome_do_produto}','id asc' , $criteria_produtos_dart_id );
        $nome_da_tarefa = new TEntry('nome_da_tarefa');
        $status_tarefa_id = new TDBCombo('status_tarefa_id', 'darti_db', 'StatusTarefa', 'id', '{descricao}','descricao asc'  );
        $data_de_inicio = new TDate('data_de_inicio');
        $data_prevista = new TDate('data_prevista');
        $data_de_termino = new TDate('data_de_termino');
        $descricao = new TText('descricao');
        $system_users_id = new THidden('system_users_id');
        $id = new THidden('id');

        $produtos_dart_id->addValidation("Produto", new TRequiredValidator()); 
        $nome_da_tarefa->addValidation("Nome da tarefa", new TRequiredValidator()); 
        $status_tarefa_id->addValidation("Status tarefa id", new TRequiredValidator()); 

        $nome_da_tarefa->setMaxLength(255);
        $produtos_dart_id->enableSearch();
        $status_tarefa_id->enableSearch();

        $data_prevista->setMask('dd/mm/yyyy');
        $data_de_inicio->setMask('dd/mm/yyyy');
        $data_de_termino->setMask('dd/mm/yyyy');

        $data_prevista->setDatabaseMask('yyyy-mm-dd');
        $data_de_inicio->setDatabaseMask('yyyy-mm-dd');
        $data_de_termino->setDatabaseMask('yyyy-mm-dd');

        $data_prevista->setValue('NULL');
        $data_de_inicio->setValue('NULL');
        $data_de_termino->setValue('NULL');
        $system_users_id->setValue(TSession::getValue("userid"));

        $id->setSize(200);
        $system_users_id->setSize(200);
        $data_prevista->setSize('100%');
        $nome_da_tarefa->setSize('100%');
        $data_de_inicio->setSize('100%');
        $descricao->setSize('100%', 100);
        $data_de_termino->setSize('100%');
        $produtos_dart_id->setSize('100%');
        $status_tarefa_id->setSize('100%');


        $row1 = $this->form->addFields([new TLabel("Produto:", '#000000', '14px', null, '100%'),$produtos_dart_id]);
        $row1->layout = [' col-sm-12'];

        $row2 = $this->form->addFields([new TLabel("Nome da tarefa:", '#000000', '14px', null, '100%'),$nome_da_tarefa],[new TLabel("Status tarefa:", '#000000', '14px', null, '100%'),$status_tarefa_id]);
        $row2->layout = [' col-sm-8',' col-sm-4'];

        $row3 = $this->form->addFields([new TLabel("Data de inicio:", null, '14px', null, '100%'),$data_de_inicio],[new TLabel("Data prevista:", null, '14px', null, '100%'),$data_prevista],[new TLabel("Data de termino:", null, '14px', null, '100%'),$data_de_termino]);
        $row3->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $row4 = $this->form->addFields([new TLabel("Descrição:", null, '14px', null, '100%'),$descricao]);
        $row4->layout = [' col-sm-12'];

        $row5 = $this->form->addFields([$system_users_id,$id]);
        $row5->layout = ['col-sm-6'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_ondelete = $this->form->addAction("Excluir", new TAction([$this, 'onDelete']), 'fas:trash-alt #dd5a43');
        $this->btn_ondelete = $btn_ondelete;

        parent::add($this->form);

    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new TarefasDart(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 

            $messageAction = new TAction(['TarefasDartGanttFormView', 'onReload']);

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle'); 

                $pageParam = ['target_container' => "bpage_kanban", "static" => 1]; 
                $class = "TarefasDartKanbanView";
                $metodo = "onShow";

                TApplication::loadPage($class, $metodo, $pageParam);

                $pageParam = ['target_container' => "bpage_grafico"]; 
                $class = "ProdutoTarefasDartForm";
                $metodo = "onShow";

                TApplication::loadPage($class, $metodo, $pageParam);

                $pageParam = ['target_container' => "bpage_grantt"]; 

                TApplication::loadPage('TarefasDartGanttFormView', 'onReload', $pageParam);

                TWindow::closeWindow(parent::getId()); 

        }
        catch (Exception $e) // in case of exception
        {
            //</catchAutoCode> 

            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    public function onDelete($param = null) 
    {
        if(isset($param['delete']) && $param['delete'] == 1)
        {
            try
            {
                $key = $param[self::$primaryKey];

                // open a transaction with database
                TTransaction::open(self::$database);

                $class = self::$activeRecord;

                // instantiates object
                $object = new $class($key, FALSE);

                // deletes the object from the database
                $object->delete();

                // close the transaction
                TTransaction::close();

                $messageAction = new TAction(array(__CLASS__.'View', 'onReload'));

                // shows the success message
                new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $messageAction);
            }
            catch (Exception $e) // in case of exception
            {
                // shows the exception error message
                new TMessage('error', $e->getMessage());
                // undo all pending operations
                TTransaction::rollback();
            }
        }
        else
        {
            // define the delete action
            $action = new TAction(array($this, 'onDelete'));
            $action->setParameters((array) $this->form->getData());
            $action->setParameter('delete', 1);
            // shows a dialog to the user
            new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);   
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

                $object = new TarefasDart($key); // instantiates the Active Record 

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

    public function onStartEdit($param)
    {

        $this->form->clear(true);

        $data = new stdClass;
        $data->status_tarefa = new stdClass();
        $data->status_tarefa->color = '#3a87ad';
        $data->status_tarefa_id = $param['group_id'] ?? '';

        if (!empty($param['start_time']))
        {
            if ($param['start_time'])
            {
                $data->data_de_inicio = $param['start_time'];
            }
        }

        $this->form->setData( $data );
    }

    public static function onUpdateEvent($param)
    {
        try
        {
            if (isset($param['id']))
            {
                TTransaction::open(self::$database);

                $class = self::$activeRecord;
                $object = new $class($param['id']);

                $object->data_de_inicio = $param['start_time'];
                $object->data_prevista   = $param['end_time'];

                $object->store();

                // close the transaction
                TTransaction::close();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            TTransaction::rollback();
        }
    }

}

