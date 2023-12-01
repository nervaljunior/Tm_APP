<?php

class UserPermissionProjeto extends TRecord
{
    const TABLENAME  = 'user_permission_projeto';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $projeto_dart;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('projeto_dart_id');
        parent::addAttribute('user');
            
    }

    /**
     * Method set_projeto_dart
     * Sample of usage: $var->projeto_dart = $object;
     * @param $object Instance of ProjetoDart
     */
    public function set_projeto_dart(ProjetoDart $object)
    {
        $this->projeto_dart = $object;
        $this->projeto_dart_id = $object->id;
    }

    /**
     * Method get_projeto_dart
     * Sample of usage: $var->projeto_dart->attribute;
     * @returns ProjetoDart instance
     */
    public function get_projeto_dart()
    {
    
        // loads the associated object
        if (empty($this->projeto_dart))
            $this->projeto_dart = new ProjetoDart($this->projeto_dart_id);
    
        // returns the associated object
        return $this->projeto_dart;
    }

    
}

