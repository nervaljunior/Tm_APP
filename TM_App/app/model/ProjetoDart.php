<?php

class ProjetoDart extends TRecord
{
    const TABLENAME  = 'projeto_dart';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

         public $users_permitidos;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome_projeto');
        parent::addAttribute('cor');
        parent::addAttribute('descricao');
    
    }

    /**
     * Method getProdutosDarts
     */
    public function getProdutosDarts()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('projeto_id', '=', $this->id));
        return ProdutosDart::getObjects( $criteria );
    }
    /**
     * Method getUserPermissionProjetos
     */
    public function getUserPermissionProjetos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('projeto_dart_id', '=', $this->id));
        return UserPermissionProjeto::getObjects( $criteria );
    }

    public function set_produtos_dart_projeto_to_string($produtos_dart_projeto_to_string)
    {
        if(is_array($produtos_dart_projeto_to_string))
        {
            $values = ProjetoDart::where('id', 'in', $produtos_dart_projeto_to_string)->getIndexedArray('nome_projeto', 'nome_projeto');
            $this->produtos_dart_projeto_to_string = implode(', ', $values);
        }
        else
        {
            $this->produtos_dart_projeto_to_string = $produtos_dart_projeto_to_string;
        }

        $this->vdata['produtos_dart_projeto_to_string'] = $this->produtos_dart_projeto_to_string;
    }

    public function get_produtos_dart_projeto_to_string()
    {
        if(!empty($this->produtos_dart_projeto_to_string))
        {
            return $this->produtos_dart_projeto_to_string;
        }
    
        $values = ProdutosDart::where('projeto_id', '=', $this->id)->getIndexedArray('projeto_id','{projeto->nome_projeto}');
        return implode(', ', $values);
    }

    public function set_user_permission_projeto_projeto_dart_to_string($user_permission_projeto_projeto_dart_to_string)
    {
        if(is_array($user_permission_projeto_projeto_dart_to_string))
        {
            $values = ProjetoDart::where('id', 'in', $user_permission_projeto_projeto_dart_to_string)->getIndexedArray('nome_projeto', 'nome_projeto');
            $this->user_permission_projeto_projeto_dart_to_string = implode(', ', $values);
        }
        else
        {
            $this->user_permission_projeto_projeto_dart_to_string = $user_permission_projeto_projeto_dart_to_string;
        }

        $this->vdata['user_permission_projeto_projeto_dart_to_string'] = $this->user_permission_projeto_projeto_dart_to_string;
    }

    public function get_user_permission_projeto_projeto_dart_to_string()
    {
        if(!empty($this->user_permission_projeto_projeto_dart_to_string))
        {
            return $this->user_permission_projeto_projeto_dart_to_string;
        }
    
        $values = UserPermissionProjeto::where('projeto_dart_id', '=', $this->id)->getIndexedArray('projeto_dart_id','{projeto_dart->nome_projeto}');
        return implode(', ', $values);
    }

 
      public function get_users_permitidos(){
    
        $this->users_permitidos = array(1,2);
    
        return $this->users_permitidos;
    
    }

}

