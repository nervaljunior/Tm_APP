<?php

class ProdutosDartForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'darti_db';
    private static $activeRecord = 'ProdutosDart';
    private static $primaryKey = 'id';
    private static $formName = 'form_ProdutosDartForm';

    use BuilderMasterDetailTrait;

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


        $projeto_id = new TDBCombo('projeto_id', 'darti_db', 'ProjetoDart', 'id', '{nome_projeto}','nome_projeto asc'  );
        $nome_do_produto = new TEntry('nome_do_produto');
        $system_users_id = new THidden('system_users_id');
        $id = new THidden('id');
        $descricao = new TText('descricao');
        $tarefas_dart_produtos_dart_id = new THidden('tarefas_dart_produtos_dart_id');
        $tarefas_dart_produtos_dart_nome_da_tarefa = new TEntry('tarefas_dart_produtos_dart_nome_da_tarefa');
        $tarefas_dart_produtos_dart_status_tarefa_id = new TDBCombo('tarefas_dart_produtos_dart_status_tarefa_id', 'darti_db', 'StatusTarefa', 'id', '{descricao}','descricao asc'  );
        $tarefas_dart_produtos_dart_data_de_inicio = new TDate('tarefas_dart_produtos_dart_data_de_inicio');
        $tarefas_dart_produtos_dart_data_prevista = new TDate('tarefas_dart_produtos_dart_data_prevista');
        $tarefas_dart_produtos_dart_data_de_termino = new TDate('tarefas_dart_produtos_dart_data_de_termino');
        $tarefas_dart_produtos_dart_system_users_id = new THidden('tarefas_dart_produtos_dart_system_users_id');
        $button_adicionar_tarefa_tarefas_dart_produtos_dart = new TButton('button_adicionar_tarefa_tarefas_dart_produtos_dart');

        $projeto_id->addValidation("Projeto id", new TRequiredValidator()); 
        $nome_do_produto->addValidation("Nome do produto", new TRequiredValidator()); 

        $projeto_id->setEditable(false);
        $button_adicionar_tarefa_tarefas_dart_produtos_dart->setAction(new TAction([$this, 'onAddDetailTarefasDartProdutosDart'],['static' => 1]), "Adicionar Tarefa");
        $button_adicionar_tarefa_tarefas_dart_produtos_dart->addStyleClass('btn-success');
        $button_adicionar_tarefa_tarefas_dart_produtos_dart->setImage('fas:plus #FFFFFF');
        $projeto_id->enableSearch();
        $tarefas_dart_produtos_dart_status_tarefa_id->enableSearch();

        $nome_do_produto->setMaxLength(255);
        $tarefas_dart_produtos_dart_nome_da_tarefa->setMaxLength(255);

        $projeto_id->setValue(TSession::getValue("projeto_id"));
        $system_users_id->setValue(TSession::getValue("userid"));
        $tarefas_dart_produtos_dart_system_users_id->setValue(TSession::getValue("userid"));

        $tarefas_dart_produtos_dart_data_prevista->setMask('dd/mm/yyyy');
        $tarefas_dart_produtos_dart_data_de_inicio->setMask('dd/mm/yyyy');
        $tarefas_dart_produtos_dart_data_de_termino->setMask('dd/mm/yyyy');

        $tarefas_dart_produtos_dart_data_prevista->setDatabaseMask('yyyy-mm-dd');
        $tarefas_dart_produtos_dart_data_de_inicio->setDatabaseMask('yyyy-mm-dd');
        $tarefas_dart_produtos_dart_data_de_termino->setDatabaseMask('yyyy-mm-dd');

        $id->setSize(200);
        $projeto_id->setSize('100%');
        $system_users_id->setSize(200);
        $descricao->setSize('100%', 100);
        $nome_do_produto->setSize('100%');
        $tarefas_dart_produtos_dart_id->setSize(200);
        $tarefas_dart_produtos_dart_system_users_id->setSize(200);
        $tarefas_dart_produtos_dart_data_prevista->setSize('100%');
        $tarefas_dart_produtos_dart_nome_da_tarefa->setSize('100%');
        $tarefas_dart_produtos_dart_data_de_inicio->setSize('100%');
        $tarefas_dart_produtos_dart_data_de_termino->setSize('100%');
        $tarefas_dart_produtos_dart_status_tarefa_id->setSize('100%');



        $button_adicionar_tarefa_tarefas_dart_produtos_dart->id = '6509ccc09d3f5';

        $this->form->appendPage("Dados Produto");

        $this->form->addFields([new THidden('current_tab')]);
        $this->form->setTabFunction("$('[name=current_tab]').val($(this).attr('data-current_page'));");

        $row1 = $this->form->addFields([new TLabel("Projeto:", '#000000', '14px', null, '100%'),$projeto_id]);
        $row1->layout = [' col-sm-12'];

        $row2 = $this->form->addFields([new TLabel("Nome do produto:", '#000000', '14px', null, '100%'),$nome_do_produto],[$system_users_id,$id]);
        $row2->layout = [' col-sm-12','col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("Descrição:", null, '14px', null, '100%'),$descricao]);
        $row3->layout = [' col-sm-12'];

        $this->form->appendPage("Tarefas");

        $this->detailFormTarefasDartProdutosDart = new BootstrapFormBuilder('detailFormTarefasDartProdutosDart');
        $this->detailFormTarefasDartProdutosDart->setProperty('style', 'border:none; box-shadow:none; width:100%;');

        $this->detailFormTarefasDartProdutosDart->setProperty('class', 'form-horizontal builder-detail-form');

        $row4 = $this->detailFormTarefasDartProdutosDart->addFields([$tarefas_dart_produtos_dart_id,new TLabel("Nome da tarefa:", '#000000', '14px', null, '100%'),$tarefas_dart_produtos_dart_nome_da_tarefa],[new TLabel("Status tarefa id:", '#000000', '14px', null, '100%'),$tarefas_dart_produtos_dart_status_tarefa_id]);
        $row4->layout = [' col-sm-8',' col-sm-4'];

        $row5 = $this->detailFormTarefasDartProdutosDart->addFields([new TLabel("Data de inicio:", null, '14px', null, '100%'),$tarefas_dart_produtos_dart_data_de_inicio],[new TLabel("Data prevista:", null, '14px', null, '100%'),$tarefas_dart_produtos_dart_data_prevista],[new TLabel("Data de termino:", null, '14px', null, '100%'),$tarefas_dart_produtos_dart_data_de_termino]);
        $row5->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $row6 = $this->detailFormTarefasDartProdutosDart->addFields([$tarefas_dart_produtos_dart_system_users_id]);
        $row6->layout = ['col-sm-6'];

        $row7 = $this->detailFormTarefasDartProdutosDart->addFields([],[$button_adicionar_tarefa_tarefas_dart_produtos_dart]);
        $row7->layout = [' col-sm-5',' col-sm-6'];

        $row8 = $this->detailFormTarefasDartProdutosDart->addFields([new THidden('tarefas_dart_produtos_dart__row__id')]);
        $this->tarefas_dart_produtos_dart_criteria = new TCriteria();

        $this->tarefas_dart_produtos_dart_list = new BootstrapDatagridWrapper(new TDataGrid);
        $this->tarefas_dart_produtos_dart_list->disableHtmlConversion();;
        $this->tarefas_dart_produtos_dart_list->generateHiddenFields();
        $this->tarefas_dart_produtos_dart_list->setId('tarefas_dart_produtos_dart_list');

        $this->tarefas_dart_produtos_dart_list->style = 'width:100%';
        $this->tarefas_dart_produtos_dart_list->class .= ' table-bordered';

        $column_tarefas_dart_produtos_dart_nome_da_tarefa = new TDataGridColumn('nome_da_tarefa', "Nome da tarefa", 'left');
        $column_tarefas_dart_produtos_dart_data_de_inicio_transformed = new TDataGridColumn('data_de_inicio', "Data de inicio", 'center');
        $column_tarefas_dart_produtos_dart_data_prevista_transformed = new TDataGridColumn('data_prevista', "Data prevista", 'center');
        $column_tarefas_dart_produtos_dart_data_de_termino_transformed = new TDataGridColumn('data_de_termino', "Data de termino", 'center');
        $column_tarefas_dart_produtos_dart_status_tarefa_descricao_transformed = new TDataGridColumn('status_tarefa->descricao', "Status tarefa", 'center');

        $column_tarefas_dart_produtos_dart__row__data = new TDataGridColumn('__row__data', '', 'center');
        $column_tarefas_dart_produtos_dart__row__data->setVisibility(false);

        $action_onEditDetailTarefasDart = new TDataGridAction(array('ProdutosDartForm', 'onEditDetailTarefasDart'));
        $action_onEditDetailTarefasDart->setUseButton(false);
        $action_onEditDetailTarefasDart->setButtonClass('btn btn-default btn-sm');
        $action_onEditDetailTarefasDart->setLabel("Editar");
        $action_onEditDetailTarefasDart->setImage('far:edit #478fca');
        $action_onEditDetailTarefasDart->setFields(['__row__id', '__row__data']);

        $this->tarefas_dart_produtos_dart_list->addAction($action_onEditDetailTarefasDart);
        $action_onDeleteDetailTarefasDart = new TDataGridAction(array('ProdutosDartForm', 'onDeleteDetailTarefasDart'));
        $action_onDeleteDetailTarefasDart->setUseButton(false);
        $action_onDeleteDetailTarefasDart->setButtonClass('btn btn-default btn-sm');
        $action_onDeleteDetailTarefasDart->setLabel("Excluir");
        $action_onDeleteDetailTarefasDart->setImage('fas:trash-alt #dd5a43');
        $action_onDeleteDetailTarefasDart->setFields(['__row__id', '__row__data']);

        $this->tarefas_dart_produtos_dart_list->addAction($action_onDeleteDetailTarefasDart);

        $this->tarefas_dart_produtos_dart_list->addColumn($column_tarefas_dart_produtos_dart_nome_da_tarefa);
        $this->tarefas_dart_produtos_dart_list->addColumn($column_tarefas_dart_produtos_dart_data_de_inicio_transformed);
        $this->tarefas_dart_produtos_dart_list->addColumn($column_tarefas_dart_produtos_dart_data_prevista_transformed);
        $this->tarefas_dart_produtos_dart_list->addColumn($column_tarefas_dart_produtos_dart_data_de_termino_transformed);
        $this->tarefas_dart_produtos_dart_list->addColumn($column_tarefas_dart_produtos_dart_status_tarefa_descricao_transformed);

        $this->tarefas_dart_produtos_dart_list->addColumn($column_tarefas_dart_produtos_dart__row__data);

        $this->tarefas_dart_produtos_dart_list->createModel();
        $tableResponsiveDiv = new TElement('div');
        $tableResponsiveDiv->class = 'table-responsive';
        $tableResponsiveDiv->add($this->tarefas_dart_produtos_dart_list);
        $this->detailFormTarefasDartProdutosDart->addContent([$tableResponsiveDiv]);

        $column_tarefas_dart_produtos_dart_data_de_inicio_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim($value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });

        $column_tarefas_dart_produtos_dart_data_prevista_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim($value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });

        $column_tarefas_dart_produtos_dart_data_de_termino_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim($value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });

        $column_tarefas_dart_produtos_dart_status_tarefa_descricao_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {

            $class = 'badge badge-pill';
            $icon  = '';
            $text  = $object->status_tarefa->descricao;
            $background_color = $object->status_tarefa->color;

            return "<span class='{$class}' style='color:#FFF; background-color:{$background_color}' > <i class='{$icon}'></i> &nbsp;{$text} &nbsp;</span>"; 

        });        $row9 = $this->form->addFields([$this->detailFormTarefasDartProdutosDart]);
        $row9->layout = [' col-sm-12'];

        // create the form actions
        $btn_onshow = $this->form->addAction("Voltar", new TAction(['ProdutosDartList', 'onShow']), 'fas:arrow-left #000000');
        $this->btn_onshow = $btn_onshow;

        $btn_onsave = $this->form->addAction("Salvar Produto", new TAction([$this, 'onSave'],['static' => 1]), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Gestão","Cadastro de produto"]));
        }
        $container->add($this->form);

        parent::add($container);

    }

    public  function onAddDetailTarefasDartProdutosDart($param = null) 
    {
        try
        {
            $data = $this->form->getData();

            $errors = [];
            $requiredFields = [];
            $requiredFields[] = ['label'=>"Nome da tarefa", 'name'=>"tarefas_dart_produtos_dart_nome_da_tarefa", 'class'=>'TRequiredValidator', 'value'=>[]];
            $requiredFields[] = ['label'=>"Status tarefa id", 'name'=>"tarefas_dart_produtos_dart_status_tarefa_id", 'class'=>'TRequiredValidator', 'value'=>[]];
            foreach($requiredFields as $requiredField)
            {
                try
                {
                    (new $requiredField['class'])->validate($requiredField['label'], $data->{$requiredField['name']}, $requiredField['value']);
                }
                catch(Exception $e)
                {
                    $errors[] = $e->getMessage() . '.';
                }
             }
             if(count($errors) > 0)
             {
                 throw new Exception(implode('<br>', $errors));
             }

            $__row__id = !empty($data->tarefas_dart_produtos_dart__row__id) ? $data->tarefas_dart_produtos_dart__row__id : 'b'.uniqid();

            TTransaction::open(self::$database);

            $grid_data = new TarefasDart();
            $grid_data->__row__id = $__row__id;
            $grid_data->id = $data->tarefas_dart_produtos_dart_id;
            $grid_data->nome_da_tarefa = $data->tarefas_dart_produtos_dart_nome_da_tarefa;
            $grid_data->status_tarefa_id = $data->tarefas_dart_produtos_dart_status_tarefa_id;
            $grid_data->data_de_inicio = $data->tarefas_dart_produtos_dart_data_de_inicio;
            $grid_data->data_prevista = $data->tarefas_dart_produtos_dart_data_prevista;
            $grid_data->data_de_termino = $data->tarefas_dart_produtos_dart_data_de_termino;
            $grid_data->system_users_id = $data->tarefas_dart_produtos_dart_system_users_id;

            $__row__data = array_merge($grid_data->toArray(), (array)$grid_data->getVirtualData());
            $__row__data['__row__id'] = $__row__id;
            $__row__data['__display__']['id'] =  $param['tarefas_dart_produtos_dart_id'] ?? null;
            $__row__data['__display__']['nome_da_tarefa'] =  $param['tarefas_dart_produtos_dart_nome_da_tarefa'] ?? null;
            $__row__data['__display__']['status_tarefa_id'] =  $param['tarefas_dart_produtos_dart_status_tarefa_id'] ?? null;
            $__row__data['__display__']['data_de_inicio'] =  $param['tarefas_dart_produtos_dart_data_de_inicio'] ?? null;
            $__row__data['__display__']['data_prevista'] =  $param['tarefas_dart_produtos_dart_data_prevista'] ?? null;
            $__row__data['__display__']['data_de_termino'] =  $param['tarefas_dart_produtos_dart_data_de_termino'] ?? null;
            $__row__data['__display__']['system_users_id'] =  $param['tarefas_dart_produtos_dart_system_users_id'] ?? null;

            $grid_data->__row__data = base64_encode(serialize((object)$__row__data));
            $row = $this->tarefas_dart_produtos_dart_list->addItem($grid_data);
            $row->id = $grid_data->__row__id;

            TDataGrid::replaceRowById('tarefas_dart_produtos_dart_list', $grid_data->__row__id, $row);

            TTransaction::close();

            $data = new stdClass;
            $data->tarefas_dart_produtos_dart_id = '';
            $data->tarefas_dart_produtos_dart_nome_da_tarefa = '';
            $data->tarefas_dart_produtos_dart_status_tarefa_id = '';
            $data->tarefas_dart_produtos_dart_data_de_inicio = '';
            $data->tarefas_dart_produtos_dart_data_prevista = '';
            $data->tarefas_dart_produtos_dart_data_de_termino = '';
            $data->tarefas_dart_produtos_dart_system_users_id = TSession::getValue("userid");
            $data->tarefas_dart_produtos_dart__row__id = '';

            TForm::sendData(self::$formName, $data);
            TScript::create("
               var element = $('#6509ccc09d3f5');
               if(typeof element.attr('add') != 'undefined')
               {
                   element.html(base64_decode(element.attr('add')));
               }
            ");

        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }

    public static function onEditDetailTarefasDart($param = null) 
    {
        try
        {

            $__row__data = unserialize(base64_decode($param['__row__data']));
            $__row__data->__display__ = is_array($__row__data->__display__) ? (object) $__row__data->__display__ : $__row__data->__display__;

            $data = new stdClass;
            $data->tarefas_dart_produtos_dart_id = $__row__data->__display__->id ?? null;
            $data->tarefas_dart_produtos_dart_nome_da_tarefa = $__row__data->__display__->nome_da_tarefa ?? null;
            $data->tarefas_dart_produtos_dart_status_tarefa_id = $__row__data->__display__->status_tarefa_id ?? null;
            $data->tarefas_dart_produtos_dart_data_de_inicio = $__row__data->__display__->data_de_inicio ?? null;
            $data->tarefas_dart_produtos_dart_data_prevista = $__row__data->__display__->data_prevista ?? null;
            $data->tarefas_dart_produtos_dart_data_de_termino = $__row__data->__display__->data_de_termino ?? null;
            $data->tarefas_dart_produtos_dart_system_users_id = $__row__data->__display__->system_users_id ?? null;
            $data->tarefas_dart_produtos_dart__row__id = $__row__data->__row__id;

            TForm::sendData(self::$formName, $data);
            TScript::create("
               var element = $('#6509ccc09d3f5');
               if(!element.attr('add')){
                   element.attr('add', base64_encode(element.html()));
               }
               element.html(\"<span><i class='far fa-edit' style='color:#478fca;padding-right:4px;'></i>Editar</span>\");
               if(!element.attr('edit')){
                   element.attr('edit', base64_encode(element.html()));
               }
            ");

        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public static function onDeleteDetailTarefasDart($param = null) 
    {
        try
        {

            $__row__data = unserialize(base64_decode($param['__row__data']));

            $data = new stdClass;
            $data->tarefas_dart_produtos_dart_id = '';
            $data->tarefas_dart_produtos_dart_nome_da_tarefa = '';
            $data->tarefas_dart_produtos_dart_status_tarefa_id = '';
            $data->tarefas_dart_produtos_dart_data_de_inicio = '';
            $data->tarefas_dart_produtos_dart_data_prevista = '';
            $data->tarefas_dart_produtos_dart_data_de_termino = '';
            $data->tarefas_dart_produtos_dart_system_users_id = '';
            $data->tarefas_dart_produtos_dart__row__id = '';

            TForm::sendData(self::$formName, $data);

            TDataGrid::removeRowById('tarefas_dart_produtos_dart_list', $__row__data->__row__id);

        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new ProdutosDart(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 

            TForm::sendData(self::$formName, (object)['id' => $object->id]);

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

//<generatedAutoCode>
            $this->tarefas_dart_produtos_dart_criteria->setProperty('order', 'id asc');
//</generatedAutoCode>
            $tarefas_dart_produtos_dart_items = $this->storeMasterDetailItems('TarefasDart', 'produtos_dart_id', 'tarefas_dart_produtos_dart', $object, $param['tarefas_dart_produtos_dart_list___row__data'] ?? [], $this->form, $this->tarefas_dart_produtos_dart_list, function($masterObject, $detailObject){ 

              $detailObject->system_users_id = TSession::getValue("userid");

            }, $this->tarefas_dart_produtos_dart_criteria); 

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

                $pageParam = ['target_container' => "bpage_grantt"]; 
                $class = "TarefasDartGanttFormView";
                $metodo = "onReload";

                TApplication::loadPage($class, $metodo, $pageParam);

                $pageParam = ['target_container' => "bpage_kanban", "static" => 1];  
                $class = "TarefasDartKanbanView";
                $metodo = "onShow";

                TApplication::loadPage($class, $metodo, $pageParam);

                $pageParam = ['target_container' => "bpage_grafico"]; 
                $class = "ProdutoTarefasDartForm";
                $metodo = "onShow";

                TApplication::loadPage($class, $metodo, $pageParam);

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('ProdutosDartList', 'onShow', $loadPageParam); 

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

                $object = new ProdutosDart($key); // instantiates the Active Record 

//<generatedAutoCode>
                $this->tarefas_dart_produtos_dart_criteria->setProperty('order', 'id asc');
//</generatedAutoCode>
                $tarefas_dart_produtos_dart_items = $this->loadMasterDetailItems('TarefasDart', 'produtos_dart_id', 'tarefas_dart_produtos_dart', $object, $this->form, $this->tarefas_dart_produtos_dart_list, $this->tarefas_dart_produtos_dart_criteria, function($masterObject, $detailObject, $objectItems){ 

                    //code here

                }); 

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

}

