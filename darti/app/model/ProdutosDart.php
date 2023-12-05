<?php

class ProdutosDart extends TRecord
{
    const TABLENAME  = 'produtos_dart';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $system_users;
    private $projeto;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('projeto_id');
        parent::addAttribute('nome_do_produto');
        parent::addAttribute('system_users_id');
        parent::addAttribute('descricao');
            
    }

    /**
     * Method set_system_users
     * Sample of usage: $var->system_users = $object;
     * @param $object Instance of SystemUsers
     */
    public function set_system_users(SystemUsers $object)
    {
        $this->system_users = $object;
        $this->system_users_id = $object->id;
    }

    /**
     * Method get_system_users
     * Sample of usage: $var->system_users->attribute;
     * @returns SystemUsers instance
     */
    public function get_system_users()
    {
        TTransaction::open('permission');
        // loads the associated object
        if (empty($this->system_users))
            $this->system_users = new SystemUsers($this->system_users_id);
        TTransaction::close();
        // returns the associated object
        return $this->system_users;
    }
    /**
     * Method set_projeto_dart
     * Sample of usage: $var->projeto_dart = $object;
     * @param $object Instance of ProjetoDart
     */
    public function set_projeto(ProjetoDart $object)
    {
        $this->projeto = $object;
        $this->projeto_id = $object->id;
    }

    /**
     * Method get_projeto
     * Sample of usage: $var->projeto->attribute;
     * @returns ProjetoDart instance
     */
    public function get_projeto()
    {
    
        // loads the associated object
        if (empty($this->projeto))
            $this->projeto = new ProjetoDart($this->projeto_id);
    
        // returns the associated object
        return $this->projeto;
    }

    /**
     * Method getTarefasDarts
     */
    public function getTarefasDarts()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('produtos_dart_id', '=', $this->id));
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
    
        $values = TarefasDart::where('produtos_dart_id', '=', $this->id)->getIndexedArray('produtos_dart_id','{produtos_dart->id}');
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
    
        $values = TarefasDart::where('produtos_dart_id', '=', $this->id)->getIndexedArray('status_tarefa_id','{status_tarefa->descricao}');
        return implode(', ', $values);
    }

    
}

