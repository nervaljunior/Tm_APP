<?php

class ProdutoTarefasDart extends TRecord
{
    const TABLENAME  = 'produto_tarefas_dart';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'max'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome_do_produto');
        parent::addAttribute('id_status_tarefa');
        parent::addAttribute('descricao');
        parent::addAttribute('color');
        parent::addAttribute('id_tarefas_dart');
        parent::addAttribute('status_tarefa_id');
        parent::addAttribute('nome_da_tarefa');
        parent::addAttribute('data_de_inicio');
        parent::addAttribute('data_prevista');
        parent::addAttribute('data_de_termino');
            
    }

    
}

