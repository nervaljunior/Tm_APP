<?php

class TarefasDartGanttFormView extends TPage
{
    private $gantt;
    private $loaded;
    private static $database = 'darti_db';

    function __construct($param = [])
    {
        parent::__construct();

        $this->gantt = new TGantt(TGantt::MODE_DAYS, 'xs');
        $this->gantt->enableStripedMonths();
        $this->gantt->enableStripedRows();
        $this->gantt->setReloadAction(new TAction([$this, 'onReload']));
        $this->gantt->setStartDate($param['start'] ?? date('Y-m-01'));
        $this->gantt->setInterval('31 days');
        $this->gantt->setTitle("Eventos");
        $this->gantt->enableFullHours();
        $this->gantt->enableViewModeButton(true, true, "Visão", 'fas:eye #333333');
        $this->gantt->enableSizeModeButton(true, true, "Zoom", 'fas:search-plus #333333');

        if (!empty(TSession::getValue(__CLASS__.'_gantt_view_mode')))
        {
            $this->gantt->setViewMode(TSession::getValue(__CLASS__.'_gantt_view_mode'));
        }

        if (!empty(TSession::getValue('gantt_size_mode')))
        {
            $this->gantt->setSizeMode(TSession::getValue('gantt_size_mode'));
        }

        $this->criteria_events = new TCriteria();
        $filterVar = TSession::getValue("projeto_id");
        $this->criteria_events->add(new TFilter('produtos_dart_id', 'in', "(SELECT id FROM produtos_dart WHERE projeto_id = '{$filterVar}')")); 

         $users_id = Configuracao::get_users_permitidos();
        if(in_array(TSession::getValue('userid'),$users_id)){

            $this->gantt->setEventClickAction(new TAction(['TarefasDartGanttForm', 'onEdit']));
            $this->gantt->setDayClickAction(new TAction(['TarefasDartGanttForm', 'onStartEdit']));
        }

        $criteria = new TCriteria();

        $criteria->setProperty('order', 'descricao desc');

        TTransaction::open('darti_db');

        $categories = StatusTarefa::getObjects($criteria);

        if($categories)
        {
            foreach($categories as $category)
            {

                $this->gantt->addRow($category->id, $category->render("{descricao}"));

            }
        }

        TTransaction::close();

        parent::add($this->gantt);
    }

    public function onReload($param = [])
    {
        try
        {
            if (! empty($param['start_time']))
            {
                $this->gantt->setStartDate($param['start_time']);
            }

            if (!empty($param['view_mode']))
            {
                TSession::setValue(__CLASS__.'_gantt_view_mode', $param['view_mode']);
                $this->gantt->setViewMode($param['view_mode']);
            }

            if (!empty($param['size_mode']))
            {
                TSession::setValue(__CLASS__.'_gantt_size_mode', $param['size_mode']);
                $this->gantt->setSizeMode($param['size_mode']);
            }

            $this->gantt->clearEvents();

            TTransaction::open('darti_db');

            $criteria = clone $this->criteria_events;

            $criteria->add(new TFilter('data_de_inicio', '<=', $this->gantt->getEndDate()));
            $criteria->add(new TFilter('data_prevista', '>=', $this->gantt->getStartDate()));

            $events = TarefasDart::getObjects($criteria);

            if ($events)
            {
                foreach ($events as $event)
                {
                    $percent = $event->data_de_termino;
                    $color = $event->status_tarefa->color;
                    $title = $event->render("{produtos_dart->nome_do_produto} - {nome_da_tarefa}");

                    $this->gantt->addEvent($event->id, $event->status_tarefa_id, $title, $event->data_de_inicio, $event->data_prevista, $color, $percent);

                }
            }

            TTransaction::close();

            $this->loaded = TRUE;
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    // show gantt
    public function show()
    {
        // check if the gantt is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) || $_GET['method'] !== 'onReload'))
        {
            $this->onReload( func_get_arg(0) );
        }

        parent::show();
    }

    public function onShow($param = null)
    {

    }

}

