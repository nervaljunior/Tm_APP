<?php

class TarefasDart extends TRecord
{
    const TABLENAME  = 'tarefas_dart';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $status_tarefa;
    private $produtos_dart;
    private $system_users;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('produtos_dart_id');
        parent::addAttribute('status_tarefa_id');
        parent::addAttribute('nome_da_tarefa');
        parent::addAttribute('data_de_inicio');
        parent::addAttribute('data_prevista');
        parent::addAttribute('data_de_termino');
        parent::addAttribute('system_users_id');
        parent::addAttribute('descricao');
            
    }

    /**
     * Method set_status_tarefa
     * Sample of usage: $var->status_tarefa = $object;
     * @param $object Instance of StatusTarefa
     */
    public function set_status_tarefa(StatusTarefa $object)
    {
        $this->status_tarefa = $object;
        $this->status_tarefa_id = $object->id;
    }

    /**
     * Method get_status_tarefa
     * Sample of usage: $var->status_tarefa->attribute;
     * @returns StatusTarefa instance
     */
    public function get_status_tarefa()
    {
    
        // loads the associated object
        if (empty($this->status_tarefa))
            $this->status_tarefa = new StatusTarefa($this->status_tarefa_id);
    
        // returns the associated object
        return $this->status_tarefa;
    }
    /**
     * Method set_produtos_dart
     * Sample of usage: $var->produtos_dart = $object;
     * @param $object Instance of ProdutosDart
     */
    public function set_produtos_dart(ProdutosDart $object)
    {
        $this->produtos_dart = $object;
        $this->produtos_dart_id = $object->id;
    }

    /**
     * Method get_produtos_dart
     * Sample of usage: $var->produtos_dart->attribute;
     * @returns ProdutosDart instance
     */
    public function get_produtos_dart()
    {
    
        // loads the associated object
        if (empty($this->produtos_dart))
            $this->produtos_dart = new ProdutosDart($this->produtos_dart_id);
    
        // returns the associated object
        return $this->produtos_dart;
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

    
}

