<?php

class Configuracao
{
    
    public $users_permitidos = array(1,2);
    
    public function __construct($param = null){
        
    }

// Função para buscar um array com o id de todos os usuario com permissão de adm

    public static function get_users_permitidos(){
        
        $config = new Configuracao();
        
        $users_permitidos = $config->users_permitidos;
        
        return $users_permitidos;
        
    }
    
 // Função para buscar os ids de todos os produtos de uma projeto - $id_projeto = 1
public static function get_ids_produtos_by_projeto($id_projeto){
        
       $ProdutosDart = ProdutosDart::select("id")->where('projeto_id',  '=', $id_projeto)->load();
         
       $count_arr = [];
                
        foreach ($ProdutosDart as $value) {
            
           $count_arr[] = $value->id;
        }
                
        
        return $count_arr;
        
    }
    
// Função para buscar a quantidade de tarefas para dado N produtos - $array_id_produtos = [1,2]
public static function get_quant_tarefas_by_produtos($array_id_produtos){
        
        $count_tarefas = TarefasDart::where('produtos_dart_id',  'in', $array_id_produtos)->count();
        
        return $count_tarefas;
        
    }

}
