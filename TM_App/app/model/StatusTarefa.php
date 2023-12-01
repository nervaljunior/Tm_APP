<?php

class StatusTarefa extends TRecord
{
    const TABLENAME  = 'status_tarefa';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const PENDENTE = '1';
    const EM_ANDAMENTO = '2';
    const CONCLUIDO = '3';
    const ATRASADA = '4';

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
        parent::addAttribute('color');
            
    }

    /**
     * Method getTarefasDarts
     */
    public function getTarefasDarts()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('status_tarefa_id', '=', $this->id));
        return TarefasDart::getObjects( $criteria );
    }

    public function set_tarefas_dart_produtos_dart_to_string($tarefas_dart_produtos_dart_to_string)
    {
        if(is_array($tarefas_dart_produtos_dart_to_string))
        {
            $values = ProdutosDart::where('id', 'in', $tarefas_dart_produtos_dart_to_string)->getIndexedArray('id', 'id');
            $this->tarefas_dart_produtos_dart_to_string = implode(', ', $values);
        }
        else
        {
            $this->tarefas_dart_produtos_dart_to_string = $tarefas_dart_produtos_dart_to_string;
        }

        $this->vdata['tarefas_dart_produtos_dart_to_string'] = $this->tarefas_dart_produtos_dart_to_string;
    }

    public function get_tarefas_dart_produtos_dart_to_string()
    {
        if(!empty($this->tarefas_dart_produtos_dart_to_string))
        {
            return $this->tarefas_dart_produtos_dart_to_string;
        }
    
        $values = TarefasDart::where('status_tarefa_id', '=', $this->id)->getIndexedArray('produtos_dart_id','{produtos_dart->id}');
        return implode(', ', $values);
    }

    public function set_tarefas_dart_status_tarefa_to_string($tarefas_dart_status_tarefa_to_string)
    {
        if(is_array($tarefas_dart_status_tarefa_to_string))
        {
            $values = StatusTarefa::where('id', 'in', $tarefas_dart_status_tarefa_to_string)->getIndexedArray('descricao', 'descricao');
            $this->tarefas_dart_status_tarefa_to_string = implode(', ', $values);
        }
        else
        {
            $this->tarefas_dart_status_tarefa_to_string = $tarefas_dart_status_tarefa_to_string;
        }

        $this->vdata['tarefas_dart_status_tarefa_to_string'] = $this->tarefas_dart_status_tarefa_to_string;
    }

    public function get_tarefas_dart_status_tarefa_to_string()
    {
        if(!empty($this->tarefas_dart_status_tarefa_to_string))
        {
            return $this->tarefas_dart_status_tarefa_to_string;
        }
    
        $values = TarefasDart::where('status_tarefa_id', '=', $this->id)->getIndexedArray('status_tarefa_id','{status_tarefa->descricao}');
        return implode(', ', $values);
    }

    
}

