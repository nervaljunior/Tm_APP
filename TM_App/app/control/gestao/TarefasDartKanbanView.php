<?php

class TarefasDartKanbanView extends TPage
{
    private static $database = 'darti_db';
    private static $activeRecord = 'TarefasDart';
    private static $primaryKey = 'id';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        try
        {
            parent::__construct();

            $kanban = new TKanban;
            $kanban->setItemDatabase(self::$database);
            $kanban->enableTopScrollbar();

            $criteriaStage = new TCriteria();
            $criteriaItem = new TCriteria();

            $criteriaStage->setProperty('order', 'id asc');
            $criteriaItem->setProperty('order', 'data_de_inicio asc');

            $filterVar = TSession::getValue("projeto_id");
            $criteriaStage->add(new TFilter('id', 'in', "(SELECT status_tarefa_id FROM tarefas_dart WHERE produtos_dart_id in  (SELECT id FROM produtos_dart WHERE projeto_id = '{$filterVar}') )")); 

            TTransaction::open(self::$database);
            $stages = StatusTarefa::getObjects($criteriaStage);
            $items  = TarefasDart::getObjects($criteriaItem);

            if($stages)
            {
                foreach ($stages as $key => $stage)
                {

                    $kanban->addStage($stage->id, "{descricao}", $stage ,$stage->color);

                }    
            }

            if($items)
            {
                foreach ($items as $key => $item)
                {

                    $item->data_de_inicio = call_user_func(function($value, $object, $row) 
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
                    }, $item->data_de_inicio, $item, null);

                    $item->data_prevista = call_user_func(function($value, $object, $row) 
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
                    }, $item->data_prevista, $item, null);

                    $item->data_de_termino = call_user_func(function($value, $object, $row) 
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
                    }, $item->data_de_termino, $item, null);

                    $kanban->addItem($item->id, $item->status_tarefa_id, "{nome_da_tarefa}", " Produto: {produtos_dart->nome_do_produto} <br>
Data Inicio: {data_de_inicio}  <br>
Data Previsão: {data_prevista} <br>
Descrição: {descricao} ", $item->status_tarefa->color, $item);

                }    
            }

         $users_id = Configuracao::get_users_permitidos();
        if(in_array(TSession::getValue('userid'),$users_id)){

            $kanban->setItemDropAction(new TAction([__CLASS__, 'onUpdateItemDropB']));

            }

            //$kanban->setTemplatePath('app/resources/card.html');

            TTransaction::close();

            $container = new TVBox;

            $container->style = 'width: 100%';
            $container->class = 'form-container';
            if(empty($param['target_container']))
            {
                $container->add(TBreadCrumb::create(["Gestão","Kanban"]));
            }
            $container->add($kanban);

            parent::add($container);
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onShow($param = null)
    {

    } 

    public static function onUpdateItemDropB($param = null) 
    {
        try
        {
            TTransaction::open(self::$database);

            if (!empty($param['order']))
            {
                foreach ($param['order'] as $key => $id)
                {
                    $sequence = ++$key;

                    $item = new TarefasDart($id);
                    $item->status_tarefa_id = $param['stage_id'];

                    if(StatusTarefa::CONCLUIDO == $param['stage_id']){
                        $item->data_de_termino = date("Y-m-d H:i:s");
                    }

                    $item->store();

                    if($id == $param['key'])
                    {
                        TScript::create("$(\"div[item_id='{$param['key']}']\").css('border-top', '3px solid {$item->status_tarefa->color}');");
                    }

                }

                TTransaction::close();

                $pageParam = ['target_container' => "bpage_grantt"]; 
                $class = "TarefasDartGanttFormView";
                $metodo = "onReload";

                TApplication::loadPage($class, $metodo, $pageParam);

                $pageParam = ['target_container' => "bpage_grafico"]; 
                $class = "ProdutoTarefasDartForm";
                $metodo = "onShow";

                TApplication::loadPage($class, $metodo, $pageParam);

            }
        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

}

